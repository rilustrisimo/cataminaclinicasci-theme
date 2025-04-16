<?php
// AJAX handler for supplies overview
add_action("wp_ajax_load_supplies_batch", "load_supplies_batch");
add_action("wp_ajax_get_supply_details", "get_supply_details");

function load_supplies_batch() {
    // Verify nonce
    if (!isset($_POST["nonce"]) || !wp_verify_nonce($_POST["nonce"], "supplies_overview_nonce")) {
        wp_send_json_error("Security check failed");
    }
    
    $offset = isset($_POST["offset"]) ? intval($_POST["offset"]) : 0;
    $batch_size = isset($_POST["batch_size"]) ? intval($_POST["batch_size"]) : 100;
    
    // Get filter parameters
    $department = isset($_POST["department"]) ? sanitize_text_field($_POST["department"]) : "";
    $section = isset($_POST["section"]) ? sanitize_text_field($_POST["section"]) : "";
    $type = isset($_POST["type"]) ? sanitize_text_field($_POST["type"]) : "";
    $until_date = isset($_POST["until_date"]) ? sanitize_text_field($_POST["until_date"]) : "";
    $duplicates_only = isset($_POST["duplicates_only"]) && $_POST["duplicates_only"] === "1";
    
    // Build the meta query based on filters
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
    
    if (!empty($type)) {
        $meta_query[] = array(
            'key' => 'type',
            'value' => $type,
            'compare' => '='
        );
    }
    
    // Get supplies batch with filters applied
    $args = array(
        "post_type" => "supplies",
        "posts_per_page" => $duplicates_only ? -1 : $batch_size, // If duplicates only, get all items first
        "offset" => $duplicates_only ? 0 : $offset, // No offset if we need all items for duplicate check
        "orderby" => "title",
        "order" => "ASC",
        "post_status" => "publish"
    );
    
    // Add meta query if any filters are applied
    if (count($meta_query) > 1) { // > 1 because we always have the 'relation' => 'AND'
        $args["meta_query"] = $meta_query;
    }
    
    // First, get total count for pagination calculation (before duplicate filtering)
    $count_args = $args;
    $count_args["posts_per_page"] = -1;
    $count_args["fields"] = "ids";
    $count_query = new WP_Query($count_args);
    $total_count = $count_query->found_posts;
    wp_reset_postdata();
    
    // Now perform the actual query
    $supplies_query = new WP_Query($args);
    $all_items = array();
    $items = array();
    $actual_count = 0;
    $release_count = 0;
    
    // Parse date for filtering
    $date_filter = !empty($until_date) ? strtotime($until_date . " 23:59:59") : null;
    
    if ($supplies_query->have_posts()) {
        // First pass: gather all items and detect duplicates
        $name_department_map = array();
        $duplicate_ids = array();
        
        while ($supplies_query->have_posts()) {
            $supplies_query->the_post();
            $supply_id = get_the_ID();
            $name = strtolower(get_the_title());
            $department = get_field("department", $supply_id) ?: "Unknown";
            
            $key = $name . '|' . $department;
            
            if (isset($name_department_map[$key])) {
                // This is a duplicate - mark both this item and the original
                $duplicate_ids[$supply_id] = true;
                $duplicate_ids[$name_department_map[$key]] = true;
            } else {
                $name_department_map[$key] = $supply_id;
            }
            
            // If we're looking for duplicates only, continue - we'll process data in second pass
            if ($duplicates_only) {
                continue;
            }
            
            // Get basic supply information
            $supply = array(
                "id" => $supply_id,
                "name" => get_the_title(),
                "department" => $department,
                "type" => get_field("type", $supply_id) ?: "Unknown",
                "section" => get_field("section", $supply_id) ?: "None",
                "purchased_date" => get_field("purchased_date", $supply_id) ?: "Unknown",
                "price_per_unit" => number_format(get_field("price_per_unit", $supply_id) ?: 0, 2),
                "actual_supplies" => array(),
                "release_supplies" => array(),
                "total_actual_quantity" => 0,
                "total_release_quantity" => 0
            );
            
            // Process supply details
            $supply = process_supply_details($supply, $date_filter, $actual_count, $release_count);
            
            // Add to items array
            $all_items[] = $supply;
        }
        wp_reset_postdata();
        
        // If we're looking for duplicates only, we need to do a second pass to get the details
        if ($duplicates_only) {
            // If no duplicates found, just return empty results
            if (empty($duplicate_ids)) {
                wp_send_json_success(array(
                    "items" => array(),
                    "actual_count" => 0,
                    "release_count" => 0,
                    "total_count" => 0
                ));
                return;
            }
            
            // Get details for duplicate items only
            foreach (array_keys($duplicate_ids) as $dup_id) {
                $supply_post = get_post($dup_id);
                
                if (!$supply_post) continue;
                
                $supply = array(
                    "id" => $dup_id,
                    "name" => $supply_post->post_title,
                    "department" => get_field("department", $dup_id) ?: "Unknown",
                    "type" => get_field("type", $dup_id) ?: "Unknown",
                    "section" => get_field("section", $dup_id) ?: "None",
                    "purchased_date" => get_field("purchased_date", $dup_id) ?: "Unknown",
                    "price_per_unit" => number_format(get_field("price_per_unit", $dup_id) ?: 0, 2),
                    "actual_supplies" => array(),
                    "release_supplies" => array(),
                    "total_actual_quantity" => 0,
                    "total_release_quantity" => 0,
                    "isDuplicate" => true
                );
                
                // Process supply details
                $supply = process_supply_details($supply, $date_filter, $actual_count, $release_count);
                
                // Add to items array
                $all_items[] = $supply;
            }
            
            // Sort by department and name
            usort($all_items, function($a, $b) {
                $dept_compare = strcmp($a["department"], $b["department"]);
                if ($dept_compare !== 0) {
                    return $dept_compare;
                }
                return strcmp($a["name"], $b["name"]);
            });
            
            // Update total count to reflect only duplicates
            $total_count = count($all_items);
        } else {
            // Mark duplicate items
            foreach ($all_items as &$item) {
                if (isset($duplicate_ids[$item["id"]])) {
                    $item["isDuplicate"] = true;
                }
            }
        }
        
        // Apply paging for non-duplicate requests
        if (!$duplicates_only) {
            $items = array_slice($all_items, 0, $batch_size);
        } else {
            $items = $all_items;
        }
    }
    
    wp_send_json_success(array(
        "items" => $items,
        "actual_count" => $actual_count,
        "release_count" => $release_count,
        "total_count" => $total_count
    ));
}

