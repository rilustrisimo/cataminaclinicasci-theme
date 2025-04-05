<?php
/**
 * Script to update all 'releasesupplies' posts and set 'confirmed' field to TRUE
 */

// Load WordPress environment
require_once( __DIR__ . '/../../../../wp-load.php' );

// Query for all releasesupplies posts
$args = array(
    'post_type' => 'releasesupplies',
    'posts_per_page' => -1, // Get all posts
    'post_status' => 'publish'
);

$query = new WP_Query($args);
$updated_count = 0;
$error_count = 0;
$already_confirmed = 0;

// Start output
echo '<div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;">';
echo '<h1>Updating "confirmed" field for Release Supplies</h1>';
echo '<p>Starting update process...</p>';

if ($query->have_posts()) {
    echo '<p>Found ' . $query->found_posts . ' releasesupplies posts to process.</p>';
    
    // Process each post
    while ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();
        
        // Check if already confirmed
        $current_value = get_field('confirmed', $post_id);
        
        if ($current_value == true) {
            $already_confirmed++;
            continue;
        }
        
        // Update the ACF field
        $update_result = update_field('confirmed', true, $post_id);
        
        if ($update_result) {
            $updated_count++;
        } else {
            $error_count++;
            echo '<p style="color: red;">Error updating post ID: ' . $post_id . ' - ' . get_the_title() . '</p>';
        }
    }
    
    // Restore original post data
    wp_reset_postdata();
    
    // Display results
    echo '<h2>Update Complete</h2>';
    echo '<p style="color: green;">Successfully updated: ' . $updated_count . ' posts</p>';
    echo '<p style="color: blue;">Already confirmed: ' . $already_confirmed . ' posts</p>';
    
    if ($error_count > 0) {
        echo '<p style="color: red;">Errors encountered: ' . $error_count . ' posts</p>';
    }
} else {
    echo '<p>No releasesupplies posts found.</p>';
}

echo '</div>';
?>