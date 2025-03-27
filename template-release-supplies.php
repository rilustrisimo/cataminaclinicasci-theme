<?php
/**
 * Template Name: Release Supply
 *
 * @author    eyorsogood.com, Rouie Ilustrisimo
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
                <div class="custom-post__add-form" form-id="134">
				    <?php $theme->createAcfForm(134, 'releasesupplies', '<i class="fa-solid fa-plus"></i> Add Release Supply'); ?>
                </div>
                <div class="custom-post__search"><div class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></div><input type="text" class="search-ajax" placeholder="Search Name"></div>
                <div class="custom-post__list" data-pt="releasesupplies">
                    <?php 
                    $header = array(
                        'supply_name' => 'Equipment / Supply Name',
                        'release_date' => 'Date Released',
                        'quantity' => 'Quantity'
                    );

                    $theme->createCustomPostListHtml('releasesupplies', 20, $header);
                    ?>
                </div>

                <div class="filtered-release-supplies card mt-5">
                    <div class="card-header d-flex justify-content-between align-items-center py-3">
                        <h2 class="mb-0 fs-5 fw-semibold text-dark">Filtered Release Supplies</h2>
                    </div>
                    <div class="card-body p-4">
                        <div class="date-filter row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filter-from-date" class="form-label small fw-medium text-muted mb-1">From Date</label>
                                    <input type="date" id="filter-from-date" class="form-control form-control-sm" value="<?php echo date('Y-m-d', strtotime('first day of this month')); ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filter-to-date" class="form-label small fw-medium text-muted mb-1">To Date</label>
                                    <input type="date" id="filter-to-date" class="form-control form-control-sm" value="<?php echo date('Y-m-d', strtotime('last day of this month')); ?>">
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button id="filter-search" class="btn btn-primary btn-sm w-100">
                                    <i class="fa-solid fa-search me-1"></i>Search
                                </button>
                            </div>
                        </div>
                        <div class="filtered-results">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr class="bg-light">
                                            <th class="small fw-semibold text-muted py-2">Equipment / Supply Name</th>
                                            <th class="small fw-semibold text-muted py-2 text-end">Total Quantity Released</th>
                                        </tr>
                                    </thead>
                                    <tbody id="filtered-results-body" class="small">
                                    </tbody>
                                </table>
                            </div>
                            <div id="loading-indicator" class="text-center d-none py-4">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                .filtered-release-supplies {
                    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                    border: 1px solid rgba(0, 0, 0, 0.08);
                }
                .filtered-release-supplies .card-header {
                    background-color: #f8f9fa;
                    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
                }
                .filtered-release-supplies .form-control {
                    border-color: #dee2e6;
                    font-size: 0.875rem;
                }
                .filtered-release-supplies .form-control:focus {
                    border-color: #80bdff;
                    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
                }
                .filtered-release-supplies .btn-primary {
                    font-size: 0.875rem;
                    padding: 0.375rem 0.75rem;
                    font-weight: 500;
                }
                .filtered-release-supplies .table {
                    font-size: 0.875rem;
                }
                .filtered-release-supplies .table th {
                    font-weight: 600;
                    letter-spacing: 0.3px;
                }
                .filtered-release-supplies .table td {
                    padding: 0.75rem 1rem;
                }
                .filtered-release-supplies .table tbody tr {
                    border-bottom: 1px solid rgba(0, 0, 0, 0.04);
                }
                .filtered-release-supplies .table tbody tr:last-child {
                    border-bottom: none;
                }
                .filtered-release-supplies .table tbody tr:hover {
                    background-color: rgba(0, 123, 255, 0.02);
                }
                @media (max-width: 768px) {
                    .filtered-release-supplies .card-body {
                        padding: 1rem;
                    }
                    .filtered-release-supplies .date-filter > div {
                        margin-bottom: 0.5rem;
                    }
                }
                </style>

                <script>
                jQuery(document).ready(function($) {
                    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
                    
                    function loadFilteredResults() {
                        var fromDate = $('#filter-from-date').val();
                        var toDate = $('#filter-to-date').val();
                        
                        // Show loading indicator
                        $('#loading-indicator').removeClass('d-none');
                        $('#filtered-results-body').html('');
                        
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'get_filtered_release_supplies',
                                from_date: fromDate,
                                to_date: toDate,
                                nonce: '<?php echo wp_create_nonce("filter_release_supplies"); ?>'
                            },
                            success: function(response) {
                                if(response.success) {
                                    var html = '';
                                    if(response.data.length === 0) {
                                        html = '<tr><td colspan="2" class="text-center text-muted py-3">No results found</td></tr>';
                                    } else {
                                        response.data.forEach(function(item) {
                                            html += '<tr>';
                                            html += '<td class="text-dark">' + item.supply_name + '</td>';
                                            html += '<td class="text-end fw-medium">' + item.total_quantity + '</td>';
                                            html += '</tr>';
                                        });
                                    }
                                    $('#filtered-results-body').html(html);
                                } else {
                                    $('#filtered-results-body').html('<tr><td colspan="2" class="text-center text-danger py-3">Error loading results</td></tr>');
                                }
                            },
                            error: function() {
                                $('#filtered-results-body').html('<tr><td colspan="2" class="text-center text-danger py-3">Error loading results</td></tr>');
                            },
                            complete: function() {
                                // Hide loading indicator
                                $('#loading-indicator').addClass('d-none');
                            }
                        });
                    }

                    $('#filter-search').on('click', loadFilteredResults);
                    
                    // Initialize tooltips
                    $('[data-bs-toggle="tooltip"]').tooltip();
                    
                    // Load initial results
                    loadFilteredResults();
                });
                </script>
			</main>
		</div>
	<?php } ?>
<?php else :
	get_template_part( 'templates/content', 'none' );
endif;

get_footer();