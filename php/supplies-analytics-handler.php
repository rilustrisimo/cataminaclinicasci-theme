<?php
/**
 * AJAX handler for supplies analytics
 * Provides price-based analytics for actual and release supplies
 */

// Log file inclusion
error_log('supplies-analytics-handler.php loaded at ' . date('Y-m-d H:i:s'));

// Register AJAX actions
add_action("wp_ajax_get_analytics_data", "get_analytics_data");
add_action("wp_ajax_nopriv_get_analytics_data", "get_analytics_data"); // Allow for non-logged users if needed

error_log('Analytics AJAX actions registered');

/**
 * Get analytics data for supplies with price calculations
 */
function get_analytics_data() {
    try {
        // Log that function is called
        error_log('=== GET_ANALYTICS_DATA CALLED ===');
        error_log('POST data: ' . print_r($_POST, true));
        
        // Verify nonce
        if (!isset($_POST["nonce"]) || !wp_verify_nonce($_POST["nonce"], "supplies_analytics_nonce")) {
            error_log('Nonce verification failed');
            error_log('Received nonce: ' . (isset($_POST["nonce"]) ? $_POST["nonce"] : 'NOT SET'));
            wp_send_json_error("Security check failed");
            return;
        }
    
    error_log('Nonce verified successfully');
    
    // Get and validate parameters
    $start_date = isset($_POST["start_date"]) ? sanitize_text_field($_POST["start_date"]) : "";
    $end_date = isset($_POST["end_date"]) ? sanitize_text_field($_POST["end_date"]) : "";
    $department = isset($_POST["department"]) ? sanitize_text_field($_POST["department"]) : "";
    $type = isset($_POST["type"]) ? sanitize_text_field($_POST["type"]) : "";
    
    error_log('Start Date: ' . $start_date);
    error_log('End Date: ' . $end_date);
    error_log('Department: ' . $department);
    error_log('Type: ' . $type);
    
    // Validate required dates
    if (empty($start_date) || empty($end_date)) {
        error_log('Missing dates - Start: ' . $start_date . ', End: ' . $end_date);
        wp_send_json_error("Start date and end date are required");
        return;
    }
    
    // Validate date format and logic
    $start_timestamp = strtotime($start_date);
    $end_timestamp = strtotime($end_date);
    
    if (!$start_timestamp || !$end_timestamp) {
        error_log('Invalid date format - Start timestamp: ' . $start_timestamp . ', End timestamp: ' . $end_timestamp);
        wp_send_json_error("Invalid date format");
        return;
    }
    
    if ($start_timestamp > $end_timestamp) {
        error_log('Start date after end date');
        wp_send_json_error("Start date must be before end date");
        return;
    }
    
    error_log('Date validation passed');
    
    // Check cache
    $cache_key = 'analytics_' . md5($start_date . $end_date . $department . $type);
    $cached = wp_cache_get($cache_key);
    
    if ($cached !== false) {
        error_log('Returning cached data');
        wp_send_json_success($cached);
        return;
    }
    
    error_log('No cache found, fetching fresh data');
    error_log('No cache found, fetching fresh data');
    
    // Determine aggregation level based on date range
    $diff_days = ($end_timestamp - $start_timestamp) / (60 * 60 * 24);
    $aggregation = 'daily';
    if ($diff_days > 90) {
        $aggregation = 'monthly';
    } elseif ($diff_days > 31) {
        $aggregation = 'weekly';
    }
    
    error_log('Aggregation level: ' . $aggregation . ' (diff_days: ' . $diff_days . ')');
    
    // Get actual supplies data
    error_log('Fetching actual supplies data...');
    $actual_data = get_actual_supplies_analytics($start_date, $end_date, $department, $type, $aggregation);
    error_log('Actual supplies data count: ' . count($actual_data));
    
    // Get release supplies data
    error_log('Fetching release supplies data...');
    $release_data = get_release_supplies_analytics($start_date, $end_date, $department, $type, $aggregation);
    error_log('Release supplies data count: ' . count($release_data));
    
    // Calculate summary
    error_log('Calculating summary...');
    $summary = calculate_analytics_summary($actual_data, $release_data, $department);
    error_log('Summary calculated');
    
    $result = array(
        "filters" => array(
            "start_date" => $start_date,
            "end_date" => $end_date,
            "department" => $department,
            "type" => $type,
            "aggregation" => $aggregation
        ),
        "actual_supplies" => $actual_data,
        "release_supplies" => $release_data,
        "summary" => $summary
    );
    
    // Cache for 1 hour
    wp_cache_set($cache_key, $result, '', 3600);
    
    error_log('Sending success response');
    wp_send_json_success($result);
    
    } catch (Exception $e) {
        error_log('=== ANALYTICS ERROR ===');
        error_log('Error message: ' . $e->getMessage());
        error_log('Error trace: ' . $e->getTraceAsString());
        wp_send_json_error(array(
            'message' => 'Analytics processing error: ' . $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ));
    }
}

