<?php
/**
 * Supplies Overview Debug Script
 * 
 * Provides a structured view of supplies with related actual supplies and release supplies.
 * Uses AJAX batch processing to prevent timeouts.
 */

// Load WordPress environment
require_once( dirname(__FILE__) . '/../../../../wp-load.php' );

// Security check - only allow admin users
if (!current_user_can('manage_options')) {
    wp_die('Access denied. You must be an administrator to view this page.');
}

// Get the Theme class
require_once( dirname(__FILE__) . '/class-main.php' );
$theme = new Theme();

// Get total count of supplies for batch processing
$supplies_count = wp_count_posts('supplies')->publish;

// Page header with styling
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplies Overview</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo site_url('/wp-admin/load-scripts.php?c=1&load=jquery-ui-core,jquery-ui-widget,jquery-ui-accordion'); ?>"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f5f5f5;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h1 {
            color: #23282d;
            margin-top: 0;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .progress-container {
            margin: 20px 0;
            background: #f1f1f1;
            border-radius: 4px;
            height: 25px;
            overflow: hidden;
        }
        .progress-bar {
            height: 100%;
            background: #0073aa;
            width: 0;
            transition: width 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: bold;
        }
        .status {
            margin-bottom: 20px;
            font-size: 14px;
        }
        .supply-item {
            border: 1px solid #ddd;
            margin-bottom: 15px;
            border-radius: 4px;
            overflow: hidden;
        }
        .supply-header {
            background: #0073aa;
            color: #fff;
            padding: 10px 15px;
            cursor: pointer;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .supply-details {
            padding: 0 15px;
            display: none;
        }
        .supply-meta {
            margin: 10px 0;
            padding-bottom: 10px;
            border-bottom: 1px dashed #eee;
            font-size: 14px;
            display: flex;
            flex-wrap: wrap;
        }
        .supply-meta span {
            margin-right: 20px;
            white-space: nowrap;
        }
        .sub-items {
            margin: 15px 0;
        }
        .sub-item-header {
            background: #f8f8f8;
            padding: 8px;
            font-weight: bold;
            border-left: 3px solid #0073aa;
            margin-bottom: 10px;
        }
        .sub-item {
            padding: 5px 10px;
            margin-bottom: 5px;
            border-left: 1px solid #ddd;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
        }
        .actual-supply {
            background-color: #e7f7e7;
        }
        .release-supply {
            background-color: #f7e7e7;
        }
        .quantity {
            font-weight: bold;
            min-width: 80px;
            text-align: right;
        }
        .positive {
            color: green;
        }
        .negative {
            color: red;
        }
        .zero {
            color: #888;
        }
        .date {
            font-size: 12px;
            color: #666;
            width: 100px;
            text-align: right;
        }
        .buttons {
            margin-top: 20px;
            text-align: center;
        }
        .button {
            background: #0073aa;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin: 0 5px;
        }
        .button:hover {
            background: #005d8c;
        }
        .button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .ui-accordion .ui-accordion-header {
            padding: 10px 15px;
            background: #f1f1f1;
            border: 1px solid #ddd;
            font-weight: bold;
            cursor: pointer;
            margin: 2px 0 0 0;
        }
        .ui-accordion .ui-accordion-content {
            padding: 15px;
            border: 1px solid #ddd;
            border-top: 0;
        }
        .summary {
            background: #f9f9f9;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 3px solid #0073aa;
        }
        .summary-item {
            margin-bottom: 5px;
            display: flex;
            justify-content: space-between;
        }
        .summary-item span:first-child {
            font-weight: bold;
        }
        .filters {
            margin: 20px 0;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .filter {
            flex: 1;
            min-width: 200px;
        }
        .filter label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .filter select, .filter input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-left: 10px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .expired-date {
            color: red;
            font-weight: bold;
        }
        .warning-date {
            color: orange;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Supplies Overview</h1>
        
        <div class="summary" id="summary">
            <div class="summary-item">
                <span>Total Supplies:</span>
                <span><?php echo $supplies_count; ?></span>
            </div>
            <div class="summary-item">
                <span>Total Loaded:</span>
                <span id="loaded-count">0</span>
            </div>
            <div class="summary-item">
                <span>Total Actual Supplies:</span>
                <span id="actual-count">0</span>
            </div>
            <div class="summary-item">
                <span>Total Release Supplies:</span>
                <span id="release-count">0</span>
            </div>
        </div>
        
        <div class="filters">
            <div class="filter">
                <label for="search">Search:</label>
                <input type="text" id="search" placeholder="Search supplies...">
            </div>
            <div class="filter">
                <label for="department-filter">Department:</label>
                <select id="department-filter">
                    <option value="">All Departments</option>
                    <?php 
                    // Get unique departments from supplies
                    global $wpdb;
                    $departments = $wpdb->get_col("
                        SELECT DISTINCT meta_value 
                        FROM {$wpdb->postmeta} 
                        WHERE meta_key = 'department' 
                        ORDER BY meta_value ASC
                    ");
                    
                    foreach ($departments as $department) {
                        echo '<option value="' . esc_attr($department) . '">' . esc_html($department) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="filter">
                <label for="type-filter">Type:</label>
                <select id="type-filter">
                    <option value="">All Types</option>
                    <?php 
                    // Get unique types from supplies
                    $types = $wpdb->get_col("
                        SELECT DISTINCT meta_value 
                        FROM {$wpdb->postmeta} 
                        WHERE meta_key = 'type' 
                        ORDER BY meta_value ASC
                    ");
                    
                    foreach ($types as $type) {
                        echo '<option value="' . esc_attr($type) . '">' . esc_html($type) . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        
        <div class="progress-container">
            <div class="progress-bar" id="progress-bar">0%</div>
        </div>
        <div class="status" id="status">Ready to load supplies data...</div>
        
        <div id="supplies-container"></div>
        
        <div class="buttons">
            <button id="load-button" class="button">Load Supplies Data</button>
            <button id="expand-all" class="button" disabled>Expand All</button>
            <button id="collapse-all" class="button" disabled>Collapse All</button>
        </div>
    </div>

    <script>
        var suppliesCount = <?php echo $supplies_count; ?>;
        var batchSize = 100;
        var currentOffset = 0;
        var isLoading = false;
        var actualSuppliesCount = 0;
        var releaseSuppliesCount = 0;
        var loadedCount = 0;
        
        jQuery(document).ready(function($) {
            $('#load-button').on('click', function() {
                if (isLoading) return;
                
                $(this).prop('disabled', true);
                isLoading = true;
                currentOffset = 0;
                
                // Clear previous data
                $('#supplies-container').empty();
                actualSuppliesCount = 0;
                releaseSuppliesCount = 0;
                loadedCount = 0;
                updateSummary();
                
                loadNextBatch();
            });
            
            // Filter and search functionality
            $('#search, #department-filter, #type-filter').on('input change', function() {
                filterSupplies();
            });
            
            // Expand/Collapse buttons
            $('#expand-all').on('click', function() {
                $('.supply-details').show();
            });
            
            $('#collapse-all').on('click', function() {
                $('.supply-details').hide();
            });
            
            // Initialize as accordion
            $(document).on('click', '.supply-header', function() {
                $(this).next('.supply-details').slideToggle();
            });
        });
        
        function updateSummary() {
            jQuery('#loaded-count').text(loadedCount);
            jQuery('#actual-count').text(actualSuppliesCount);
            jQuery('#release-count').text(releaseSuppliesCount);
        }
        
        function filterSupplies() {
            var search = jQuery('#search').val().toLowerCase();
            var department = jQuery('#department-filter').val();
            var type = jQuery('#type-filter').val();
            
            jQuery('.supply-item').each(function() {
                var $this = jQuery(this);
                var supplyName = $this.find('.supply-name').text().toLowerCase();
                var supplyDepartment = $this.data('department');
                var supplyType = $this.data('type');
                
                var showBySearch = !search || supplyName.indexOf(search) > -1;
                var showByDepartment = !department || supplyDepartment === department;
                var showByType = !type || supplyType === type;
                
                if (showBySearch && showByDepartment && showByType) {
                    $this.show();
                } else {
                    $this.hide();
                }
            });
        }
        
        function loadNextBatch() {
            var $ = jQuery;
            
            if (currentOffset >= suppliesCount) {
                isLoading = false;
                $('#load-button').prop('disabled', false);
                $('#expand-all, #collapse-all').prop('disabled', false);
                $('#status').text('All supplies loaded successfully.');
                return;
            }
            
            var progress = Math.round((currentOffset / suppliesCount) * 100);
            $('#progress-bar').css('width', progress + '%').text(progress + '%');
            $('#status').html('Loading supplies... (' + currentOffset + ' of ' + suppliesCount + ') <span class="loading"></span>');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'load_supplies_batch',
                    offset: currentOffset,
                    batch_size: batchSize,
                    nonce: '<?php echo wp_create_nonce('supplies_overview_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        loadedCount += response.data.items.length;
                        actualSuppliesCount += response.data.actual_count;
                        releaseSuppliesCount += response.data.release_count;
                        
                        updateSummary();
                        
                        // Append items to container
                        $.each(response.data.items, function(index, item) {
                            var supplyItem = $(
                                '<div class="supply-item" data-department="' + item.department + '" data-type="' + item.type + '">' +
                                    '<div class="supply-header">' +
                                        '<span class="supply-name">' + item.name + '</span>' +
                                        '<span>ID: ' + item.id + '</span>' +
                                    '</div>' +
                                    '<div class="supply-details">' +
                                        '<div class="supply-meta">' +
                                            '<span><strong>Department:</strong> ' + item.department + '</span>' +
                                            '<span><strong>Type:</strong> ' + item.type + '</span>' +
                                            '<span><strong>Section:</strong> ' + item.section + '</span>' +
                                            '<span><strong>Date Added:</strong> ' + item.purchased_date + '</span>' +
                                            '<span><strong>Price Per Unit:</strong> ₱' + item.price_per_unit + '</span>' +
                                        '</div>' +
                                        '<div class="sub-items">' +
                                            '<div class="sub-item-header">Actual Supplies (' + item.actual_supplies.length + ')</div>' +
                                            '<div id="actual-supplies-' + item.id + '"></div>' +
                                        '</div>' +
                                        '<div class="sub-items">' +
                                            '<div class="sub-item-header">Released Supplies (' + item.release_supplies.length + ')</div>' +
                                            '<div id="release-supplies-' + item.id + '"></div>' +
                                        '</div>' +
                                        '<div class="summary">' +
                                            '<div class="summary-item">' +
                                                '<span>Total Actual Supplies:</span>' +
                                                '<span>' + item.total_actual_quantity + '</span>' +
                                            '</div>' +
                                            '<div class="summary-item">' +
                                                '<span>Total Released Supplies:</span>' +
                                                '<span>' + item.total_release_quantity + '</span>' +
                                            '</div>' +
                                            '<div class="summary-item">' +
                                                '<span>Current Balance:</span>' +
                                                '<span class="' + (item.balance > 0 ? 'positive' : (item.balance < 0 ? 'negative' : 'zero')) + '">' + 
                                                    item.balance + 
                                                '</span>' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>'
                            );
                            
                            $('#supplies-container').append(supplyItem);
                            
                            // Add actual supplies
                            $.each(item.actual_supplies, function(i, actual) {
                                var expiryClass = '';
                                
                                // Check for expiration dates
                                if (actual.expiry_date) {
                                    var expDate = new Date(actual.expiry_date);
                                    var today = new Date();
                                    
                                    // Add 6 months to today
                                    var sixMonthsLater = new Date();
                                    sixMonthsLater.setMonth(sixMonthsLater.getMonth() + 6);
                                    
                                    if (expDate < today) {
                                        expiryClass = 'expired-date';
                                    } else if (expDate < sixMonthsLater) {
                                        expiryClass = 'warning-date';
                                    }
                                }
                                
                                var actualItem = $('<div class="sub-item actual-supply">' +
                                    '<div>' + actual.id + ': ' + (actual.lot_number ? 'Lot#: ' + actual.lot_number : '') + 
                                    (actual.expiry_date ? ' <span class="' + expiryClass + '">Exp: ' + actual.expiry_date + '</span>' : '') + '</div>' +
                                    '<div class="date">' + actual.date_added + '</div>' +
                                    '<div class="quantity positive">' + actual.quantity + '</div>' +
                                '</div>');
                                
                                $('#actual-supplies-' + item.id).append(actualItem);
                            });
                            
                            // Add release supplies
                            $.each(item.release_supplies, function(i, release) {
                                var releaseItem = $('<div class="sub-item release-supply">' +
                                    '<div>' + release.id + ': To: ' + release.department + (release.confirmed ? ' (Confirmed)' : ' (Pending)') + '</div>' +
                                    '<div class="date">' + release.release_date + '</div>' +
                                    '<div class="quantity negative">' + release.quantity + '</div>' +
                                '</div>');
                                
                                $('#release-supplies-' + item.id).append(releaseItem);
                            });
                        });
                        
                        // Move to next batch
                        currentOffset += batchSize;
                        setTimeout(loadNextBatch, 500); // Small delay to prevent browser freezing
                    } else {
                        isLoading = false;
                        $('#load-button').prop('disabled', false);
                        $('#status').text('Error: ' + response.data);
                    }
                },
                error: function(xhr, status, error) {
                    isLoading = false;
                    $('#load-button').prop('disabled', false);
                    $('#status').text('AJAX Error: ' + error);
                    console.log(xhr.responseText);
                }
            });
        }
    </script>
</body>
</html>
<?php
// Add the AJAX handler in WordPress
add_action('wp_ajax_load_supplies_batch', 'load_supplies_batch');

function load_supplies_batch() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'supplies_overview_nonce')) {
        wp_send_json_error('Security check failed');
    }
    
    $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
    $batch_size = isset($_POST['batch_size']) ? intval($_POST['batch_size']) : 100;
    
    // Get supplies batch
    $args = array(
        'post_type' => 'supplies',
        'posts_per_page' => $batch_size,
        'offset' => $offset,
        'orderby' => 'title',
        'order' => 'ASC',
        'post_status' => 'publish'
    );
    
    $supplies_query = new WP_Query($args);
    $items = array();
    $actual_count = 0;
    $release_count = 0;
    
    if ($supplies_query->have_posts()) {
        while ($supplies_query->have_posts()) {
            $supplies_query->the_post();
            $supply_id = get_the_ID();
            
            // Get basic supply information
            $supply = array(
                'id' => $supply_id,
                'name' => get_the_title(),
                'department' => get_field('department', $supply_id) ?: 'Unknown',
                'type' => get_field('type', $supply_id) ?: 'Unknown',
                'section' => get_field('section', $supply_id) ?: 'None',
                'purchased_date' => get_field('purchased_date', $supply_id) ?: 'Unknown',
                'price_per_unit' => number_format(get_field('price_per_unit', $supply_id) ?: 0, 2),
                'actual_supplies' => array(),
                'release_supplies' => array(),
                'total_actual_quantity' => 0,
                'total_release_quantity' => 0
            );
            
            // Get related actual supplies
            $actual_args = array(
                'post_type' => 'actualsupplies',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => 'supply_name',
                        'value' => $supply_id,
                        'compare' => '='
                    )
                ),
                'orderby' => 'meta_value',
                'meta_key' => 'date_added',
                'order' => 'DESC'
            );
            
            $actual_query = new WP_Query($actual_args);
            
            if ($actual_query->have_posts()) {
                while ($actual_query->have_posts()) {
                    $actual_query->the_post();
                    $actual_id = get_the_ID();
                    $quantity = floatval(get_field('quantity', $actual_id));
                    
                    $supply['actual_supplies'][] = array(
                        'id' => $actual_id,
                        'quantity' => $quantity,
                        'date_added' => get_field('date_added', $actual_id) ?: 'Unknown',
                        'lot_number' => get_field('lot_number', $actual_id) ?: '',
                        'expiry_date' => get_field('expiry_date', $actual_id) ?: ''
                    );
                    
                    $supply['total_actual_quantity'] += $quantity;
                    $actual_count++;
                }
                wp_reset_postdata();
            }
            
            // Get related release supplies
            $release_args = array(
                'post_type' => 'releasesupplies',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => 'supply_name',
                        'value' => $supply_id,
                        'compare' => '='
                    )
                ),
                'orderby' => 'meta_value',
                'meta_key' => 'release_date',
                'order' => 'DESC'
            );
            
            $release_query = new WP_Query($release_args);
            
            if ($release_query->have_posts()) {
                while ($release_query->have_posts()) {
                    $release_query->the_post();
                    $release_id = get_the_ID();
                    $quantity = floatval(get_field('quantity', $release_id));
                    
                    $supply['release_supplies'][] = array(
                        'id' => $release_id,
                        'quantity' => $quantity,
                        'release_date' => get_field('release_date', $release_id) ?: 'Unknown',
                        'department' => get_field('department', $release_id) ?: 'Unknown',
                        'confirmed' => get_field('confirmed', $release_id) ? true : false
                    );
                    
                    $supply['total_release_quantity'] += $quantity;
                    $release_count++;
                }
                wp_reset_postdata();
            }
            
            // Calculate balance
            $supply['balance'] = $supply['total_actual_quantity'] - $supply['total_release_quantity'];
            
            $items[] = $supply;
        }
        wp_reset_postdata();
    }
    
    wp_send_json_success(array(
        'items' => $items,
        'actual_count' => $actual_count,
        'release_count' => $release_count
    ));
}
?>
