<?php
// AJAX handler for supplies overview
add_action("wp_ajax_load_supplies_batch", "load_supplies_batch");
add_action("wp_ajax_get_supply_details", "get_supply_details");

/**
 * Loads a batch of supplies data with filters and relationships
 * Includes efficient processing of duplicate detection and expired items
 */
function load_supplies_batch() {
    // Verify nonce
    if (!isset($_POST["nonce"]) || !wp_verify_nonce($_POST["nonce"], "supplies_overview_nonce")) {
        wp_send_json_error("Security check failed");
    }
    
    // Clean and validate input parameters
    $offset = isset($_POST["offset"]) ? intval($_POST["offset"]) : 0;
    $batch_size = isset($_POST["batch_size"]) ? min(intval($_POST["batch_size"]), 200) : 100; // Limit max batch size
    
    // Get filter parameters with proper sanitization
    $department = isset($_POST["department"]) ? sanitize_text_field($_POST["department"]) : "";
    $section = isset($_POST["section"]) ? sanitize_text_field($_POST["section"]) : "";
    $sub_section = isset($_POST["sub_section"]) ? sanitize_text_field($_POST["sub_section"]) : "";
    $type = isset($_POST["type"]) ? sanitize_text_field($_POST["type"]) : "";
    $until_date = isset($_POST["until_date"]) ? sanitize_text_field($_POST["until_date"]) : "";
    $duplicates_only = isset($_POST["duplicates_only"]) && $_POST["duplicates_only"] === "1";
    
    // Format date once if provided
    $date_filter_timestamp = !empty($until_date) ? strtotime($until_date . " 23:59:59") : null;
    $formatted_date = $date_filter_timestamp ? date('Y-m-d', $date_filter_timestamp) : null;
    
    // Helper function for date formatting
    function format_display_date($date_str) {
        if ($date_str === 'Unknown') return 'Unknown';
        $timestamp = strtotime($date_str);
        return $timestamp ? date('m/d/Y', $timestamp) : $date_str;
    }
    
    // Build optimized meta query with proper indexing
    $meta_query = array('relation' => 'AND');
    
    if (!empty($department)) {
        $meta_query[] = array(
            'key' => 'department',
            'value' => $department,
            'compare' => '='
        );
    }
    
    if (!empty($section)) {
        $meta_query[] = array(
            'key' => 'section',
            'value' => $section,
            'compare' => '='
        );
    }
    
    if (!empty($sub_section)) {
        $meta_query[] = array(
            'key' => 'sub_section',
            'value' => $sub_section,
            'compare' => '='
        );
    }
    
    if (!empty($type)) {
        $meta_query[] = array(
            'key' => 'type',
            'value' => $type,
            'compare' => '='
        );
    }
    
    // Setup main query args - for duplicates_only, get all supplies to detect duplicates efficiently
    $args = array(
        "post_type" => "supplies",
        "posts_per_page" => $duplicates_only ? -1 : $batch_size,
        "offset" => $duplicates_only ? 0 : $offset,
        "orderby" => "title",
        "order" => "ASC",
        "post_status" => "publish",
        "fields" => "all", // Get all post data for better WordPress caching
        "no_found_rows" => $duplicates_only, // Skip counting rows when getting all for duplicates
        "update_post_meta_cache" => true, // Ensure meta is preloaded efficiently
        "update_post_term_cache" => false // Don't need terms for this query
    );
    
    // Add meta query if filters are applied
    if (count($meta_query) > 1) {
        $args["meta_query"] = $meta_query;
    }
    
    // First, get total count for pagination calculation if not duplicate mode
    $total_count = 0;
    if (!$duplicates_only) {
        $count_args = $args;
        $count_args["posts_per_page"] = -1;
        $count_args["fields"] = "ids";
        $count_args["no_found_rows"] = true;
        $count_query = new WP_Query($count_args);
        $total_count = count($count_query->posts);
        wp_reset_postdata();
    }
    
    // Run the main query for supplies
    $supplies_query = new WP_Query($args);
    $supplies = $supplies_query->posts;
    $supply_ids = array();
    wp_reset_postdata();
    
    // Initialize counters and result arrays
    $all_items = array();
    $items = array();
    $actual_count = 0;
    $release_count = 0;
    $pending_count = 0;
    
    // First pass: gather all items and detect duplicates (if needed)
    $name_department_map = array();
    $duplicate_ids = array();
    $supply_data = array();
    
    if ($duplicates_only || count($supplies) > 0) {
        // Process and cache supply metadata for better performance
        foreach ($supplies as $supply_post) {
            $supply_id = $supply_post->ID;
            $supply_ids[] = $supply_id;
            
            $name = strtolower($supply_post->post_title);
            $department = get_post_meta($supply_id, 'department', true) ?: 'Unknown';
            
            $key = $department . '|' . $name;
            
            if (isset($name_department_map[$key])) {
                // This is a duplicate - mark both this item and the original
                $duplicate_ids[$supply_id] = true;
                $duplicate_ids[$name_department_map[$key]] = true;
            } else {
                $name_department_map[$key] = $supply_id;
            }
            
            // Store parsed data for later use
            $supply_data[$supply_id] = array(
                "id" => $supply_id,
                "name" => $supply_post->post_title,
                "department" => $department,
                "type" => get_post_meta($supply_id, 'type', true) ?: 'Unknown',
                "section" => get_post_meta($supply_id, 'section', true) ?: 'None',
                "sub_section" => get_post_meta($supply_id, 'sub_section', true) ?: '',
                "purchased_date" => format_display_date(get_post_meta($supply_id, 'purchased_date', true) ?: 'Unknown'),
                "price_per_unit" => number_format((float)get_post_meta($supply_id, 'price_per_unit', true), 2),
                "actual_supplies" => array(),
                "release_supplies" => array(),
                "total_actual_quantity" => 0,
                "total_release_quantity" => 0,
                "expired_quantity" => 0,
                "isDuplicate" => false
            );
        }
        
        // Update duplicate flags
        foreach ($duplicate_ids as $dup_id => $value) {
            if (isset($supply_data[$dup_id])) {
                $supply_data[$dup_id]["isDuplicate"] = true;
            }
        }
        
        // Skip non-duplicates if filtering for duplicates only
        if ($duplicates_only) {
            foreach ($supply_data as $id => $data) {
                if (!isset($duplicate_ids[$id])) {
                    unset($supply_data[$id]);
                }
            }
            
            // Update total count for duplicates only
            $total_count = count($supply_data);
            $supply_ids = array_keys($supply_data);
        }
        
        // Prepare to fetch related data if we have supplies
        if (count($supply_ids) > 0) {
            // Get actual supplies in a single efficient query
            $actual_args = array(
                "post_type" => "actualsupplies",
                "posts_per_page" => -1,
                "meta_query" => array(
                    'relation' => 'AND',
                    array(
                        "key" => "supply_name",
                        "value" => $supply_ids,
                        "compare" => "IN",
                        "type" => "NUMERIC"
                    ),
                    array(
                        'relation' => 'OR',
                        array(
                            "key" => "related_release_id",
                            "compare" => "NOT EXISTS"
                        ),
                        array(
                            "key" => "related_release_id",
                            "value" => "",
                            "compare" => "="
                        )
                    )
                ),
                "update_post_meta_cache" => true
            );
            
            // Get all actual supplies
            $actual_query = new WP_Query($actual_args);
            $actual_supplies = $actual_query->posts;
            wp_reset_postdata();
            
            // Process actual supplies
            foreach ($actual_supplies as $actual_post) {
                $actual_id = $actual_post->ID;
                $supply_id_raw = get_post_meta($actual_id, 'supply_name', true);
                
                // Ensure we have a proper ID
                $supply_id = null;
                if (is_object($supply_id_raw) && isset($supply_id_raw->ID)) {
                    $supply_id = $supply_id_raw->ID;
                } elseif (is_numeric($supply_id_raw)) {
                    $supply_id = intval($supply_id_raw);
                }
                
                if (!$supply_id || !isset($supply_data[$supply_id])) continue;
                
                $quantity = (float)get_post_meta($actual_id, 'quantity', true);
                $date_added = get_post_meta($actual_id, 'date_added', true) ?: 'Unknown';
                $lot_number = get_post_meta($actual_id, 'lot_number', true) ?: '';
                $expiry_date = get_post_meta($actual_id, 'expiry_date', true) ?: '';

                // Proper date comparison with format handling
                if (!empty($formatted_date) && isset($date_added)) {
                    $actual_date = $date_added;
                    // Ensure dates are in Y-m-d format for reliable comparison
                    $actual_date_formatted = date('Y-m-d', strtotime($actual_date));
                    
                    if ($actual_date_formatted > $formatted_date) {
                        continue; // Skip items added after the filter date
                    }
                }
                
                // Store actual supply data
                $supply_data[$supply_id]["actual_supplies"][] = array(
                    "id" => $actual_id,
                    "quantity" => $quantity,
                    "date_added" => format_display_date($date_added),
                    "lot_number" => $lot_number,
                    "expiry_date" => format_display_date($expiry_date)
                );
                
                // Update counters
                $supply_data[$supply_id]["total_actual_quantity"] += $quantity;
                $actual_count++;
                
                // Check for expired items
                if (!empty($expiry_date) && !empty($date_filter_timestamp)) {
                    $expiry_timestamp = strtotime($expiry_date);
                    if ($expiry_timestamp && $expiry_timestamp <= $date_filter_timestamp) {
                        $supply_data[$supply_id]["raw_expired_quantity"] = 
                            (isset($supply_data[$supply_id]["raw_expired_quantity"]) ? 
                             $supply_data[$supply_id]["raw_expired_quantity"] : 0) + $quantity;
                    }
                }
            }
            
            // Get release supplies in a single efficient query
            $release_args = array(
                "post_type" => "releasesupplies",
                "posts_per_page" => -1,
                "meta_query" => array(
                    'relation' => 'AND',
                    array(
                        "key" => "supply_name",
                        "value" => $supply_ids,
                        "compare" => "IN",
                        "type" => "NUMERIC"
                    )
                ),
                "update_post_meta_cache" => true
            );
            
            // Apply date filter for releasesupplies if specified
            if (!empty($formatted_date)) {
                $release_args["meta_query"][] = array(
                    "key" => "release_date",
                    "value" => $formatted_date,
                    "compare" => "<=",
                    "type" => "DATE"
                );
            }
            
            // Get all release supplies
            $release_query = new WP_Query($release_args);
            $release_supplies = $release_query->posts;
            wp_reset_postdata();
            
            // Process release supplies
            foreach ($release_supplies as $release_post) {
                $release_id = $release_post->ID;
                $supply_id_raw = get_post_meta($release_id, 'supply_name', true);
                
                // Ensure we have a proper ID
                $supply_id = null;
                if (is_object($supply_id_raw) && isset($supply_id_raw->ID)) {
                    $supply_id = $supply_id_raw->ID;
                } elseif (is_numeric($supply_id_raw)) {
                    $supply_id = intval($supply_id_raw);
                }
                
                if (!$supply_id || !isset($supply_data[$supply_id])) continue;
                
                $quantity = (float)get_post_meta($release_id, 'quantity', true);
                $release_date = get_post_meta($release_id, 'release_date', true) ?: 'Unknown';
                $department = get_post_meta($release_id, 'department', true) ?: 'Unknown';
                $is_confirmed = get_post_meta($release_id, 'confirmed', true) == '1';
                
                // Store release supply data
                $supply_data[$supply_id]["release_supplies"][] = array(
                    "id" => $release_id,
                    "quantity" => $quantity,
                    "release_date" => format_display_date($release_date),
                    "department" => $department,
                    "confirmed" => $is_confirmed
                );
                
                // Update counters
                if ($is_confirmed) {
                    $supply_data[$supply_id]["total_release_quantity"] += $quantity;
                    $release_count++;
                } else {
                    $pending_count++;
                }
            }
            
            // Final calculations for each supply
            foreach ($supply_data as $supply_id => &$supply) {
                // Calculate expired quantity (adjusting for releases) and final balance
                $raw_expired = isset($supply["raw_expired_quantity"]) ? $supply["raw_expired_quantity"] : 0;
                $supply["expired_quantity"] = max(0, $raw_expired - $supply["total_release_quantity"]);
                $supply["balance"] = $supply["total_actual_quantity"] - $supply["total_release_quantity"] - $supply["expired_quantity"];
                unset($supply["raw_expired_quantity"]);
                
                // Add to final items array
                $all_items[] = $supply;
            }
            
            // Sort by name for consistent display
            usort($all_items, function($a, $b) {
                return strcmp($a["name"], $b["name"]);
            });
            
            // Apply pagination for duplicates if needed
            if ($duplicates_only && $offset > 0) {
                $items = array_slice($all_items, $offset, $batch_size);
            } else {
                $items = $all_items;
            }
        }
    }
    
    // Clean up and return the response
    wp_send_json_success(array(
        "items" => $items,
        "actual_count" => $actual_count,
        "release_count" => $release_count,
        "pending_count" => $pending_count,
        "total_count" => $total_count
    ));
}