/**
 * Process details for a supply item
 * Helper function to avoid code duplication
 */
function process_supply_details($supply, $date_filter, &$actual_count, &$release_count) {
    $supply_id = $supply['id'];
    
    // Get related actual supplies with date filter
    $actual_args = array(
        "post_type" => "actualsupplies",
        "posts_per_page" => -1,
        "meta_query" => array(
            array(
                "key" => "supply_name",
                "value" => $supply_id,
                "compare" => "="
            )
        ),
        "orderby" => "meta_value",
        "meta_key" => "date_added",
        "order" => "DESC"
    );
    
    // Apply date filter for actualsupplies if specified
    if ($date_filter) {
        $actual_args["meta_query"][] = array(
            "key" => "date_added",
            "value" => date("Y-m-d", $date_filter),
            "compare" => "<=",
            "type" => "DATE"
        );
    }
    
    $actual_query = new WP_Query($actual_args);
    
    if ($actual_query->have_posts()) {
        while ($actual_query->have_posts()) {
            $actual_query->the_post();
            $actual_id = get_the_ID();
            $quantity = floatval(get_field("quantity", $actual_id));
            $date_added = get_field("date_added", $actual_id) ?: "Unknown";
            
            // Double-check date filter manually for better accuracy
            if ($date_filter) {
                $actual_date = strtotime($date_added);
                if ($actual_date && $actual_date > $date_filter) {
                    continue; // Skip this record if it's after the filter date
                }
            }
            
            $supply["actual_supplies"][] = array(
                "id" => $actual_id,
                "quantity" => $quantity,
                "date_added" => $date_added,
                "lot_number" => get_field("lot_number", $actual_id) ?: "",
                "expiry_date" => get_field("expiry_date", $actual_id) ?: ""
            );
            
            $supply["total_actual_quantity"] += $quantity;
            $actual_count++;
        }
        wp_reset_postdata();
    }
    
    // Get related release supplies with date filter
    $release_args = array(
        "post_type" => "releasesupplies",
        "posts_per_page" => -1,
        "meta_query" => array(
            array(
                "key" => "supply_name",
                "value" => $supply_id,
                "compare" => "="
            )
        ),
        "orderby" => "meta_value",
        "meta_key" => "release_date",
        "order" => "DESC"
    );
    
    // Apply date filter for releasesupplies if specified
    if ($date_filter) {
        $release_args["meta_query"][] = array(
            "key" => "release_date",
            "value" => date("Y-m-d", $date_filter),
            "compare" => "<=",
            "type" => "DATE"
        );
    }
    
    $release_query = new WP_Query($release_args);
    
    if ($release_query->have_posts()) {
        while ($release_query->have_posts()) {
            $release_query->the_post();
            $release_id = get_the_ID();
            $quantity = floatval(get_field("quantity", $release_id));
            $release_date = get_field("release_date", $release_id) ?: "Unknown";
            
            // Double-check date filter manually for better accuracy
            if ($date_filter) {
                $rel_date = strtotime($release_date);
                if ($rel_date && $rel_date > $date_filter) {
                    continue; // Skip this record if it's after the filter date
                }
            }
            
            $supply["release_supplies"][] = array(
                "id" => $release_id,
                "quantity" => $quantity,
                "release_date" => $release_date,
                "department" => get_field("department", $release_id) ?: "Unknown",
                "confirmed" => get_field("confirmed", $release_id) ? true : false
            );
            
            $supply["total_release_quantity"] += $quantity;
            $release_count++;
        }
        wp_reset_postdata();
    }
    
    // Calculate balance
    $supply["balance"] = $supply["total_actual_quantity"] - $supply["total_release_quantity"];
    
    return $supply;
}

