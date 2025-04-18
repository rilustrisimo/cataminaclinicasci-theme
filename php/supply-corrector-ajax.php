<?php
// Register AJAX actions
add_action('wp_ajax_process_supply_csv', 'process_supply_csv');
add_action('wp_ajax_search_supply_matches', 'search_supply_matches');
add_action('wp_ajax_confirm_supply_matches', 'confirm_supply_matches');
add_action('wp_ajax_check_supply_discrepancies', 'check_supply_discrepancies');
add_action('wp_ajax_update_supply_quantities', 'update_supply_quantities');

/**
 * Process uploaded CSV file
 */
function process_supply_csv() {
    check_ajax_referer('supply_corrector_nonce', 'nonce');

    if (!isset($_FILES['csv_file'])) {
        wp_send_json_error('No file uploaded');
    }

    $file = $_FILES['csv_file'];
    $csv_data = array();

    // Open uploaded CSV file
    if (($handle = fopen($file['tmp_name'], "r")) !== FALSE) {
        // Read header row
        $headers = fgetcsv($handle);
        
        // Convert headers to lowercase for case-insensitive comparison
        $headers = array_map('trim', array_map('strtolower', $headers));
        $required_headers = array('supply_name', 'actual_count', 'expiry_date', 'date_added', 'serial', 'states__status', 'lot_number');
        
        // Validate headers
        $missing_headers = array_diff($required_headers, $headers);
        if (!empty($missing_headers)) {
            fclose($handle);
            wp_send_json_error('Invalid CSV format. Missing required headers: ' . implode(', ', $missing_headers));
        }

        // Read data rows
        $row_count = 0;
        $error_rows = [];
        
        while (($data = fgetcsv($handle)) !== FALSE) {
            $row_count++;
            
            // Skip empty rows
            if (count(array_filter($data)) === 0) {
                continue;
            }
            
            // Make sure the row has the correct number of columns
            if (count($data) !== count($headers)) {
                $error_rows[] = $row_count;
                continue;
            }
            
            $row = array_combine($headers, $data);
            
            // Validate required fields
            if (empty($row['supply_name']) || empty($row['actual_count'])) {
                $error_rows[] = $row_count;
                continue;
            }
            
            // Try to ensure actual_count is numeric
            if (!is_numeric($row['actual_count'])) {
                // Try to clean the value
                $row['actual_count'] = preg_replace('/[^0-9.]/', '', $row['actual_count']);
                if (!is_numeric($row['actual_count'])) {
                    $error_rows[] = $row_count;
                    continue;
                }
            }
            
            // Format dates if possible
            foreach (['expiry_date', 'date_added'] as $date_field) {
                if (!empty($row[$date_field])) {
                    $timestamp = strtotime($row[$date_field]);
                    if ($timestamp) {
                        $row[$date_field] = date('Y-m-d', $timestamp);
                    }
                }
            }
            
            $csv_data[] = $row;
        }
        fclose($handle);
        
        // Report if there were problematic rows
        if (!empty($error_rows)) {
            if (count($error_rows) === $row_count) {
                wp_send_json_error('All rows in the CSV file contain errors. Please check the format and try again.');
            }
        }

        // Return the CSV data to be stored in localStorage (no need for transients anymore)
        wp_send_json_success(array(
            'message' => 'CSV processed successfully',
            'total_records' => count($csv_data),
            'error_rows' => $error_rows,
            'csv_data' => $csv_data
        ));
    } else {
        wp_send_json_error('Failed to process CSV file');
    }
}

/**
 * Search for matching supplies using word-by-word search
 */