/**
 * Get actual supplies analytics data
 * Modified to match SOC Report logic: shows cumulative inventory from beginning up to end_date
 */
function get_actual_supplies_analytics($start_date, $end_date, $department, $type, $aggregation) {
    error_log('get_actual_supplies_analytics called with start: ' . $start_date . ', end: ' . $end_date . ', dept: ' . $department . ', type: ' . $type);
    
    // Build meta query for actual supplies - query ALL supplies up to end_date (cumulative)
    $meta_query = array(
        'relation' => 'AND',
        array(
            'key' => 'date_added',
            'value' => $end_date,
            'compare' => '<=',
            'type' => 'DATE'
        )
    );
    
    $args = array(
        "post_type" => "actualsupplies",
        "posts_per_page" => -1,
        "meta_query" => $meta_query,
        "orderby" => "meta_value",
        "meta_key" => "date_added",
        "order" => "ASC",
        "update_post_meta_cache" => true
    );
    
    $query = new WP_Query($args);
    $actual_supplies = $query->posts;
    wp_reset_postdata();
    
    // Get all unique supply IDs to fetch their data
    $supply_ids = array();
    foreach ($actual_supplies as $actual) {
        $supply_id = get_post_meta($actual->ID, 'supply_name', true);
        if ($supply_id) {
            // Ensure we have an ID, not a WP_Post object
            if (is_object($supply_id) && isset($supply_id->ID)) {
                $supply_ids[] = $supply_id->ID;
            } elseif (is_numeric($supply_id)) {
                $supply_ids[] = intval($supply_id);
            }
        }
    }
    
    // Fetch supply data (name, department, price) in one query
    $supply_data_cache = array();
    if (!empty($supply_ids)) {
        $supply_ids = array_unique($supply_ids);
        $supplies_query = new WP_Query(array(
            'post_type' => 'supplies',
            'post__in' => $supply_ids,
            'posts_per_page' => -1,
            'update_post_meta_cache' => true
        ));
        
        foreach ($supplies_query->posts as $supply_post) {
            $supply_meta = get_post_meta($supply_post->ID);
            $supply_dept = isset($supply_meta['department'][0]) ? $supply_meta['department'][0] : 'Unknown';
            $supply_type = isset($supply_meta['type'][0]) ? $supply_meta['type'][0] : 'Unknown';
            $supply_price = isset($supply_meta['price_per_unit'][0]) ? (float)$supply_meta['price_per_unit'][0] : 0;
            
            $supply_data_cache[$supply_post->ID] = array(
                'name' => $supply_post->post_title,
                'department' => $supply_dept,
                'type' => $supply_type,
                'price_per_unit' => $supply_price
            );
        }
        wp_reset_postdata();
    }
    
    // Process and aggregate data
    $aggregated_data = array();
    
    foreach ($actual_supplies as $actual) {
        $actual_id = $actual->ID;
        $supply_id_raw = get_post_meta($actual_id, 'supply_name', true);
        
        // Ensure we have a proper ID
        $supply_id = null;
        if (is_object($supply_id_raw) && isset($supply_id_raw->ID)) {
            $supply_id = $supply_id_raw->ID;
        } elseif (is_numeric($supply_id_raw)) {
            $supply_id = intval($supply_id_raw);
        }
        
        // Log problematic entries for debugging
        if (!$supply_id) {
            error_log("Analytics Debug - Actual Supply ID {$actual_id}: Invalid supply_id_raw type: " . gettype($supply_id_raw));
            continue;
        }
        
        if (!isset($supply_data_cache[$supply_id])) {
            error_log("Analytics Debug - Actual Supply ID {$actual_id}: Supply ID {$supply_id} not found in cache");
            continue;
        }
        
        $supply_info = $supply_data_cache[$supply_id];
        
        // Apply department filter
        if (!empty($department) && $supply_info['department'] !== $department) {
            continue;
        }
        
        // Apply type filter
        if (!empty($type) && $supply_info['type'] !== $type) {
            continue;
        }
        
        $quantity = (float)get_post_meta($actual_id, 'quantity', true);
        $date_added = get_post_meta($actual_id, 'date_added', true);
        $price_per_unit = $supply_info['price_per_unit'];
        $total_price = $quantity * $price_per_unit;
        
        // Determine aggregation key
        $date_key = get_aggregation_key($date_added, $aggregation);
        
        // Initialize aggregation bucket if needed
        if (!isset($aggregated_data[$date_key])) {
            $aggregated_data[$date_key] = array(
                'date' => $date_key,
                'total_value' => 0,
                'item_count' => 0,
                'by_department' => array(),
                'items' => array()
            );
        }
        
        // Add to aggregation
        $aggregated_data[$date_key]['total_value'] += $total_price;
        $aggregated_data[$date_key]['item_count']++;
        
        // Track by department
        if (!isset($aggregated_data[$date_key]['by_department'][$supply_info['department']])) {
            $aggregated_data[$date_key]['by_department'][$supply_info['department']] = 0;
        }
        $aggregated_data[$date_key]['by_department'][$supply_info['department']] += $total_price;
        
        // Store item details
        $aggregated_data[$date_key]['items'][] = array(
            'supply_id' => $supply_id,
            'supply_name' => $supply_info['name'],
            'department' => $supply_info['department'],
            'quantity' => $quantity,
            'price_per_unit' => $price_per_unit,
            'total_price' => $total_price,
            'date_added' => $date_added
        );
    }
    
    // Convert to indexed array and sort by date
    $result = array_values($aggregated_data);
    usort($result, function($a, $b) {
        return strcmp($a['date'], $b['date']);
    });
    
    return $result;
}

