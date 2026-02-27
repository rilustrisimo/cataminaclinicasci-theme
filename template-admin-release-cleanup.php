<?php
/**
 * Template Name: Admin - Release Cleanup
 *
 * @author    eyorsogood.com
 * @package   SwishDesign
 * @version   1.0.0
 */

/**
 * No direct access to this file.
 *
 * @since 1.0.0
 */

// Restrict access to administrators only
if (!current_user_can('manage_options')) {
    wp_die('You do not have permission to access this page.');
}

$theme = new Theme();

get_header();
if ( have_posts() ) : ?>
    <?php while ( have_posts() ) { the_post(); ?>
        <div class="page-single">
            <main class="page-single__content" role="main">
                <div class="release-cleanup-management card mt-5">
                    <div class="card-header py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2 class="mb-0 fs-5 fw-semibold text-dark">
                                <i class="fa-solid fa-broom me-2"></i>Pending Release Cleanup
                            </h2>
                            <span class="badge bg-danger">Admin Only</span>
                        </div>
                        <p class="mb-0 mt-2 text-muted small">Manage and bulk delete irrelevant pending release supplies</p>
                    </div>
                    <div class="card-body p-4">
                        
                        <!-- Filters Section -->
                        <div class="filters-section card bg-light mb-4">
                            <div class="card-body">
                                <div class="row g-3">
                                    <!-- Department Filter -->
                                    <div class="col-md-3">
                                        <label for="filter-department" class="form-label fw-medium text-dark mb-1 small">
                                            <i class="fa-solid fa-building me-1"></i>Department
                                        </label>
                                        <select id="filter-department" class="form-select form-select-sm">
                                            <option value="ALL">All Departments</option>
                                            <option value="NURSING">NURSING</option>
                                            <option value="LABORATORY">LABORATORY</option>
                                            <option value="PHARMACY">PHARMACY</option>
                                            <option value="HOUSEKEEPING">HOUSEKEEPING</option>
                                            <option value="MAINTENANCE">MAINTENANCE</option>
                                            <option value="RADIOLOGY">RADIOLOGY</option>
                                            <option value="BUSINESS OFFICE">BUSINESS OFFICE</option>
                                            <option value="INFORMATION / TRIAGE">INFORMATION / TRIAGE</option>
                                            <option value="PHYSICAL THERAPY">PHYSICAL THERAPY</option>
                                            <option value="KONSULTA PROGRAM">KONSULTA PROGRAM</option>
                                            <option value="CLINIC A">CLINIC A</option>
                                            <option value="CLINIC B">CLINIC B</option>
                                            <option value="CLINIC C">CLINIC C</option>
                                            <option value="CLINIC D">CLINIC D</option>
                                            <option value="PHILHEALTH - KP">PHILHEALTH - KP</option>
                                            <option value="PHILHEALTH - ASC">PHILHEALTH - ASC</option>
                                            <option value="PHILHEALTH - CLINIC A">PHILHEALTH - CLINIC A</option>
                                            <option value="DSWD">DSWD</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Search by Supply Name -->
                                    <div class="col-md-3">
                                        <label for="filter-supply-name" class="form-label fw-medium text-dark mb-1 small">
                                            <i class="fa-solid fa-box me-1"></i>Supply Name
                                        </label>
                                        <input type="text" id="filter-supply-name" class="form-control form-control-sm" placeholder="Search supply...">
                                    </div>
                                    
                                    <!-- Date From -->
                                    <div class="col-md-2">
                                        <label for="filter-date-from" class="form-label fw-medium text-dark mb-1 small">
                                            <i class="fa-solid fa-calendar me-1"></i>Date From
                                        </label>
                                        <input type="date" id="filter-date-from" class="form-control form-control-sm">
                                    </div>
                                    
                                    <!-- Date To -->
                                    <div class="col-md-2">
                                        <label for="filter-date-to" class="form-label fw-medium text-dark mb-1 small">
                                            <i class="fa-solid fa-calendar me-1"></i>Date To
                                        </label>
                                        <input type="date" id="filter-date-to" class="form-control form-control-sm">
                                    </div>
                                    
                                    <!-- Filter Button -->
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button id="apply-filters" class="btn btn-primary btn-sm w-100">
                                            <i class="fa-solid fa-filter me-1"></i>Apply Filters
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <button id="clear-filters" class="btn btn-link btn-sm text-muted p-0">
                                            <i class="fa-solid fa-times me-1"></i>Clear All Filters
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Bulk Actions Bar -->
                        <div class="bulk-actions-bar card mb-3" id="bulk-actions-bar" style="display: none;">
                            <div class="card-body py-2 px-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="fw-medium text-dark me-2">
                                            <i class="fa-solid fa-check-square me-1"></i>
                                            <span id="selected-count">0</span> selected
                                        </span>
                                    </div>
                                    <div>
                                        <button id="bulk-delete-btn" class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash me-1"></i>Delete Selected
                                        </button>
                                        <button id="deselect-all-btn" class="btn btn-secondary btn-sm">
                                            <i class="fa-solid fa-times me-1"></i>Deselect All
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Results Summary -->
                        <div class="results-summary mb-3">
                            <span class="text-muted small">
                                <i class="fa-solid fa-info-circle me-1"></i>
                                Total: <strong id="total-count">0</strong> pending releases
                            </span>
                        </div>
                        
                        <!-- Pending Releases Table -->
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle mb-0" id="pending-releases-table">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 40px;">
                                            <input type="checkbox" id="select-all-checkbox" class="form-check-input" title="Select/Deselect All">
                                        </th>
                                        <th class="fw-semibold text-dark">Supply Name</th>
                                        <th class="fw-semibold text-dark">Release Date</th>
                                        <th class="fw-semibold text-dark text-end">Quantity</th>
                                        <th class="fw-semibold text-dark">Released To (Dept)</th>
                                        <th class="fw-semibold text-dark">Released By</th>
                                        <th class="fw-semibold text-dark">Section</th>
                                        <th class="fw-semibold text-dark">Sub Section</th>
                                        <th class="fw-semibold text-dark text-center" style="width: 100px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="pending-releases-body">
                                    <tr>
                                        <td colspan="9" class="text-center py-5 text-muted">
                                            <i class="fa-solid fa-spinner fa-spin me-2"></i>Loading pending releases...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Loading Indicator -->
                        <div id="loading-indicator" class="text-center d-none py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        
                    </div>
                </div>

                <style>
                .release-cleanup-management {
                    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                    border: 1px solid rgba(0, 0, 0, 0.08);
                }
                
                .release-cleanup-management .card-header {
                    background-color: #f8f9fa;
                    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
                }
                
                .release-cleanup-management .form-control,
                .release-cleanup-management .form-select {
                    border-color: #dee2e6;
                    font-size: 0.9rem;
                }
                
                .release-cleanup-management .table {
                    font-size: 0.9rem;
                }
                
                .release-cleanup-management .table th {
                    font-weight: 600;
                    letter-spacing: 0.3px;
                    background-color: #f8f9fa;
                    border-bottom: 2px solid #dee2e6;
                }
                
                .release-cleanup-management .table td {
                    padding: 0.75rem;
                    vertical-align: middle;
                }
                
                .release-cleanup-management .table tbody tr:hover {
                    background-color: rgba(0, 123, 255, 0.02);
                }
                
                .release-cleanup-management .table tbody tr.selected {
                    background-color: rgba(13, 110, 253, 0.1);
                }
                
                .filters-section {
                    border: 1px solid #e0e0e0;
                }
                
                .bulk-actions-bar {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    border: none;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                }
                
                .bulk-actions-bar .card-body {
                    color: white;
                }
                
                .bulk-actions-bar .text-dark {
                    color: white !important;
                }
                
                .action-btn {
                    padding: 0.25rem 0.5rem;
                    font-size: 0.85rem;
                    transition: all 0.2s;
                }
                
                .action-btn:hover {
                    transform: translateY(-1px);
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                }
                
                .form-check-input {
                    cursor: pointer;
                    width: 18px;
                    height: 18px;
                }
                
                .badge {
                    font-weight: 500;
                    padding: 0.35em 0.65em;
                }
                
                @media (max-width: 768px) {
                    .release-cleanup-management .card-body {
                        padding: 1rem;
                    }
                    
                    .release-cleanup-management .table {
                        font-size: 0.8rem;
                    }
                    
                    .filters-section .col-md-3,
                    .filters-section .col-md-2 {
                        margin-bottom: 0.5rem;
                    }
                }
                </style>

                <script>
                jQuery(document).ready(function($) {
                    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
                    var selectedReleases = [];
                    var allReleases = [];
                    
                    // Load pending releases with filters
                    function loadPendingReleases() {
                        var department = $('#filter-department').val() || 'ALL';
                        var supplyName = $('#filter-supply-name').val().trim();
                        var dateFrom = $('#filter-date-from').val();
                        var dateTo = $('#filter-date-to').val();
                        
                        $('#pending-releases-body').html('<tr><td colspan="9" class="text-center py-5 text-muted"><i class="fa-solid fa-spinner fa-spin me-2"></i>Loading...</td></tr>');
                        
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'get_pending_releases_for_cleanup',
                                department: department,
                                supply_name: supplyName,
                                date_from: dateFrom,
                                date_to: dateTo,
                                nonce: '<?php echo wp_create_nonce("pending_releases_cleanup"); ?>'
                            },
                            success: function(response) {
                                if(response.success) {
                                    allReleases = response.data;
                                    displayReleases(allReleases);
                                    $('#total-count').text(allReleases.length);
                                } else {
                                    $('#pending-releases-body').html('<tr><td colspan="9" class="text-center text-danger py-4">Error: ' + response.data + '</td></tr>');
                                    $('#total-count').text('0');
                                }
                            },
                            error: function() {
                                $('#pending-releases-body').html('<tr><td colspan="9" class="text-center text-danger py-4">Server error occurred</td></tr>');
                                $('#total-count').text('0');
                            }
                        });
                    }
                    
                    // Display releases in table
                    function displayReleases(releases) {
                        var html = '';
                        
                        if(releases.length === 0) {
                            html = '<tr><td colspan="9" class="text-center py-5 text-muted"><i class="fa-solid fa-check-circle me-2"></i>No pending releases found</td></tr>';
                        } else {
                            releases.forEach(function(item) {
                                var isSelected = selectedReleases.includes(item.id);
                                var rowClass = isSelected ? 'selected' : '';
                                
                                html += '<tr class="' + rowClass + '" data-id="' + item.id + '">';
                                html += '<td class="text-center">';
                                html += '<input type="checkbox" class="form-check-input release-checkbox" data-id="' + item.id + '" ' + (isSelected ? 'checked' : '') + '>';
                                html += '</td>';
                                html += '<td class="text-dark fw-medium">' + item.supply_name + '</td>';
                                html += '<td>' + item.release_date + '</td>';
                                html += '<td class="text-end fw-medium">' + parseFloat(item.quantity).toFixed(2) + '</td>';
                                html += '<td>' + item.department + '</td>';
                                html += '<td>' + item.released_by + '</td>';
                                html += '<td>' + (item.section || '-') + '</td>';
                                html += '<td>' + (item.sub_section || '-') + '</td>';
                                html += '<td class="text-center">';
                                html += '<button class="btn btn-danger btn-sm action-btn delete-single" data-id="' + item.id + '" title="Delete this release">';
                                html += '<i class="fa-solid fa-trash"></i>';
                                html += '</button>';
                                html += '</td>';
                                html += '</tr>';
                            });
                        }
                        
                        $('#pending-releases-body').html(html);
                        updateSelectAllCheckbox();
                    }
                    
                    // Update selected count and show/hide bulk actions bar
                    function updateBulkActionsBar() {
                        var count = selectedReleases.length;
                        $('#selected-count').text(count);
                        
                        if(count > 0) {
                            $('#bulk-actions-bar').slideDown();
                        } else {
                            $('#bulk-actions-bar').slideUp();
                        }
                    }
                    
                    // Update select all checkbox state
                    function updateSelectAllCheckbox() {
                        var totalCheckboxes = $('.release-checkbox').length;
                        var checkedCheckboxes = $('.release-checkbox:checked').length;
                        
                        if(totalCheckboxes === 0) {
                            $('#select-all-checkbox').prop('checked', false);
                            $('#select-all-checkbox').prop('indeterminate', false);
                        } else if(checkedCheckboxes === 0) {
                            $('#select-all-checkbox').prop('checked', false);
                            $('#select-all-checkbox').prop('indeterminate', false);
                        } else if(checkedCheckboxes === totalCheckboxes) {
                            $('#select-all-checkbox').prop('checked', true);
                            $('#select-all-checkbox').prop('indeterminate', false);
                        } else {
                            $('#select-all-checkbox').prop('checked', false);
                            $('#select-all-checkbox').prop('indeterminate', true);
                        }
                    }
                    
                    // Select/Deselect All
                    $(document).on('change', '#select-all-checkbox', function() {
                        var isChecked = $(this).prop('checked');
                        $('.release-checkbox').prop('checked', isChecked);
                        
                        if(isChecked) {
                            selectedReleases = allReleases.map(function(r) { return r.id; });
                            $('tr[data-id]').addClass('selected');
                        } else {
                            selectedReleases = [];
                            $('tr[data-id]').removeClass('selected');
                        }
                        
                        updateBulkActionsBar();
                    });
                    
                    // Individual checkbox change
                    $(document).on('change', '.release-checkbox', function() {
                        var releaseId = parseInt($(this).data('id'));
                        var isChecked = $(this).prop('checked');
                        
                        if(isChecked) {
                            if(!selectedReleases.includes(releaseId)) {
                                selectedReleases.push(releaseId);
                            }
                            $('tr[data-id="' + releaseId + '"]').addClass('selected');
                        } else {
                            selectedReleases = selectedReleases.filter(function(id) { return id !== releaseId; });
                            $('tr[data-id="' + releaseId + '"]').removeClass('selected');
                        }
                        
                        updateSelectAllCheckbox();
                        updateBulkActionsBar();
                    });
                    
                    // Deselect all button
                    $('#deselect-all-btn').on('click', function() {
                        selectedReleases = [];
                        $('.release-checkbox').prop('checked', false);
                        $('tr[data-id]').removeClass('selected');
                        updateSelectAllCheckbox();
                        updateBulkActionsBar();
                    });
                    
                    // Bulk delete releases
                    $('#bulk-delete-btn').on('click', function() {
                        if(selectedReleases.length === 0) {
                            return;
                        }
                        
                        var confirmMessage = 'Are you sure you want to permanently delete ' + selectedReleases.length + ' release(s)?\n\nThis action cannot be undone.';
                        
                        if(confirm(confirmMessage)) {
                            deleteReleases(selectedReleases);
                        }
                    });
                    
                    // Delete single release
                    $(document).on('click', '.delete-single', function() {
                        var releaseId = parseInt($(this).data('id'));
                        
                        if(confirm('Are you sure you want to delete this release?\n\nThis action cannot be undone.')) {
                            deleteReleases([releaseId]);
                        }
                    });
                    
                    // Function to delete releases
                    function deleteReleases(releaseIds) {
                        $('#loading-indicator').removeClass('d-none');
                        
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'bulk_delete_pending_releases',
                                release_ids: releaseIds,
                                nonce: '<?php echo wp_create_nonce("bulk_delete_releases"); ?>'
                            },
                            success: function(response) {
                                $('#loading-indicator').addClass('d-none');
                                
                                if(response.success) {
                                    // Show success message
                                    showMessage('success', response.data.message || 'Successfully deleted ' + response.data.deleted_count + ' release(s)');
                                    
                                    // Reset selections
                                    selectedReleases = [];
                                    updateBulkActionsBar();
                                    
                                    // Reload the table
                                    loadPendingReleases();
                                } else {
                                    showMessage('error', 'Error: ' + response.data);
                                }
                            },
                            error: function() {
                                $('#loading-indicator').addClass('d-none');
                                showMessage('error', 'Server error occurred. Please try again.');
                            }
                        });
                    }
                    
                    // Show message
                    function showMessage(type, message) {
                        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                        var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
                        
                        var alert = $('<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                            '<i class="fa-solid ' + icon + ' me-2"></i>' + message +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>');
                        
                        $('.release-cleanup-management .card-body').prepend(alert);
                        
                        setTimeout(function() {
                            alert.fadeOut(function() {
                                $(this).remove();
                            });
                        }, 5000);
                    }
                    
                    // Apply filters
                    $('#apply-filters').on('click', function() {
                        selectedReleases = [];
                        updateBulkActionsBar();
                        loadPendingReleases();
                    });
                    
                    // Clear filters
                    $('#clear-filters').on('click', function() {
                        $('#filter-department').val('ALL');
                        $('#filter-supply-name').val('');
                        $('#filter-date-from').val('');
                        $('#filter-date-to').val('');
                        selectedReleases = [];
                        updateBulkActionsBar();
                        loadPendingReleases();
                    });
                    
                    // Enter key on search
                    $('#filter-supply-name').on('keypress', function(e) {
                        if(e.which === 13) {
                            $('#apply-filters').click();
                        }
                    });
                    
                    // Initial load
                    loadPendingReleases();
                });
                </script>
            </main>
        </div>
    <?php } ?>
<?php else :
    get_template_part( 'templates/content', 'none' );
endif;

get_footer();
