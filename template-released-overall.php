<?php
/**
 * Template Name: Released Supply Overall
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
                <div class="filtered-release-supplies card mt-5">
                    <div class="card-header d-flex justify-content-between align-items-center py-3">
                        <h2 class="mb-0 fs-5 fw-semibold text-dark">Filtered Release Supplies</h2>
                        <button id="export-pdf" class="btn btn-outline-primary btn-sm">
                            <i class="fa-solid fa-file-pdf me-1"></i>Export PDF
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <div class="date-filter row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filter-from-date" class="form-label fw-medium text-dark mb-1">From Date</label>
                                    <input type="date" id="filter-from-date" class="form-control" value="<?php echo date('Y-m-d', strtotime('first day of this month')); ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filter-to-date" class="form-label fw-medium text-dark mb-1">To Date</label>
                                    <input type="date" id="filter-to-date" class="form-control" value="<?php echo date('Y-m-d', strtotime('last day of this month')); ?>">
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-center">
                                <button id="filter-search" class="btn btn-primary w-100">
                                    <i class="fa-solid fa-search me-1"></i>Search
                                </button>
                            </div>
                        </div>
                        <div class="filtered-results">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr class="bg-light">
                                            <th class="fw-semibold text-dark py-2">Equipment / Supply Name</th>
                                            <th class="fw-semibold text-dark py-2 text-end">Quantity</th>
                                            <th class="fw-semibold text-dark py-2 text-end">Price per Unit</th>
                                            <th class="fw-semibold text-dark py-2 text-end">Total Price</th>
                                        </tr>
                                    </thead>
                                    <tbody id="filtered-results-body">
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-light">
                                            <td colspan="3" class="fw-bold text-end">Total Amount:</td>
                                            <td id="grand-total" class="fw-bold text-end">₱0.00</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div id="loading-indicator" class="text-center d-none py-4">
                                <div class="custom-loader"></div>
                                <div class="mt-2 text-dark">Loading results...</div>
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
                    font-size: 0.95rem;
                    padding: 0.5rem 0.75rem;
                }
                .filtered-release-supplies .form-control:focus {
                    border-color: #80bdff;
                    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
                }
                .filtered-release-supplies .btn-primary {
                    font-size: 0.95rem;
                    padding: 0.5rem 1rem;
                    font-weight: 500;
                }
                .filtered-release-supplies .btn-outline-primary {
                    font-size: 0.875rem;
                    padding: 0.375rem 0.75rem;
                    font-weight: 500;
                }
                .filtered-release-supplies .table {
                    font-size: 0.95rem;
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

                @media (max-width: 768px) {
                    .filtered-release-supplies .card-body {
                        padding: 1rem;
                    }
                    .filtered-release-supplies .date-filter > div {
                        margin-bottom: 0.5rem;
                    }
                    .filtered-release-supplies .card-header {
                        flex-direction: column;
                        gap: 1rem;
                        align-items: stretch !important;
                    }
                    .filtered-release-supplies .btn-outline-primary {
                        width: 100%;
                    }
                }
                </style>

                <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
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
                                    var grandTotal = 0;
                                    
                                    if(response.data.length === 0) {
                                        html = '<tr><td colspan="4" class="text-center text-dark py-3">No results found</td></tr>';
                                        $('#export-pdf').prop('disabled', true);
                                    } else {
                                        response.data.forEach(function(item) {
                                            var totalPrice = parseFloat(item.price_per_unit) * parseInt(item.total_quantity);
                                            grandTotal += totalPrice;
                                            
                                            html += '<tr>';
                                            html += '<td class="text-dark">' + item.supply_name + '</td>';
                                            html += '<td class="text-end fw-medium text-dark">' + item.total_quantity + '</td>';
                                            html += '<td class="text-end fw-medium text-dark">₱' + parseFloat(item.price_per_unit).toFixed(2) + '</td>';
                                            html += '<td class="text-end fw-medium text-dark">₱' + totalPrice.toFixed(2) + '</td>';
                                            html += '</tr>';
                                        });
                                        $('#export-pdf').prop('disabled', false);
                                    }
                                    $('#filtered-results-body').html(html);
                                    $('#grand-total').text('₱' + grandTotal.toFixed(2));
                                } else {
                                    $('#filtered-results-body').html('<tr><td colspan="4" class="text-center text-danger py-3">Error loading results</td></tr>');
                                    $('#export-pdf').prop('disabled', true);
                                }
                            },
                            error: function() {
                                $('#filtered-results-body').html('<tr><td colspan="4" class="text-center text-danger py-3">Error loading results</td></tr>');
                                $('#export-pdf').prop('disabled', true);
                            },
                            complete: function() {
                                // Hide loading indicator
                                $('#loading-indicator').addClass('d-none');
                            }
                        });
                    }

                    function generatePDF() {
                        var fromDate = $('#filter-from-date').val();
                        var toDate = $('#filter-to-date').val();
                        
                        // Get the table data
                        var data = [];
                        $('#filtered-results-body tr').each(function() {
                            var row = [];
                            $(this).find('td').each(function() {
                                row.push($(this).text().trim());
                            });
                            if (row.length > 0) {
                                data.push(row);
                            }
                        });

                        // Create PDF
                        const { jsPDF } = window.jspdf;
                        const doc = new jsPDF();

                        // Add title
                        doc.setFontSize(16);
                        doc.text('Release Supplies Report', 14, 15);
                        doc.setFontSize(12);
                        doc.text('Period: ' + fromDate + ' to ' + toDate, 14, 25);

                        // Add table
                        doc.autoTable({
                            startY: 30,
                            head: [['Equipment / Supply Name', 'Quantity', 'Price per Unit', 'Total Price']],
                            body: data,
                            theme: 'grid',
                            styles: {
                                fontSize: 10,
                                cellPadding: 5,
                                cellWidth: 'auto',
                                halign: 'left'
                            },
                            columnStyles: {
                                1: { halign: 'right' },
                                2: { halign: 'right' },
                                3: { halign: 'right' }
                            },
                            headStyles: {
                                fillColor: [0, 123, 255],
                                textColor: 255,
                                fontSize: 11,
                                fontStyle: 'bold'
                            },
                            footStyles: {
                                fillColor: [248, 249, 250],
                                textColor: 0,
                                fontSize: 11,
                                fontStyle: 'bold'
                            },
                            didDrawPage: function(data) {
                                // Add grand total at the bottom
                                var grandTotal = $('#grand-total').text();
                                doc.setFontSize(11);
                                doc.setFont(undefined, 'bold');
                                doc.text('Total Amount: ' + grandTotal, data.settings.margin.left, doc.internal.pageSize.height - 10);
                            }
                        });

                        // Save the PDF
                        doc.save('release-supplies-' + fromDate + '-to-' + toDate + '.pdf');
                    }

                    // Event handlers
                    $('#filter-search').on('click', loadFilteredResults);
                    $('#export-pdf').on('click', function(e) {
                        e.preventDefault();
                        if (!$(this).prop('disabled')) {
                            // Show loading state
                            var $btn = $(this);
                            var originalText = $btn.html();
                            $btn.html('<i class="fa-solid fa-spinner fa-spin me-1"></i>Generating PDF...').prop('disabled', true);

                            // Generate PDF
                            generatePDF();

                            // Reset button after a short delay
                            setTimeout(function() {
                                $btn.html(originalText).prop('disabled', false);
                            }, 1000);
                        }
                    });
                    
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