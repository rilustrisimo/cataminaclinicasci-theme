<?php
require_once( __DIR__ . '/../../../../wp-load.php' );
require_once( __DIR__ . '/class-main.php' );

$theme = new Theme();

// Set up batch processing variables
$batch_size = 50; // Process posts in batches to avoid timeouts
$offset = 0;
$total_updated = 0;
$total_processed = 0;
$errors = [];

echo '<h1>Updating ActualSupplies Post Objects</h1>';
echo '<pre>';

// Query all actualsupplies posts where related_release_id is not empty
$args = array(
    'post_type' => 'actualsupplies',
    'posts_per_page' => $batch_size,
    'offset' => $offset,
    'meta_query' => array(
        array(
            'key' => 'related_release_id',
            'compare' => 'EXISTS',
        ),
        array(
            'key' => 'related_release_id',
            'value' => '',
            'compare' => '!=',
        ),
    ),
);

$query = new WP_Query($args);
$found_posts = $query->found_posts;

echo "Found {$found_posts} actualsupplies posts with related_release_id...\n\n";

while ($query->have_posts()) {
    // Process posts in batches
    while ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();
        $total_processed++;
        
        try {
            // Get the current supply_name value (which could be an ID, string, or already an object)
            $current_supply_name = get_field('supply_name', $post_id);
            
            // Get the supply ID
            $supply_id = null;
            
            if (is_object($current_supply_name) && isset($current_supply_name->ID)) {
                // Already a post object, but we'll re-update it anyway
                $supply_id = $current_supply_name->ID;
                echo "Post #{$post_id} - Supply name is already a post object (ID: {$supply_id}), re-updating...\n";
            } elseif (is_numeric($current_supply_name)) {
                // It's just an ID
                $supply_id = $current_supply_name;
            } else {
                // Try to find the supply post by title
                $supply_args = array(
                    'post_type' => 'supplies',
                    'posts_per_page' => 1,
                    'title' => $current_supply_name,
                    'fields' => 'ids',
                );
                
                $supply_query = new WP_Query($supply_args);
                if ($supply_query->have_posts()) {
                    $supply_id = $supply_query->posts[0];
                }
                wp_reset_postdata();
            }
            
            if ($supply_id) {
                // Get the supply post object
                $supply_post = get_post($supply_id);
                
                if ($supply_post) {
                    // Always update the field with the post object, even if it's already an object
                    $update_result = update_field('supply_name', $supply_post, $post_id);
                    
                    // Consider all updates as successful since we're forcing re-updates
                    echo "Updated post #{$post_id} - Set supply_name to post object (ID: {$supply_id}, Title: {$supply_post->post_title})\n";
                    $total_updated++;
                } else {
                    echo "Error updating post #{$post_id} - Supply post #{$supply_id} not found\n";
                    $errors[] = "Post #{$post_id} - Supply post #{$supply_id} not found";
                }
            } else {
                echo "Error updating post #{$post_id} - Could not determine supply_id from value: " . print_r($current_supply_name, true) . "\n";
                $errors[] = "Post #{$post_id} - Could not determine supply_id";
            }
        } catch (Exception $e) {
            echo "Error processing post #{$post_id}: " . $e->getMessage() . "\n";
            $errors[] = "Post #{$post_id} - " . $e->getMessage();
        }
    }
    
    // Get the next batch
    $offset += $batch_size;
    wp_reset_postdata();
    
    $args['offset'] = $offset;
    $query = new WP_Query($args);
    
    // If no more posts, break the loop
    if (!$query->have_posts()) {
        break;
    }
    
    // Give the server a small break
    usleep(500000); // 0.5 seconds
}

echo "\nProcess completed.";
echo "\nTotal processed: {$total_processed}";
echo "\nTotal updated: {$total_updated}";

if (count($errors) > 0) {
    echo "\n\nErrors:";
    foreach ($errors as $error) {
        echo "\n- {$error}";
    }
}

echo '</pre>';
?>