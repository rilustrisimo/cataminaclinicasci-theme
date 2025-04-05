<?php
/**
 * Template Name: Released Supplies Management
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

$theme = new Theme();

get_header();
if ( have_posts() ) : ?>
    <?php while ( have_posts() ) { the_post(); ?>
        <div class="page-single">
            <main class="page-single__content" role="main">
                <div class="release-management card mt-5">
                    <div class="card-header d-flex justify-content-between align-items-center py-3">
                        <h2 class="mb-0 fs-5 fw-semibold text-dark">Released Supplies Management</h2>
                    </div>
                    <div class="card-body p-4">
                        <?php
                        // Get current user's roles
                        $user = wp_get_current_user();
                        $roles = $user->roles;
                        $user_id = $user->ID;
                        
                        // Department options with their IDs
                        $departments = array(
                            'ALL' => 0,
                            'NURSING' => 7,
                            'LABORATORY' => 6,
                            'PHARMACY' => 4,
                            'HOUSEKEEPING' => 8,
                            'MAINTENANCE' => 8,
                            'RADIOLOGY' => 5,
                            'BUSINESS OFFICE' => 9,
                            'INFORMATION / TRIAGE' => 10,
                            'PHYSICAL THERAPY' => 14,
                            'KONSULTA PROGRAM' => 11,
                            'CLINIC A' => 12,
                            'CLINIC B' => 12,
                            'CLINIC C' => 12,
                            'CLINIC D' => 12,
                            'PHILHEALTH - KP' => 11,
                            'PHILHEALTH - ASC' => 7,
                            'PHILHEALTH - CLINIC A' => 12,
                            'DSWD' => 10,
                        );
                        
                        // Check if user has advanced permissions
                        $has_advanced_access = current_user_can('manage_options') || in_array('um_accounting', $roles) || $user_id == 4;
                        
                        // If admin, accounting, or pharmacy - show department filter
                        if($has_advanced_access): ?>
                            <div class="department-filter row g-3 mb-4">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="filter-department" class="form-label fw-medium text-dark mb-1">
                                            <i class="fa-solid fa-building me-1"></i>Department
                                        </label>
                                        <div class="input-group">
                                            <select id="filter-department" class="form-select">
                                                <?php foreach($departments as $department => $id): ?>
                                                    <option value="<?php echo esc_attr($department); ?>"><?php echo esc_html($department); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button id="filter-releases" class="btn btn-primary">
                                                <i class="fa-solid fa-search me-1"></i>Filter
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Pending Releases Section -->
                        <h3 class="fs-6 fw-semibold text-dark mb-3">Pending Confirmation</h3>
                        <div class="table-responsive mb-5">
                            <table class="table table-hover align-middle mb-0" id="pending-releases-table">
                                <thead>
                                    <tr class="bg-light">
                                        <th class="fw-semibold text-dark py-2">Supply Name</th>
                                        <th class="fw-semibold text-dark py-2">Release Date</th>
                                        <th class="fw-semibold text-dark py-2 text-end">Quantity</th>
                                        <th class="fw-semibold text-dark py-2 text-end">Price per Unit</th>
                                        <th class="fw-semibold text-dark py-2 text-end">Total</th>
                                        <th class="fw-semibold text-dark py-2">Released By</th>
                                        <th class="fw-semibold text-dark py-2 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="pending-releases-body">
                                    <tr>
                                        <td colspan="7" class="text-center py-4">Loading pending releases...</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <td colspan="4" class="fw-bold text-end">Total Amount:</td>
                                        <td id="pending-total" class="fw-bold text-end">₱0.00</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <!-- Confirmed Releases Section -->
                        <h3 class="fs-6 fw-semibold text-dark mb-3">Confirmed Releases</h3>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="confirmed-releases-table">
                                <thead>
                                    <tr class="bg-light">
                                        <th class="fw-semibold text-dark py-2">Supply Name</th>
                                        <th class="fw-semibold text-dark py-2">Release Date</th>
                                        <th class="fw-semibold text-dark py-2 text-end">Quantity</th>
                                        <th class="fw-semibold text-dark py-2 text-end">Price per Unit</th>
                                        <th class="fw-semibold text-dark py-2 text-end">Total</th>
                                        <th class="fw-semibold text-dark py-2">Released By</th>
                                        <th class="fw-semibold text-dark py-2 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="confirmed-releases-body">
                                    <tr>
                                        <td colspan="7" class="text-center py-4">Loading confirmed releases...</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <td colspan="4" class="fw-bold text-end">Current Page Total:</td>
                                        <td id="confirmed-page-total" class="fw-bold text-end">₱0.00</td>
                                        <td colspan="2"></td>
                                    </tr>
                                    <tr class="bg-light">
                                        <td colspan="4" class="fw-bold text-end">Overall Total:</td>
                                        <td id="confirmed-total" class="fw-bold text-end">₱0.00</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                            
                            <!-- Pagination for confirmed releases -->
                            <div class="d-flex justify-content-between align-items-center mt-3" id="confirmed-pagination-container">
                                <div class="pagination-info">
                                    Showing <span id="pagination-start">0</span> to <span id="pagination-end">0</span> of <span id="pagination-total">0</span> entries
                                </div>
                                <nav aria-label="Confirmed releases pagination">
                                    <ul class="pagination pagination-sm mb-0" id="confirmed-pagination">
                                        <!-- Pagination will be generated here -->
                                    </ul>
                                </nav>
                            </div>
                        </div>
                        
                        <!-- Loading Indicator -->
                        <div id="loading-indicator" class="text-center d-none py-4">
                            <div class="spinner"></div>
                        </div>
                        
                        <style>
                        .release-management {
                            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                            border: 1px solid rgba(0, 0, 0, 0.08);
                        }
                        .release-management .card-header {
                            background-color: #f8f9fa;
                            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
                        }
                        .release-management .form-control,
                        .release-management .form-select {
                            border-color: #dee2e6;
                            font-size: 0.95rem;
                            padding: 0.5rem 0.75rem;
                            height: 42px;
                        }
                        
                        .release-management .input-group .form-select {
                            border-top-right-radius: 0;
                            border-bottom-right-radius: 0;
                            flex: 1;
                            min-width: 0;
                        }
                        
                        .release-management .input-group .btn-primary {
                            border-top-left-radius: 0;
                            border-bottom-left-radius: 0;
                            font-weight: 500;
                            padding: 0.5rem 1.25rem;
                            height: 42px;
                        }
                        
                        .release-management .table {
                            font-size: 0.95rem;
                        }
                        .release-management .table th {
                            font-weight: 600;
                            letter-spacing: 0.3px;
                        }
                        .release-management .table td {
                            padding: 0.75rem 1rem;
                        }
                        .release-management .table tbody tr {
                            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
                        }
                        .release-management .table tbody tr:last-child {
                            border-bottom: none;
                        }
                        .release-management .table tbody tr:hover {
                            background-color: rgba(0, 123, 255, 0.02);
                        }
                        
                        .action-btn {
                            padding: 0.25rem 0.5rem;
                            font-size: 0.85rem;
                        }
                        .action-btn:hover {
                            transform: translateY(-1px);
                        }
                        
                        /* Custom Loader */
                        .custom-loader {
                            width: 50px;
                            height: 50px;
                            border-radius: 50%;
                            background: 
                                radial-gradient(farthest-side,#007bff 94%,#0000) top/8px 8px no-repeat,
                                conic-gradient(#0000 30%,#007bff);
                            -webkit-mask: radial-gradient(farthest-side,#0000 calc(100% - 8px),#000 0);
                            animation: spinner-animation 1s infinite linear;
                            margin: 0 auto;
                        }

                        @keyframes spinner-animation {
                            100% {
                                transform: rotate(360deg);
                            }
                        }

                        /* Clean Spinner */
                        .spinner {
                            width: 40px;
                            height: 40px;
                            margin: 0 auto;
                            border: 4px solid rgba(0, 123, 255, 0.1);
                            border-left-color: #007bff;
                            border-radius: 50%;
                            animation: spinner-animation 1s linear infinite;
                        }
                        
                        @keyframes spinner-animation {
                            to {
                                transform: rotate(360deg);
                            }
                        }

                        /* Status badges */
                        .badge-pending {
                            background-color: #ffc107;
                            color: #212529;
                        }
                        .badge-confirmed {
                            background-color: #28a745;
                            color: #fff;
                        }
                        
                        /* Table empty state */
                        .table-empty-state {
                            padding: 2rem;
                            text-align: center;
                            color: #6c757d;
                        }
                        
                        /* Pagination styling */
                        .pagination {
                            list-style: none;
                            padding-left: 0;
                        }
                        
                        .pagination .page-item {
                            display: inline-block;
                            margin: 0 2px;
                        }
                        
                        .pagination .page-link {
                            padding: 0.25rem 0.5rem;
                            font-size: 0.875rem;
                            border-radius: 3px;
                            color: #007bff;
                            background-color: #fff;
                            border: 1px solid #dee2e6;
                            text-decoration: none;
                            transition: all 0.2s;
                        }
                        
                        .pagination .page-item.active .page-link {
                            color: #fff;
                            background-color: #007bff;
                            border-color: #007bff;
                        }
                        
                        .pagination .page-item.disabled .page-link {
                            color: #6c757d;
                            background-color: #fff;
                            border-color: #dee2e6;
                            pointer-events: none;
                        }
                        
                        .pagination .page-link:hover:not(.active) {
                            background-color: #e9ecef;
                            border-color: #dee2e6;
                        }
                        
                        @media (max-width: 768px) {
                            .release-management .card-body {
                                padding: 1rem;
                            }
                            .release-management .department-filter > div {
                                margin-bottom: 0.5rem;
                            }
                            .release-management .card-header {
                                flex-direction: column;
                                gap: 1rem;
                                align-items: stretch !important;
                            }
                            .release-management .input-group {
                                flex-wrap: nowrap;
                            }
                            .release-management .input-group .form-select {
                                width: 75%;
                            }
                            .release-management .input-group .btn-primary {
                                width: 25%;
                                padding-left: 0.5rem;
                                padding-right: 0.5rem;
                            }
                        }
                        </style>

                <script>
                jQuery(document).ready(function($) {
                    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
                    var userId = '<?php echo esc_js($user_id); ?>';
                    var hasAdvancedAccess = <?php echo json_encode($has_advanced_access); ?>;
                    var itemsPerPage = 50;
                    var currentConfirmedPage = 1;
                    var allConfirmedItems = [];
                    
                    // Function to load releases based on status and department
                    function loadReleases(status) {
                        var department = $('#filter-department').val() || 'ALL';
                        var tableId = (status === 'pending') ? '#pending-releases-body' : '#confirmed-releases-body';
                        var totalId = (status === 'pending') ? '#pending-total' : '#confirmed-total';
                        
                        $(tableId).html('<tr><td colspan="7" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
                        
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'get_department_releases',
                                status: status,
                                department: department,
                                user_id: userId,
                                nonce: '<?php echo wp_create_nonce("get_department_releases"); ?>'
                            },
                            success: function(response) {
                                if(response.success) {
                                    if(status === 'pending') {
                                        displayPendingReleases(response.data);
                                    } else {
                                        // Store all confirmed items and display first page
                                        allConfirmedItems = response.data;
                                        currentConfirmedPage = 1;
                                        displayConfirmedReleases();
                                    }
                                } else {
                                    $(tableId).html('<tr><td colspan="7" class="text-center text-danger py-4">Error loading releases</td></tr>');
                                }
                            },
                            error: function() {
                                $(tableId).html('<tr><td colspan="7" class="text-center text-danger py-4">Error loading releases</td></tr>');
                            }
                        });
                    }
                    
                    // Function to display pending releases
                    function displayPendingReleases(items) {
                        var html = '';
                        var totalAmount = 0;
                        
                        if(items.length === 0) {
                            html = '<tr><td colspan="7" class="text-center py-4">No pending releases found</td></tr>';
                        } else {
                            items.forEach(function(item) {
                                var quantity = parseFloat(item.quantity) || 0;
                                var pricePerUnit = parseFloat(item.price_per_unit) || 0;
                                var totalPrice = quantity * pricePerUnit;
                                totalAmount += totalPrice;
                                
                                html += '<tr data-id="' + item.id + '">';
                                html += '<td class="text-dark">' + item.supply_name + '</td>';
                                html += '<td>' + item.release_date + '</td>';
                                html += '<td class="text-end fw-medium text-dark">' + quantity + '</td>';
                                html += '<td class="text-end fw-medium text-dark">₱' + pricePerUnit.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</td>';
                                html += '<td class="text-end fw-medium text-dark">₱' + totalPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</td>';
                                html += '<td class="text-dark">' + item.released_by + '</td>';
                                html += '<td class="text-center">';
                                html += '<button class="btn btn-sm btn-success action-btn confirm-release" data-id="' + item.id + '"><i class="fa-solid fa-check me-1"></i>Confirm</button>';
                                html += '</td>';
                                html += '</tr>';
                            });
                        }
                        
                        $('#pending-releases-body').html(html);
                        $('#pending-total').text('₱' + totalAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    }
                    
                    // Function to display paginated confirmed releases
                    function displayConfirmedReleases() {
                        var totalItems = allConfirmedItems.length;
                        var totalPages = Math.ceil(totalItems / itemsPerPage);
                        var startIndex = (currentConfirmedPage - 1) * itemsPerPage;
                        var endIndex = Math.min(startIndex + itemsPerPage, totalItems);
                        var currentPageItems = allConfirmedItems.slice(startIndex, endIndex);
                        
                        var html = '';
                        var totalAmount = calculateTotalAmount(allConfirmedItems);
                        var pageAmount = calculateTotalAmount(currentPageItems);
                        
                        if(totalItems === 0) {
                            html = '<tr><td colspan="7" class="text-center py-4">No confirmed releases found</td></tr>';
                        } else {
                            currentPageItems.forEach(function(item) {
                                var quantity = parseFloat(item.quantity) || 0;
                                var pricePerUnit = parseFloat(item.price_per_unit) || 0;
                                var totalPrice = quantity * pricePerUnit;
                                
                                html += '<tr data-id="' + item.id + '">';
                                html += '<td class="text-dark">' + item.supply_name + '</td>';
                                html += '<td>' + item.release_date + '</td>';
                                html += '<td class="text-end fw-medium text-dark">' + quantity + '</td>';
                                html += '<td class="text-end fw-medium text-dark">₱' + pricePerUnit.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</td>';
                                html += '<td class="text-end fw-medium text-dark">₱' + totalPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</td>';
                                html += '<td class="text-dark">' + item.released_by + '</td>';
                                html += '<td class="text-center">';
                                html += '<button class="btn btn-sm btn-warning action-btn revert-confirmation" data-id="' + item.id + '"><i class="fa-solid fa-rotate-left me-1"></i>Revert</button>';
                                html += '</td>';
                                html += '</tr>';
                            });
                        }
                        
                        $('#confirmed-releases-body').html(html);
                        $('#confirmed-page-total').text('₱' + pageAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        $('#confirmed-total').text('₱' + totalAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        
                        // Update pagination information
                        $('#pagination-start').text(totalItems > 0 ? startIndex + 1 : 0);
                        $('#pagination-end').text(endIndex);
                        $('#pagination-total').text(totalItems);
                        
                        // Generate pagination
                        generatePagination(totalPages);
                    }
                    
                    // Calculate total amount for all items
                    function calculateTotalAmount(items) {
                        var totalAmount = 0;
                        items.forEach(function(item) {
                            var quantity = parseFloat(item.quantity) || 0;
                            var pricePerUnit = parseFloat(item.price_per_unit) || 0;
                            totalAmount += quantity * pricePerUnit;
                        });
                        return totalAmount;
                    }
                    
                    // Generate pagination links
                    function generatePagination(totalPages) {
                        var paginationHtml = '';
                        
                        // Previous button
                        paginationHtml += '<li class="page-item' + (currentConfirmedPage === 1 ? ' disabled' : '') + '">';
                        paginationHtml += '<a class="page-link" href="#" data-page="prev" aria-label="Previous">';
                        paginationHtml += '<span aria-hidden="true">&laquo;</span>';
                        paginationHtml += '</a></li>';
                        
                        // Page numbers
                        var startPage = Math.max(1, currentConfirmedPage - 2);
                        var endPage = Math.min(totalPages, startPage + 4);
                        
                        if (endPage - startPage < 4 && startPage > 1) {
                            startPage = Math.max(1, endPage - 4);
                        }
                        
                        // First page
                        if (startPage > 1) {
                            paginationHtml += '<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>';
                            if (startPage > 2) {
                                paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                        }
                        
                        // Page numbers
                        for (var i = startPage; i <= endPage; i++) {
                            paginationHtml += '<li class="page-item' + (i === currentConfirmedPage ? ' active' : '') + '">';
                            paginationHtml += '<a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
                        }
                        
                        // Last page
                        if (endPage < totalPages) {
                            if (endPage < totalPages - 1) {
                                paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                            paginationHtml += '<li class="page-item"><a class="page-link" href="#" data-page="' + totalPages + '">' + totalPages + '</a></li>';
                        }
                        
                        // Next button
                        paginationHtml += '<li class="page-item' + (currentConfirmedPage === totalPages || totalPages === 0 ? ' disabled' : '') + '">';
                        paginationHtml += '<a class="page-link" href="#" data-page="next" aria-label="Next">';
                        paginationHtml += '<span aria-hidden="true">&raquo;</span>';
                        paginationHtml += '</a></li>';
                        
                        $('#confirmed-pagination').html(paginationHtml);
                        
                        // Hide pagination if only one page
                        if(totalPages <= 1) {
                            $('#confirmed-pagination-container').hide();
                        } else {
                            $('#confirmed-pagination-container').show();
                        }
                    }

                    // Function to update release status
                    function updateReleaseStatus(releaseId, newStatus) {
                        $('#loading-indicator').removeClass('d-none');
                        
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'update_release_status',
                                release_id: releaseId,
                                status: newStatus,
                                nonce: '<?php echo wp_create_nonce("update_release_status"); ?>'
                            },
                            success: function(response) {
                                if(response.success) {
                                    // Show success message
                                    $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                      'Status updated successfully!' +
                                      '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                      '</div>').insertBefore('#pending-releases-table').delay(3000).fadeOut(function() {
                                          $(this).remove();
                                      });
                                    
                                    // Reload both tables
                                    loadReleases('pending');
                                    loadReleases('confirmed');
                                } else {
                                    // Show error message
                                    $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                      'Failed to update status: ' + response.data +
                                      '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                      '</div>').insertBefore('#pending-releases-table').delay(3000).fadeOut(function() {
                                          $(this).remove();
                                      });
                                }
                                $('#loading-indicator').addClass('d-none');
                            },
                            error: function() {
                                // Show error message
                                $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                  'Server error occurred. Please try again.' +
                                  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                  '</div>').insertBefore('#pending-releases-table').delay(3000).fadeOut(function() {
                                      $(this).remove();
                                  });
                                $('#loading-indicator').addClass('d-none');
                            }
                        });
                    }

                    // Event handlers
                    $('#filter-releases').on('click', function() {
                        loadReleases('pending');
                        loadReleases('confirmed');
                    });
                    
                    // Event delegation for dynamically created buttons
                    $(document).on('click', '.confirm-release', function() {
                        var releaseId = $(this).data('id');
                        updateReleaseStatus(releaseId, 'confirmed');
                    });
                    
                    $(document).on('click', '.revert-confirmation', function() {
                        var releaseId = $(this).data('id');
                        updateReleaseStatus(releaseId, 'pending');
                    });
                    
                    // Pagination click event
                    $(document).on('click', '#confirmed-pagination .page-link', function(e) {
                        e.preventDefault();
                        var page = $(this).data('page');
                        
                        if (page === 'prev') {
                            if (currentConfirmedPage > 1) {
                                currentConfirmedPage--;
                            }
                        } else if (page === 'next') {
                            var totalPages = Math.ceil(allConfirmedItems.length / itemsPerPage);
                            if (currentConfirmedPage < totalPages) {
                                currentConfirmedPage++;
                            }
                        } else {
                            currentConfirmedPage = parseInt(page);
                        }
                        
                        displayConfirmedReleases();
                        
                        // Scroll to top of table
                        $('#confirmed-releases-table').get(0).scrollIntoView({ behavior: 'smooth', block: 'start' });
                    });
                    
                    // Load initial data
                    loadReleases('pending');
                    loadReleases('confirmed');
                });
                </script>
            </main>
        </div>
    <?php } ?>
<?php else :
    get_template_part( 'templates/content', 'none' );
endif;

get_footer();