<?php
// Register AJAX actions
add_action('wp_ajax_process_supply_csv', 'process_supply_csv');
add_action('wp_ajax_search_supply_matches', 'search_supply_matches');
add_action('wp_ajax_confirm_supply_matches', 'confirm_supply_matches');
add_action('wp_ajax_check_supply_discrepancies', 'check_supply_discrepancies');
add_action('wp_ajax_update_supply_quantities', 'update_supply_quantities');
add_action('wp_ajax_export_matches_csv', 'export_matches_csv');
add_action('wp_ajax_nopriv_export_matches_csv', 'export_matches_csv');
add_action('wp_ajax_export_raw_matches_csv', 'export_raw_matches_csv');
add_action('wp_ajax_nopriv_export_raw_matches_csv', 'export_raw_matches_csv');

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
        $rows_data = [];
        $temp_csv_data = []; // Temporary array to store rows before consolidation
        
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
            
            // Sanitize and clean all values to handle single quotes and other special characters
            foreach ($row as $key => $value) {
                // Handle single quotes by escaping them properly
                $clean_value = stripslashes($value); // Remove any existing escaped slashes
                $clean_value = str_replace("'", "'", $clean_value); // Replace smart quotes
                $clean_value = trim($clean_value); // Remove surrounding whitespace
                
                // Store the sanitized value
                $row[$key] = $clean_value;
            }
            
            $rows_data[] = $row;
            
            // Validate required fields
            if (empty($row['supply_name']) || (!isset($row['actual_count']) && $row['actual_count'] !== '0')) {
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
            
            $temp_csv_data[] = $row;
        }
        fclose($handle);
        
        // Report if there were problematic rows
        if (!empty($error_rows)) {
            if (count($error_rows) === $row_count) {
                wp_send_json_error('All rows in the CSV file contain errors. Please check the format and try again.');
            }
        }
        
        // NEW CODE: Consolidate supplies with identical names
        $consolidated = [];
        
        foreach ($temp_csv_data as $row) {
            $supply_name = trim($row['supply_name']);
            
            if (isset($consolidated[$supply_name])) {
                // Add quantity to existing entry
                $consolidated[$supply_name]['actual_count'] = (float)$consolidated[$supply_name]['actual_count'] + (float)$row['actual_count'];
                
                // Check if we should update expiry date (use closest expiry)
                if (!empty($row['expiry_date']) && !empty($consolidated[$supply_name]['expiry_date'])) {
                    $current_expiry = strtotime($consolidated[$supply_name]['expiry_date']);
                    $new_expiry = strtotime($row['expiry_date']);
                    
                    // If new date is closer to now (will expire sooner)
                    if ($new_expiry < $current_expiry && $new_expiry >= time()) {
                        $consolidated[$supply_name]['expiry_date'] = $row['expiry_date'];
                    }
                } 
                // If current entry doesn't have expiry but new one does
                else if (!empty($row['expiry_date']) && empty($consolidated[$supply_name]['expiry_date'])) {
                    $consolidated[$supply_name]['expiry_date'] = $row['expiry_date'];
                }
                
                // Combine lot numbers if they're different
                if (!empty($row['lot_number']) && !empty($consolidated[$supply_name]['lot_number']) && 
                    $row['lot_number'] !== $consolidated[$supply_name]['lot_number']) {
                    $consolidated[$supply_name]['lot_number'] .= ', ' . $row['lot_number'];
                } 
                // If current entry doesn't have lot number but new one does
                else if (!empty($row['lot_number']) && empty($consolidated[$supply_name]['lot_number'])) {
                    $consolidated[$supply_name]['lot_number'] = $row['lot_number'];
                }
            } else {
                // Create new entry
                $consolidated[$supply_name] = $row;
            }
        }
        
        // Convert consolidated array back to indexed array
        $csv_data = array_values($consolidated);

        // Return the CSV data to be stored in localStorage (no need for transients anymore)
        wp_send_json_success(array(
            'message' => 'CSV processed successfully',
            'total_records' => count($csv_data),
            'original_records' => count($temp_csv_data),
            'consolidated_records' => count($temp_csv_data) - count($csv_data),
            'error_rows' => $error_rows,
            'csv_data' => $csv_data,
            'rows' => $rows_data
        ));
    } else {
        wp_send_json_error('Failed to process CSV file');
    }
}

