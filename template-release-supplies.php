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

                <div class="filtered-release-supplies">
                    <h2>Filtered Release Supplies</h2>
                    <div class="date-filter">
                        <div class="filter-group">
                            <label>From Date:</label>
                            <input type="date" id="filter-from-date" value="<?php echo date('Y-m-d', strtotime('last day of this month')); ?>">
                        </div>
                        <div class="filter-group">
                            <label>To Date:</label>
                            <input type="date" id="filter-to-date" value="<?php echo date('Y-m-d', strtotime('first day of this month')); ?>">
                        </div>
                        <button id="filter-search" class="button button-primary">Search</button>
                    </div>
                    <div class="filtered-results">
                        <table>
                            <thead>
                                <tr>
                                    <th>Equipment / Supply Name</th>
                                    <th>Total Quantity Released</th>
                                </tr>
                            </thead>
                            <tbody id="filtered-results-body">
                            </tbody>
                        </table>
                    </div>
                </div>

                <style>
                .filtered-release-supplies {
                    margin-top: 40px;
                    padding: 20px;
                    background: #f9f9f9;
                    border-radius: 8px;
                }
                .filtered-release-supplies h2 {
                    margin-bottom: 20px;
                    color: #333;
                }
                .date-filter {
                    display: flex;
                    gap: 20px;
                    margin-bottom: 20px;
                    align-items: flex-end;
                }
                .filter-group {
                    display: flex;
                    flex-direction: column;
                    gap: 5px;
                }
                .filter-group label {
                    font-weight: 500;
                    color: #666;
                }
                .filter-group input {
                    padding: 8px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                }
                #filter-search {
                    padding: 8px 16px;
                    height: 38px;
                }
                .filtered-results table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }
                .filtered-results th,
                .filtered-results td {
                    padding: 12px;
                    text-align: left;
                    border-bottom: 1px solid #ddd;
                }
                .filtered-results th {
                    background: #f5f5f5;
                    font-weight: 600;
                }
                @media (max-width: 768px) {
                    .date-filter {
                        flex-direction: column;
                        gap: 15px;
                    }
                    .filter-group {
                        width: 100%;
                    }
                    #filter-search {
                        width: 100%;
                    }
                }
                </style>

                <script>
                jQuery(document).ready(function($) {
                    function loadFilteredResults() {
                        var fromDate = $('#filter-from-date').val();
                        var toDate = $('#filter-to-date').val();
                        
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
                                    response.data.forEach(function(item) {
                                        html += '<tr>';
                                        html += '<td>' + item.supply_name + '</td>';
                                        html += '<td>' + item.total_quantity + '</td>';
                                        html += '</tr>';
                                    });
                                    $('#filtered-results-body').html(html);
                                }
                            }
                        });
                    }

                    $('#filter-search').on('click', loadFilteredResults);
                    loadFilteredResults(); // Load initial results
                });
                </script>
			</main>
		</div>
	<?php } ?>
<?php else :
	get_template_part( 'templates/content', 'none' );
endif;

get_footer();