/**
 * Get release supplies analytics data
 * Modified to match SOC Report logic: shows cumulative releases from beginning up to end_date
 */
function get_release_supplies_analytics($start_date, $end_date, $department, $type, $aggregation) {
    // Build meta query for release supplies - query ALL releases up to end_date (cumulative)
    $meta_query = array(
        'relation' => 'AND',
        array(
            'key' => 'release_date',
            'value' => $end_date,
            'compare' => '<=',
            'type' => 'DATE'
        )
    );
    
    $args = array(
        "post_type" => "releasesupplies",
        "posts_per_page" => -1,
        "meta_query" => $meta_query,
        "orderby" => "meta_value",
        "meta_key" => "release_date",
        "order" => "ASC",
        "update_post_meta_cache" => true
    );
    
    $query = new WP_Query($args);
    $release_supplies = $query->posts;
    wp_reset_postdata();
    
    // Get all unique supply IDs to fetch their data
    $supply_ids = array();
    foreach ($release_supplies as $release) {
        $supply_id = get_post_meta($release->ID, 'supply_name', true);
        if ($supply_id) {
            // Ensure we have an ID, not a WP_Post object
            if (is_object($supply_id) && isset($supply_id->ID)) {
                $supply_ids[] = $supply_id->ID;
            } elseif (is_numeric($supply_id)) {
                $supply_ids[] = intval($supply_id);
            }
        }
    }
    
    // Fetch supply data (name, department, price) in one query
    $supply_data_cache = array();
    if (!empty($supply_ids)) {
        $supply_ids = array_unique($supply_ids);
        $supplies_query = new WP_Query(array(
            'post_type' => 'supplies',
            'post__in' => $supply_ids,
            'posts_per_page' => -1,
            'update_post_meta_cache' => true
        ));
        
        foreach ($supplies_query->posts as $supply_post) {
            $supply_meta = get_post_meta($supply_post->ID);
            $supply_dept = isset($supply_meta['department'][0]) ? $supply_meta['department'][0] : 'Unknown';
            $supply_type = isset($supply_meta['type'][0]) ? $supply_meta['type'][0] : 'Unknown';
            $supply_price = isset($supply_meta['price_per_unit'][0]) ? (float)$supply_meta['price_per_unit'][0] : 0;
            
            $supply_data_cache[$supply_post->ID] = array(
                'name' => $supply_post->post_title,
                'department' => $supply_dept,
                'type' => $supply_type,
                'price_per_unit' => $supply_price
            );
        }
        wp_reset_postdata();
    }
    
    // Process and aggregate data
    $aggregated_data = array();
    
    foreach ($release_supplies as $release) {
        $release_id = $release->ID;
        $supply_id_raw = get_post_meta($release_id, 'supply_name', true);
        
        // Ensure we have a proper ID
        $supply_id = null;
        if (is_object($supply_id_raw) && isset($supply_id_raw->ID)) {
            $supply_id = $supply_id_raw->ID;
        } elseif (is_numeric($supply_id_raw)) {
            $supply_id = intval($supply_id_raw);
        }
        
        // Log problematic entries for debugging
        if (!$supply_id) {
            error_log("Analytics Debug - Release Supply ID {$release_id}: Invalid supply_id_raw type: " . gettype($supply_id_raw));
            continue;
        }
        
        if (!isset($supply_data_cache[$supply_id])) {
            error_log("Analytics Debug - Release Supply ID {$release_id}: Supply ID {$supply_id} not found in cache");
            continue;
        }
        
        $supply_info = $supply_data_cache[$supply_id];
        
        // Apply department filter
        if (!empty($department) && $supply_info['department'] !== $department) {
            continue;
        }
        
        // Apply type filter
        if (!empty($type) && $supply_info['type'] !== $type) {
            continue;
        }
        
        $quantity = (float)get_post_meta($release_id, 'quantity', true);
        $release_date = get_post_meta($release_id, 'release_date', true);
        $is_confirmed = get_post_meta($release_id, 'confirmed', true) == '1';
        $release_dept = get_post_meta($release_id, 'department', true) ?: 'Unknown';
        $price_per_unit = $supply_info['price_per_unit'];
        $total_price = $quantity * $price_per_unit;
        
        // Determine aggregation key
        $date_key = get_aggregation_key($release_date, $aggregation);
        
        // Initialize aggregation bucket if needed
        if (!isset($aggregated_data[$date_key])) {
            $aggregated_data[$date_key] = array(
                'date' => $date_key,
                'total_value' => 0,
                'confirmed_value' => 0,
                'pending_value' => 0,
                'item_count' => 0,
                'confirmed_count' => 0,
                'pending_count' => 0,
                'by_department' => array(),
                'items' => array()
            );
        }
        
        // Add to aggregation
        $aggregated_data[$date_key]['total_value'] += $total_price;
        $aggregated_data[$date_key]['item_count']++;
        
        if ($is_confirmed) {
            $aggregated_data[$date_key]['confirmed_value'] += $total_price;
            $aggregated_data[$date_key]['confirmed_count']++;
        } else {
            $aggregated_data[$date_key]['pending_value'] += $total_price;
            $aggregated_data[$date_key]['pending_count']++;
        }
        
        // Track by department (supply's department, not release department)
        if (!isset($aggregated_data[$date_key]['by_department'][$supply_info['department']])) {
            $aggregated_data[$date_key]['by_department'][$supply_info['department']] = 0;
        }
        $aggregated_data[$date_key]['by_department'][$supply_info['department']] += $total_price;
        
        // Store item details
        $aggregated_data[$date_key]['items'][] = array(
            'supply_id' => $supply_id,
            'supply_name' => $supply_info['name'],
            'department' => $supply_info['department'],
            'release_department' => $release_dept,
            'quantity' => $quantity,
            'price_per_unit' => $price_per_unit,
            'total_price' => $total_price,
            'release_date' => $release_date,
            'confirmed' => $is_confirmed
        );
    }
    
    // Convert to indexed array and sort by date
    $result = array_values($aggregated_data);
    usort($result, function($a, $b) {
        return strcmp($a['date'], $b['date']);
    });
    
    return $result;
}