/**
 * Search for matching supplies using word-by-word search with a point-based matching system
 * Prioritizes 100% exact matches first, then processes partial matches
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
    $exact_matches = array();
    $partial_matches = array();
    
    // Get stopwords for more efficient searching
    $stopwords = array('the', 'and', 'or', 'for', 'with', 'by', 'in', 'on', 'at', 'to', 'of', 'a', 'an');

    // Initialize array to track which database records have been matched
    // and their match scores with each CSV row
    $db_matches = array();
    
    // Get existing matches if available
    $existing_matches = isset($_POST['existing_matches']) ? json_decode(stripslashes($_POST['existing_matches']), true) : array();
    
    // FIRST PASS: Identify 100% exact matches by name
    foreach ($batch_data as $csv_index => $row) {
        $supply_name = trim($row['supply_name']);
        
        // Skip empty supply names
        if (empty($supply_name)) {
            $exact_matches[$csv_index] = false;
            $partial_matches[$csv_index] = false;
            continue;
        }
        
        // Try exact string match using post_title
        $exact_title_args = array(
            'post_type' => 'supplies',
            'posts_per_page' => 5,
            'title' => $supply_name,
            'exact' => true,
            'fields' => 'ids'
        );
        
        $exact_title_query = new WP_Query($exact_title_args);
        $found_exact_match = false;
        
        if ($exact_title_query->have_posts()) {
            foreach ($exact_title_query->posts as $post_id) {
                $db_title = get_the_title($post_id);
                
                // Check for 100% exact string match (case insensitive)
                if (strcasecmp($supply_name, $db_title) === 0) {
                    $match_data = array(
                        'id' => $post_id,
                        'name' => $db_title,
                        'department' => get_post_meta($post_id, 'department', true),
                        'type' => get_post_meta($post_id, 'type', true),
                        'section' => get_post_meta($post_id, 'section', true),
                        'match_quality' => 'exact',
                        'score' => 100
                    );
                    
                    $exact_matches[$csv_index] = array(
                        'csv_row' => $row,
                        'matches' => array($match_data),
                        'match_scores' => array($post_id => 100)
                    );
                    
                    // Track this match for the database record
                    if (!isset($db_matches[$post_id])) {
                        $db_matches[$post_id] = array();
                    }
                    $db_matches[$post_id][$csv_index] = 100;
                    
                    $found_exact_match = true;
                    break;
                }
            }
            wp_reset_postdata();
        }
        
        // If no exact match was found, add to partial matches for later processing
        if (!$found_exact_match) {
            $partial_matches[$csv_index] = $row;
            $exact_matches[$csv_index] = false;
        }
    }
    
    // SECOND PASS: Process partial matches only if they weren't exactly matched
    foreach ($partial_matches as $csv_index => $row) {
        // Skip entries that were already exactly matched or had empty names
        if ($row === false) {
            continue;
        }
        
        $supply_name = trim($row['supply_name']);
        
        // Prepare the supply name for searching
        $original_words = explode(' ', strtolower($supply_name));
        $words = array_filter($original_words, function($word) use ($stopwords) {
            return strlen($word) > 2 && !in_array(strtolower($word), $stopwords);
        });
        
        // Sort words by length (descending) to prioritize more specific terms
        usort($words, function($a, $b) {
            return strlen($b) - strlen($a);
        });
        
        // Check if we have existing matches for this CSV row
        if (isset($existing_matches[$csv_index])) {
            $matches = isset($existing_matches[$csv_index]['matches']) ? $existing_matches[$csv_index]['matches'] : array();
            $match_scores = isset($existing_matches[$csv_index]['match_scores']) ? $existing_matches[$csv_index]['match_scores'] : array();
            
            // If we have existing matches, use them and skip further processing for this row
            if (!empty($matches)) {
                // Add match information to db_matches tracking
                foreach ($match_scores as $post_id => $score) {
                    if (!isset($db_matches[$post_id])) {
                        $db_matches[$post_id] = array();
                    }
                    $db_matches[$post_id][$csv_index] = $score;
                }
                
                $results[$csv_index] = array(
                    'csv_row' => $row,
                    'matches' => array_values($matches), // Ensure it's an indexed array
                    'match_scores' => $match_scores
                );
                continue; // Skip further processing for this row
            }
        }
        
        $matches = array();
        $match_scores = array();
        
        // Try LIKE match on meta value
        $exact_match_args = array(
            'post_type' => 'supplies',
            'posts_per_page' => 10,
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
                $db_title = get_the_title();
                
                // Skip if this post_id already has a match for this CSV index
                if (isset($db_matches[$post_id]) && isset($db_matches[$post_id][$csv_index])) {
                    continue;
                }
                
                // Calculate match score
                $score = calculate_match_score($supply_name, $db_title);
                
                // Only include if score is above threshold - INCREASED TO 60
                if ($score >= 60) {
                    $match_data = array(
                        'id' => $post_id,
                        'name' => $db_title,
                        'department' => get_post_meta($post_id, 'department', true),
                        'type' => get_post_meta($post_id, 'type', true),
                        'section' => get_post_meta($post_id, 'section', true),
                        'match_quality' => 'partial',
                        'score' => $score
                    );
                    
                    // Only add if not already matched
                    if (!isset($matches[$post_id])) {
                        $matches[$post_id] = $match_data;
                        $match_scores[$post_id] = $score;
                        
                        // Track this match for the database record
                        if (!isset($db_matches[$post_id])) {
                            $db_matches[$post_id] = array();
                        }
                        $db_matches[$post_id][$csv_index] = $score;
                    }
                }
            }
            wp_reset_postdata();
        }
        
        // If still no matches with high score, try word-by-word search
        if (empty($matches) && !empty($words)) {
            foreach ($words as $word) {
                // Skip common words and short terms
                if (strlen($word) <= 2) {
                    continue;
                }

                // Search for supplies with this word
                $args = array(
                    'post_type' => 'supplies',
                    'posts_per_page' => 15, // Increased to get more potential matches
                    's' => $word,
                    'orderby' => 'title',
                    'order' => 'ASC'
                );

                $query = new WP_Query($args);
                
                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        $post_id = get_the_ID();
                        $db_title = get_the_title();
                        
                        // Skip if this post_id already has a match for this CSV index
                        if (isset($db_matches[$post_id]) && isset($db_matches[$post_id][$csv_index])) {
                            continue;
                        }
                        
                        // Skip if we already have a match for this post_id
                        if (isset($matches[$post_id])) {
                            continue;
                        }
                        
                        // Calculate match score
                        $score = calculate_match_score($supply_name, $db_title);
                        
                        // Only include if score is above threshold - INCREASED TO 60
                        if ($score >= 60) {
                            $match_data = array(
                                'id' => $post_id,
                                'name' => $db_title,
                                'department' => get_post_meta($post_id, 'department', true),
                                'type' => get_post_meta($post_id, 'type', true),
                                'section' => get_post_meta($post_id, 'section', true),
                                'match_quality' => 'partial',
                                'score' => $score
                            );
                            
                            $matches[$post_id] = $match_data;
                            $match_scores[$post_id] = $score;
                            
                            // Track this match for the database record
                            if (!isset($db_matches[$post_id])) {
                                $db_matches[$post_id] = array();
                            }
                            $db_matches[$post_id][$csv_index] = $score;
                        }
                    }
                    // Don't break early - collect all potential matches
                }
                wp_reset_postdata();
            }
        }
        
        // If still no matches, try searching post meta
        if (empty($matches) && !empty($words)) {
            foreach ($words as $word) {
                if (strlen($word) <= 2) continue;
                
                $meta_args = array(
                    'post_type' => 'supplies',
                    'posts_per_page' => 15, // Increased to get more potential matches
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
                        $db_title = get_the_title();
                        
                        // Skip if this post_id already has a match for this CSV index
                        if (isset($db_matches[$post_id]) && isset($db_matches[$post_id][$csv_index])) {
                            continue;
                        }
                        
                        // Skip if we already have a match for this post_id
                        if (isset($matches[$post_id])) {
                            continue;
                        }
                        
                        // Calculate match score
                        $score = calculate_match_score($supply_name, $db_title);
                        
                        // Only include if score is above threshold - INCREASED TO 60
                        if ($score >= 60) {
                            $match_data = array(
                                'id' => $post_id,
                                'name' => $db_title,
                                'department' => get_post_meta($post_id, 'department', true),
                                'type' => get_post_meta($post_id, 'type', true),
                                'section' => get_post_meta($post_id, 'section', true),
                                'match_quality' => 'meta',
                                'score' => $score
                            );
                            
                            $matches[$post_id] = $match_data;
                            $match_scores[$post_id] = $score;
                            
                            // Track this match for the database record
                            if (!isset($db_matches[$post_id])) {
                                $db_matches[$post_id] = array();
                            }
                            $db_matches[$post_id][$csv_index] = $score;
                        }
                    }
                    // Don't break early - collect all potential matches
                }
                wp_reset_postdata();
            }
        }
        
        // Sort matches by score (descending)
        arsort($match_scores);
        
        // CHANGED: Only include the single best match if it has a score of at least 50
        $best_match = array();
        if (!empty($match_scores)) {
            // Get the highest scoring match
            $best_id = key($match_scores);
            $best_score = current($match_scores);
            
            // Only include the match if score is 50 or higher
            if ($best_score >= 50) {
                $best_match[] = $matches[$best_id];
                
                // Keep track of only this match
                foreach ($db_matches as $post_id => $csv_matches) {
                    if ($post_id != $best_id && isset($csv_matches[$csv_index])) {
                        unset($db_matches[$post_id][$csv_index]);
                        if (empty($db_matches[$post_id])) {
                            unset($db_matches[$post_id]);
                        }
                    }
                }
            }
        }
        
        // Add to results 
        $results[$csv_index] = array(
            'csv_row' => $row,
            'matches' => $best_match,
            'match_scores' => $match_scores
        );
    }
    
    // THIRD PASS: Merge exact matches and processed partial matches
    foreach ($exact_matches as $csv_index => $match_data) {
        if ($match_data !== false) {
            // This was an exact match, add it to results
            $results[$csv_index] = $match_data;
        } elseif (!isset($results[$csv_index])) {
            // This had no exact match and no partial match was found
            // Make sure we include the original row with empty matches
            $results[$csv_index] = array(
                'csv_row' => $batch_data[$csv_index],
                'matches' => array(),
                'match_scores' => array()
            );
        }
    }
    
    // Sort results by index to maintain original order
    ksort($results);
    
    // Second pass: Resolve conflicts where multiple CSV rows match the same DB record
    foreach ($db_matches as $post_id => $csv_matches) {
        // If this DB record has multiple CSV matches, find the best match
        if (count($csv_matches) > 1) {
            // First, check if any of the matches are exact (score = 100)
            $has_exact_match = false;
            $exact_match_index = null;
            
            foreach ($csv_matches as $csv_index => $score) {
                if ($score == 100) {
                    $has_exact_match = true;
                    $exact_match_index = $csv_index;
                    break; // Found an exact match, no need to continue
                }
            }
            
            // If we have an exact match, always prioritize it
            if ($has_exact_match) {
                $best_csv_index = $exact_match_index;
            } else {
                // Otherwise, find the highest scoring CSV row for this DB record
                arsort($csv_matches);
                $best_csv_index = key($csv_matches);
            }
            
            // Remove this DB record from all other CSV rows' matches
            foreach ($csv_matches as $csv_index => $score) {
                if ($csv_index != $best_csv_index) {
                    // Remove this match from the other CSV rows
                    $filtered_matches = array();
                    foreach ($results[$csv_index]['matches'] as $match) {
                        if ($match['id'] != $post_id) {
                            $filtered_matches[] = $match;
                        }
                    }
                    $results[$csv_index]['matches'] = $filtered_matches;
                    
                    // Also update match_scores
                    unset($results[$csv_index]['match_scores'][$post_id]);
                }
            }
        }
    }
    
    // Convert to sequential array for response
    $final_results = array();
    foreach ($results as $result) {
        // Remove temporary match_scores field
        unset($result['match_scores']);
        $final_results[] = $result;
    }
    
    // Get the offset from the request or use 0 as default
    $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
    
    wp_send_json_success(array(
        'results' => $final_results,
        'offset' => $offset + count($batch_data),
        'has_more' => false // The client will now determine if there are more items
    ));
}

/**
 * Calculate a match score between the CSV supply name and the database record title
 *
 * @param string $csv_name  The CSV record's supply name
 * @param string $db_name   The database record's title/name
 * @return int              Match score (higher = better match)
 */