function search_supply_matches() {
    check_ajax_referer('supply_corrector_nonce', 'nonce');
    
    // Get batch data directly from the request instead of looking up transients
    $batch_data = isset($_POST['batch_data']) ? json_decode(stripslashes($_POST['batch_data']), true) : null;
    
    if (!$batch_data) {
        // For backward compatibility, try the old method
        $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
        $user_id = get_current_user_id();
        $transient_key = 'supply_corrector_' . $user_id;
        $csv_data = get_transient($transient_key);
        
        if (!$csv_data) {
            wp_send_json_error('CSV data not found. Please upload the file again.');
        }
        
        $batch_size = 10;
        $batch_data = array_slice($csv_data, $offset, $batch_size);
    }
    
    $results = array();
    
    // Get stopwords for more efficient searching
    $stopwords = array('the', 'and', 'or', 'for', 'with', 'by', 'in', 'on', 'at', 'to', 'of', 'a', 'an');

    foreach ($batch_data as $row) {
        $supply_name = trim($row['supply_name']);
        
        if (empty($supply_name)) {
            $results[] = array(
                'csv_row' => $row,
                'matches' => array()
            );
            continue;
        }
        
        // Prepare the supply name for searching
        $words = explode(' ', strtolower($supply_name));
        $words = array_filter($words, function($word) use ($stopwords) {
            return strlen($word) > 2 && !in_array(strtolower($word), $stopwords);
        });
        
        // Sort words by length (descending) to prioritize more specific terms
        usort($words, function($a, $b) {
            return strlen($b) - strlen($a);
        });
        
        $matches = array();
        $matched_ids = array();
        
        // First try an exact match on the full supply name
        $exact_match_args = array(
            'post_type' => 'supplies',
            'posts_per_page' => 5,
            'meta_query' => array(
                array(
                    'key' => 'supply_name',
                    'value' => $supply_name,
                    'compare' => 'LIKE'
                )
            )
        );
        
        $exact_query = new WP_Query($exact_match_args);
        if ($exact_query->have_posts()) {
            while ($exact_query->have_posts()) {
                $exact_query->the_post();
                $post_id = get_the_ID();
                if (!in_array($post_id, $matched_ids)) {
                    $matched_ids[] = $post_id;
                    $matches[] = array(
                        'id' => $post_id,
                        'name' => get_the_title(),
                        'department' => get_post_meta($post_id, 'department', true),
                        'type' => get_post_meta($post_id, 'type', true),
                        'section' => get_post_meta($post_id, 'section', true),
                        'match_quality' => 'exact'
                    );
                }
            }
            wp_reset_postdata();
        }
        
        // If no exact matches, try word-by-word search
        if (empty($matches)) {
            foreach ($words as $word) {
                // Skip common words and short terms
                if (strlen($word) <= 2) {
                    continue;
                }

                // Search for supplies with this word
                $args = array(
                    'post_type' => 'supplies',
                    'posts_per_page' => 8,
                    's' => $word,
                    'orderby' => 'title',
                    'order' => 'ASC'
                );

                $query = new WP_Query($args);
                
                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        $post_id = get_the_ID();
                        if (!in_array($post_id, $matched_ids)) {
                            $matched_ids[] = $post_id;
                            $matches[] = array(
                                'id' => $post_id,
                                'name' => get_the_title(),
                                'department' => get_post_meta($post_id, 'department', true),
                                'type' => get_post_meta($post_id, 'type', true),
                                'section' => get_post_meta($post_id, 'section', true),
                                'match_quality' => 'partial'
                            );
                        }
                    }
                    // If we found matches, stop searching with other words
                    break;
                }
                wp_reset_postdata();
            }
        }
        
        // If still no matches, try searching post meta
        if (empty($matches)) {
            foreach ($words as $word) {
                if (strlen($word) <= 2) continue;
                
                $meta_args = array(
                    'post_type' => 'supplies',
                    'posts_per_page' => 8,
                    'meta_query' => array(
                        array(
                            'key' => 'supply_name',
                            'value' => $word,
                            'compare' => 'LIKE'
                        )
                    )
                );

                $meta_query = new WP_Query($meta_args);
                
                if ($meta_query->have_posts()) {
                    while ($meta_query->have_posts()) {
                        $meta_query->the_post();
                        $post_id = get_the_ID();
                        if (!in_array($post_id, $matched_ids)) {
                            $matched_ids[] = $post_id;
                            $matches[] = array(
                                'id' => $post_id,
                                'name' => get_the_title(),
                                'department' => get_post_meta($post_id, 'department', true),
                                'type' => get_post_meta($post_id, 'type', true),
                                'section' => get_post_meta($post_id, 'section', true),
                                'match_quality' => 'meta'
                            );
                        }
                    }
                    break;
                }
                wp_reset_postdata();
            }
        }

        $results[] = array(
            'csv_row' => $row,
            'matches' => array_unique($matches, SORT_REGULAR)
        );
    }

    // Get the offset from the request or use 0 as default
    $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
    
    wp_send_json_success(array(
        'results' => $results,
        'offset' => $offset + count($batch_data),
        'has_more' => false // The client will now determine if there are more items
    ));
}

/**
 * Confirm supply matches
 */