/**
 * Get detailed information for a specific supply with optimized queries
 */
function get_supply_details() {
    // Verify nonce
    if (!isset($_POST["nonce"]) || !wp_verify_nonce($_POST["nonce"], "supply_details_nonce")) {
        wp_send_json_error("Security check failed");
    }
    
    $supply_id = isset($_POST["supply_id"]) ? intval($_POST["supply_id"]) : 0;
    $until_date = isset($_POST["until_date"]) ? sanitize_text_field($_POST["until_date"]) : "";
    
    if (!$supply_id) {
        wp_send_json_error("Invalid supply ID");
    }
    
    // Get supply post
    $supply_post = get_post($supply_id);
    
    if (!$supply_post || $supply_post->post_type !== 'supplies') {
        wp_send_json_error("Supply not found");
    }
    
    // Helper function for date formatting (for get_supply_details)
    function format_display_date($date_str) {
        if ($date_str === 'Unknown') return 'Unknown';
        $timestamp = strtotime($date_str);
        return $timestamp ? date('m/d/Y', $timestamp) : $date_str;
    }
    
    // Use a cache for frequently accessed supply details
    $cache_key = 'supply_details_' . $supply_id . '_' . md5($until_date);
    $cached = wp_cache_get($cache_key);
    
    if ($cached !== false) {
        wp_send_json_success($cached);
        return;
    }
    
    // Format date once
    $date_filter_timestamp = !empty($until_date) ? strtotime($until_date . " 23:59:59") : null;
    $formatted_date = $date_filter_timestamp ? date('Y-m-d', $date_filter_timestamp) : null;
    
    // Efficiently get all supply meta at once
    $all_meta = get_post_meta($supply_id);
    
    $supply = array(
        "id" => $supply_id,
        "name" => $supply_post->post_title,
        "department" => isset($all_meta['department'][0]) ? $all_meta['department'][0] : "Unknown",
        "type" => isset($all_meta['type'][0]) ? $all_meta['type'][0] : "Unknown",
        "section" => isset($all_meta['section'][0]) ? $all_meta['section'][0] : "None",
        "sub_section" => isset($all_meta['sub_section'][0]) ? $all_meta['sub_section'][0] : "",
        "purchased_date" => isset($all_meta['purchased_date'][0]) ? format_display_date($all_meta['purchased_date'][0]) : "Unknown",
        "price_per_unit" => number_format(isset($all_meta['price_per_unit'][0]) ? (float)$all_meta['price_per_unit'][0] : 0, 2),
        "actual_supplies" => array(),
        "release_supplies" => array(),
        "total_actual_quantity" => 0,
        "total_release_quantity" => 0,
        "expired_quantity" => 0
    );
    
    // Get related actual supplies with optimized query
    $actual_args = array(
        "post_type" => "actualsupplies",
        "posts_per_page" => -1,
        "meta_query" => array(
            array(
                "key" => "supply_name",
                "value" => $supply_id,
                "compare" => "=",
                "type" => "NUMERIC"
            ),
            array(
                'relation' => 'OR',
                array(
                    "key" => "related_release_id",
                    "compare" => "NOT EXISTS"
                ),
                array(
                    "key" => "related_release_id",
                    "value" => "",
                    "compare" => "="
                )
            )
        ),
        "orderby" => "meta_value",
        "meta_key" => "date_added",
        "order" => "DESC",
        "update_post_meta_cache" => true
    );
    
    $actual_query = new WP_Query($actual_args);
    $actual_supplies = $actual_query->posts;
    wp_reset_postdata();
    
    // Process actual supplies
    $raw_expired_quantity = 0;
    
    foreach ($actual_supplies as $actual_post) {
        $actual_id = $actual_post->ID;
        $all_actual_meta = get_post_meta($actual_id);

         // Proper date comparison with format handling
         if (!empty($formatted_date) && isset($all_actual_meta['date_added'][0])) {
            $actual_date = $all_actual_meta['date_added'][0];
            // Ensure dates are in Y-m-d format for reliable comparison
            $actual_date_formatted = date('Y-m-d', strtotime($actual_date));
            
            if ($actual_date_formatted > $formatted_date) {
                continue; // Skip items added after the filter date
            }
        }
        
        $quantity = isset($all_actual_meta['quantity'][0]) ? (float)$all_actual_meta['quantity'][0] : 0;
        $date_added = isset($all_actual_meta['date_added'][0]) ? $all_actual_meta['date_added'][0] : 'Unknown';
        $lot_number = isset($all_actual_meta['lot_number'][0]) ? $all_actual_meta['lot_number'][0] : '';
        $expiry_date = isset($all_actual_meta['expiry_date'][0]) ? $all_actual_meta['expiry_date'][0] : '';
        
        // Store actual supply data
        $supply["actual_supplies"][] = array(
            "id" => $actual_id,
            "quantity" => $quantity,
            "date_added" => format_display_date($date_added),
            "lot_number" => $lot_number,
            "expiry_date" => format_display_date($expiry_date)
        );
        
        // Update counters
        $supply["total_actual_quantity"] += $quantity;
        
        // Check for expired items
        if (!empty($expiry_date) && !empty($date_filter_timestamp)) {
            $expiry_timestamp = strtotime($expiry_date);
            if ($expiry_timestamp && $expiry_timestamp <= $date_filter_timestamp) {
                $raw_expired_quantity += $quantity;
            }
        }
    }
    
    // Get related release supplies with optimized query
    $release_args = array(
        "post_type" => "releasesupplies",
        "posts_per_page" => -1,
        "meta_query" => array(
            array(
                "key" => "supply_name",
                "value" => $supply_id,
                "compare" => "=",
                "type" => "NUMERIC"
            )
        ),
        "orderby" => "meta_value",
        "meta_key" => "release_date",
        "order" => "DESC",
        "update_post_meta_cache" => true
    );
    
    // Apply date filter if specified
    if (!empty($formatted_date)) {
        $release_args["meta_query"][] = array(
            "key" => "release_date",
            "value" => $formatted_date,
            "compare" => "<=",
            "type" => "DATE"
        );
    }
    
    $release_query = new WP_Query($release_args);
    $release_supplies = $release_query->posts;
    wp_reset_postdata();
    
    // Process release supplies
    foreach ($release_supplies as $release_post) {
        $release_id = $release_post->ID;
        $all_release_meta = get_post_meta($release_id);
        
        $quantity = isset($all_release_meta['quantity'][0]) ? (float)$all_release_meta['quantity'][0] : 0;
        $release_date = isset($all_release_meta['release_date'][0]) ? $all_release_meta['release_date'][0] : 'Unknown';
        $department = isset($all_release_meta['department'][0]) ? $all_release_meta['department'][0] : 'Unknown';
        $is_confirmed = isset($all_release_meta['confirmed'][0]) && $all_release_meta['confirmed'][0] == '1';
        
        // Store release supply data
        $supply["release_supplies"][] = array(
            "id" => $release_id,
            "quantity" => $quantity,
            "release_date" => format_display_date($release_date),
            "department" => $department,
            "confirmed" => $is_confirmed
        );
        
        // Update counters for confirmed releases
        if ($is_confirmed) {
            $supply["total_release_quantity"] += $quantity;
        }
    }
    
    // Calculate balance and expired quantity
    $supply["expired_quantity"] = max(0, $raw_expired_quantity - $supply["total_release_quantity"]);
    $supply["balance"] = $supply["total_actual_quantity"] - $supply["total_release_quantity"] - $supply["expired_quantity"];
    
    // Cache the result for 5 minutes (300 seconds)
    wp_cache_set($cache_key, $supply, '', 300);
    
    wp_send_json_success($supply);
}
?>