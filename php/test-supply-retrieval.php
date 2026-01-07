<?php
/**
 * Test script to debug supply retrieval for supply ID 31673
 * Access via: /wp-content/themes/eyor-theme/php/test-supply-retrieval.php
 */

// Load WordPress
require_once('../../../../../../wp-load.php');

$supply_id = 31673;

echo "<h1>Supply Retrieval Debug Test for Supply ID: {$supply_id}</h1>";
echo "<style>body{font-family:monospace;padding:20px;} .section{background:#f5f5f5;padding:15px;margin:10px 0;border-left:4px solid #333;} .error{color:red;} .success{color:green;} pre{background:#fff;padding:10px;overflow:auto;}</style>";

// 1. Check if supply exists
echo "<div class='section'>";
echo "<h2>1. Supply Post Check</h2>";
$supply_post = get_post($supply_id);
if ($supply_post) {
    echo "<div class='success'>✓ Supply post exists</div>";
    echo "<pre>";
    echo "ID: " . $supply_post->ID . "\n";
    echo "Title: " . $supply_post->post_title . "\n";
    echo "Type: " . $supply_post->post_type . "\n";
    echo "Status: " . $supply_post->post_status . "\n";
    echo "</pre>";
    
    $supply_meta = get_post_meta($supply_id);
    echo "<h3>Supply Meta Data:</h3><pre>";
    print_r($supply_meta);
    echo "</pre>";
} else {
    echo "<div class='error'>✗ Supply post not found</div>";
}
echo "</div>";

// 2. Find all actual supplies linked to this supply
echo "<div class='section'>";
echo "<h2>2. Actual Supplies Check</h2>";

// Method 1: Direct query
$actual_args = array(
    "post_type" => "actualsupplies",
    "posts_per_page" => -1,
    "meta_query" => array(
        array(
            "key" => "supply_name",
            "value" => $supply_id,
            "compare" => "=",
            "type" => "NUMERIC"
        )
    )
);

$actual_query = new WP_Query($actual_args);
echo "<h3>Method 1: Meta Query (NUMERIC)</h3>";
echo "Found: " . $actual_query->found_posts . " posts<br>";