function confirm_supply_matches() {
    check_ajax_referer('supply_corrector_nonce', 'nonce');

    if (!isset($_POST['matches'])) {
        wp_send_json_error('No matches provided');
    }

    $matches = json_decode(stripslashes($_POST['matches']), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error('Invalid JSON data: ' . json_last_error_msg());
    }
    
    // Validate matches data
    $valid_matches = array();
    foreach ($matches as $match) {
        if (!isset($match['supply_id']) || !isset($match['csv_data'])) {
            continue;
        }
        
        $supply_id = intval($match['supply_id']);
        $csv_data = $match['csv_data'];
        
        // Verify that supply_id exists
        $supply_post = get_post($supply_id);
        if (!$supply_post || $supply_post->post_type !== 'supplies') {
            continue;
        }
        
        $valid_matches[] = $match;
    }
    
    if (empty($valid_matches)) {
        wp_send_json_error('No valid matches found');
    }
    
    // No need to store in transients anymore, data is in localStorage
    wp_send_json_success(array(
        'message' => 'Matches confirmed successfully',
        'count' => count($valid_matches)
    ));
}

/**
 * Check supply discrepancies
 */
function check_supply_discrepancies() {
    check_ajax_referer('supply_corrector_nonce', 'nonce');

    // Get matches directly from the request
    $matches = isset($_POST['matches']) ? json_decode(stripslashes($_POST['matches']), true) : null;
    
    if (!$matches) {
        // For backward compatibility, try the old method
        $matches_key = 'supply_matches_' . wp_get_current_user()->ID;
        $matches = get_transient($matches_key);
        
        if (!$matches) {
            wp_send_json_error('Match data not found. Please go back and select matches again.');
        }
        
        $batch_size = 10;
        $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
        $matches = array_slice($matches, $offset, $batch_size);
    }

    $results = array();

    foreach ($matches as $match) {
        if (!isset($match['supply_id']) || !isset($match['csv_data'])) {
            continue;
        }

        $supply_id = $match['supply_id'];
        $csv_data = $match['csv_data'];
        
        // Get current balance using the same logic as supplies-ajax-handler.php
        $actual_quantity = 0;
        $release_quantity = 0;

        // Get actual supplies
        $actual_supplies = get_posts(array(
            'post_type' => 'actualsupplies',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'supply_name',
                    'value' => $supply_id,
                    'compare' => '='
                )
            )
        ));

        foreach ($actual_supplies as $actual) {
            $quantity = (float)get_post_meta($actual->ID, 'quantity', true);
            $actual_quantity += $quantity;
            
            // No longer tracking expired quantities separately
            // Expired items are now included in the total count
        }

        // Get release supplies
        $release_supplies = get_posts(array(
            'post_type' => 'releasesupplies',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'supply_name',
                    'value' => $supply_id,
                    'compare' => '='
                )
            )
        ));

        foreach ($release_supplies as $release) {
            if (get_post_meta($release->ID, 'confirmed', true) == '1') {
                $quantity = (float)get_post_meta($release->ID, 'quantity', true);
                $release_quantity += $quantity;
            }
        }

        // Calculate current balance - now we don't subtract expired_quantity
        $current_balance = $actual_quantity - $release_quantity;
        $csv_count = (float)$csv_data['actual_count'];
        
        // Find the absolute and percentage discrepancy
        $discrepancy = $csv_count - $current_balance;
        $percent_discrepancy = $current_balance > 0 ? 
            round(($discrepancy / $current_balance) * 100, 1) : 
            ($discrepancy != 0 ? 100 : 0);

        $results[] = array(
            'supply_id' => $supply_id,
            'supply_name' => get_the_title($supply_id),
            'current_balance' => $current_balance,
            'csv_count' => $csv_count,
            'discrepancy' => $discrepancy,
            'percent_discrepancy' => $percent_discrepancy,
            'csv_data' => $csv_data
        );
    }

    // Get the offset from the request
    $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
    
    wp_send_json_success(array(
        'results' => $results,
        'offset' => $offset + count($matches),
        'has_more' => false // Client will determine if there are more items
    ));
}

/**
 * Update supply quantities based on CSV data
 */
