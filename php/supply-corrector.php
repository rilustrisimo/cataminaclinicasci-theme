<?php
/**
 * Supply Count Corrector
 * 
 * Tool for correcting supply counts by comparing CSV data with existing records
 */

// Load WordPress environment
require_once(dirname(__FILE__) . '/../../../../wp-load.php');

// Security check - only allow admin users
if (!current_user_can('manage_options')) {
    wp_die('Access denied. You must be an administrator to view this page.');
}

// Verify the nonce for additional security
if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'supply_corrector_access')) {
    wp_die('Security check failed. Please access this tool through the WordPress admin menu.');
}

// Get the Theme class
require_once(dirname(__FILE__) . '/class-main.php');
$theme = new Theme();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supply Count Corrector</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            line-height: 1.4;
            color: #333;
            background: #f5f5f5;
            padding: 20px;
            margin: 0;
            font-size: 14px;
        }
        .container {
            max-width: 100%;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h1 {
            margin: 0 0 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            color: #23282d;
            font-size: 24px;
        }
        .step-container {
            margin-bottom: 30px;
            padding: 20px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            position: relative;
        }
        .step-header {
            margin: -20px -20px 20px;
            padding: 15px 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #ddd;
            border-radius: 4px 4px 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .step-header h2 {
            margin: 0;
            font-size: 18px;
            color: #23282d;
        }
        .step-number {
            display: inline-block;
            width: 28px;
            height: 28px;
            background: #0073aa;
            color: #fff;
            border-radius: 50%;
            text-align: center;
            line-height: 28px;
            margin-right: 10px;
            font-weight: bold;
        }
        .step-content {
            position: relative;
        }
        .upload-zone {
            border: 2px dashed #ccc;
            padding: 30px;
            text-align: center;
            background: #fafafa;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        .upload-zone:hover {
            border-color: #0073aa;
            background: #f0f0f0;
        }
        .upload-zone.dragover {
            border-color: #0073aa;
            background: #e3f2fd;
        }
        .csv-info {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        .csv-info code {
            display: block;
            padding: 10px;
            margin: 10px 0;
            background: #f1f1f1;
            border-radius: 3px;
            font-family: monospace;
            word-break: break-all;
        }
        .progress-container {
            margin: 20px 0;
        }
        .progress-bar {
            height: 10px;
            background: #f1f1f1;
            border-radius: 5px;
            overflow: hidden;
            margin-top: 10px;
        }
        .progress-fill {
            height: 100%;
            background: #0073aa;
            width: 0;
            transition: width 0.3s ease;
        }
        .status-message {
            margin: 15px 0;
            padding: 12px;
            border-radius: 4px;
        }
        .status-message.error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        .status-message.success {
            background: #efe;
            color: #3c3;
            border: 1px solid #cfc;
        }
        .status-message.info {
            background: #e6f2fd;
            color: #0073aa;
            border: 1px solid #cce5ff;
        }
        .match-results {
            margin-top: 20px;
            max-height: 600px;
            overflow-y: auto;
            padding-right: 10px;
            scrollbar-width: thin;
        }
        .match-item {
            padding: 12px;
            border: 1px solid #ddd;
            margin-bottom: 8px;
            border-radius: 4px;
            background: #fff;
            transition: all 0.2s ease;
            font-size: 13px;
        }
        .match-item:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .match-item.no-matches {
            border-left: 4px solid #dc3545;
        }
        .match-item.has-matches {
            border-left: 4px solid #28a745;
        }
        .match-item.single-match {
            border-left: 4px solid #0073aa;
            background-color: #f8fcff;
        }
        .match-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .match-item-header h3 {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
        }
        .match-item-details {
            font-size: 12px;
            color: #666;
            margin-top: 4px;
        }
        .match-selected-info {
            background-color: #e3f2fd;
            padding: 6px;
            border-radius: 4px;
            margin-top: 6px;
            border: 1px solid #0073aa;
            font-size: 12px;
        }
        .match-options {
            margin-top: 10px;
            padding: 0;
            background: #f8f9fa;
            border-radius: 4px;
            font-size: 12px;
        }
        .match-options-header {
            font-size: 12px;
            font-weight: 600;
            margin: 5px 0;
        }
        .match-option {
            padding: 8px;
            margin: 6px;
            border: 1px solid #ddd;
            border-radius: 3px;
            cursor: pointer;
            background: #fff;
            transition: all 0.2s ease;
            font-size: 12px;
            display: inline-block;
            width: calc(33.333% - 14px);
            vertical-align: top;
            box-sizing: border-box;
        }
        .match-option:hover {
            background: #f0f0f0;
        }
        .match-option.selected {
            background: #e3f2fd;
            border-color: #0073aa;
        }
        .match-option strong {
            color: #0073aa;
            display: block;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .compact-match-details {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 5px;
        }
        .compact-match-detail {
            background: #f1f1f1;
            border-radius: 4px;
            padding: 1px 5px;
            font-size: 11px;
            white-space: nowrap;
        }
        .discrepancy-item {
            padding: 16px;
            border: 1px solid #ddd;
            margin-bottom: 15px;
            border-radius: 4px;
            background: #fff;
            transition: all 0.2s ease;
            display: flex;
            flex-wrap: wrap;
        }
        .discrepancy-item:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .discrepancy-high {
            border-left: 4px solid #dc3545;
        }
        .discrepancy-low {
            border-left: 4px solid #ffc107;
        }
        .discrepancy-match {
            border-left: 4px solid #28a745;
        }
        .discrepancy-info {
            flex: 2;
            min-width: 250px;
        }
        .discrepancy-counts {
            flex: 1;
            min-width: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin-left: 15px;
        }
        .discrepancy-details {
            flex-basis: 100%;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            line-height: 1.4;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            background: #0073aa;
            color: #fff;
            border: none;
            transition: all 0.3s ease;
            margin-right: 10px;
        }
        .button:hover {
            background: #005177;
        }
        .button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .button.secondary {
            background: #f8f9fa;
            color: #23282d;
            border: 1px solid #ddd;
        }
        .button.secondary:hover {
            background: #e2e6ea;
        }
        .button-group {
            margin-top: 20px;
            display: flex;
        }
        .hidden {
            display: none !important;
        }
        .loading {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }
        .loading::after {
            content: '';
            width: 30px;
            height: 30px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #0073aa;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: bold;
            margin-left: 6px;
        }
        .badge.success {
            background: #28a745;
            color: #fff;
        }
        .badge.warning {
            background: #ffc107;
            color: #212529;
        }
        .badge.danger {
            background: #dc3545;
            color: #fff;
        }
        .match-summary {
            display: flex;
            margin-bottom: 20px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
        }
        .match-stat {
            flex: 1;
            text-align: center;
            padding: 10px;
            border-right: 1px solid #eee;
        }
        .match-stat:last-child {
            border-right: none;
        }
        .match-stat-value {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #0073aa;
        }
        .match-stat-label {
            font-size: 12px;
            text-transform: uppercase;
            color: #666;
        }
        .discrepancy-count {
            font-size: 20px;
            font-weight: bold;
            margin: 5px 0;
        }
        .discrepancy-count.high {
            color: #dc3545;
        }
        .discrepancy-count.low {
            color: #ffc107;
        }
        .discrepancy-count.match {
            color: #28a745;
        }
        .discrepancy-percentage {
            font-size: 14px;
            color: #666;
        }
        .discrepancy-summary {
            display: flex;
            margin-bottom: 20px;
        }
        .discrepancy-stat {
            flex: 1;
            padding: 15px;
            text-align: center;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-right: 10px;
        }
        .discrepancy-stat:last-child {
            margin-right: 0;
        }
        .discrepancy-stat-value {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .discrepancy-stat-label {
            font-size: 12px;
            text-transform: uppercase;
            color: #666;
        }
        .update-controls {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
        }
        .sticky-header {
            position: sticky;
            top: 0;
            background: #f8f9fa;
            z-index: 5;
            padding: 10px;
            margin: -10px -10px 15px;
            border-bottom: 1px solid #ddd;
            border-radius: 4px 4px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .filters {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-left: auto;
        }
        .filter-control {
            display: flex;
            align-items: center;
        }
        .filter-control label {
            margin-right: 8px;
            font-weight: 600;
            font-size: 13px;
        }
        .filter-control select {
            padding: 6px 30px 6px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #fff;
            appearance: none;
            background-image: url("data:image/svg+xml;utf8,<svg fill='black' height='24' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/><path d='M0 0h24v24H0z' fill='none'/></svg>");
            background-repeat: no-repeat;
            background-position: right 5px top 50%;
            background-size: 20px;
        }
        .no-matches-message {
            font-size: 12px;
            padding: 5px 0;
            color: #dc3545;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @media screen and (max-width: 1200px) {
            .match-option {
                width: calc(50% - 14px);
            }
        }
        @media screen and (max-width: 768px) {
            .discrepancy-item {
                flex-direction: column;
            }
            .discrepancy-counts {
                margin: 15px 0 0;
            }
            .match-summary, .discrepancy-summary {
                flex-direction: column;
                gap: 10px;
            }
            .match-stat, .discrepancy-stat {
                border-right: none;
                border-bottom: 1px solid #eee;
                padding-bottom: 15px;
                margin-bottom: 15px;
            }
            .match-stat:last-child, .discrepancy-stat:last-child {
                border-bottom: none;
                margin-bottom: 0;
            }
            .button-group {
                flex-direction: column;
                gap: 10px;
            }
            .button {
                width: 100%;
                margin-right: 0;
            }
            .match-option {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Supply Count Corrector</h1>

        <!-- Step 1: CSV Upload -->
        <div class="step-container" id="step-1">
            <div class="step-header">
                <div class="d-flex align-items-center">
                    <div class="step-number">1</div>
                    <h2>Upload CSV File</h2>
                </div>
            </div>
            <div class="step-content">
                <div class="upload-zone" id="upload-zone">
                    <input type="file" id="csv-file" accept=".csv" style="display: none;">
                    <div>
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#0073aa" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                    </div>
                    <p><strong>Drop your CSV file here or click to browse</strong></p>
                    <p>File must be in CSV format with the required headers</p>
                </div>
                
                <div class="csv-info">
                    <p><strong>Required CSV Headers:</strong></p>
                    <code>supply_name,actual_count,expiry_date,date_added,serial,states__status,lot_number</code>
                    <p><strong>Example Data:</strong></p>
                    <code>Surgical Gloves (Medium),500,2025-12-31,2023-05-20,SG2023-001,active,LOT123456</code>
                    <p><a href="#" id="download-template" class="button secondary">Download Template</a></p>
                </div>
                
                <div class="progress-container hidden">
                    <div class="progress-label">Uploading... <span class="progress-percent">0%</span></div>
                    <div class="progress-bar">
                        <div class="progress-fill"></div>
                    </div>
                </div>
                
                <div class="status-message hidden"></div>
            </div>
        </div>

        <!-- Step 2: Match Records -->
        <div class="step-container hidden" id="step-2">
            <div class="step-header">
                <div class="d-flex align-items-center">
                    <div class="step-number">2</div>
                    <h2>Match Records</h2>
                </div>
            </div>
            <div class="step-content">
                <p class="status-message info">We'll now try to match each item in your CSV file with existing supplies in the database. For each item with matches, please select the correct match.</p>
                
                <div class="match-summary">
                    <div class="match-stat">
                        <div class="match-stat-value" id="total-records">0</div>
                        <div class="match-stat-label">Total Records</div>
                    </div>
                    <div class="match-stat">
                        <div class="match-stat-value" id="matched-records">0</div>
                        <div class="match-stat-label">Records with Matches</div>
                    </div>
                    <div class="match-stat">
                        <div class="match-stat-value" id="unmatched-records">0</div>
                        <div class="match-stat-label">Records without Matches</div>
                    </div>
                    <div class="match-stat">
                        <div class="match-stat-value" id="selected-matches">0</div>
                        <div class="match-stat-label">Selected Matches</div>
                    </div>
                </div>
                
                <div class="sticky-header">
                    <div>
                        <h3 style="margin:0">CSV Records</h3>
                    </div>
                    <div class="filters">
                        <div class="filter-control">
                            <label for="match-filter">(<span id="filter-count"></span>) Show:</label>
                            <select id="match-filter">
                                <option value="all">All Records</option>
                                <option value="matched">With Matches</option>
                                <option value="unmatched">Without Matches</option>
                                <option value="selected">Selected Only</option>
                                <option value="unselected">Unselected Only</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="match-results"></div>
                
                <div class="button-group">
                    <div class="allow-partial-container" style="margin-bottom: 15px; display: flex; align-items: center;">
                        <input type="checkbox" id="allow-partial-matches" style="margin-right: 8px;">
                        <label for="allow-partial-matches">Allow continuing with only selected matches (items without matches will be skipped)</label>
                    </div>
                    <button id="export-matches-csv" class="button secondary">Export to CSV</button>
                    <button id="confirm-matches" class="button" disabled>Confirm Matches & Continue</button>
                    <button id="reset-step-2" class="button secondary">Reset & Start Over</button>
                </div>
            </div>
        </div>

        <!-- Step 3: Review Discrepancies -->
        <div class="step-container hidden" id="step-3">
            <div class="step-header">
                <div class="d-flex align-items-center">
                    <div class="step-number">3</div>
                    <h2>Review Discrepancies</h2>
                </div>
            </div>
            <div class="step-content">
                <p class="status-message info">We've compared your CSV counts with our current inventory balances. Review the discrepancies below.</p>
                
                <div class="discrepancy-summary">
                    <div class="discrepancy-stat">
                        <div class="discrepancy-stat-value" id="total-items">0</div>
                        <div class="discrepancy-stat-label">Total Items</div>
                    </div>
                    <div class="discrepancy-stat">
                        <div class="discrepancy-stat-value text-success" id="matching-items">0</div>
                        <div class="discrepancy-stat-label">Matching Counts</div>
                    </div>
                    <div class="discrepancy-stat">
                        <div class="discrepancy-stat-value text-warning" id="low-items">0</div>
                        <div class="discrepancy-stat-label">Lower in CSV</div>
                    </div>
                    <div class="discrepancy-stat">
                        <div class="discrepancy-stat-value text-danger" id="high-items">0</div>
                        <div class="discrepancy-stat-label">Higher in CSV</div>
                    </div>
                </div>
                
                <div class="sticky-header">
                    <div>
                        <h3 style="margin:0">Inventory Discrepancies</h3>
                    </div>
                    <div class="filters">
                        <div class="filter-control">
                            <label for="discrepancy-filter">Show:</label>
                            <select id="discrepancy-filter">
                                <option value="all">All Items</option>
                                <option value="match">Matching Counts</option>
                                <option value="higher">Higher in CSV</option>
                                <option value="lower">Lower in CSV</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div id="discrepancy-results"></div>

                <div id="skipped-items-container" class="csv-info" style="display: none; margin-top: 30px;">
                    <h3>Skipped Items (No Matches)</h3>
                    <p>The following items from your CSV had no matches in the database and were skipped:</p>
                    <div id="skipped-items-list" style="max-height: 200px; overflow-y: auto;"></div>
                </div>
                
                <div class="button-group">
                    <button id="export-report" class="button">Export Report</button>
                    <button id="update-quantities" class="button secondary">Update Inventory Quantities</button>
                    <button id="reset-step-3" class="button secondary">Start Over</button>
                </div>
            </div>
        </div>
        
        <!-- Step 4: Update Confirmation -->
        <div class="step-container hidden" id="step-4">
            <div class="step-header">
                <div class="d-flex align-items-center">
                    <div class="step-number">4</div>
                    <h2>Update Confirmation</h2>
                </div>
            </div>
            <div class="step-content">
                <div class="status-message success">
                    <h3>Inventory Update Complete</h3>
                    <p>The inventory quantities have been successfully updated based on your CSV data.</p>
                </div>
                
                <div id="update-summary" class="csv-info">
                    <p><strong>Update Summary:</strong></p>
                    <div class="discrepancy-summary">
                        <div class="discrepancy-stat">
                            <div class="discrepancy-stat-value" id="total-updated">0</div>
                            <div class="discrepancy-stat-label">Total Items Updated</div>
                        </div>
                        <div class="discrepancy-stat">
                            <div class="discrepancy-stat-value" id="positive-updates">0</div>
                            <div class="discrepancy-stat-label">Quantity Increases</div>
                        </div>
                        <div class="discrepancy-stat">
                            <div class="discrepancy-stat-value" id="negative-updates">0</div>
                            <div class="discrepancy-stat-label">Quantity Decreases</div>
                        </div>
                        <div class="discrepancy-stat">
                            <div class="discrepancy-stat-value" id="error-updates">0</div>
                            <div class="discrepancy-stat-label">Failed Updates</div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Reports Section -->
                <div class="sticky-header" style="margin-top: 20px;">
                    <div>
                        <h3 style="margin:0">Detailed Update Reports</h3>
                    </div>
                    <div class="filters">
                        <div class="filter-control">
                            <label for="update-filter">Show:</label>
                            <select id="update-filter">
                                <option value="all">All Updates</option>
                                <option value="created">New Records</option>
                                <option value="updated">Updated Records</option>
                                <option value="failed">Failed Updates</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Successful Updates -->
                <div id="successful-updates" style="margin-top: 20px;">
                    <h4>Successfully Updated Items</h4>
                    <div id="successful-updates-list" class="match-results" style="margin-top: 10px;"></div>
                </div>
                
                <!-- Failed Updates -->
                <div id="failed-updates" style="margin-top: 20px; display: none;">
                    <h4>Failed Updates</h4>
                    <div id="failed-updates-list" class="match-results" style="margin-top: 10px;"></div>
                </div>
                
                <div class="button-group">
                    <button id="export-update-report" class="button">Export Update Report</button>
                    <button id="finish" class="button secondary">Finish</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        jQuery(document).ready(function($) {
            let csvData = null;
            let matchedRecords = {};
            let processedRows = new Set(); // Track processed CSV rows to prevent duplicates
            
            // File Upload Handling
            const uploadZone = $('#upload-zone');
            const fileInput = $('#csv-file');
            
            // Check if we have saved data in localStorage
            const checkForSavedData = function() {
                try {
                    const savedCsvData = localStorage.getItem('supply_corrector_csv_data');
                    if (savedCsvData) {
                        const parsedData = JSON.parse(savedCsvData);
                        const timestamp = localStorage.getItem('supply_corrector_timestamp');
                        const now = Date.now();
                        
                        // Check if data is less than 3 hours old
                        if (timestamp && (now - parseInt(timestamp)) < 3 * 60 * 60 * 1000) {
                            // Ask user if they want to continue with saved data
                            if (confirm("We found previously uploaded CSV data. Would you like to continue working with this data?")) {
                                csvData = parsedData;
                                $('#step-1').addClass('hidden');
                                $('#step-2').removeClass('hidden');
                                startMatching();
                                return true;
                            } else {
                                // Clear saved data if user doesn't want to use it
                                localStorage.removeItem('supply_corrector_csv_data');
                                localStorage.removeItem('supply_corrector_timestamp');
                            }
                        } else {
                            // Clear saved data if it's too old
                            localStorage.removeItem('supply_corrector_csv_data');
                            localStorage.removeItem('supply_corrector_timestamp');
                        }
                    }
                } catch (e) {
                    console.error("Error checking for saved data:", e);
                    localStorage.removeItem('supply_corrector_csv_data');
                    localStorage.removeItem('supply_corrector_timestamp');
                }
                return false;
            };
            
            // Check for saved data when the page loads
            if (!checkForSavedData()) {
                // Continue with normal initialization if no saved data is used
                uploadZone.on('click', () => fileInput.click());
                
                uploadZone.on('dragover dragenter', (e) => {
                    e.preventDefault();
                    uploadZone.addClass('dragover');
                });
                
                uploadZone.on('dragleave dragend drop', (e) => {
                    e.preventDefault();
                    uploadZone.removeClass('dragover');
                });
                
                uploadZone.on('drop', (e) => {
                    e.preventDefault();
                    const files = e.originalEvent.dataTransfer.files;
                    if (files.length) handleFile(files[0]);
                });
                
                fileInput.on('change', (e) => {
                    if (e.target.files.length) handleFile(e.target.files[0]);
                });
            }
            
            function handleFile(file) {
                if (!file.name.endsWith('.csv') && file.type !== 'text/csv') {
                    showStatus('Please upload a CSV file.', 'error');
                    return;
                }
                
                // Clear all localStorage items when a new file is uploaded
                try {
                    localStorage.removeItem('supply_corrector_csv_data');
                    localStorage.removeItem('supply_corrector_matches');
                    localStorage.removeItem('supply_corrector_discrepancies');
                    localStorage.removeItem('supply_corrector_skipped');
                    localStorage.removeItem('supply_corrector_skipped_export');
                    localStorage.removeItem('supply_corrector_updates');
                    localStorage.removeItem('supply_corrector_timestamp');
                } catch (e) {
                    console.warn("Error clearing localStorage:", e);
                }
                
                // Clear all match results and statistics
                $('.match-results').empty();
                $('#successful-updates-list').empty();
                $('#failed-updates-list').empty();
                $('#total-records').text('0');
                $('#matched-records').text('0');
                $('#unmatched-records').text('0');
                $('#selected-matches').text('0');
                
                // Clear step 3 & 4 contents to avoid any counting issues
                $('#discrepancy-results').empty();
                
                // Make sure later steps are hidden
                $('#step-2, #step-3, #step-4').addClass('hidden');
                
                // Reset the processed rows tracking
                processedRows = new Set();
                
                // Show a loading message
                showStatus('Processing your CSV file...', 'info');
                
                const formData = new FormData();
                formData.append('action', 'process_supply_csv');
                formData.append('csv_file', file);
                formData.append('nonce', '<?php echo wp_create_nonce("supply_corrector_nonce"); ?>');
                
                $('.progress-container').removeClass('hidden');
                $('.progress-fill').css('width', '0%');
                
                $.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhr: function() {
                        const xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener('progress', (e) => {
                            if (e.lengthComputable) {
                                const percent = Math.round((e.loaded / e.total) * 100);
                                $('.progress-fill').css('width', percent + '%');
                                $('.progress-percent').text(percent + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    success: function(response) {
                        if (response.success) {
                            // Make sure csvData is properly formatted
                            csvData = response.data;
                            
                            // Store the data in localStorage instead of using transients
                            try {
                                localStorage.setItem('supply_corrector_csv_data', JSON.stringify(response.data));
                                localStorage.setItem('supply_corrector_timestamp', Date.now().toString());
                            } catch (e) {
                                console.warn("Failed to store data in localStorage:", e);
                                // This might happen if the data is too large or localStorage is full
                                showStatus('Warning: Could not store data in browser storage. Your progress might be lost if you refresh the page.', 'info');
                            }
                            
                            showStatus('CSV processed successfully. ' + response.data.total_records + ' records found. Starting record matching...', 'success');
                            
                            // If there were error rows, show a note
                            if (response.data.error_rows && response.data.error_rows.length) {
                                showStatus('Note: ' + response.data.error_rows.length + ' rows in your CSV had formatting issues and were skipped.', 'info');
                            }
                            
                            setTimeout(() => {
                                $('#step-1').addClass('hidden');
                                $('#step-2').removeClass('hidden');
                                startMatching();
                            }, 1500);
                        } else {
                            showStatus(response.data, 'error');
                        }
                    },
                    error: function() {
                        showStatus('Error uploading file. Please try again.', 'error');
                    }
                });
            }
            
            function startMatching() {
                showLoading('#step-2');
                $('.match-results').empty(); // Clear previous results
                
                // Ensure all previous items in step 4 are cleared to prevent them from affecting match counts
                $('#successful-updates-list').empty();
                $('#failed-updates-list').empty();
                
                // Also make sure step 4 is hidden to prevent any visual confusion
                $('#step-4').addClass('hidden');
                
                processedRows = new Set(); // Reset the set of processed rows
                processNextBatch(0);
            }
            
            function processNextBatch(offset = 0) {
                // Check if we have the CSV data in memory or retrieve from localStorage
                if (!csvData) {
                    try {
                        const savedData = localStorage.getItem('supply_corrector_csv_data');
                        if (savedData) {
                            csvData = JSON.parse(savedData);
                        }
                    } catch (e) {
                        console.error("Error retrieving saved data:", e);
                    }
                    
                    if (!csvData) {
                        hideLoading('#step-2');
                        showStatus('CSV data not found. Please upload the file again.', 'error');
                        return;
                    }
                }
                
                // Make sure we have the csv_data array from the response
                const csvRows = Array.isArray(csvData.csv_data) ? csvData.csv_data : 
                               (Array.isArray(csvData) ? csvData : []);
                
                // Process the batch client-side instead of sending to server
                const batchSize = 10;
                const batch = csvRows.slice(offset, offset + batchSize);
                
                if (!batch || batch.length === 0) {
                    hideLoading('#step-2');
                    // Apply current filter after loading all batches
                    applyCurrentFilter();
                    return;
                }
                
                // Process this batch on the server
                $.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    type: 'POST',
                    data: {
                        action: 'search_supply_matches',
                        nonce: '<?php echo wp_create_nonce("supply_corrector_nonce"); ?>',
                        offset: offset,
                        batch_data: JSON.stringify(batch) // Send the actual batch data instead of just the offset
                    },
                    success: function(response) {
                        if (response.success) {
                            displayMatches(response.data.results);
                            updateMatchStats();
                            
                            // If there are more items to process, continue with the next batch
                            const totalRecords = csvData.total_records || csvRows.length;
                            if (offset + batchSize < totalRecords) {
                                processNextBatch(offset + batchSize);
                            } else {
                                hideLoading('#step-2');
                                // Apply current filter after loading all batches
                                applyCurrentFilter();
                            }
                        } else {
                            hideLoading('#step-2');
                            showStatus(response.data, 'error');
                        }
                    },
                    error: function() {
                        hideLoading('#step-2');
                        showStatus('Error processing matches. Please try again.', 'error');
                    }
                });
            }
            
            function displayMatches(results) {
                results.forEach(result => {
                    // Skip if we've already processed this row (prevent duplicates)
                    const rowKey = result.csv_row.supply_name + 
                                   (result.csv_row.lot_number || '') + 
                                   (result.csv_row.serial || '');
                    
                    if (processedRows.has(rowKey)) {
                        return;
                    }
                    
                    // Mark this row as processed
                    processedRows.add(rowKey);
                    
                    // Determine if the item has matches
                    const hasMatches = result.matches && result.matches.length > 0;
                    const isSingleMatch = hasMatches && result.matches.length === 1;
                    
                    // Set the appropriate match class - this is critical for counting
                    let matchClass = '';
                    if (!hasMatches) {
                        matchClass = 'no-matches';
                    } else if (isSingleMatch) {
                        matchClass = 'single-match has-matches';
                    } else {
                        matchClass = 'has-matches';
                    }
                    
                    // Create the appropriate badge
                    const matchBadge = hasMatches ? 
                        `<span class="badge success">${result.matches.length} ${result.matches.length === 1 ? 'match' : 'matches'}</span>` : 
                        '<span class="badge danger">No matches</span>';
                    
                    // Create compact details display
                    const detailItems = [];
                    if (result.csv_row.actual_count) detailItems.push(`<span class="compact-match-detail">Qty: ${result.csv_row.actual_count}</span>`);
                    if (result.csv_row.expiry_date) detailItems.push(`<span class="compact-match-detail">Exp: ${result.csv_row.expiry_date}</span>`);
                    if (result.csv_row.lot_number) detailItems.push(`<span class="compact-match-detail">Lot: ${result.csv_row.lot_number}</span>`);
                    const compactDetails = detailItems.length ? `<div class="compact-match-details">${detailItems.join('')}</div>` : '';
                    
                    // For single matches, automatically select the match
                    let autoSelectedMatch = '';
                    if (isSingleMatch) {
                        const match = result.matches[0];
                        autoSelectedMatch = `
                            <div class="match-selected-info">
                                <div><strong>Automatic match:</strong> ${match.name}</div>
                                <div class="compact-match-details">
                                    <span class="compact-match-detail">Department: ${match.department || 'Unknown'}</span>
                                    <span class="compact-match-detail">Type: ${match.type || 'Unknown'}</span>
                                    ${match.section ? `<span class="compact-match-detail">Section: ${match.section}</span>` : ''}
                                </div>
                            </div>
                        `;
                    }
                    
                    // Modified match item to always display options (no accordion)
                    const matchHtml = `
                        <div class="match-item ${matchClass}" data-csv-row='${JSON.stringify(result.csv_row)}'>
                            <div class="match-item-header">
                                <div>
                                    <h3>${result.csv_row.supply_name} ${matchBadge}</h3>
                                    ${compactDetails}
                                </div>
                            </div>
                            ${autoSelectedMatch}
                            ${hasMatches && !isSingleMatch ? `
                                <div class="match-options">
                                    <p class="match-options-header">Available matches:</p>
                                    ${result.matches.map(match => `
                                        <div class="match-option" data-supply-id="${match.id}">
                                            <strong>${match.name}</strong>
                                            <div class="compact-match-details">
                                                <span class="compact-match-detail">Department: ${match.department || 'Unknown'}</span>
                                                <span class="compact-match-detail">Type: ${match.type || 'Unknown'}</span>
                                                ${match.section ? `<span class="compact-match-detail">Section: ${match.section}</span>` : ''}
                                                <span class="compact-match-detail">Match: ${match.match_quality === 'exact' ? 
                                                    '<span class="badge success">Exact</span>' : 
                                                    match.match_quality === 'partial' ? 
                                                        '<span class="badge warning">Partial</span>' : 
                                                        '<span class="badge warning">Field</span>'
                                                }</span>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            ` : (!hasMatches ? '<p class="no-matches-message">No matches found in the database. This item will be skipped.</p>' : '')}
                        </div>
                    `;
                    $('.match-results').append(matchHtml);

                    // If it's a single match, automatically select it
                    if (isSingleMatch) {
                        const $item = $('.match-results .match-item').last();
                        const matchOption = $('<div class="match-option selected hidden"></div>')
                            .attr('data-supply-id', result.matches[0].id);
                        $item.append(matchOption);
                    }
                });
                
                // Match option selection handling with toggle functionality
                $('.match-results').off('click', '.match-option').on('click', '.match-option', function() {
                    const $this = $(this);
                    // Don't handle clicks for hidden auto-selected options
                    if (!$this.hasClass('hidden')) {
                        // If this option is already selected, deselect it (toggle functionality)
                        if ($this.hasClass('selected')) {
                            $this.removeClass('selected');
                        } else {
                            // Otherwise deselect siblings and select this one
                            $this.siblings('.match-option').removeClass('selected');
                            $this.addClass('selected');
                        }
                        updateMatchStats();
                    }
                });
                
                // Update stats after adding all items
                updateMatchStats();
            }
            
            // Function to apply the current filter selection
            function applyCurrentFilter() {
                const filter = $('#match-filter').val();
                
                $('.match-item').each(function() {
                    const $item = $(this);
                    const hasMatches = $item.hasClass('has-matches');
                    // Check if this item has a selected match option - include hidden selected options for auto-selected matches
                    const hasSelected = $item.find('.match-option.selected').length > 0 || $item.hasClass('single-match');
                    
                    switch (filter) {
                        case 'all':
                            $item.show();
                            break;
                        case 'matched':
                            hasMatches ? $item.show() : $item.hide();
                            break;
                        case 'unmatched':
                            !hasMatches ? $item.show() : $item.hide();
                            break;
                        case 'selected':
                            hasSelected ? $item.show() : $item.hide();
                            break;
                        case 'unselected':
                            // Show items that have matches but no selection
                            (hasMatches && !hasSelected) ? $item.show() : $item.hide();
                            break;
                    }
                });

                $('#filter-count').text($('.match-item:visible').length);
            }
            
            // Match filtering - reattach event handler
            $('#match-filter').off('change').on('change', function() {
                applyCurrentFilter();
            });
            
            function updateMatchStats() {
                // Count all match items in step-2, including those without matches
                const total = $('#step-2 .match-results .match-item').length;
                const matchedItems = $('#step-2 .match-results .match-item.has-matches').length;
                
                // Fixed: The issue was here - explicitly count elements with the no-matches class
                const unmatched = $('#step-2 .match-item.no-matches').length;
                
                // Count ALL selected match options on the page
                const selected = $('.match-option.selected').length;
                
                $('#total-records').text(total);
                $('#matched-records').text(matchedItems); 
                $('#unmatched-records').text(unmatched);
                $('#selected-matches').text(selected);
                
                const allowPartial = $('#allow-partial-matches').is(':checked');
                
                // Enable the button if:
                // - All items with matches have selections, OR
                // - Allow partial matches is checked AND at least one match is selected
                const allMatchedSelected = matchedItems === $('#step-2 .match-results .match-item.has-matches:has(.match-option.selected)').length;
                const someSelected = selected > 0;
                
                $('#confirm-matches').prop('disabled', !(allMatchedSelected || (allowPartial && someSelected)));
            }
            
            // Allow partial match checkbox
            $('#allow-partial-matches').on('change', function() {
                updateMatchStats();
            });
            
            // Match filtering
            $('#match-filter').on('change', function() {
                applyCurrentFilter();
            });
            
            $('#confirm-matches').on('click', function() {
                const matches = [];
                const skippedItems = []; // Track items that are skipped
                
                // First collect items without matches
                $('.match-item.no-matches').each(function() {
                    const $item = $(this);
                    const csvRow = JSON.parse($item.attr('data-csv-row'));
                    skippedItems.push(csvRow);
                });
                
                // Then collect items with matches but no selection
                $('.match-item.has-matches').each(function() {
                    const $item = $(this);
                    const csvRow = JSON.parse($item.attr('data-csv-row'));
                    const selectedMatch = $item.find('.match-option.selected');
                    
                    if (selectedMatch.length) {
                        matches.push({
                            supply_id: selectedMatch.data('supply-id'),
                            csv_data: csvRow
                        });
                    } else if ($('#allow-partial-matches').is(':checked')) {
                        // If partial matches allowed, track unmatched items
                        skippedItems.push(csvRow);
                    }
                });
                
                if (matches.length === 0) {
                    showStatus('No matches have been selected.', 'error');
                    return;
                }
                
                showLoading('#step-2');
                
                // Store matches in localStorage
                try {
                    localStorage.setItem('supply_corrector_matches', JSON.stringify(matches));
                    // Also store skipped items for display in Step 3
                    localStorage.setItem('supply_corrector_skipped', JSON.stringify(skippedItems));
                } catch (e) {
                    console.warn("Failed to store data in localStorage:", e);
                }
                
                $.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    type: 'POST',
                    data: {
                        action: 'confirm_supply_matches',
                        nonce: '<?php echo wp_create_nonce("supply_corrector_nonce"); ?>',
                        matches: JSON.stringify(matches)
                    },
                    success: function(response) {
                        hideLoading('#step-2');
                        if (response.success) {
                            $('#step-2').addClass('hidden');
                            $('#step-3').removeClass('hidden');
                            checkDiscrepancies();
                        } else {
                            showStatus(response.data, 'error');
                        }
                    },
                    error: function() {
                        hideLoading('#step-2');
                        showStatus('Error confirming matches. Please try again.', 'error');
                    }
                });
            });
            
            function checkDiscrepancies(offset = 0) {
                if (offset === 0) {
                    showLoading('#step-3');
                    // Reset counters
                    $('#total-items').text('0');
                    $('#matching-items').text('0');
                    $('#low-items').text('0');
                    $('#high-items').text('0');
                    $('#discrepancy-results').empty();
                    
                    // Display skipped items if any
                    try {
                        const skippedItems = localStorage.getItem('supply_corrector_skipped');
                        if (skippedItems) {
                            const parsedSkipped = JSON.parse(skippedItems);
                            if (parsedSkipped && parsedSkipped.length > 0) {
                                $('#skipped-items-container').show();
                                const $skippedList = $('#skipped-items-list');
                                $skippedList.empty();
                                
                                parsedSkipped.forEach(item => {
                                    const itemHtml = `
                                        <div class="skipped-item" style="padding: 8px; margin-bottom: 8px; border-bottom: 1px solid #eee;">
                                            <strong>${item.supply_name}</strong>
                                            <div class="compact-match-details">
                                                ${item.actual_count ? `<span class="compact-match-detail">Qty: ${item.actual_count}</span>` : ''}
                                                ${item.expiry_date ? `<span class="compact-match-detail">Exp: ${item.expiry_date}</span>` : ''}
                                                ${item.lot_number ? `<span class="compact-match-detail">Lot: ${item.lot_number}</span>` : ''}
                                                ${item.serial ? `<span class="compact-match-detail">Serial: ${item.serial}</span>` : ''}
                                            </div>
                                        </div>
                                    `;
                                    $skippedList.append(itemHtml);
                                });
                                
                                // Add to report
                                $('#export-report').on('click', function() {
                                    const skippedRows = [
                                        ['Supply Name', 'Quantity', 'Expiry Date', 'Lot Number', 'Serial', 'Status', 'Reason']
                                    ];
                                    
                                    parsedSkipped.forEach(item => {
                                        skippedRows.push([
                                            item.supply_name || '',
                                            item.actual_count || '',
                                            item.expiry_date || '',
                                            item.lot_number || '',
                                            item.serial || '',
                                            item.states__status || '',
                                            'No match found'
                                        ]);
                                    });
                                    
                                    // Create a second sheet in the report
                                    localStorage.setItem('supply_corrector_skipped_export', JSON.stringify(skippedRows));
                                });
                            } else {
                                $('#skipped-items-container').hide();
                            }
                        } else {
                            $('#skipped-items-container').hide();
                        }
                    } catch (e) {
                        console.warn("Error displaying skipped items:", e);
                        $('#skipped-items-container').hide();
                    }
                }
                
                // Get matches from localStorage if available
                let matches = [];
                try {
                    const savedMatches = localStorage.getItem('supply_corrector_matches');
                    if (savedMatches) {
                        matches = JSON.parse(savedMatches);
                    }
                } catch (e) {
                    console.error("Error retrieving saved matches:", e);
                }
                
                // If no matches in localStorage, check if there's a variable in memory
                if (matches.length === 0 && matchedRecords.length > 0) {
                    matches = matchedRecords;
                }
                
                // If we still don't have matches, show an error
                if (matches.length === 0) {
                    hideLoading('#step-3');
                    showStatus('Match data not found. Please go back and select matches again.', 'error');
                    return;
                }
                
                // Use the batch of matches for this request
                const batchSize = 10;
                const currentBatch = matches.slice(offset, offset + batchSize);
                
                if (currentBatch.length === 0) {
                    hideLoading('#step-3');
                    return;
                }
                
                $.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    type: 'POST',
                    data: {
                        action: 'check_supply_discrepancies',
                        nonce: '<?php echo wp_create_nonce("supply_corrector_nonce"); ?>',
                        offset: offset,
                        matches: JSON.stringify(currentBatch) // Send the actual batch data
                    },
                    success: function(response) {
                        if (response.success) {
                            displayDiscrepancies(response.data.results);
                            updateDiscrepancyStats();
                            
                            // Store the discrepancy results in localStorage
                            try {
                                const existingResults = localStorage.getItem('supply_corrector_discrepancies');
                                let allResults = existingResults ? JSON.parse(existingResults) : [];
                                allResults = allResults.concat(response.data.results);
                                localStorage.setItem('supply_corrector_discrepancies', JSON.stringify(allResults));
                            } catch (e) {
                                console.warn("Failed to store discrepancies in localStorage:", e);
                            }
                            
                            if (offset + batchSize < matches.length) {
                                checkDiscrepancies(offset + batchSize);
                            } else {
                                hideLoading('#step-3');
                            }
                        } else {
                            hideLoading('#step-3');
                            showStatus(response.data, 'error');
                        }
                    },
                    error: function() {
                        hideLoading('#step-3');
                        showStatus('Error checking discrepancies. Please try again.', 'error');
                    }
                });
            }
            
            function displayDiscrepancies(results) {
                results.forEach(result => {
                    const discrepancyClass = result.discrepancy > 0 ? 'discrepancy-high' : 
                                           result.discrepancy < 0 ? 'discrepancy-low' : 
                                           'discrepancy-match';
                    
                    const discrepancyHtml = `
                        <div class="discrepancy-item ${discrepancyClass}" 
                            data-supply-id="${result.supply_id}" 
                            data-discrepancy="${result.discrepancy}" 
                            data-csv-data='${JSON.stringify(result.csv_data)}'>
                            <div class="discrepancy-info">
                                <h3 style="margin-top:0;">${result.supply_name}</h3>
                                <div>Serial: ${result.csv_data.serial || 'N/A'}</div>
                                <div>Lot Number: ${result.csv_data.lot_number || 'N/A'}</div>
                                <div>Expiry Date: ${result.csv_data.expiry_date || 'N/A'}</div>
                                <div>Status: ${result.csv_data.states__status || 'N/A'}</div>
                            </div>
                            <div class="discrepancy-counts">
                                <div>Current Balance: <strong>${result.current_balance}</strong></div>
                                <div>CSV Count: <strong>${result.csv_count}</strong></div>
                                <div class="discrepancy-count ${
                                    result.discrepancy > 0 ? 'high' : 
                                    result.discrepancy < 0 ? 'low' : 'match'
                                }">
                                    Discrepancy: ${result.discrepancy > 0 ? '+' : ''}${result.discrepancy}
                                </div>
                                <div class="discrepancy-percentage">
                                    (${result.percent_discrepancy}%)
                                </div>
                            </div>
                        </div>
                    `;
                    $('#discrepancy-results').append(discrepancyHtml);
                });
            }
            
            function updateDiscrepancyStats() {
                const items = $('.discrepancy-item');
                const total = items.length;
                const matching = $('.discrepancy-match').length;
                const low = $('.discrepancy-low').length;
                const high = $('.discrepancy-high').length;
                
                $('#total-items').text(total);
                $('#matching-items').text(matching);
                $('#low-items').text(low);
                $('#high-items').text(high);
            }
            
            // Discrepancy filtering
            $('#discrepancy-filter').on('change', function() {
                const filter = $(this).val();
                
                $('.discrepancy-item').each(function() {
                    const $item = $(this);
                    
                    switch (filter) {
                        case 'all':
                            $item.show();
                            break;
                        case 'match':
                            $item.hasClass('discrepancy-match') ? $item.show() : $item.hide();
                            break;
                        case 'higher':
                            $item.hasClass('discrepancy-high') ? $item.show() : $item.hide();
                            break;
                        case 'lower':
                            $item.hasClass('discrepancy-low') ? $item.show() : $item.hide();
                            break;
                    }
                });
            });
            
            $('#export-report').on('click', function() {
                // Define headers and prepare data arrays
                const mainHeaders = ['Supply Name', 'Current Balance', 'CSV Count', 'Discrepancy', 'Percent Discrepancy', 'Serial', 'Lot Number', 'Expiry Date', 'Status'];
                
                const mainRows = [mainHeaders];
                let hasSkippedItems = false;
                
                // Process matched items with discrepancies
                $('.discrepancy-item').each(function() {
                    const $item = $(this);
                    const csvData = JSON.parse($item.attr('data-csv-data'));
                    const supplyName = $item.find('h3').text();
                    const currentBalance = $item.find('.discrepancy-counts div:nth-child(1) strong').text();
                    const csvCount = $item.find('.discrepancy-counts div:nth-child(2) strong').text();
                    const discrepancy = $item.attr('data-discrepancy');
                    const percentDiscrepancy = $item.find('.discrepancy-percentage').text().replace(/[()%]/g, '');
                    
                    mainRows.push([
                        supplyName || '',
                        currentBalance || '',
                        csvCount || '',
                        discrepancy || '',
                        percentDiscrepancy || '',
                        csvData.serial || '',
                        csvData.lot_number || '',
                        csvData.expiry_date || '',
                        csvData.states__status || ''
                    ]);
                });
                
                // Process skipped items
                let skippedRows = [];
                try {
                    const skippedItems = localStorage.getItem('supply_corrector_skipped');
                    if (skippedItems) {
                        const parsedSkipped = JSON.parse(skippedItems);
                        if (parsedSkipped && parsedSkipped.length > 0) {
                            hasSkippedItems = true;
                            skippedRows = [
                                ['Supply Name', 'Quantity', 'Expiry Date', 'Lot Number', 'Serial', 'Status', 'Reason']
                            ];
                            
                            parsedSkipped.forEach(item => {
                                skippedRows.push([
                                    item.supply_name || '',
                                    item.actual_count || '',
                                    item.expiry_date || '',
                                    item.lot_number || '',
                                    item.serial || '',
                                    item.states__status || '',
                                    'No match found'
                                ]);
                            });
                        }
                    }
                } catch (e) {
                    console.warn("Error processing skipped items for report:", e);
                }
                
                // Create CSV content with proper escaping
                let csvContent = "data:text/csv;charset=utf-8,";
                
                // Helper function to properly format a CSV row
                const formatCSVRow = (row) => {
                    return row.map(cell => {
                        // Handle null/undefined values
                        if (cell === null || cell === undefined) {
                            return '';
                        }
                        
                        // Convert to string and escape properly
                        const strCell = String(cell);
                        if (strCell.includes(',') || strCell.includes('"') || strCell.includes('\n')) {
                            return '"' + strCell.replace(/"/g, '""') + '"';
                        }
                        return strCell;
                    }).join(',');
                };
                
                // Add main data to CSV
                mainRows.forEach(row => {
                    csvContent += formatCSVRow(row) + '\r\n';
                });
                
                // Add skipped items section if any
                if (hasSkippedItems) {
                    csvContent += '\r\n"SKIPPED ITEMS (NO MATCHES)"\r\n\r\n';
                    
                    skippedRows.forEach(row => {
                        csvContent += formatCSVRow(row) + '\r\n';
                    });
                }
                
                // Download the CSV file
                const encodedUri = encodeURI(csvContent);
                const link = document.createElement('a');
                link.setAttribute('href', encodedUri);
                link.setAttribute('download', 'inventory_discrepancy_report_' + formatDate(new Date()) + '.csv');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
            
            $('#update-quantities').on('click', function() {
                if (confirm("Are you sure you want to update the inventory quantities based on your CSV data? This action cannot be undone.")) {
                    const updates = [];
                    
                    // Collect all discrepancies to update
                    $('.discrepancy-item').each(function() {
                        const $item = $(this);
                        const supplyId = $item.attr('data-supply-id');
                        const discrepancy = parseFloat($item.attr('data-discrepancy'));
                        const csvData = JSON.parse($item.attr('data-csv-data'));
                        
                        // Only include items with discrepancies
                        if (discrepancy !== 0) {
                            updates.push({
                                supply_id: supplyId,
                                new_quantity: parseFloat(csvData.actual_count),
                                discrepancy: discrepancy, // Now explicitly parsed as float
                                csv_data: csvData,
                                lot_number: csvData.lot_number || '',
                                serial: csvData.serial || '',
                                states__status: csvData.states__status || '',
                                expiry_date: csvData.expiry_date || ''
                            });
                        }
                    });
                    
                    if (updates.length === 0) {
                        showStatus('No discrepancies to update.', 'info');
                        return;
                    }
                    
                    showLoading('#step-3');
                    
                    $.ajax({
                        url: '<?php echo admin_url("admin-ajax.php"); ?>',
                        type: 'POST',
                        data: {
                            action: 'update_supply_quantities',
                            nonce: '<?php echo wp_create_nonce("supply_corrector_nonce"); ?>',
                            updates: JSON.stringify(updates)
                        },
                        success: function(response) {
                            console.log("Update response:", response);
                            hideLoading('#step-3');
                            if (response.success) {
                                // Clear the localStorage data since the process is complete
                                try {
                                    localStorage.removeItem('supply_corrector_csv_data');
                                    localStorage.removeItem('supply_corrector_matches');
                                    localStorage.removeItem('supply_corrector_discrepancies');
                                    localStorage.removeItem('supply_corrector_timestamp');
                                } catch (e) {
                                    console.warn("Error clearing localStorage:", e);
                                }
                                
                                // Store update results for reporting
                                try {
                                    localStorage.setItem('supply_corrector_updates', JSON.stringify(response.data));
                                } catch (e) {
                                    console.warn("Failed to store update results in localStorage:", e);
                                }
                                
                                $('#step-3').addClass('hidden');
                                $('#step-4').removeClass('hidden');
                                
                                // Process and display the update results
                                displayUpdateResults(response.data);
                            } else {
                                showStatus(response.data, 'error');
                            }
                        },
                        error: function() {
                            hideLoading('#step-3');
                            showStatus('Error updating quantities. Please try again.', 'error');
                        }
                    });
                }
            });
            
            // Display detailed update results on Step 4
            function displayUpdateResults(data) {
                // Calculate update statistics
                const totalUpdates = data.success.length;
                const failedUpdates = data.failed.length;
                let positiveUpdates = 0;
                let negativeUpdates = 0;
                
                $('#total-updated').text(totalUpdates);
                $('#error-updates').text(failedUpdates);
                
                // Clear previous results
                $('#successful-updates-list').empty();
                $('#failed-updates-list').empty();
                
                // Process successful updates
                if (data.success.length > 0) {
                    data.success.forEach(item => {
                        // Count positives and negatives based on simplified action types
                        if (item.action === 'created_actual') {
                            positiveUpdates++;
                        } else if (item.action === 'created_release') {
                            negativeUpdates++;
                        }
                        
                        // Style based on update type
                        let itemClass = 'match-item';
                        let badge = '';
                        let details = '';
                        
                        if (item.action === 'created_actual') {
                            itemClass += ' discrepancy-high';
                            badge = '<span class="badge success">Quantity Added</span>';
                            details = `
                                <div class="match-item-details">
                                    <p><strong>New record created in actualsupplies with quantity:</strong> ${item.quantity_added}</p>
                                    ${item.lot_number ? `<p><strong>Lot Number:</strong> ${item.lot_number}</p>` : ''}
                                    ${item.expiry_date ? `<p><strong>Expiry Date:</strong> ${item.expiry_date}</p>` : ''}
                                    ${item.serial ? `<p><strong>Serial:</strong> ${item.serial}</p>` : ''}
                                </div>
                            `;
                        } else if (item.action === 'created_release') {
                            itemClass += ' discrepancy-low';
                            badge = '<span class="badge warning">Quantity Released</span>';
                            details = `
                                <div class="match-item-details">
                                    <p><strong>New record created in releasesupplies with quantity:</strong> ${item.quantity_released}</p>
                                    <p><strong>Department:</strong> Inventory Adjustment</p>
                                    <p><strong>Status:</strong> Confirmed</p>
                                </div>
                            `;
                        }
                        
                        // Create update item HTML
                        const updateItemHtml = `
                            <div class="${itemClass}" data-action="${item.action}">
                                <div class="match-item-header">
                                    <div>
                                        <h3>Supply ID: ${item.supply_id} ${badge}</h3>
                                    </div>
                                </div>
                                ${details}
                            </div>
                        `;
                        
                        $('#successful-updates-list').append(updateItemHtml);
                    });
                    
                    // Update the counts
                    $('#positive-updates').text(positiveUpdates);
                    $('#negative-updates').text(negativeUpdates);
                } else {
                    $('#successful-updates').append('<p>No successful updates were made.</p>');
                }
                
                // Process failed updates
                if (data.failed.length > 0) {
                    $('#failed-updates').show();
                    
                    data.failed.forEach(item => {
                        const failedItemHtml = `
                            <div class="match-item discrepancy-high">
                                <div class="match-item-header">
                                    <div>
                                        <h3>Supply ID: ${item.supply_id} <span class="badge danger">Failed</span></h3>
                                    </div>
                                </div>
                                <div class="match-item-details">
                                    <p><strong>Reason:</strong> ${item.reason}</p>
                                </div>
                            </div>
                        `;
                        
                        $('#failed-updates-list').append(failedItemHtml);
                    });
                } else {
                    $('#failed-updates').hide();
                }
            }
            
            // Update filter
            $('#update-filter').on('change', function() {
                const filter = $(this).val();
                
                $('#successful-updates-list .match-item').each(function() {
                    const $item = $(this);
                    const action = $item.data('action');
                    
                    switch (filter) {
                        case 'all':
                            $item.show();
                            break;
                        case 'created':
                            action === 'created' ? $item.show() : $item.hide();
                            break;
                        case 'updated':
                            (action === 'updated_multiple' || action === 'updated_partially') ? $item.show() : $item.hide();
                            break;
                        case 'failed':
                            $('#successful-updates').hide();
                            $('#failed-updates').show();
                            return;
                    }
                    
                    if (filter !== 'failed') {
                        $('#successful-updates').show();
                        $('#failed-updates').hide();
                    }
                });
            });
            
            // Export update report
            $('#export-update-report').on('click', function() {
                let updateData = null;
                
                // Try to get update data from localStorage
                try {
                    const savedData = localStorage.getItem('supply_corrector_updates');
                    if (savedData) {
                        updateData = JSON.parse(savedData);
                    }
                } catch (e) {
                    console.error("Error retrieving update data:", e);
                }
                
                if (!updateData) {
                    showStatus('Update data not found for export.', 'error');
                    return;
                }
                
                // Headers for the update report
                const successHeaders = ['Supply ID', 'Action Type', 'Quantity Changed', 'Details', 'Date'];
                const failedHeaders = ['Supply ID', 'Failure Reason', 'Date'];
                
                const successRows = [successHeaders];
                const failedRows = [failedHeaders];
                
                // Current date
                const currentDate = formatDate(new Date(), true);
                
                // Process successful updates
                updateData.success.forEach(item => {
                    let actionType = '';
                    let quantityChanged = '';
                    let details = '';
                    
                    if (item.action === 'created') {
                        actionType = 'New Record Created';
                        quantityChanged = item.quantity_added;
                        details = `Lot: ${item.lot_number || 'N/A'}, Expiry: ${item.expiry_date || 'N/A'}`;
                    } else if (item.action === 'updated_multiple') {
                        actionType = 'Quantity Reduced';
                        quantityChanged = -item.total_reduction;
                        details = `Updated ${item.updated_records.length} records`;
                    } else if (item.action === 'updated_partially') {
                        actionType = 'Partially Updated';
                        quantityChanged = -item.actual_reduction;
                        details = `Target reduction: ${item.target_reduction}, Actual: ${item.actual_reduction}`;
                    }
                    
                    successRows.push([
                        item.supply_id || '',
                        actionType,
                        quantityChanged || '',
                        details,
                        currentDate
                    ]);
                });
                
                // Process failed updates
                updateData.failed.forEach(item => {
                    failedRows.push([
                        item.supply_id || '',
                        item.reason || 'Unknown error',
                        currentDate
                    ]);
                });
                
                // Create CSV content
                let csvContent = "data:text/csv;charset=utf-8,";
                
                // Helper function to properly format a CSV row
                const formatCSVRow = (row) => {
                    return row.map(cell => {
                        if (cell === null || cell === undefined) {
                            return '';
                        }
                        
                        const strCell = String(cell);
                        if (strCell.includes(',') || strCell.includes('"') || strCell.includes('\n')) {
                            return '"' + strCell.replace(/"/g, '""') + '"';
                        }
                        return strCell;
                    }).join(',');
                };
                
                // Add successful updates to CSV
                csvContent += "SUCCESSFUL UPDATES\r\n";
                successRows.forEach(row => {
                    csvContent += formatCSVRow(row) + '\r\n';
                });
                
                // Add failed updates if any
                if (updateData.failed.length > 0) {
                    csvContent += '\r\nFAILED UPDATES\r\n';
                    failedRows.forEach(row => {
                        csvContent += formatCSVRow(row) + '\r\n';
                    });
                }
                
                // Download the CSV file
                const encodedUri = encodeURI(csvContent);
                const link = document.createElement('a');
                link.setAttribute('href', encodedUri);
                link.setAttribute('download', 'inventory_update_report_' + formatDate(new Date()) + '.csv');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
            
            // Template download
            $('#download-template').on('click', function(e) {
                e.preventDefault();
                const header = 'supply_name,actual_count,expiry_date,date_added,serial,states__status,lot_number\n';
                const exampleRow = 'Surgical Gloves (Medium),500,2025-12-31,2023-05-20,SG2023-001,active,LOT123456\n';
                const template = header + exampleRow;
                
                const blob = new Blob([template], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.setAttribute('href', url);
                a.setAttribute('download', 'supply_count_template.csv');
                a.click();
                window.URL.revokeObjectURL(url);
            });
            
            // Reset buttons should clear localStorage
            $('#reset-step-2').on('click', function() {
                if (confirm("Are you sure you want to start over? All match progress will be lost.")) {
                    try {
                        localStorage.removeItem('supply_corrector_csv_data');
                        localStorage.removeItem('supply_corrector_matches');
                        localStorage.removeItem('supply_corrector_discrepancies');
                        localStorage.removeItem('supply_corrector_timestamp');
                    } catch (e) {
                        console.warn("Error clearing localStorage:", e);
                    }
                    window.location.reload();
                }
            });
            
            $('#reset-step-3').on('click', function() {
                if (confirm("Are you sure you want to start over? All progress will be lost.")) {
                    try {
                        localStorage.removeItem('supply_corrector_csv_data');
                        localStorage.removeItem('supply_corrector_matches');
                        localStorage.removeItem('supply_corrector_discrepancies');
                        localStorage.removeItem('supply_corrector_timestamp');
                    } catch (e) {
                        console.warn("Error clearing localStorage:", e);
                    }
                    window.location.reload();
                }
            });
            
            $('#finish').on('click', function() {
                try {
                    localStorage.removeItem('supply_corrector_csv_data');
                    localStorage.removeItem('supply_corrector_matches');
                    localStorage.removeItem('supply_corrector_discrepancies');
                    localStorage.removeItem('supply_corrector_timestamp');
                    localStorage.removeItem('supply_corrector_updates');
                } catch (e) {
                    console.warn("Error clearing localStorage:", e);
                }
                window.location.reload();
            });
            
            // Helper functions
            function showStatus(message, type) {
                const statusDiv = $('.status-message');
                statusDiv.removeClass('hidden error success info').addClass(type)
                    .text(message).show();
            }
            
            function showLoading(container) {
                const loadingEl = $('<div class="loading"></div>');
                $(container).find('.step-content').append(loadingEl);
            }
            
            function hideLoading(container) {
                $(container).find('.loading').remove();
            }
            
            function formatDate(date, includeTime = false) {
                if (includeTime) {
                    return date.getFullYear() + 
                        '-' + pad(date.getMonth() + 1) + 
                        '-' + pad(date.getDate()) + 
                        ' ' + pad(date.getHours()) + 
                        ':' + pad(date.getMinutes()) +
                        ':' + pad(date.getSeconds());
                }
                
                return date.getFullYear() + 
                       '-' + pad(date.getMonth() + 1) + 
                       '-' + pad(date.getDate()) + 
                       '_' + pad(date.getHours()) + 
                       '-' + pad(date.getMinutes());
            }
            
            function pad(num) {
                return (num < 10 ? '0' : '') + num;
            }

            // Step 2 - Export to CSV functionality
            $('#export-matches-csv').on('click', function() {
                const matchedItems = [];
                
                // Get all visible matched items from the UI - fixing selector to use .match-item
                $('.match-item:visible').each(function() {
                    const $item = $(this);
                    const csvRow = JSON.parse($item.attr('data-csv-row'));
                    let matchedSupply = null;
                    
                    // If matched, get the match data
                    if ($item.hasClass('has-matches')) {
                        const $selectedOption = $item.find('.match-option.selected');
                        if ($selectedOption.length) {
                            const supplyId = $selectedOption.data('supply-id');
                            const supplyName = $selectedOption.find('strong').text();
                            // Get department, type and section from the match details
                            const $details = $selectedOption.find('.compact-match-details');
                            const departmentText = $details.find('.compact-match-detail:contains("Department:")').text();
                            const typeText = $details.find('.compact-match-detail:contains("Type:")').text();
                            const sectionText = $details.find('.compact-match-detail:contains("Section:")').text();
                            
                            matchedSupply = {
                                id: supplyId,
                                name: supplyName,
                                department: departmentText.replace('Department:', '').trim(),
                                type: typeText.replace('Type:', '').trim(),
                                section: sectionText.replace('Section:', '').trim()
                            };
                        } else if ($item.hasClass('single-match')) {
                            // For automatically matched items
                            const supplyInfo = $item.find('.match-selected-info');
                            const supplyName = supplyInfo.find('div:first-child').text().replace('Automatic match:', '').trim();
                            const $hiddenOption = $item.find('.match-option.selected.hidden');
                            const supplyId = $hiddenOption.data('supply-id');
                            const $details = supplyInfo.find('.compact-match-details');
                            const departmentText = $details.find('.compact-match-detail:contains("Department:")').text();
                            const typeText = $details.find('.compact-match-detail:contains("Type:")').text();
                            const sectionText = $details.find('.compact-match-detail:contains("Section:")').text();
                            
                            matchedSupply = {
                                id: supplyId,
                                name: supplyName,
                                department: departmentText.replace('Department:', '').trim(),
                                type: typeText.replace('Type:', '').trim(),
                                section: sectionText.replace('Section:', '').trim()
                            };
                        }
                    }
                    
                    matchedItems.push({
                        csv_row: csvRow,
                        matched_supply: matchedSupply
                    });
                });
                
                // No data to export
                if (matchedItems.length === 0) {
                    showStatus('No data to export', 'error');
                    return;
                }
                
                // Show loading state
                $(this).prop('disabled', true).text('Exporting...');
                const exportButton = $(this);
                
                // Call the AJAX endpoint to get the CSV data
                $.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    type: 'POST',
                    data: {
                        action: 'export_matches_csv',
                        nonce: '<?php echo wp_create_nonce("supply_corrector_nonce"); ?>',
                        matches_data: JSON.stringify(matchedItems)
                    },
                    success: function(response) {
                        if (response.success && response.data.csv_data) {
                            // Create and download the CSV file
                            downloadCSV(response.data.csv_data, response.data.filename);
                            showStatus('CSV exported successfully', 'success');
                        } else {
                            showStatus('Failed to export CSV: ' + (response.data || 'Unknown error'), 'error');
                        }
                        exportButton.prop('disabled', false).text('Export to CSV');
                    },
                    error: function(xhr, status, error) {
                        showStatus('Error exporting CSV: ' + error, 'error');
                        exportButton.prop('disabled', false).text('Export to CSV');
                    }
                });
            });
            
            // Function to download CSV data as a file
            function downloadCSV(data, filename) {
                let csvContent = '';
                
                // Convert data array to CSV string
                data.forEach(function(row) {
                    let rowString = '';
                    for (let i = 0; i < row.length; i++) {
                        // Quote values with commas and escape existing quotes
                        let value = row[i] !== null ? row[i].toString() : '';
                        if (value.includes(',') || value.includes('"') || value.includes('\n')) {
                            value = '"' + value.replace(/"/g, '""') + '"';
                        }
                        rowString += (i > 0 ? ',' : '') + value;
                    }
                    csvContent += rowString + '\n';
                });
                
                // Create a Blob with the CSV data
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                
                // Create download link and trigger click
                if (navigator.msSaveBlob) { // For IE
                    navigator.msSaveBlob(blob, filename);
                } else {
                    const link = document.createElement('a');
                    if (link.download !== undefined) {
                        const url = URL.createObjectURL(blob);
                        link.setAttribute('href', url);
                        link.setAttribute('download', filename);
                        link.style.visibility = 'hidden';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        URL.revokeObjectURL(url);
                    }
                }
            }
        });
    </script>
</body>
</html>