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
        
        /* Checkbox styling */
        .checkbox-label {
            display: flex !important;
            align-items: center;
            cursor: pointer;
        }
        
        .checkbox-label input {
            margin-right: 8px;
            width: auto !important;
        }
        
        .duplicates-filter {
            margin-top: 10px;
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
            <div class="filter"> 
                <label for="date-filter">Data Until:</label>
                <input type="date" id="date-filter" value="<?php echo date('Y-m-d'); ?>">
                <small style="display: block; margin-top: 2px; font-style: italic; color: #666;">Filter actual/release supplies by date</small>
            </div>
            <div class="filter duplicates-filter">
                <label for="duplicates-only" class="checkbox-label">
                    <input type="checkbox" id="duplicates-only">
                    <span>Show Duplicates Only</span>
                </label>
                <small style="display: block; margin-top: 2px; font-style: italic; color: #666;">Show only items with duplicate names in same department</small>
            </div>
        </div>
        
        <!-- Apply Filters Button -->
        <div style="margin: 15px 0;">
            <button id="apply-filters" class="button">Apply Filters</button>
            <button id="reset-filters" class="button">Reset Filters</button>
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
            
            // Apply filters when the button is clicked
            $('#apply-filters').on('click', function() {
                if (isLoading) return;
                
                $(this).prop('disabled', true);
                $('#reset-filters').prop('disabled', true);
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
            
            // Reset all filters
            $('#reset-filters').on('click', function() {
                $('#department-filter').val('');
                $('#section-filter').val('');
                $('#type-filter').val('');
                $('#search').val('');
                $('#date-filter').val('<?php echo date('Y-m-d'); ?>');
                $('#duplicates-only').prop('checked', false);
                
                // Hide the section filter
                updateSectionFilter('');
                
                // If data already loaded, trigger a re-filter
                if (loadedCount > 0) {
                    filterSupplies();
                }
            });
            
            // Load button functionality
            $('#load-button').on('click', function() {
                if (isLoading) return;
                
                $(this).prop('disabled', true);
                $('#apply-filters').prop('disabled', true);
                $('#reset-filters').prop('disabled', true);
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
            
            // Filter and search functionality - now just filters the visible items, doesn't reload
            $('#search, #section-filter').on('input change', function() {
                filterSupplies();
            });
            
            // Set default date to today
            if (!$('#date-filter').val()) {
                $('#date-filter').val('<?php echo date('Y-m-d'); ?>');
            }
            
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
            
            // Before making the AJAX call, update the status to reflect the filters being applied
            var filterMsg = '';
            if ($('#department-filter').val()) {
                filterMsg += ' | Department: ' + $('#department-filter').val();
            }
            if ($('#section-filter').val()) {
                filterMsg += ' | Section: ' + $('#section-filter').val();
            }
            if ($('#type-filter').val()) {
                filterMsg += ' | Type: ' + $('#type-filter').val();
            }
            if ($('#date-filter').val()) {
                filterMsg += ' | Until: ' + $('#date-filter').val();
            }
            if ($('#duplicates-only').is(':checked')) {
                filterMsg += ' | Duplicates Only';
            }
            
            if (currentOffset >= suppliesCount) {
                isLoading = false;
                $('#load-button').prop('disabled', false);
                $('#apply-filters').prop('disabled', false);
                $('#reset-filters').prop('disabled', false);
                $('#expand-all, #collapse-all').prop('disabled', false);
                $('#status').text('All supplies loaded successfully.' + filterMsg);
                return;
            }
            
            var progress = Math.round((currentOffset / suppliesCount) * 100);
            $('#progress-bar').css('width', progress + '%').text(progress + '%');
            $('#status').html('Loading supplies... (' + currentOffset + ' of ' + suppliesCount + ')' + filterMsg + ' <span class="loading"></span>');
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'load_supplies_batch',
                    offset: currentOffset,
                    batch_size: batchSize,
                    nonce: '<?php echo wp_create_nonce('supplies_overview_nonce'); ?>',
                    // Add filters to the AJAX request
                    department: $('#department-filter').val(),
                    section: $('#section-filter').val(),
                    type: $('#type-filter').val(),
                    until_date: $('#date-filter').val(),
                    duplicates_only: $('#duplicates-only').is(':checked') ? '1' : '0'
                },
                success: function(response) {
                    if (response.success) {
                        loadedCount += response.data.items.length;
                        actualSuppliesCount += response.data.actual_count;
                        releaseSuppliesCount += response.data.release_count;
                        
                        updateSummary();
                        
                        // If this is the first batch, update the total count for progress calculation
                        if (currentOffset === 0 && response.data.total_count !== undefined) {
                            suppliesCount = response.data.total_count;
                        }
                        
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
                        
                        // After adding all rows, check if we need to apply the current search filter
                        if ($('#search').val()) {
                            filterSupplies();
                        }
                        
                        // Move to next batch if there are more items to load
                        if (response.data.items.length > 0 && loadedCount < suppliesCount) {
                            currentOffset += batchSize;
                            setTimeout(loadNextBatch, 500);
                        } else {
                            // All done or no results
                            isLoading = false;
                            $('#load-button').prop('disabled', false);
                            $('#apply-filters').prop('disabled', false);
                            $('#reset-filters').prop('disabled', false);
                            $('#expand-all, #collapse-all').prop('disabled', false);
                            
                            if (loadedCount === 0) {
                                $('#status').text('No supplies found matching your filters.' + filterMsg);
                            } else {
                                $('#status').text('All matching supplies loaded successfully.' + filterMsg);
                            }
                        }
                    } else {
                        isLoading = false;
                        $('#load-button').prop('disabled', false);
                        $('#apply-filters').prop('disabled', false);
                        $('#reset-filters').prop('disabled', false);
                        $('#status').text('Error: ' + response.data);
                    }
                },
                error: function(xhr, status, error) {
                    isLoading = false;
                    $('#load-button').prop('disabled', false);
                    $('#apply-filters').prop('disabled', false);
                    $('#reset-filters').prop('disabled', false);
                    $('#status').text('AJAX Error: ' + error);
                    console.log(xhr.responseText);
                }
            });
        }
        
        // Fix view details functionality with proper event delegation
        jQuery(document).ready(function($) {
            // Delegate click event to document so it works with dynamically created buttons
            $(document).on('click', '.view-details-btn', function() {
                var supplyId = $(this).data('id');
                var currentRow = $(this).closest('tr');
                
                // Show loading state on button
                var $button = $(this);
                var originalButtonText = $button.text();
                $button.text('Loading...').prop('disabled', true);
                
                // Fetch details via AJAX to get fresh data
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'get_supply_details',
                        supply_id: supplyId,
                        nonce: '<?php echo wp_create_nonce('supply_details_nonce'); ?>',
                        until_date: $('#date-filter').val()
                    },
                    success: function(response) {
                        $button.text(originalButtonText).prop('disabled', false);
                        
                        if (response.success) {
                            var item = response.data;
                            
                            // Create a modal with the details
                            var modalHtml = `
                                <div class="modal-overlay">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h3>${item.name} (ID: ${item.id})</h3>
                                            <button class="close-modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="details-section">
                                                <h4>Supply Details</h4>
                                                <table class="details-table">
                                                    <tr><td>Department:</td><td>${item.department}</td></tr>
                                                    <tr><td>Type:</td><td>${item.type}</td></tr>
                                                    <tr><td>Section:</td><td>${item.section}</td></tr>
                                                    <tr><td>Date Added:</td><td>${item.purchased_date}</td></tr>
                                                    <tr><td>Price Per Unit:</td><td>₱${item.price_per_unit}</td></tr>
                                                </table>
                                            </div>
                                            
                                            <div class="details-section">
                                                <h4>Actual Supplies (${item.actual_supplies.length})</h4>
                                                <table class="details-table">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Date Added</th>
                                                            <th>Quantity</th>
                                                            <th>Lot #</th>
                                                            <th>Expiry</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>`;
                            
                            if (item.actual_supplies.length > 0) {
                                $.each(item.actual_supplies, function(i, actual) {
                                    var expiryClass = '';
                                    
                                    // Check for expiration dates
                                    if (actual.expiry_date) {
                                        var expDateStr = actual.expiry_date;
                                        // Convert date to consistent format (yyyy-mm-dd)
                                        var expDate = new Date(expDateStr.replace(/(\d+)\/(\d+)\/(\d+)/, '$3-$1-$2'));
                                        var today = new Date();
                                        today.setHours(0,0,0,0);
                                        
                                        // Add 6 months to today
                                        var sixMonthsLater = new Date(today);
                                        sixMonthsLater.setMonth(today.getMonth() + 6);
                                        
                                        if (!isNaN(expDate.getTime())) {
                                            if (expDate < today) {
                                                expiryClass = 'expired-date';
                                            } else if (expDate < sixMonthsLater) {
                                                expiryClass = 'warning-date';
                                            }
                                        }
                                    }
                                    
                                    modalHtml += `
                                        <tr>
                                            <td>${actual.id}</td>
                                            <td>${actual.date_added}</td>
                                            <td class="text-right positive">${actual.quantity}</td>
                                            <td>${actual.lot_number || '-'}</td>
                                            <td class="${expiryClass}">${actual.expiry_date || '-'}</td>
                                        </tr>
                                    `;
                                });
                            } else {
                                modalHtml += '<tr><td colspan="5" class="text-center">No actual supplies found</td></tr>';
                            }
                            
                            modalHtml += `</tbody>
                                                </table>
                                            </div>
                                            
                                            <div class="details-section">
                                                <h4>Released Supplies (${item.release_supplies.length})</h4>
                                                <table class="details-table">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Release Date</th>
                                                            <th>Department</th>
                                                            <th>Quantity</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>`;
                            
                            if (item.release_supplies.length > 0) {
                                $.each(item.release_supplies, function(i, release) {
                                    modalHtml += `
                                        <tr>
                                            <td>${release.id}</td>
                                            <td>${release.release_date}</td>
                                            <td>${release.department}</td>
                                            <td class="text-right negative">${release.quantity}</td>
                                            <td>${release.confirmed ? '<span class="status-confirmed">Confirmed</span>' : '<span class="status-pending">Pending</span>'}</td>
                                        </tr>
                                    `;
                                });
                            } else {
                                modalHtml += '<tr><td colspan="5" class="text-center">No release supplies found</td></tr>';
                            }
                            
                            modalHtml += `</tbody>
                                                </table>
                                            </div>
                                            
                                            <div class="details-section">
                                                <h4>Summary</h4>
                                                <table class="details-table">
                                                    <tr>
                                                        <td>Total Actual Supplies:</td>
                                                        <td class="text-right">${item.total_actual_quantity}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Total Released Supplies:</td>
                                                        <td class="text-right">${item.total_release_quantity}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Current Balance:</td>
                                                        <td class="text-right ${item.balance > 0 ? 'positive' : (item.balance < 0 ? 'negative' : 'zero')}">
                                                            ${item.balance}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            
                            // Remove any existing modals
                            $('.modal-overlay').remove();
                            
                            // Append and show modal
                            $('body').append(modalHtml);
                            
                            // Add event handlers for the modal
                            $('.close-modal, .modal-overlay').on('click', function(e) {
                                if (e.target === this) {
                                    $('.modal-overlay').remove();
                                }
                            });
                            
                            // Close modal with ESC key
                            $(document).on('keydown.modal', function(e) {
                                if (e.keyCode === 27) { // ESC key
                                    $('.modal-overlay').remove();
                                    $(document).off('keydown.modal');
                                }
                            });
                        } else {
                            alert('Error loading supply details. Please try again.');
                        }
                    },
                    error: function() {
                        $button.text(originalButtonText).prop('disabled', false);
                        alert('Error connecting to the server. Please try again.');
                    }
                });
            });
        });
    </script>
</body>
</html>
<?php
// Instead of dynamically creating the file, we'll just load the existing one
// The file should be committed to Git and deployed properly

// Create a function to register our AJAX handler through the WordPress init hook
function register_supplies_ajax_handler() {
    require_once(dirname(__FILE__) . '/supplies-ajax-handler.php');
}

// Add this function to WordPress's init hook
add_action('init', 'register_supplies_ajax_handler');

// For immediate testing, include the handler file here too
require_once(dirname(__FILE__) . '/supplies-ajax-handler.php');
?>