function update_supply_quantities() {
    check_ajax_referer('supply_corrector_nonce', 'nonce');
    
    $updates_to_make = isset($_POST['updates']) ? json_decode(stripslashes($_POST['updates']), true) : [];
    
    if (empty($updates_to_make)) {
        wp_send_json_error('No updates provided');
    }
    
    $results = array(
        'success' => [],
        'failed' => []
    );
    
    foreach ($updates_to_make as $update) {
        $supply_id = isset($update['supply_id']) ? intval($update['supply_id']) : 0;
        $new_quantity = isset($update['new_quantity']) ? floatval($update['new_quantity']) : 0;
        $discrepancy = 0;
        $csv_data = array();
        
        // Get discrepancy from the update data - ensure it's a float
        if (isset($update['discrepancy'])) {
            $discrepancy = floatval($update['discrepancy']);
        } else if (isset($update['csv_data']) && isset($update['csv_data']['actual_count'])) {
            // Calculate discrepancy if not provided directly
            $current_balance = get_supply_current_balance($supply_id);
            $csv_count = floatval($update['csv_data']['actual_count']);
            $discrepancy = $csv_count - $current_balance;
        }
        
        // Extract CSV data
        if (isset($update['csv_data'])) {
            $csv_data = $update['csv_data'];
        }
        
        $lot_number = isset($csv_data['lot_number']) ? sanitize_text_field($csv_data['lot_number']) : '';
        $expiry_date = isset($csv_data['expiry_date']) ? sanitize_text_field($csv_data['expiry_date']) : '';
        $serial = isset($csv_data['serial']) ? sanitize_text_field($csv_data['serial']) : '';
        $states__status = isset($csv_data['states__status']) ? sanitize_text_field($csv_data['states__status']) : 'active';
        
        // Skip if supply ID is invalid
        if (!$supply_id) {
            $results['failed'][] = array(
                'supply_id' => $supply_id,
                'reason' => 'Invalid supply ID'
            );
            continue;
        }
        
        // Get the supply name for use in titles
        $supply_name = get_the_title($supply_id);
        
        // Debug information to help diagnose issues
        error_log("Processing supply update: ID=$supply_id, Name=$supply_name, Discrepancy=$discrepancy, Type=" . gettype($discrepancy));
        error_log("CSV Data: " . print_r($csv_data, true));
        
        // Different handling based on whether discrepancy is positive or negative
        if ($discrepancy < 0) {
            error_log("Handling negative discrepancy: $discrepancy");
            
            // For negative discrepancy, we may need to update multiple actualsupplies records
            
            // Convert discrepancy to positive value for easier calculation
            $remaining_reduction = abs($discrepancy);
            $updated_records = [];
            
            // Get all actualsupplies for this supply, ordered by date (newest first)
            $all_actual_supplies = get_posts(array(
                'post_type' => 'actualsupplies',
                'posts_per_page' => -1,
                'orderby' => 'date',
                'order' => 'DESC',
                'meta_query' => array(
                    array(
                        'key' => 'supply_name',
                        'value' => $supply_id,
                        'compare' => '='
                    )
                )
            ));
            
            if (empty($all_actual_supplies)) {
                error_log("No actual supplies found for negative adjustment");
                $results['failed'][] = array(
                    'supply_id' => $supply_id,
                    'reason' => 'No existing actual supplies found for negative adjustment'
                );
                continue;
            }
            
            error_log("Found " . count($all_actual_supplies) . " actual supplies records to process");
            
            // Iterate through actual supplies and reduce quantities until discrepancy is fully applied
            foreach ($all_actual_supplies as $actual_supply) {
                if ($remaining_reduction <= 0) {
                    break; // No more reduction needed
                }
                
                $actual_id = $actual_supply->ID;
                $current_quantity = floatval(get_post_meta($actual_id, 'quantity', true));
                
                error_log("Processing record ID=$actual_id with quantity=$current_quantity");
                
                // Skip if this record already has zero quantity
                if ($current_quantity <= 0) {
                    error_log("Skipping record with zero quantity");
                    continue;
                }
                
                // Determine how much to reduce from this record
                $reduction_for_this_record = min($current_quantity, $remaining_reduction);
                $new_record_quantity = $current_quantity - $reduction_for_this_record;
                
                error_log("Reducing by $reduction_for_this_record, new quantity: $new_record_quantity");
                
                // Update the quantity
                update_post_meta($actual_id, 'quantity', $new_record_quantity);
                
                // Update expiry date if provided (only for the first record)
                if (!empty($expiry_date) && empty($updated_records)) {
                    update_post_meta($actual_id, 'expiry_date', $expiry_date);
                    error_log("Updated expiry date to $expiry_date");
                }
                
                // Add adjustment note
                update_post_meta($actual_id, 'adjustment_note', 'CSV correction on ' . date('Y-m-d') . ' - Reduced by ' . $reduction_for_this_record);
                
                // Track this update
                $updated_records[] = array(
                    'actual_id' => $actual_id,
                    'old_quantity' => $current_quantity,
                    'new_quantity' => $new_record_quantity,
                    'reduction' => $reduction_for_this_record
                );
                
                // Reduce the remaining amount to be applied
                $remaining_reduction -= $reduction_for_this_record;
                error_log("Remaining reduction: $remaining_reduction");
            }
            
            // Check if we were able to fully apply the discrepancy
            if ($remaining_reduction > 0) {
                // We couldn't fully apply the discrepancy, but we'll consider it a partial success
                error_log("Could not fully apply reduction, remaining: $remaining_reduction");
                $results['success'][] = array(
                    'supply_id' => $supply_id,
                    'action' => 'updated_partially',
                    'target_reduction' => abs($discrepancy),
                    'actual_reduction' => abs($discrepancy) - $remaining_reduction,
                    'updated_records' => $updated_records,
                    'note' => 'Warning: Could not fully apply the reduction as there was insufficient quantity in existing records'
                );
            } else {
                // Discrepancy was fully applied
                error_log("Successfully applied full reduction");
                $results['success'][] = array(
                    'supply_id' => $supply_id,
                    'action' => 'updated_multiple',
                    'total_reduction' => abs($discrepancy),
                    'updated_records' => $updated_records
                );
            }
            
        } else if ($discrepancy > 0) {
            error_log("Handling positive discrepancy: $discrepancy");
            // For positive discrepancy, create a new actualsupplies post
            
            // Ensure we have a positive number, properly parsed as float
            $quantity_to_add = floatval($discrepancy);
            error_log("Quantity to add (parsed as float): $quantity_to_add");
            
            // Create a new actual supply entry with the same title as the supply name
            $post_id = wp_insert_post(array(
                'post_title' => $supply_name,
                'post_type' => 'actualsupplies',
                'post_status' => 'publish'
            ));
            
            if (is_wp_error($post_id)) {
                error_log("Error creating new post: " . $post_id->get_error_message());
                $results['failed'][] = array(
                    'supply_id' => $supply_id,
                    'reason' => $post_id->get_error_message()
                );
                continue;
            }
            
            error_log("Created new actualsupplies post with ID: $post_id");
            
            // Set meta values
            update_post_meta($post_id, 'supply_name', $supply_id);
            update_post_meta($post_id, 'quantity', $quantity_to_add); // Now using the properly parsed value
            update_post_meta($post_id, 'date_added', date('Y-m-d'));
            update_post_meta($post_id, 'adjustment_type', 'csv_import');
            
            // Add additional meta from CSV
            if (!empty($lot_number)) {
                update_post_meta($post_id, 'lot_number', $lot_number);
            }
            
            if (!empty($expiry_date)) {
                update_post_meta($post_id, 'expiry_date', $expiry_date);
            }
            
            if (!empty($serial)) {
                update_post_meta($post_id, 'serial', $serial);
            }
            
            if (!empty($states__status)) {
                update_post_meta($post_id, 'states__status', $states__status);
            }
            
            $results['success'][] = array(
                'supply_id' => $supply_id,
                'action' => 'created',
                'actual_supply_id' => $post_id,
                'quantity_added' => $quantity_to_add,
                'lot_number' => $lot_number,
                'expiry_date' => $expiry_date,
                'serial' => $serial
            );
        }
    }
    
    wp_send_json_success($results);
}

/**
 * Helper function to get the current balance of a supply
 */
function get_supply_current_balance($supply_id) {
    $actual_quantity = 0;
    $release_quantity = 0;

    // Get actual supplies
    $actual_supplies = get_posts(array(
        'post_type' => 'actualsupplies',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'supply_name',
                'value' => $supply_id,
                'compare' => '='
            )
        )
    ));

    foreach ($actual_supplies as $actual) {
        $quantity = (float)get_post_meta($actual->ID, 'quantity', true);
        $actual_quantity += $quantity;
    }

    // Get release supplies
    $release_supplies = get_posts(array(
        'post_type' => 'releasesupplies',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'supply_name',
                'value' => $supply_id,
                'compare' => '='
            )
        )
    ));

    foreach ($release_supplies as $release) {
        if (get_post_meta($release->ID, 'confirmed', true) == '1') {
            $quantity = (float)get_post_meta($release->ID, 'quantity', true);
            $release_quantity += $quantity;
        }
    }

    // Calculate current balance
    return $actual_quantity - $release_quantity;
}