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
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Filtered Release Supplies</h2>
                    </div>
                    <div class="card-body">
                        <div class="date-filter row mb-4">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filter-from-date" class="form-label">From Date:</label>
                                    <input type="date" id="filter-from-date" class="form-control" value="<?php echo date('Y-m-d', strtotime('last day of this month')); ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filter-to-date" class="form-label">To Date:</label>
                                    <input type="date" id="filter-to-date" class="form-control" value="<?php echo date('Y-m-d', strtotime('first day of this month')); ?>">
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button id="filter-search" class="btn btn-primary">
                                    <i class="fa-solid fa-search me-2"></i>Search
                                </button>
                            </div>
                        </div>
                        <div class="filtered-results">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Equipment / Supply Name</th>
                                            <th class="text-end">Total Quantity Released</th>
                                        </tr>
                                    </thead>
                                    <tbody id="filtered-results-body">
                                    </tbody>
                                </table>
                            </div>
                            <div id="loading-indicator" class="text-center d-none">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                .filtered-release-supplies {
                    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                }
                .filtered-release-supplies .card-header {
                    background-color: #f8f9fa;
                    border-bottom: 1px solid #e9ecef;
                }
                .filtered-release-supplies h2 {
                    color: #333;
                    font-size: 1.25rem;
                }
                .filtered-release-supplies .form-label {
                    font-weight: 500;
                    color: #666;
                }
                .filtered-release-supplies .form-control {
                    border-color: #dee2e6;
                }
                .filtered-release-supplies .form-control:focus {
                    border-color: #80bdff;
                    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
                }
                .filtered-release-supplies .btn-primary {
                    min-width: 120px;
                }
                .filtered-release-supplies .table {
                    margin-bottom: 0;
                }
                .filtered-release-supplies .table th {
                    font-weight: 600;
                }
                .filtered-release-supplies .table td {
                    vertical-align: middle;
                }
                @media (max-width: 768px) {
                    .filtered-release-supplies .date-filter > div {
                        margin-bottom: 1rem;
                    }
                    .filtered-release-supplies .btn-primary {
                        width: 100%;
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
                                        html = '<tr><td colspan="2" class="text-center">No results found</td></tr>';
                                    } else {
                                        response.data.forEach(function(item) {
                                            html += '<tr>';
                                            html += '<td>' + item.supply_name + '</td>';
                                            html += '<td class="text-end">' + item.total_quantity + '</td>';
                                            html += '</tr>';
                                        });
                                    }
                                    $('#filtered-results-body').html(html);
                                } else {
                                    $('#filtered-results-body').html('<tr><td colspan="2" class="text-center text-danger">Error loading results</td></tr>');
                                }
                            },
                            error: function() {
                                $('#filtered-results-body').html('<tr><td colspan="2" class="text-center text-danger">Error loading results</td></tr>');
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