if ($actual_query->have_posts()) {
    echo "<table border='1' cellpadding='5' style='border-collapse:collapse;width:100%;'>";
    echo "<tr><th>ID</th><th>supply_name (raw)</th><th>supply_name (type)</th><th>Quantity</th><th>Date Added</th></tr>";
    
    while ($actual_query->have_posts()) {
        $actual_query->the_post();
        $actual_id = get_the_ID();
        $supply_name_raw = get_post_meta($actual_id, 'supply_name', true);
        $quantity = get_post_meta($actual_id, 'quantity', true);
        $date_added = get_post_meta($actual_id, 'date_added', true);
        
        $supply_name_type = gettype($supply_name_raw);
        $supply_name_display = $supply_name_raw;
        
        if (is_object($supply_name_raw)) {
            $supply_name_display = "Object: " . get_class($supply_name_raw);
            if (isset($supply_name_raw->ID)) {
                $supply_name_display .= " (ID: " . $supply_name_raw->ID . ")";
            }
        }
        
        echo "<tr>";
        echo "<td>{$actual_id}</td>";
        echo "<td>" . esc_html($supply_name_display) . "</td>";
        echo "<td>{$supply_name_type}</td>";
        echo "<td>{$quantity}</td>";
        echo "<td>{$date_added}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='error'>No actual supplies found with numeric comparison</div>";
}
wp_reset_postdata();

// Method 2: Get ALL actual supplies and check manually
echo "<h3>Method 2: Check ALL Actual Supplies (first 100)</h3>";
$all_actual_args = array(
    "post_type" => "actualsupplies",
    "posts_per_page" => 100,
    "orderby" => "ID",
    "order" => "DESC"
);

$all_actual_query = new WP_Query($all_actual_args);
$found_matches = array();

if ($all_actual_query->have_posts()) {
    while ($all_actual_query->have_posts()) {
        $all_actual_query->the_post();
        $actual_id = get_the_ID();
        $supply_name_raw = get_post_meta($actual_id, 'supply_name', true);
        
        $extracted_id = null;
        if (is_object($supply_name_raw) && isset($supply_name_raw->ID)) {
            $extracted_id = $supply_name_raw->ID;
        } elseif (is_numeric($supply_name_raw)) {
            $extracted_id = intval($supply_name_raw);
        }
        
        if ($extracted_id == $supply_id) {
            $found_matches[] = array(
                'actual_id' => $actual_id,
                'raw' => $supply_name_raw,
                'extracted' => $extracted_id,
                'quantity' => get_post_meta($actual_id, 'quantity', true),
                'date_added' => get_post_meta($actual_id, 'date_added', true)
            );
        }
    }
}
wp_reset_postdata();

echo "Manually found " . count($found_matches) . " matches<br>";
if (count($found_matches) > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse:collapse;width:100%;'>";
    echo "<tr><th>Actual ID</th><th>Raw Value Type</th><th>Extracted ID</th><th>Quantity</th><th>Date Added</th></tr>";
    foreach ($found_matches as $match) {
        $type_display = is_object($match['raw']) ? get_class($match['raw']) : gettype($match['raw']);
        echo "<tr>";
        echo "<td>{$match['actual_id']}</td>";
        echo "<td>{$type_display}</td>";
        echo "<td>{$match['extracted']}</td>";
        echo "<td>{$match['quantity']}</td>";
        echo "<td>{$match['date_added']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "</div>";

// 3. Find all release supplies linked to this supply
echo "<div class='section'>";
echo "<h2>3. Release Supplies Check</h2>";

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
    )
);

$release_query = new WP_Query($release_args);
echo "<h3>Method 1: Meta Query (NUMERIC)</h3>";
echo "Found: " . $release_query->found_posts . " posts<br>";

if ($release_query->have_posts()) {
    echo "<table border='1' cellpadding='5' style='border-collapse:collapse;width:100%;'>";
    echo "<tr><th>ID</th><th>supply_name (raw)</th><th>supply_name (type)</th><th>Quantity</th><th>Release Date</th><th>Confirmed</th></tr>";
    
    while ($release_query->have_posts()) {
        $release_query->the_post();
        $release_id = get_the_ID();
        $supply_name_raw = get_post_meta($release_id, 'supply_name', true);
        $quantity = get_post_meta($release_id, 'quantity', true);
        $release_date = get_post_meta($release_id, 'release_date', true);
        $confirmed = get_post_meta($release_id, 'confirmed', true);
        
        $supply_name_type = gettype($supply_name_raw);
        $supply_name_display = $supply_name_raw;
        
        if (is_object($supply_name_raw)) {
            $supply_name_display = "Object: " . get_class($supply_name_raw);
            if (isset($supply_name_raw->ID)) {
                $supply_name_display .= " (ID: " . $supply_name_raw->ID . ")";
            }
        }
        
        echo "<tr>";
        echo "<td>{$release_id}</td>";
        echo "<td>" . esc_html($supply_name_display) . "</td>";
        echo "<td>{$supply_name_type}</td>";
        echo "<td>{$quantity}</td>";
        echo "<td>{$release_date}</td>";
        echo "<td>" . ($confirmed == '1' ? 'Yes' : 'No') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='error'>No release supplies found with numeric comparison</div>";
}
wp_reset_postdata();

// Method 2: Check ALL release supplies manually
echo "<h3>Method 2: Check ALL Release Supplies (first 100)</h3>";
$all_release_args = array(
    "post_type" => "releasesupplies",
    "posts_per_page" => 100,
    "orderby" => "ID",
    "order" => "DESC"
);

$all_release_query = new WP_Query($all_release_args);
$found_release_matches = array();

if ($all_release_query->have_posts()) {
    while ($all_release_query->have_posts()) {
        $all_release_query->the_post();
        $release_id = get_the_ID();
        $supply_name_raw = get_post_meta($release_id, 'supply_name', true);
        
        $extracted_id = null;
        if (is_object($supply_name_raw) && isset($supply_name_raw->ID)) {
            $extracted_id = $supply_name_raw->ID;
        } elseif (is_numeric($supply_name_raw)) {
            $extracted_id = intval($supply_name_raw);
        }
        
        if ($extracted_id == $supply_id) {
            $found_release_matches[] = array(
                'release_id' => $release_id,
                'raw' => $supply_name_raw,
                'extracted' => $extracted_id,
                'quantity' => get_post_meta($release_id, 'quantity', true),
                'release_date' => get_post_meta($release_id, 'release_date', true),
                'confirmed' => get_post_meta($release_id, 'confirmed', true)
            );
        }
    }
}
wp_reset_postdata();

echo "Manually found " . count($found_release_matches) . " matches<br>";
if (count($found_release_matches) > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse:collapse;width:100%;'>";
    echo "<tr><th>Release ID</th><th>Raw Value Type</th><th>Extracted ID</th><th>Quantity</th><th>Release Date</th><th>Confirmed</th></tr>";
    foreach ($found_release_matches as $match) {
        $type_display = is_object($match['raw']) ? get_class($match['raw']) : gettype($match['raw']);
        echo "<tr>";
        echo "<td>{$match['release_id']}</td>";
        echo "<td>{$type_display}</td>";
        echo "<td>{$match['extracted']}</td>";
        echo "<td>{$match['quantity']}</td>";
        echo "<td>{$match['release_date']}</td>";
        echo "<td>" . ($match['confirmed'] == '1' ? 'Yes' : 'No') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "</div>";

// 4. Summary
echo "<div class='section'>";
echo "<h2>4. Summary</h2>";
echo "<ul>";
echo "<li><strong>Supply exists:</strong> " . ($supply_post ? 'Yes' : 'No') . "</li>";
echo "<li><strong>Actual supplies (numeric query):</strong> " . $actual_query->found_posts . "</li>";
echo "<li><strong>Actual supplies (manual check):</strong> " . count($found_matches) . "</li>";
echo "<li><strong>Release supplies (numeric query):</strong> " . $release_query->found_posts . "</li>";
echo "<li><strong>Release supplies (manual check):</strong> " . count($found_release_matches) . "</li>";
echo "</ul>";

if (count($found_matches) > $actual_query->found_posts) {
    echo "<div class='error'><strong>⚠️ ISSUE FOUND:</strong> Manual check found more actual supplies than the numeric query. This means supply_name is stored as objects, not numeric IDs.</div>";
}

if (count($found_release_matches) > $release_query->found_posts) {
    echo "<div class='error'><strong>⚠️ ISSUE FOUND:</strong> Manual check found more release supplies than the numeric query. This means supply_name is stored as objects, not numeric IDs.</div>";
}

echo "</div>";
