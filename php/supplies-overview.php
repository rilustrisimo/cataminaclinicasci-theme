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
        
        /* Sub-section filter styles */
        .sub-section-filter {
            display: none; /* Hidden by default, shown only when NURSING->ASC is selected */
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
        
        /* Tab Navigation Styles */
        .tab-navigation {
            display: flex;
            border-bottom: 2px solid #ddd;
            margin-bottom: 20px;
            gap: 5px;
        }
        
        .tab-button {
            background: #f5f5f5;
            border: none;
            padding: 12px 24px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            color: #555;
            border-radius: 5px 5px 0 0;
            transition: all 0.3s ease;
            border: 1px solid #ddd;
            border-bottom: none;
            position: relative;
            bottom: -2px;
        }
        
        .tab-button:hover {
            background: #e8e8e8;
            color: #333;
        }
        
        .tab-button.active {
            background: #fff;
            color: #0073aa;
            border-color: #0073aa;
            border-bottom: 2px solid #fff;
            font-weight: 600;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        /* Analytics Specific Styles */
        .analytics-filters {
            background: #f9f9f9;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        
        .analytics-filters .filter-row {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }
        
        .analytics-filters .filter {
            flex: 1;
            min-width: 200px;
        }
        
        .analytics-summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .summary-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .summary-card.actual {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
        }
        
        .summary-card.release {
            background: linear-gradient(135deg, #F44336 0%, #d32f2f 100%);
        }
        
        .summary-card.net {
            background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
        }
        
        .summary-card h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            font-weight: 500;
            opacity: 0.9;
        }
        
        .summary-card .value {
            font-size: 28px;
            font-weight: bold;
            margin: 0;
        }
        
        .summary-card .context {
            font-size: 11px;
            opacity: 0.8;
            margin-top: 5px;
        }
        
        .chart-container {
            background: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: relative;
        }
        
        .chart-container h3 {
            margin-top: 0;
            color: #23282d;
            font-size: 16px;
            border-bottom: 2px solid #0073aa;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .chart-wrapper {
            position: relative;
            height: 400px;
        }
        
        .chart-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }
        
        .analytics-status {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
        
        /* Supplies List Styles */
        .supplies-list {
            max-height: 500px;
            overflow-y: auto;
        }
        
        .supply-list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid #eee;
            transition: background 0.2s;
        }
        
        .supply-list-item:hover {
            background: #f9f9f9;
        }
        
        .supply-list-item:last-child {
            border-bottom: none;
        }
        
        .supply-info {
            flex: 1;
            min-width: 0;
        }
        
        .supply-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .supply-details {
            font-size: 11px;
            color: #666;
        }
        
        .supply-value {
            text-align: right;
            margin-left: 15px;
        }
        
        .supply-value .value {
            font-weight: bold;
            color: #0073aa;
            font-size: 14px;
            white-space: nowrap;
        }
        
        .supply-value .quantity {
            font-size: 11px;
            color: #999;
            margin-top: 2px;
        }
        
        .supply-rank {
            background: #0073aa;
            color: white;
            font-weight: bold;
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 4px;
            margin-right: 12px;
            min-width: 30px;
            text-align: center;
        }
        
        .supply-rank.top-3 {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Supplies Management System</h1>
        
        <!-- Tab Navigation -->
        <div class="tab-navigation">
            <button class="tab-button active" data-tab="overview">Overview</button>
            <button class="tab-button" data-tab="analytics">Analytics</button>
        </div>
        
        <!-- Overview Tab -->
        <div id="overview-tab" class="tab-content active">
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
            <div class="summary-item">
                <span>Total Pending Releases:</span>
                <span id="pending-count">0</span>
            </div>
            <div class="summary-item">
                <span>Total Expired Items:</span>
                <span id="expired-count">0</span>
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
            <div class="filter sub-section-filter" id="sub-section-filter-container">
                <label for="sub-section-filter">Sub-Section:</label>
                <select id="sub-section-filter">
                    <option value="">All Sub-Sections</option>
                    <option value="Nurses Station">Nurses Station</option>
                    <option value="Clean Up Area">Clean Up Area</option>
                    <option value="Dressing Rooms">Dressing Rooms</option>
                    <option value="OR 1">OR 1</option>
                    <option value="OR 2">OR 2</option>
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
            <button id="export-csv" class="button" style="background: #2e7d32;" disabled>Export to CSV</button>
        </div>
        
        <div class="progress-container">
            <div class="progress-bar" id="progress-bar">0%</div>
        </div>
        <div class="status" id="status">Ready to load supplies data...</div>
        
        <div id="supplies-container"></div>
        </div>
        <!-- End Overview Tab -->
        
        <!-- Analytics Tab -->
        <div id="analytics-tab" class="tab-content">
            <div class="analytics-filters">
                <div class="filter-row">
                    <div class="filter">
                        <label for="analytics-start-date">From Date:</label>
                        <input type="date" id="analytics-start-date" class="analytics-filter-input">
                    </div>
                    <div class="filter">
                        <label for="analytics-end-date">To Date:</label>
                        <input type="date" id="analytics-end-date" class="analytics-filter-input">
                    </div>
                    <div class="filter">
                        <label for="analytics-department-filter">Department:</label>
                        <select id="analytics-department-filter" class="analytics-filter-input">
                            <option value="">All Departments</option>
                            <option value="NURSING">NURSING</option>
                            <option value="LABORATORY">LABORATORY</option>
                            <option value="PHARMACY">PHARMACY</option>
                            <option value="HOUSEKEEPING">HOUSEKEEPING</option>
                            <option value="MAINTENANCE">MAINTENANCE</option>
                            <option value="ADMINISTRATION">ADMINISTRATION</option>
                        </select>
                    </div>
                    <div class="filter">
                        <label for="analytics-type-filter">Type:</label>
                        <select id="analytics-type-filter" class="analytics-filter-input">
                            <option value="Supply">Supply</option>
                            <option value="Equipment">Equipment</option>
                            <option value="Reagent">Reagent</option>
                            <option value="Miscellaneous">Miscellaneous</option>
                            <option value="Adjustment">Adjustment</option>
                        </select>
                    </div>
                </div>
                <div style="margin-top: 10px;">
                    <button id="apply-analytics-filters" class="button">Apply Filters</button>
                    <button id="reset-analytics-filters" class="button">Reset to All Records</button>
                </div>
            </div>
            
            <div class="analytics-summary-cards" id="analytics-summary">
                <div class="summary-card actual">
                    <h3>Total Supplies Inventory</h3>
                    <p class="value" id="total-actual-value">₱0.00</p>
                    <p class="context" id="actual-context">Cumulative up to end date</p>
                </div>
                <div class="summary-card release">
                    <h3>Total Released Supplies</h3>
                    <p class="value" id="total-release-value">₱0.00</p>
                    <p class="context" id="release-context">Cumulative up to end date</p>
                </div>
                <div class="summary-card net">
                    <h3>Current Inventory Value</h3>
                    <p class="value" id="net-value">₱0.00</p>
                    <p class="context" id="net-context">Available inventory (matches SOC Report)</p>
                </div>
                <div class="summary-card" id="department-count-card">
                    <h3>Active Departments</h3>
                    <p class="value" id="active-departments">0</p>
                    <p class="context">As of end date</p>
                </div>
            </div>
            
            <div class="chart-container">
                <h3 id="actual-chart-title">Actual Supplies Value Over Time</h3>
                <div class="chart-wrapper">
                    <canvas id="actual-supplies-chart"></canvas>
                    <div class="chart-loading" id="actual-chart-loading" style="display:none;">
                        <span class="loading"></span>
                        <p>Loading chart data...</p>
                    </div>
                </div>
            </div>
            
            <div class="chart-container">
                <h3 id="release-chart-title">Release Supplies Value Over Time</h3>
                <div class="chart-wrapper">
                    <canvas id="release-supplies-chart"></canvas>
                    <div class="chart-loading" id="release-chart-loading" style="display:none;">
                        <span class="loading"></span>
                        <p>Loading chart data...</p>
                    </div>
                </div>
            </div>
            
            <div class="chart-container">
                <h3 id="comparison-chart-title">Combined Comparison</h3>
                <div class="chart-wrapper">
                    <canvas id="comparison-chart"></canvas>
                    <div class="chart-loading" id="comparison-chart-loading" style="display:none;">
                        <span class="loading"></span>
                        <p>Loading chart data...</p>
                    </div>
                </div>
            </div>
            
            <div class="chart-container" id="department-breakdown-container" style="display:none;">
                <h3>Department Breakdown</h3>
                <div class="chart-wrapper">
                    <canvas id="department-breakdown-chart"></canvas>
                </div>
            </div>
            
            <!-- Top Supplies Lists -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="chart-container">
                    <h3>Top Actual Supplies Added (By Value)</h3>
                    <div id="top-actual-supplies-list" class="supplies-list">
                        <p style="text-align: center; color: #999; padding: 20px;">Loading...</p>
                    </div>
                </div>
                
                <div class="chart-container">
                    <h3>Top Released Supplies (By Value)</h3>
                    <div id="top-release-supplies-list" class="supplies-list">
                        <p style="text-align: center; color: #999; padding: 20px;">Loading...</p>
                    </div>
                </div>
            </div>
            
            <div class="analytics-status" id="analytics-status">
                Select a date range and click "Apply Filters" to view analytics data.
            </div>
        </div>
        <!-- End Analytics Tab -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    <script>
        var suppliesCount = <?php echo $supplies_count; ?>;
        var batchSize = 100;
        var currentOffset = 0;
        var isLoading = false;
        var actualSuppliesCount = 0;
        var releaseSuppliesCount = 0;
        var loadedCount = 0;
        var pendingReleasesCount = 0;
        var expiredCount = 0;
        
        // Department to Sections mapping
        var departmentSections = {
            "NURSING": ["Treatment Room (Clinic A)", "Ambulatory Surgery Center (ASC)"],
            "LABORATORY": ["Clinical Chemistry", "Immunology", "Histopathology", "Clinical Microscopy", "Hematology"],
            "PHARMACY": ["Medical Supplies", "Medicines", "Goods"],
            "HOUSEKEEPING": ["Comfort Rooms", "Janitor's Closet", "Autoclave Room"],
            "MAINTENANCE": ["Transport Vehicle", "Septic Vault", "Generator", "Water Tank System", "Solar", "CCTV"]
        };
        
        jQuery(document).ready(function($) {
            // ===== TAB NAVIGATION =====
            $('.tab-button').on('click', function() {
                var tabName = $(this).data('tab');
                
                // Update active button
                $('.tab-button').removeClass('active');
                $(this).addClass('active');
                
                // Update active content
                $('.tab-content').removeClass('active');
                $('#' + tabName + '-tab').addClass('active');
                
                // If switching to analytics, load default data if not already loaded
                if (tabName === 'analytics' && !window.analyticsLoaded) {
                    initializeAnalytics();
                }
            });
            
            // ===== ANALYTICS FUNCTIONALITY =====
            var analyticsCharts = {
                actual: null,
                release: null,
                comparison: null,
                department: null
            };
            
            // Initialize analytics with default date range (from beginning of time to today)
            function initializeAnalytics() {
                var today = new Date();
                // Set start date to a very early date (beginning of records)
                // Using year 2000 as a safe "beginning of time" for this system
                var beginningOfTime = new Date('2000-01-01');
                
                $('#analytics-end-date').val(formatDate(today));
                $('#analytics-start-date').val(formatDate(beginningOfTime));
                $('#analytics-department-filter').val('');
                $('#analytics-type-filter').val('Supply'); // Default to Supply
                
                loadAnalyticsData();
            }
            
            // Format date to YYYY-MM-DD
            function formatDate(date) {
                var year = date.getFullYear();
                var month = String(date.getMonth() + 1).padStart(2, '0');
                var day = String(date.getDate()).padStart(2, '0');
                return year + '-' + month + '-' + day;
            }
            
            // Apply analytics filters
            $('#apply-analytics-filters').on('click', function() {
                loadAnalyticsData();
            });
            
            // Reset analytics filters
            $('#reset-analytics-filters').on('click', function() {
                initializeAnalytics();
            });
            
            // Load analytics data via AJAX
            function loadAnalyticsData() {
                var startDate = $('#analytics-start-date').val();
                var endDate = $('#analytics-end-date').val();
                var department = $('#analytics-department-filter').val();
                var type = $('#analytics-type-filter').val();
                
                console.log('=== ANALYTICS DEBUG ===');
                console.log('Start Date:', startDate);
                console.log('End Date:', endDate);
                console.log('Department:', department);
                console.log('Type:', type);
                console.log('AJAX URL:', '<?php echo admin_url('admin-ajax.php'); ?>');
                
                // Validation
                if (!startDate || !endDate) {
                    alert('Please select both start and end dates');
                    return;
                }
                
                // Show loading state
                $('#analytics-status').html('<span class="loading"></span> Loading analytics data...').show();
                $('#apply-analytics-filters').prop('disabled', true);
                $('.chart-loading').show();
                
                var ajaxData = {
                    action: 'get_analytics_data',
                    start_date: startDate,
                    end_date: endDate,
                    department: department,
                    type: type,
                    nonce: '<?php echo wp_create_nonce('supplies_analytics_nonce'); ?>'
                };
                
                console.log('AJAX Data:', ajaxData);
                
                // Make AJAX request
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: ajaxData,
                    success: function(response) {
                        console.log('AJAX Success Response:', response);
                        $('#apply-analytics-filters').prop('disabled', false);
                        $('.chart-loading').hide();
                        
                        if (response.success) {
                            window.analyticsLoaded = true;
                            renderAnalytics(response.data);
                            $('#analytics-status').hide();
                        } else {
                            console.error('Response Error:', response.data);
                            $('#analytics-status').text('Error: ' + response.data);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('=== AJAX ERROR ===');
                        console.error('Status:', status);
                        console.error('Error:', error);
                        console.error('Response Text:', xhr.responseText);
                        console.error('Status Code:', xhr.status);
                        
                        $('#apply-analytics-filters').prop('disabled', false);
                        $('.chart-loading').hide();
                        $('#analytics-status').text('AJAX Error: ' + error + ' (Status: ' + xhr.status + ')');
                    }
                });
            }
            
            // Render analytics data and charts
            function renderAnalytics(data) {
                var department = $('#analytics-department-filter').val();
                var deptContext = department ? department : 'All Departments';
                
                // Update summary cards
                $('#total-actual-value').text('₱' + formatMoney(data.summary.total_actual_value));
                $('#total-release-value').text('₱' + formatMoney(data.summary.total_release_value));
                $('#net-value').text('₱' + formatMoney(data.summary.net_value));
                $('#active-departments').text(data.summary.active_departments);
                
                $('#actual-context').text(deptContext);
                $('#release-context').text(deptContext);
                
                // Update chart titles
                $('#actual-chart-title').text('Actual Supplies Value Over Time' + (department ? ' (' + department + ')' : ' (All Departments)'));
                $('#release-chart-title').text('Release Supplies Value Over Time' + (department ? ' (' + department + ')' : ' (All Departments)'));
                $('#comparison-chart-title').text('Combined Comparison' + (department ? ' (' + department + ')' : ' (All Departments)'));
                
                // Render charts
                renderActualSuppliesChart(data.actual_supplies);
                renderReleaseSuppliesChart(data.release_supplies);
                renderComparisonChart(data.actual_supplies, data.release_supplies);
                
                // Show/hide department breakdown chart
                if (!department && data.summary.active_departments > 1) {
                    $('#department-breakdown-container').show();
                    renderDepartmentBreakdownChart(data.summary.department_breakdown);
                } else {
                    $('#department-breakdown-container').hide();
                }
                
                // Render top supplies lists
                renderTopSuppliesLists(data.actual_supplies, data.release_supplies);
            }
            
            // Format money with commas
            function formatMoney(value) {
                return parseFloat(value).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }
            
            // Render Actual Supplies Chart
            function renderActualSuppliesChart(data) {
                var ctx = document.getElementById('actual-supplies-chart');
                
                if (analyticsCharts.actual) {
                    analyticsCharts.actual.destroy();
                }
                
                var labels = data.map(item => item.date);
                var values = data.map(item => item.total_value);
                
                analyticsCharts.actual = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Actual Supplies Value (₱)',
                            data: values,
                            borderColor: '#4CAF50',
                            backgroundColor: 'rgba(76, 175, 80, 0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += '₱' + formatMoney(context.parsed.y);
                                        var dataPoint = data[context.dataIndex];
                                        label += ' (' + dataPoint.item_count + ' items)';
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '₱' + formatMoney(value);
                                    }
                                }
                            }
                        }
                    }
                });
            }
            
            // Render Release Supplies Chart
            function renderReleaseSuppliesChart(data) {
                var ctx = document.getElementById('release-supplies-chart');
                
                if (analyticsCharts.release) {
                    analyticsCharts.release.destroy();
                }
                
                var labels = data.map(item => item.date);
                var confirmedValues = data.map(item => item.confirmed_value);
                var pendingValues = data.map(item => item.pending_value);
                
                analyticsCharts.release = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Confirmed Releases (₱)',
                            data: confirmedValues,
                            backgroundColor: '#F44336',
                            stack: 'releases'
                        }, {
                            label: 'Pending Releases (₱)',
                            data: pendingValues,
                            backgroundColor: '#FF9800',
                            stack: 'releases'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += '₱' + formatMoney(context.parsed.y);
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                stacked: true,
                                ticks: {
                                    callback: function(value) {
                                        return '₱' + formatMoney(value);
                                    }
                                }
                            },
                            x: {
                                stacked: true
                            }
                        }
                    }
                });
            }
            
            // Render Comparison Chart
            function renderComparisonChart(actualData, releaseData) {
                var ctx = document.getElementById('comparison-chart');
                
                if (analyticsCharts.comparison) {
                    analyticsCharts.comparison.destroy();
                }
                
                // Merge dates from both datasets
                var allDates = [...new Set([
                    ...actualData.map(item => item.date),
                    ...releaseData.map(item => item.date)
                ])].sort();
                
                // Create data arrays aligned by date
                var actualValues = allDates.map(date => {
                    var item = actualData.find(d => d.date === date);
                    return item ? item.total_value : 0;
                });
                
                var releaseValues = allDates.map(date => {
                    var item = releaseData.find(d => d.date === date);
                    return item ? item.confirmed_value : 0;
                });
                
                // Calculate cumulative net value
                var netValues = [];
                var cumulativeActual = 0;
                var cumulativeRelease = 0;
                
                for (var i = 0; i < allDates.length; i++) {
                    cumulativeActual += actualValues[i];
                    cumulativeRelease += releaseValues[i];
                    netValues.push(cumulativeActual - cumulativeRelease);
                }
                
                analyticsCharts.comparison = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: allDates,
                        datasets: [{
                            label: 'Actual Supplies (₱)',
                            data: actualValues,
                            borderColor: '#4CAF50',
                            backgroundColor: 'rgba(76, 175, 80, 0.1)',
                            yAxisID: 'y',
                            tension: 0.4
                        }, {
                            label: 'Released Supplies (₱)',
                            data: releaseValues,
                            borderColor: '#F44336',
                            backgroundColor: 'rgba(244, 67, 54, 0.1)',
                            yAxisID: 'y',
                            tension: 0.4
                        }, {
                            label: 'Net Value (Cumulative) (₱)',
                            data: netValues,
                            borderColor: '#2196F3',
                            backgroundColor: 'rgba(33, 150, 243, 0.1)',
                            yAxisID: 'y1',
                            borderWidth: 3,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += '₱' + formatMoney(context.parsed.y);
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Daily Value (₱)'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return '₱' + formatMoney(value);
                                    }
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'Cumulative Net Value (₱)'
                                },
                                grid: {
                                    drawOnChartArea: false
                                },
                                ticks: {
                                    callback: function(value) {
                                        return '₱' + formatMoney(value);
                                    }
                                }
                            }
                        }
                    }
                });
            }
            
            // Render Department Breakdown Chart
            function renderDepartmentBreakdownChart(breakdown) {
                var ctx = document.getElementById('department-breakdown-chart');
                
                if (analyticsCharts.department) {
                    analyticsCharts.department.destroy();
                }
                
                var departments = Object.keys(breakdown);
                var actualValues = departments.map(dept => breakdown[dept].actual);
                var releaseValues = departments.map(dept => breakdown[dept].release);
                
                analyticsCharts.department = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: departments,
                        datasets: [{
                            label: 'Actual Supplies (₱)',
                            data: actualValues,
                            backgroundColor: '#4CAF50'
                        }, {
                            label: 'Released Supplies (₱)',
                            data: releaseValues,
                            backgroundColor: '#F44336'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += '₱' + formatMoney(context.parsed.y);
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '₱' + formatMoney(value);
                                    }
                                }
                            }
                        }
                    }
                });
            }
            
            // Render Top Supplies Lists
            function renderTopSuppliesLists(actualData, releaseData) {
                // Aggregate supplies by supply_id and name from actual supplies
                var actualSuppliesMap = {};
                actualData.forEach(function(datePoint) {
                    datePoint.items.forEach(function(item) {
                        var key = item.supply_id;
                        if (!actualSuppliesMap[key]) {
                            actualSuppliesMap[key] = {
                                supply_id: item.supply_id,
                                supply_name: item.supply_name,
                                department: item.department,
                                total_value: 0,
                                total_quantity: 0
                            };
                        }
                        actualSuppliesMap[key].total_value += parseFloat(item.total_price);
                        actualSuppliesMap[key].total_quantity += parseFloat(item.quantity);
                    });
                });
                
                // Convert to array and sort by total value (descending)
                var topActualSupplies = Object.values(actualSuppliesMap)
                    .sort(function(a, b) {
                        return b.total_value - a.total_value;
                    })
                    .slice(0, 20); // Top 20
                
                // Aggregate supplies by supply_id and name from release supplies
                var releaseSuppliesMap = {};
                releaseData.forEach(function(datePoint) {
                    datePoint.items.forEach(function(item) {
                        var key = item.supply_id;
                        if (!releaseSuppliesMap[key]) {
                            releaseSuppliesMap[key] = {
                                supply_id: item.supply_id,
                                supply_name: item.supply_name,
                                department: item.department,
                                total_value: 0,
                                total_quantity: 0,
                                confirmed_count: 0,
                                pending_count: 0
                            };
                        }
                        releaseSuppliesMap[key].total_value += parseFloat(item.total_price);
                        releaseSuppliesMap[key].total_quantity += parseFloat(item.quantity);
                        if (item.confirmed) {
                            releaseSuppliesMap[key].confirmed_count++;
                        } else {
                            releaseSuppliesMap[key].pending_count++;
                        }
                    });
                });
                
                // Convert to array and sort by total value (descending)
                var topReleaseSupplies = Object.values(releaseSuppliesMap)
                    .sort(function(a, b) {
                        return b.total_value - a.total_value;
                    })
                    .slice(0, 20); // Top 20
                
                // Render actual supplies list
                var actualListHtml = '';
                if (topActualSupplies.length > 0) {
                    topActualSupplies.forEach(function(supply, index) {
                        var rankClass = index < 3 ? 'top-3' : '';
                        actualListHtml += `
                            <div class="supply-list-item">
                                <div class="supply-rank ${rankClass}">${index + 1}</div>
                                <div class="supply-info">
                                    <div class="supply-name" title="${supply.supply_name}">${supply.supply_name}</div>
                                    <div class="supply-details">${supply.department} | Qty: ${supply.total_quantity.toFixed(0)}</div>
                                </div>
                                <div class="supply-value">
                                    <div class="value">₱${formatMoney(supply.total_value)}</div>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    actualListHtml = '<p style="text-align: center; color: #999; padding: 20px;">No actual supplies found in this date range</p>';
                }
                $('#top-actual-supplies-list').html(actualListHtml);
                
                // Render release supplies list
                var releaseListHtml = '';
                if (topReleaseSupplies.length > 0) {
                    topReleaseSupplies.forEach(function(supply, index) {
                        var rankClass = index < 3 ? 'top-3' : '';
                        var statusInfo = supply.confirmed_count > 0 && supply.pending_count > 0 
                            ? `${supply.confirmed_count} confirmed, ${supply.pending_count} pending`
                            : supply.confirmed_count > 0 
                            ? `${supply.confirmed_count} confirmed`
                            : `${supply.pending_count} pending`;
                        
                        releaseListHtml += `
                            <div class="supply-list-item">
                                <div class="supply-rank ${rankClass}">${index + 1}</div>
                                <div class="supply-info">
                                    <div class="supply-name" title="${supply.supply_name}">${supply.supply_name}</div>
                                    <div class="supply-details">${supply.department} | Qty: ${supply.total_quantity.toFixed(0)} | ${statusInfo}</div>
                                </div>
                                <div class="supply-value">
                                    <div class="value">₱${formatMoney(supply.total_value)}</div>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    releaseListHtml = '<p style="text-align: center; color: #999; padding: 20px;">No released supplies found in this date range</p>';
                }
                $('#top-release-supplies-list').html(releaseListHtml);
            }
            
            // ===== OVERVIEW TAB FUNCTIONALITY (Existing Code) =====
            
            // Initialize section filter
            $('#department-filter').on('change', function() {
                var department = $(this).val();
                updateSectionFilter(department);
            });
            
            // Initialize sub-section filter
            $('#section-filter').on('change', function() {
                var department = $('#department-filter').val();
                var section = $(this).val();
                updateSubSectionFilter(department, section);
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
                $('#sub-section-filter').val('');
                $('#type-filter').val('');
                $('#search').val('');
                $('#date-filter').val('<?php echo date('Y-m-d'); ?>');
                $('#duplicates-only').prop('checked', false);
                
                // Hide the section and sub-section filters
                updateSectionFilter('');
                updateSubSectionFilter('', '');
                
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
            $('#search, #section-filter, #sub-section-filter').on('input change', function() {
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

            // Export to CSV button functionality
            $('#export-csv').on('click', function() {
                exportTableToCSV();
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
            
            // Reset sub-section filter when department changes
            updateSubSectionFilter(department, '');
        }
        
        // Function to update sub-section filter based on department and section
        function updateSubSectionFilter(department, section) {
            var $subSectionFilter = jQuery('#sub-section-filter');
            var $subSectionContainer = jQuery('#sub-section-filter-container');
            
            // Hide by default
            $subSectionContainer.hide();
            
            // Show sub-section filter only for NURSING department and ASC section
            if (department === 'NURSING' && section === 'Ambulatory Surgery Center (ASC)') {
                $subSectionContainer.show();
            }
            
            // Reset sub-section selection
            $subSectionFilter.val('');
        }
        
        function updateSummary() {
            jQuery('#loaded-count').text(loadedCount);
            jQuery('#actual-count').text(actualSuppliesCount);
            jQuery('#release-count').text(releaseSuppliesCount);
            jQuery('#pending-count').text(pendingReleasesCount);
            jQuery('#expired-count').text(expiredCount);
        }
        
        function filterSupplies() {
            var search = jQuery('#search').val().toLowerCase();
            var department = jQuery('#department-filter').val();
            var type = jQuery('#type-filter').val();
            var section = jQuery('#section-filter').val();
            var subSection = jQuery('#sub-section-filter').val();
            
            jQuery('.supply-row').each(function() {
                var $this = jQuery(this);
                var supplyName = $this.find('.supply-name').text().toLowerCase();
                var supplyDepartment = $this.data('department');
                var supplyType = $this.data('type');
                var supplySection = $this.data('section');
                var supplySubSection = $this.data('sub-section');
                
                var showBySearch = !search || supplyName.indexOf(search) > -1;
                var showByDepartment = !department || supplyDepartment === department;
                var showByType = !type || supplyType === type;
                var showBySection = !section || supplySection === section;
                var showBySubSection = !subSection || supplySubSection === subSection;
                
                // Remove highlighting first
                $this.removeClass('department-section-match');
                
                // Apply highlighting for section matches when department is selected
                if (department && supplyDepartment === department && (!section || supplySection === section)) {
                    $this.addClass('department-section-match');
                }
                
                if (showBySearch && showByDepartment && showByType && showBySection && showBySubSection) {
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
            if ($('#sub-section-filter').val()) {
                filterMsg += ' | Sub-Section: ' + $('#sub-section-filter').val();
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
                // Make sure progress bar shows 100% when complete
                $('#progress-bar').css('width', '100%').text('100%');
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
                    sub_section: $('#sub-section-filter').val(),
                    type: $('#type-filter').val(),
                    until_date: $('#date-filter').val(),
                    duplicates_only: $('#duplicates-only').is(':checked') ? '1' : '0'
                },
                success: function(response) {
                    if (response.success) {
                        loadedCount += response.data.items.length;
                        actualSuppliesCount += response.data.actual_count;
                        releaseSuppliesCount += response.data.release_count;
                        pendingReleasesCount += response.data.pending_count;
                        
                        // Add expired items to the count
                        response.data.items.forEach(function(item) {
                            if (item.expired_quantity) {
                                expiredCount += parseFloat(item.expired_quantity);
                            }
                        });
                        
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
                                                <th>Sub-Section</th>
                                                <th>Price</th>
                                                <th>Added</th>
                                                <th>Actual Qty</th>
                                                <th>Release Qty</th>
                                                <th>Pending Qty</th>
                                                <th>Expired Qty</th>
                                                <th>Balance</th>
                                                <th>Details</th>
                                            </tr>
                                        </thead>
                                        <tbody id="supplies-table-body"></tbody>
                                    </table>
                                </div>
                            `;
                            $('#supplies-container').html(tableHtml);
                            
                            // Initialize global duplicate tracking for multiple batches
                            window.allDuplicateItems = [];
                            window.globalDepartmentNameMap = {};
                        }
                        
                        // Duplicate detection
                        var duplicateItems = [];
                        var departmentNameMap = window.globalDepartmentNameMap || {};
                        
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
                                    
                                    // Also add to global tracking if it's not already there
                                    if (!window.allDuplicateItems.some(d => d.id === departmentNameMap[key].id)) {
                                        window.allDuplicateItems.push(departmentNameMap[key]);
                                    }
                                }
                                
                                duplicateItems.push(item);
                                
                                // Add to global tracking
                                if (!window.allDuplicateItems.some(d => d.id === item.id)) {
                                    window.allDuplicateItems.push(item);
                                }
                            } else {
                                departmentNameMap[key] = item;
                            }
                        });
                        
                        // Save updated department name map to global
                        window.globalDepartmentNameMap = departmentNameMap;
                        
                        // Add each item as a row in the table
                        $.each(response.data.items, function(index, item) {
                            var balanceClass = item.balance > 0 ? 'positive' : (item.balance < 0 ? 'negative' : 'zero');
                            var rowClass = item.isDuplicate ? 'duplicate-row' : '';
                            
                            // Calculate pending release quantity
                            var pendingReleaseQuantity = 0;
                            if (item.release_supplies) {
                                item.release_supplies.forEach(function(release) {
                                    if (!release.confirmed) {
                                        pendingReleaseQuantity += parseFloat(release.quantity);
                                    }
                                });
                            }
                            
                            // Add each item as a row in the table
                            var rowHtml = `
                                <tr class="supply-row ${rowClass}" data-id="${item.id}" data-department="${item.department}" data-type="${item.type}" data-section="${item.section}" data-sub-section="${item.sub_section || ''}">
                                    <td>${item.id}</td>
                                    <td class="supply-name">${item.isDuplicate ? '<span class="duplicate-marker">⚠️</span>' : ''} ${item.name}</td>
                                    <td>${item.department}</td>
                                    <td>${item.type}</td>
                                    <td>${item.section}</td>
                                    <td>${item.sub_section || '-'}</td>
                                    <td>₱${item.price_per_unit}</td>
                                    <td>${item.purchased_date}</td>
                                    <td class="text-right">${item.total_actual_quantity}</td>
                                    <td class="text-right">${item.total_release_quantity}</td>
                                    <td class="text-right" style="color: orange;">${pendingReleaseQuantity > 0 ? pendingReleaseQuantity : '-'}</td>
                                    <td class="text-right" style="color: red;">${item.expired_quantity > 0 ? item.expired_quantity : '-'}</td>
                                    <td class="text-right ${balanceClass}">${item.balance}</td>
                                    <td><button class="view-details-btn" data-id="${item.id}">View</button></td>
                                </tr>
                            `;
                            
                            $('#supplies-table-body').append(rowHtml);
                        });
                        
                        // Update duplicates summary if found
                        if (window.allDuplicateItems && window.allDuplicateItems.length > 0) {
                            // Show the duplicates summary section
                            $('.duplicates-summary').show();
                            
                            // Generate HTML table for all duplicate items found so far
                            var duplicateHtml = '<table class="duplicates-table"><thead><tr><th>ID</th><th>Name</th><th>Department</th><th>Type</th><th>Section</th><th>Sub-Section</th></tr></thead><tbody>';
                            
                            // Use all duplicate items accumulated so far
                            $.each(window.allDuplicateItems, function(index, item) {
                                duplicateHtml += `
                                    <tr>
                                        <td>${item.id}</td>
                                        <td>${item.name}</td>
                                        <td>${item.department}</td>
                                        <td>${item.type}</td>
                                        <td>${item.section}</td>
                                        <td>${item.sub_section || '-'}</td>
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
                            // All done or no results - FIXED: Ensure progress bar shows 100% when complete
                            $('#progress-bar').css('width', '100%').text('100%');
                            
                            isLoading = false;
                            $('#load-button').prop('disabled', false);
                            $('#apply-filters').prop('disabled', false);
                            $('#reset-filters').prop('disabled', false);
                            $('#expand-all, #collapse-all').prop('disabled', false);
                            
                            if (loadedCount === 0) {
                                $('#status').text('No supplies found matching your filters.' + filterMsg);
                                // Disable export button if no results found
                                $('#export-csv').prop('disabled', true);
                            } else {
                                $('#status').text('All matching supplies loaded successfully.' + filterMsg);
                                // Enable export button once data is fully loaded
                                $('#export-csv').prop('disabled', false);
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

                        console.log(response);
                        
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
                                                    ${item.sub_section ? `<tr><td>Sub-Section:</td><td>${item.sub_section}</td></tr>` : ''}
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
                                                        <td>Total Released Supplies (Confirmed):</td>
                                                        <td class="text-right">${item.total_release_quantity}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Pending Releases:</td>
                                                        <td class="text-right" style="color: orange;">${calculatePendingReleases(item.release_supplies)}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Expired Items:</td>
                                                        <td class="text-right" style="color: red;">${item.expired_quantity || 0}</td>
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
        
        // Function to calculate pending releases
        function calculatePendingReleases(releaseSupplies) {
            var pendingCount = 0;
            releaseSupplies.forEach(function(release) {
                if (!release.confirmed) {
                    pendingCount += parseFloat(release.quantity);
                }
            });
            return pendingCount;
        }

        // Export to CSV functionality
        function exportTableToCSV() {
            var $ = jQuery;
            var rows = [];
            var headers = [];

            // Get the headers (excluding the Details column)
            $('.supplies-table thead th').each(function(index, th) {
                if (index < $('.supplies-table thead th').length - 1) { // Skip the last column (Details)
                    headers.push($(th).text().trim());
                }
            });
            rows.push(headers);

            // Get all visible rows from the table (respecting current filters)
            $('.supplies-table tbody tr:visible').each(function() {
                var row = [];
                // Get all cells except the last one (Details column)
                $(this).find('td').each(function(index, td) {
                    if (index < $(this).parent().find('td').length - 1) { // Skip the last column (Details)
                        // For cells with special content (like duplicate markers)
                        if (index === 1 && $(td).find('.duplicate-marker').length > 0) {
                            // Handle duplicate markers - get text and add (Duplicate) suffix
                            var nameText = $(td).text().replace('⚠️', '').trim();
                            row.push('"' + nameText + ' (Duplicate)' + '"');
                        } else {
                            // For normal cells, get text content without HTML
                            var cellText = $(td).text().trim();
                            // Replace any double quotes in the content with two double quotes for CSV
                            row.push('"' + cellText.replace(/"/g, '""') + '"');
                        }
                    }
                });
                rows.push(row);
            });

            // Format rows into CSV content with UTF-8 BOM for Excel compatibility
            var csvContent = '\ufeff' + rows.map(e => e.join(",")).join("\n");
            
            // Create a blob and download link
            var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            
            // Create downloadable link
            var link = document.createElement("a");
            var url = URL.createObjectURL(blob);
            
            // Set the filename with current date
            var now = new Date();
            var filename = 'supplies_export_' + 
                now.getFullYear() + '-' + 
                String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                String(now.getDate()).padStart(2, '0') + '.csv';
            
            // Set up and click the download link
            link.setAttribute("href", url);
            link.setAttribute("download", filename);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>
</html>
<?php
// AJAX handlers are now loaded globally via functions.php
// No need to include them here
?>