function calculate_match_score($csv_name, $db_name) {
    // Normalize both strings: lowercase, remove extra spaces
    $csv_name = strtolower(trim($csv_name));
    $db_name = strtolower(trim($db_name));
    
    // Return early if either string is empty
    if (empty($csv_name) || empty($db_name)) {
        return 0;
    }
    
    // Exact match gets highest score
    if ($csv_name === $db_name) {
        return 100;
    }
    
    $score = 0;
    
    // Check if one string contains the other completely
    if (strpos($csv_name, $db_name) !== false) {
        $score += 50;
    } elseif (strpos($db_name, $csv_name) !== false) {
        $score += 40;
    }
    
    // Split into words
    $csv_words = explode(' ', $csv_name);
    $db_words = explode(' ', $db_name);
    
    // Calculate word matches and positions
    $matching_words = 0;
    $position_matches = 0;
    
    foreach ($csv_words as $csv_index => $csv_word) {
        if (strlen($csv_word) <= 2) continue; // Skip very short words
        
        foreach ($db_words as $db_index => $db_word) {
            if (strlen($db_word) <= 2) continue; // Skip very short words
            
            // Check for exact word matches
            if ($csv_word === $db_word) {
                $matching_words++;
                
                // Bonus for matching words in the same position
                if ($csv_index === $db_index) {
                    $position_matches++;
                }
                break;
            }
            // Check for partial word matches (e.g. "GLIMEP" in "GLIMEPIRIDE")
            elseif (strlen($csv_word) >= 4 && (
                strpos($db_word, $csv_word) === 0 || // CSV word is prefix of DB word
                strpos($csv_word, $db_word) === 0    // DB word is prefix of CSV word
            )) {
                $matching_words += 0.7; // Partial match gets lower score
                break;
            }
        }
    }
    
    // Calculate percentage of matching words
    $total_meaningful_words = 0;
    foreach ($csv_words as $word) {
        if (strlen($word) > 2) $total_meaningful_words++;
    }
    
    if ($total_meaningful_words > 0) {
        $word_match_percentage = $matching_words / $total_meaningful_words;
        $score += $word_match_percentage * 30; // Up to 30 points for word matching
    }
    
    // Bonus for position matches
    $score += $position_matches * 5; // 5 points per position match
    
    // Check for pharmaceutical variants like dosage and form
    // Examples: "GLIMEPIRIDE 2MG TABLET" vs "GLIMEPIRIDE 2MG"
    $common_dosage_patterns = array('/\d+(\.\d+)?\s*(mg|g|ml|mcg|iu)/i');
    $common_form_words = array('tablet', 'capsule', 'injection', 'syrup', 'suspension');
    
    foreach ($common_dosage_patterns as $pattern) {
        $csv_has_dosage = preg_match($pattern, $csv_name);
        $db_has_dosage = preg_match($pattern, $db_name);
        
        if ($csv_has_dosage && $db_has_dosage) {
            // Extract the dosage values
            preg_match($pattern, $csv_name, $csv_dosage);
            preg_match($pattern, $db_name, $db_dosage);
            
            if ($csv_dosage[0] === $db_dosage[0]) {
                $score += 15; // Exact dosage match
            }
        }
    }
    
    // Check for common form words
    foreach ($common_form_words as $form) {
        $csv_has_form = stripos($csv_name, $form) !== false;
        $db_has_form = stripos($db_name, $form) !== false;
        
        if ($csv_has_form && $db_has_form) {
            $score += 10; // Both contain the same form
        }
    }
    
    // Bonus for length similarity (as a percentage of the longer string)
    $len_csv = strlen($csv_name);
    $len_db = strlen($db_name);
    $max_len = max($len_csv, $len_db);
    $min_len = min($len_csv, $len_db);
    if ($max_len > 0) {
        $length_similarity = $min_len / $max_len;
        $score += $length_similarity * 10; // Up to 10 points for length similarity
    }
    
    // Levenshtein distance for overall string similarity (normalized by length)
    $levenshtein = levenshtein($csv_name, $db_name);
    $max_possible_levenshtein = max($len_csv, $len_db);
    if ($max_possible_levenshtein > 0) {
        $levenshtein_similarity = 1 - ($levenshtein / $max_possible_levenshtein);
        $score += $levenshtein_similarity * 20; // Up to 20 points for overall string similarity
    }
    
    // Handle parenthetical qualifiers like "GLIMEPIRIDE 2MG (GLIMEP)"
    if (preg_match('/\((.*?)\)/', $db_name, $db_matches) && 
        preg_match('/\b' . preg_quote($db_matches[1], '/') . '\b/i', $csv_name)) {
        $score += 20; // Big bonus for matching a parenthetical identifier
    }
    
    // Handle brand/generic name variations by looking for multiple words that appear
    // in both strings even if in different orders
    $csv_words_set = array_flip($csv_words);
    $common_word_count = 0;
    foreach ($db_words as $word) {
        if (strlen($word) > 2 && isset($csv_words_set[$word])) {
            $common_word_count++;
        }
    }
    if (count($db_words) > 0) {
        $common_word_ratio = $common_word_count / count($db_words);
        $score += $common_word_ratio * 15; // Up to 15 points for common words
    }
    
    return round($score);
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

    // Get previous consolidated data if available from localStorage
    $previous_consolidated = isset($_POST['previous_consolidated']) ? json_decode(stripslashes($_POST['previous_consolidated']), true) : [];
    
    // Initialize consolidated array using previous data or create new one
    $consolidated = is_array($previous_consolidated) ? $previous_consolidated : [];
    
    // First pass: Collect all supply IDs and their quantities
    foreach ($matches as $match) {
        if (!isset($match['supply_id']) || !isset($match['csv_data'])) {
            continue;
        }

        $supply_id = $match['supply_id'];
        $csv_data = $match['csv_data'];
        $csv_count = (float)$csv_data['actual_count'];
        
        if (isset($consolidated[$supply_id])) {
            // Add the CSV count to the existing entry
            $consolidated[$supply_id]['csv_count'] += $csv_count;
            
            // If lot numbers are different and both exist, combine them
            if (!empty($csv_data['lot_number']) && !empty($consolidated[$supply_id]['csv_data']['lot_number']) && 
                $csv_data['lot_number'] !== $consolidated[$supply_id]['csv_data']['lot_number']) {
                $consolidated[$supply_id]['csv_data']['lot_number'] .= ', ' . $csv_data['lot_number'];
            }
            // If current entry doesn't have lot number but new one does
            else if (!empty($csv_data['lot_number']) && empty($consolidated[$supply_id]['csv_data']['lot_number'])) {
                $consolidated[$supply_id]['csv_data']['lot_number'] = $csv_data['lot_number'];
            }
            
            // Keep the earliest expiry date if both exist
            if (!empty($csv_data['expiry_date']) && !empty($consolidated[$supply_id]['csv_data']['expiry_date'])) {
                $current_expiry = strtotime($consolidated[$supply_id]['csv_data']['expiry_date']);
                $new_expiry = strtotime($csv_data['expiry_date']);
                
                if ($new_expiry < $current_expiry) {
                    $consolidated[$supply_id]['csv_data']['expiry_date'] = $csv_data['expiry_date'];
                }
            }
            // If current entry doesn't have expiry date but new one does
            else if (!empty($csv_data['expiry_date']) && empty($consolidated[$supply_id]['csv_data']['expiry_date'])) {
                $consolidated[$supply_id]['csv_data']['expiry_date'] = $csv_data['expiry_date'];
            }
        } else {
            // Create new entry for this supply_id
            $consolidated[$supply_id] = [
                'supply_id' => $supply_id,
                'csv_count' => $csv_count,
                'csv_data' => $csv_data
            ];
        }
    }

    // Second pass: Calculate discrepancies for each consolidated supply
    $results = [];
    
    // Store supply IDs to ensure we don't have duplicates in results
    $processed_ids = [];
    
    foreach ($consolidated as $supply_id => $data) {
        // Skip if we've already processed this supply ID
        if (in_array($supply_id, $processed_ids)) {
            continue;
        }
        
        // Add to processed list
        $processed_ids[] = $supply_id;
        
        // Initialize counters
        $actual_quantity = 0;
        $release_quantity = 0;
        $actual_ids = [];

        // Get actual supplies using WP_Query instead of get_posts
        $actual_args = array(
            'post_type' => 'actualsupplies',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'supply_name',
                    'value' => $supply_id,
                    'compare' => '=',
                    'type' => 'NUMERIC'
                )
            ),
            'update_post_meta_cache' => true,
        );

        $actual_query = new WP_Query($actual_args);
        $actual_supplies = $actual_query->posts;

        foreach ($actual_supplies as $actual) {
            $quantity = (float)get_post_meta($actual->ID, 'quantity', true);
            $actual_quantity += $quantity;
        }
        wp_reset_postdata(); // Reset post data after WP_Query

        // Get release supplies using WP_Query
        $release_args = array(
            'post_type' => 'releasesupplies',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'supply_name',
                    'value' => $supply_id,
                    'compare' => '=',
                    'type' => 'NUMERIC'
                )
            ),
            'update_post_meta_cache' => true
        );

        $release_query = new WP_Query($release_args);
        $release_supplies = $release_query->posts;

        foreach ($release_supplies as $release) {
            if (get_post_meta($release->ID, 'confirmed', true) == '1') {
                $quantity = (float)get_post_meta($release->ID, 'quantity', true);
                $release_quantity += $quantity;
            }
        }
        wp_reset_postdata(); // Reset post data after WP_Query

        // Calculate current balance - KEEPING THE ORIGINAL LOGIC
        $current_balance = $actual_quantity - $release_quantity;
        $csv_count = $data['csv_count'];
        
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
            'csv_data' => $data['csv_data'],
            'actual_supplies' => $actual_supplies,
        );
    }

    // Check if this is the final batch
    $is_final_batch = isset($_POST['is_final_batch']) ? boolval($_POST['is_final_batch']) : false;

    // Get the offset from the request
    $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
    $batch_count = count($matches);
    
    wp_send_json_success(array(
        'results' => $results,
        'offset' => $offset + $batch_count,
        'has_more' => !$is_final_batch,
        'consolidated' => $consolidated,
        'total_consolidated' => count($consolidated)
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
        $csv_data = isset($update['csv_data']) ? $update['csv_data'] : array();
        
        // Skip if supply ID is invalid
        if (!$supply_id) {
            $results['failed'][] = array(
                'supply_id' => $supply_id,
                'reason' => 'Invalid supply ID'
            );
            continue;
        }
        
        // Get the supply object for use in titles and ACF fields
        $supply_object = get_post($supply_id);
        if (!$supply_object || $supply_object->post_type !== 'supplies') {
            $results['failed'][] = array(
                'supply_id' => $supply_id,
                'reason' => 'Supply not found'
            );
            continue;
        }
        $supply_name = $supply_object->post_title;
        
        // Get the provided discrepancy value directly without validation
        $discrepancy = isset($update['discrepancy']) ? floatval($update['discrepancy']) : 0;
        
        // Extract CSV data fields
        $lot_number = isset($csv_data['lot_number']) ? sanitize_text_field($csv_data['lot_number']) : '';
        $expiry_date = isset($csv_data['expiry_date']) ? sanitize_text_field($csv_data['expiry_date']) : '';
        $serial = isset($csv_data['serial']) ? sanitize_text_field($csv_data['serial']) : '';
        $states__status = isset($csv_data['states__status']) ? sanitize_text_field($csv_data['states__status']) : 'active';
        
        // Debug information to help diagnose issues
        error_log("Processing supply update: ID=$supply_id, Name=$supply_name, Discrepancy=$discrepancy, Type=" . gettype($discrepancy));
        
        // Go directly to adjustment based on discrepancy
        if ($discrepancy > 0) {
            // For positive discrepancy, add new actualsupplies entry
            $quantity_to_add = floatval($discrepancy);
            error_log("Handling positive discrepancy: Adding $quantity_to_add to actualsupplies");
            
            // Create a new actual supply entry
            $post_id = wp_insert_post(array(
                'post_title' => $supply_name,
                'post_type' => 'actualsupplies',
                'post_status' => 'publish'
            ));
            
            if (is_wp_error($post_id)) {
                error_log("Error creating new actualsupplies post: " . $post_id->get_error_message());
                $results['failed'][] = array(
                    'supply_id' => $supply_id,
                    'reason' => $post_id->get_error_message()
                );
                continue;
            }
            
            // Set ACF fields using update_field for proper ACF handling
            // Using update_field instead of update_post_meta ensures proper ACF storage and display
            update_field('supply_name', $supply_id, $post_id); // This is an ACF relationship field
            update_field('quantity', $quantity_to_add, $post_id);
            update_field('date_added', date('Y-m-d'), $post_id);
            update_field('adjustment_type', 'csv_import', $post_id);
            
            // Add additional meta from CSV
            if (!empty($lot_number)) {
                update_field('lot_number', $lot_number, $post_id);
            }
            
            if (!empty($expiry_date)) {
                update_field('expiry_date', $expiry_date, $post_id);
            }
            
            if (!empty($serial)) {
                update_field('serial', $serial, $post_id);
            }
            
            if (!empty($states__status)) {
                update_field('states__status', $states__status, $post_id);
            }
            
            // Trigger ACF save to update any computed fields
            do_action('acf/save_post', $post_id);
            
            $results['success'][] = array(
                'supply_id' => $supply_id,
                'action' => 'created_actual',
                'post_id' => $post_id,
                'quantity_added' => $quantity_to_add,
                'lot_number' => $lot_number,
                'expiry_date' => $expiry_date,
                'serial' => $serial,
                'discrepancy' => $discrepancy
            );
            
        } else if ($discrepancy < 0) {
            // For negative discrepancy, create a new releasesupplies entry
            $quantity_to_release = abs(floatval($discrepancy));
            error_log("Handling negative discrepancy: Adding $quantity_to_release to releasesupplies");
            
            // Create a new release supply entry
            $post_id = wp_insert_post(array(
                'post_title' => $supply_name,
                'post_type' => 'releasesupplies',
                'post_status' => 'publish'
            ));
            
            if (is_wp_error($post_id)) {
                error_log("Error creating new releasesupplies post: " . $post_id->get_error_message());
                $results['failed'][] = array(
                    'supply_id' => $supply_id,
                    'reason' => $post_id->get_error_message()
                );
                continue;
            }
            
            // Set ACF fields using update_field for proper ACF handling
            update_field('supply_name', $supply_id, $post_id); // This is an ACF relationship field
            update_field('quantity', $quantity_to_release, $post_id);
            update_field('release_date', date('Y-m-d'), $post_id);
            update_field('department', 'Inventory Adjustment', $post_id);
            update_field('confirmed', '1', $post_id); // Mark as confirmed so it's deducted immediately
            update_field('adjustment_type', 'csv_correction', $post_id);
            update_field('adjustment_note', 'CSV inventory correction on ' . date('Y-m-d'), $post_id);
            
            // Trigger ACF save to update any computed fields
            do_action('acf/save_post', $post_id);
            
            $results['success'][] = array(
                'supply_id' => $supply_id,
                'action' => 'created_release',
                'post_id' => $post_id,
                'quantity_released' => $quantity_to_release,
                'discrepancy' => $discrepancy
            );
        } else {
            // No discrepancy, nothing to update
            $results['success'][] = array(
                'supply_id' => $supply_id,
                'action' => 'no_change',
                'reason' => 'No discrepancy found',
                'discrepancy' => $discrepancy
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
        'post_status' => 'publish',
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
        'post_status' => 'publish',
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

/**
 * Export matched supplies data to CSV
 */
function export_matches_csv() {
    check_ajax_referer('supply_corrector_nonce', 'nonce');

    // Get matches data from the request
    $matches_data = isset($_POST['matches_data']) ? json_decode(stripslashes($_POST['matches_data']), true) : [];
    
    if (empty($matches_data)) {
        wp_send_json_error('No data provided for export');
    }
    
    // Set up CSV headers
    $csv_headers = [
        'Supply Name (CSV)', 'Quantity', 'Expiry Date', 'Lot Number', 'Serial', 
        'Status', 'Date Added', 'Matched Supply ID', 'Matched Supply Name', 
        'Department', 'Type', 'Section'
    ];
    
    // Set up CSV data array
    $csv_data = [$csv_headers];
    
    // Process each match
    foreach ($matches_data as $match) {
        $csv_row = $match['csv_row'];
        $matched_supply = isset($match['matched_supply']) ? $match['matched_supply'] : null;
        
        $row = [
            $csv_row['supply_name'] ?? '',
            $csv_row['actual_count'] ?? '',
            $csv_row['expiry_date'] ?? '',
            $csv_row['lot_number'] ?? '',
            $csv_row['serial'] ?? '',
            $csv_row['states__status'] ?? '',
            $csv_row['date_added'] ?? '',
        ];
        
        // Add matched supply information if available
        if ($matched_supply) {
            $row[] = $matched_supply['id'] ?? '';
            $row[] = $matched_supply['name'] ?? '';
            $row[] = $matched_supply['department'] ?? '';
            $row[] = $matched_supply['type'] ?? '';
            $row[] = $matched_supply['section'] ?? '';
        } else {
            $row[] = 'No match';
            $row[] = 'No match';
            $row[] = '';
            $row[] = '';
            $row[] = '';
        }
        
        $csv_data[] = $row;
    }
    
    // Return the CSV data
    wp_send_json_success([
        'csv_data' => $csv_data,
        'filename' => 'supply_matches_' . date('Y-m-d_H-i-s') . '.csv'
    ]);
}