/**
 * Calculate summary statistics
 */
function calculate_analytics_summary($actual_data, $release_data, $department) {
    $summary = array(
        'total_actual_value' => 0,
        'total_release_value' => 0,
        'total_confirmed_value' => 0,
        'total_pending_value' => 0,
        'net_value' => 0,
        'department_breakdown' => array(),
        'active_departments' => 0
    );
    
    $dept_totals = array();
    
    // Sum actual supplies
    foreach ($actual_data as $data_point) {
        $summary['total_actual_value'] += $data_point['total_value'];
        
        // Track by department
        foreach ($data_point['by_department'] as $dept => $value) {
            if (!isset($dept_totals[$dept])) {
                $dept_totals[$dept] = array(
                    'actual' => 0,
                    'release' => 0,
                    'confirmed' => 0,
                    'pending' => 0,
                    'net' => 0
                );
            }
            $dept_totals[$dept]['actual'] += $value;
        }
    }
    
    // Sum release supplies
    foreach ($release_data as $data_point) {
        $summary['total_release_value'] += $data_point['total_value'];
        $summary['total_confirmed_value'] += $data_point['confirmed_value'];
        $summary['total_pending_value'] += $data_point['pending_value'];
        
        // Track by department
        foreach ($data_point['by_department'] as $dept => $value) {
            if (!isset($dept_totals[$dept])) {
                $dept_totals[$dept] = array(
                    'actual' => 0,
                    'release' => 0,
                    'confirmed' => 0,
                    'pending' => 0,
                    'net' => 0
                );
            }
            $dept_totals[$dept]['release'] += $value;
        }
    }
    
    // Calculate net values
    $summary['net_value'] = $summary['total_actual_value'] - $summary['total_confirmed_value'];
    
    foreach ($dept_totals as $dept => &$totals) {
        $totals['net'] = $totals['actual'] - $totals['release'];
    }
    
    $summary['department_breakdown'] = $dept_totals;
    $summary['active_departments'] = count($dept_totals);
    
    return $summary;
}

/**
 * Get aggregation key based on date and aggregation level
 */
function get_aggregation_key($date, $aggregation) {
    $timestamp = strtotime($date);
    
    if (!$timestamp) {
        return $date;
    }
    
    switch ($aggregation) {
        case 'daily':
            return date('Y-m-d', $timestamp);
            
        case 'weekly':
            // Get the Monday of the week
            $monday = strtotime('monday this week', $timestamp);
            return date('Y-m-d', $monday);
            
        case 'monthly':
            return date('Y-m', $timestamp) . '-01';
            
        default:
            return date('Y-m-d', $timestamp);
    }
}
?>
