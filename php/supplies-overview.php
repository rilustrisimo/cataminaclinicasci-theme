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
            line-height: 1.4;
            color: #333;
            background: #f5f5f5;
            padding: 20px;
            margin: 0;
            font-size: 13px;
        }
        .container {
            max-width: 98%;
            margin: 0 auto;
            background: #fff;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h1 {
            color: #23282d;
            margin-top: 0;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            font-size: 1.5em;
        }
        h3 {
            font-size: 1.2em;
            margin-bottom: 10px;
        }
        h4 {
            font-size: 1.1em;
            margin: 10px 0;
        }
        /* Progress bar styles */
        .progress-container {
            margin: 15px 0;
            background: #f1f1f1;
            border-radius: 4px;
            height: 20px;
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
            font-size: 11px;
            font-weight: bold;
        }
        .status {
            margin-bottom: 15px;
            font-size: 13px;
        }
        
        /* Table styles */
        .table-container {
            overflow-x: auto;
            margin-bottom: 15px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 12px;
        }
        th, td {
            padding: 5px 8px;
            text-align: left;
            border: 1px solid #ddd;
            vertical-align: middle;
            white-space: nowrap;
        }
        th {
            background-color: #f8f8f8;
            font-weight: bold;
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 1px 1px rgba(0,0,0,0.1);
        }
        .supplies-table {
            margin-bottom: 20px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        
        /* Colors for quantity and status */
        .positive {
            color: green;
            font-weight: bold;
        }
        .negative {
            color: red;
            font-weight: bold;
        }
        .zero {
            color: #888;
        }
        .status-confirmed {
            color: green;
        }
        .status-pending {
            color: orange;
        }
        .view-details-btn {
            background: #0073aa;
            color: #fff;
            border: none;
            padding: 2px 6px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 11px;
        }
        .view-details-btn:hover {
            background: #005d8c;
        }
        
        /* Button styles */
        .buttons {
            margin-top: 15px;
            text-align: center;
        }
        .button {
            background: #0073aa;
            color: #fff;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            margin: 0 5px;
        }
        .button:hover {
            background: #005d8c;
        }
        .button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        /* Filter styles */
        .filters {
            margin: 15px 0;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .filter {
            flex: 1;
            min-width: 180px;
        }
        .filter label {
            display: block;
            margin-bottom: 3px;
            font-weight: bold;
            font-size: 12px;
        }
        .filter select, .filter input {
            width: 100%;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 12px;
        }
        
        /* Loading animation */
        .loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-left: 8px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Duplicate marking */
        .duplicate-row {
            background-color: #fff8e1;
        }
        .duplicate-marker {
            color: #f57f17;
            font-size: 11px;
            margin-right: 3px;
        }
        .duplicates-summary {
            background-color: #fff8e1;
            border-left: 4px solid #f57f17;
            padding: 10px;
            margin-bottom: 15px;
        }
        .duplicates-table {
            margin-top: 10px;
            font-size: 11px;
        }
        
        /* Modal styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: #fff;
            width: 90%;
            max-width: 900px;
            max-height: 90vh;
            border-radius: 5px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            overflow-y: auto;
        }
        .modal-header {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-header h3 {
            margin: 0;
        }
        .close-modal {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #777;
        }
        .modal-body {
            padding: 15px;
        }
        .details-section {
            margin-bottom: 20px;
        }
        .details-table {
            width: 100%;
            margin-bottom: 10px;
        }
        
        /* Expiration date styling */
        .expired-date {
            color: red;
            font-weight: bold;
        }
        .warning-date {
            color: orange;
            font-weight: bold;
        }
        
        /* Summary styling */
        .summary {
            background: #f9f9f9;
            padding: 10px;
            margin-bottom: 15px;
            border-left: 3px solid #0073aa;
            font-size: 12px;
        }
        .summary-item {
            margin-bottom: 3px;
            display: flex;
            justify-content: space-between;
        }
        .summary-item span:first-child {
            font-weight: bold;
        }
        
        /* Section filter styles */
        .section-filter {
            display: none; /* Hidden by default, shown only when a relevant department is selected */
        }
        
        /* Department-specific section highlight */
        .department-section-match {
            background-color: #e8f5e9;
        }
        
        /* Hidden sections */
        .hidden-section-row {
            display: none;
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
            <div class="filter section-filter" id="section-filter-container">
                <label for="section-filter">Section:</label>
                <select id="section-filter">
                    <option value="">All Sections</option>
                    <!-- Section options will be populated dynamically based on department -->
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
        
        // Department to Sections mapping
        var departmentSections = {
            "NURSING": ["Treatment Room (Clinic A)", "Ambulatory Surgery Center (ASC)"],
            "LABORATORY": ["Clinical Chemistry", "Immunology", "Histopathology", "Clinical Microscopy", "Hematology"],
            "PHARMACY": ["Medical Supplies", "Medicines", "Goods"],
            "HOUSEKEEPING": ["Comfort Rooms", "Janitor's Closet", "Autoclave Room"],
            "MAINTENANCE": ["Transport Vehicle", "Septic Vault", "Generator", "Water Tank System", "Solar", "CCTV"]
        };
        
        jQuery(document).ready(function($) {
            // Initialize section filter
            $('#department-filter').on('change', function() {
                var department = $(this).val();
                updateSectionFilter(department);
            });
            
            // Handle both department and section filtering
            $('#section-filter').on('change', function() {
                filterSupplies();
            });
            
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
            $('#search, #department-filter, #type-filter, #section-filter').on('input change', function() {
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
        
        // Function to update section filter based on department
        function updateSectionFilter(department) {
            var $sectionFilter = jQuery('#section-filter');
            var $sectionContainer = jQuery('#section-filter-container');
            
            // Clear existing options except the first one
            $sectionFilter.find('option:not(:first)').remove();
            
            // Hide by default
            $sectionContainer.hide();
            
            // If department is in our mapping, show the filter and add relevant sections
            if (department && departmentSections[department]) {
                var sections = departmentSections[department];
                
                // Add section options
                sections.forEach(function(section) {
                    $sectionFilter.append('<option value="' + section + '">' + section + '</option>');
                });
                
                // Show the section filter
                $sectionContainer.show();
            }
            
            // Reset section selection
            $sectionFilter.val('');
        }
        
        function updateSummary() {
            jQuery('#loaded-count').text(loadedCount);
            jQuery('#actual-count').text(actualSuppliesCount);
            jQuery('#release-count').text(releaseSuppliesCount);
        }
        
        function filterSupplies() {
            var search = jQuery('#search').val().toLowerCase();
            var department = jQuery('#department-filter').val();
            var type = jQuery('#type-filter').val();
            var section = jQuery('#section-filter').val();
            
            jQuery('.supply-row').each(function() {
                var $this = jQuery(this);
                var supplyName = $this.find('.supply-name').text().toLowerCase();
                var supplyDepartment = $this.data('department');
                var supplyType = $this.data('type');
                var supplySection = $this.data('section');
                
                var showBySearch = !search || supplyName.indexOf(search) > -1;
                var showByDepartment = !department || supplyDepartment === department;
                var showByType = !type || supplyType === type;
                var showBySection = !section || supplySection === section;
                
                // Remove highlighting first
                $this.removeClass('department-section-match');
                
                // Apply highlighting for section matches when department is selected
                if (department && supplyDepartment === department && (!section || supplySection === section)) {
                    $this.addClass('department-section-match');
                }
                
                if (showBySearch && showByDepartment && showByType && showBySection) {
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
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
            
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
                        
                        // Check for first batch to create table structure
                        if (currentOffset === 0) {
                            // Create the table structure
                            var tableHtml = `
                                <div class="table-container">
                                    <div class="duplicates-summary" style="display: none;">
                                        <h3>Duplicate Supplies Found</h3>
                                        <div class="duplicates-list"></div>
                                    </div>
                                    <table class="supplies-table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Department</th>
                                                <th>Type</th>
                                                <th>Section</th>
                                                <th>Price</th>
                                                <th>Added</th>
                                                <th>Actual Qty</th>
                                                <th>Release Qty</th>
                                                <th>Balance</th>
                                                <th>Details</th>
                                            </tr>
                                        </thead>
                                        <tbody id="supplies-table-body"></tbody>
                                    </table>
                                </div>
                            `;
                            $('#supplies-container').html(tableHtml);
                        }
                        
                        // Duplicate detection
                        var duplicateItems = [];
                        var departmentNameMap = {};
                        
                        $.each(response.data.items, function(index, item) {
                            var nameLower = item.name.toLowerCase();
                            var key = item.department + '|' + nameLower;
                            
                            if (departmentNameMap[key]) {
                                // Mark current item as duplicate
                                item.isDuplicate = true;
                                
                                // Mark the first occurrence as duplicate
                                if (!departmentNameMap[key].isDuplicate) {
                                    departmentNameMap[key].isDuplicate = true;
                                    duplicateItems.push(departmentNameMap[key]);
                                }
                                
                                duplicateItems.push(item);
                            } else {
                                departmentNameMap[key] = item;
                            }
                        });
                        
                        // Add each item as a row in the table
                        $.each(response.data.items, function(index, item) {
                            var balanceClass = item.balance > 0 ? 'positive' : (item.balance < 0 ? 'negative' : 'zero');
                            var rowClass = item.isDuplicate ? 'duplicate-row' : '';
                            
                            // Add each item as a row in the table
                            var rowHtml = `
                                <tr class="supply-row ${rowClass}" data-id="${item.id}" data-department="${item.department}" data-type="${item.type}" data-section="${item.section}">
                                    <td>${item.id}</td>
                                    <td class="supply-name">${item.isDuplicate ? '<span class="duplicate-marker">⚠️</span>' : ''} ${item.name}</td>
                                    <td>${item.department}</td>
                                    <td>${item.type}</td>
                                    <td>${item.section}</td>
                                    <td>₱${item.price_per_unit}</td>
                                    <td>${item.purchased_date}</td>
                                    <td class="text-right">${item.total_actual_quantity}</td>
                                    <td class="text-right">${item.total_release_quantity}</td>
                                    <td class="text-right ${balanceClass}">${item.balance}</td>
                                    <td><button class="view-details-btn" data-id="${item.id}">View</button></td>
                                </tr>
                            `;
                            
                            $('#supplies-table-body').append(rowHtml);
                        });
                        
                        // Update duplicates summary if found
                        if (duplicateItems.length > 0 && currentOffset === 0) {
                            $('.duplicates-summary').show();
                            var duplicateHtml = '<table class="duplicates-table"><thead><tr><th>ID</th><th>Name</th><th>Department</th><th>Type</th><th>Section</th></tr></thead><tbody>';
                            
                            $.each(duplicateItems, function(index, item) {
                                duplicateHtml += `
                                    <tr>
                                        <td>${item.id}</td>
                                        <td>${item.name}</td>
                                        <td>${item.department}</td>
                                        <td>${item.type}</td>
                                        <td>${item.section}</td>
                                    </tr>
                                `;
                            });
                            
                            duplicateHtml += '</tbody></table>';
                            $('.duplicates-list').html(duplicateHtml);
                        }
                        
                        // After adding all rows, check if we need to apply the current filter
                        if ($('#department-filter').val() || $('#section-filter').val() || $('#type-filter').val() || $('#search').val()) {
                            filterSupplies();
                        }
                        
                        // Move to next batch
                        currentOffset += batchSize;
                        setTimeout(loadNextBatch, 500);
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
// The issue is that the AJAX handler is being defined in the same file that outputs HTML
// This causes WordPress to not recognize the function when the AJAX request is made
// Solution: Move the AJAX handler to a separate file or add it directly to functions.php

// Instead of adding the action here, include it in a file that's loaded on admin-ajax.php requests
// Remove this line: add_action('wp_ajax_load_supplies_batch', 'load_supplies_batch');

// Create and load a separate file for the AJAX handler
$ajax_handler_path = dirname(__FILE__) . '/supplies-ajax-handler.php';
file_put_contents($ajax_handler_path, '<?php
// AJAX handler for supplies overview
add_action("wp_ajax_load_supplies_batch", "load_supplies_batch");

function load_supplies_batch() {
    // Verify nonce
    if (!isset($_POST["nonce"]) || !wp_verify_nonce($_POST["nonce"], "supplies_overview_nonce")) {
        wp_send_json_error("Security check failed");
    }
    
    $offset = isset($_POST["offset"]) ? intval($_POST["offset"]) : 0;
    $batch_size = isset($_POST["batch_size"]) ? intval($_POST["batch_size"]) : 100;
    
    // Get supplies batch
    $args = array(
        "post_type" => "supplies",
        "posts_per_page" => $batch_size,
        "offset" => $offset,
        "orderby" => "title",
        "order" => "ASC",
        "post_status" => "publish"
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
                "id" => $supply_id,
                "name" => get_the_title(),
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
            
            // Get related actual supplies
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
            
            $actual_query = new WP_Query($actual_args);
            
            if ($actual_query->have_posts()) {
                while ($actual_query->have_posts()) {
                    $actual_query->the_post();
                    $actual_id = get_the_ID();
                    $quantity = floatval(get_field("quantity", $actual_id));
                    
                    $supply["actual_supplies"][] = array(
                        "id" => $actual_id,
                        "quantity" => $quantity,
                        "date_added" => get_field("date_added", $actual_id) ?: "Unknown",
                        "lot_number" => get_field("lot_number", $actual_id) ?: "",
                        "expiry_date" => get_field("expiry_date", $actual_id) ?: ""
                    );
                    
                    $supply["total_actual_quantity"] += $quantity;
                    $actual_count++;
                }
                wp_reset_postdata();
            }
            
            // Get related release supplies
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
            
            $release_query = new WP_Query($release_args);
            
            if ($release_query->have_posts()) {
                while ($release_query->have_posts()) {
                    $release_query->the_post();
                    $release_id = get_the_ID();
                    $quantity = floatval(get_field("quantity", $release_id));
                    
                    $supply["release_supplies"][] = array(
                        "id" => $release_id,
                        "quantity" => $quantity,
                        "release_date" => get_field("release_date", $release_id) ?: "Unknown",
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
            
            $items[] = $supply;
        }
        wp_reset_postdata();
    }
    
    wp_send_json_success(array(
        "items" => $items,
        "actual_count" => $actual_count,
        "release_count" => $release_count
    ));
}');

// Create a function to register our AJAX handler through the WordPress admin_init hook
function register_supplies_ajax_handler() {
    require_once(dirname(__FILE__) . '/supplies-ajax-handler.php');
}

// Add this function to WordPress's init hook
add_action('init', 'register_supplies_ajax_handler');

// For immediate testing, include the handler file here too
require_once(dirname(__FILE__) . '/supplies-ajax-handler.php');

// This comment line indicates we're removing the original function from this file
// Original function load_supplies_batch() removed to prevent duplication
?>