/**
 * Get detailed information for a specific supply
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
    
    // Parse date for filtering
    $date_filter = !empty($until_date) ? strtotime($until_date . " 23:59:59") : null;
    
    // Get basic supply information
    $supply = array(
        "id" => $supply_id,
        "name" => $supply_post->post_title,
        "department" => get_field("department", $supply_id) ?: "Unknown",
        "type" => get_field("type", $supply_id) ?: "Unknown",
        "section" => get_field("section", $supply_id) ?: "None",
        "purchased_date" => get_field("purchased_date", $supply_id) ?: "Unknown",
        "price_per_unit" => number_format(get_field("price_per_unit", $supply_id) ?: 0, 2),
        "actual_supplies" => array(),
        "release_supplies" => array(),
        "total_actual_quantity" => 0,
        "total_release_quantity" => 0
    );
    
    // Get related actual supplies with date filter
    $actual_args = array(
        "post_type" => "actualsupplies",
        "posts_per_page" => -1,
        "meta_query" => array(
            array(
                "key" => "supply_name",
                "value" => $supply_id,
                "compare" => "="
            )
        ),
        "orderby" => "meta_value",
        "meta_key" => "date_added",
        "order" => "DESC"
    );
    
    // Apply date filter if specified
    if ($date_filter) {
        $actual_args["meta_query"][] = array(
            "key" => "date_added",
            "value" => date("Y-m-d", $date_filter),
            "compare" => "<=",
            "type" => "DATE"
        );
    }
    
    $actual_query = new WP_Query($actual_args);
    
    if ($actual_query->have_posts()) {
        while ($actual_query->have_posts()) {
            $actual_query->the_post();
            $actual_id = get_the_ID();
            $quantity = floatval(get_field("quantity", $actual_id));
            $date_added = get_field("date_added", $actual_id) ?: "Unknown";
            
            // Double-check date filter manually for better accuracy
            if ($date_filter) {
                $actual_date = strtotime($date_added);
                if ($actual_date && $actual_date > $date_filter) {
                    continue; // Skip this record if it's after the filter date
                }
            }
            
            $supply["actual_supplies"][] = array(
                "id" => $actual_id,
                "quantity" => $quantity,
                "date_added" => $date_added,
                "lot_number" => get_field("lot_number", $actual_id) ?: "",
                "expiry_date" => get_field("expiry_date", $actual_id) ?: ""
            );
            
            $supply["total_actual_quantity"] += $quantity;
        }
        wp_reset_postdata();
    }
    
    // Get related release supplies with date filter
    $release_args = array(
        "post_type" => "releasesupplies",
        "posts_per_page" => -1,
        "meta_query" => array(
            array(
                "key" => "supply_name",
                "value" => $supply_id,
                "compare" => "="
            )
        ),
        "orderby" => "meta_value",
        "meta_key" => "release_date",
        "order" => "DESC"
    );
    
    // Apply date filter if specified
    if ($date_filter) {
        $release_args["meta_query"][] = array(
            "key" => "release_date",
            "value" => date("Y-m-d", $date_filter),
            "compare" => "<=",
            "type" => "DATE"
        );
    }
    
    $release_query = new WP_Query($release_args);
    
    if ($release_query->have_posts()) {
        while ($release_query->have_posts()) {
            $release_query->the_post();
            $release_id = get_the_ID();
            $quantity = floatval(get_field("quantity", $release_id));
            $release_date = get_field("release_date", $release_id) ?: "Unknown";
            
            // Double-check date filter manually for better accuracy
            if ($date_filter) {
                $rel_date = strtotime($release_date);
                if ($rel_date && $rel_date > $date_filter) {
                    continue; // Skip this record if it's after the filter date
                }
            }
            
            $supply["release_supplies"][] = array(
                "id" => $release_id,
                "quantity" => $quantity,
                "release_date" => $release_date,
                "department" => get_field("department", $release_id) ?: "Unknown",
                "confirmed" => get_field("confirmed", $release_id) ? true : false
            );
            
            $supply["total_release_quantity"] += $quantity;
        }
        wp_reset_postdata();
    }
    
    // Calculate balance
    $supply["balance"] = $supply["total_actual_quantity"] - $supply["total_release_quantity"];
    
    wp_send_json_success($supply);
}
?>