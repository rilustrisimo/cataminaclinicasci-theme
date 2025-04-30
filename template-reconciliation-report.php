<?php
/**
 * Template Name: Reconciliation Report
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
 $dept = false;
get_header();
?>
<!-- PDF Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>
<?php
if ( have_posts() ) : ?>
	<?php while ( have_posts() ) { the_post(); ?>
		<div class="page-single">
			<main class="page-single__content" role="main">
                <div class="report__filter">
                    <form action="#" method="POST" id="filter-data" data-report="reconciliation_report" data-title="Reconciliation Report">
                        <div class="report__filter-date"><div class="date-icon"><i class="fa-solid fa-calendar"></i></div><input type="text" placeholder="From" class="date-from"></div>
                        <div class="report__filter-date"><div class="date-icon"><i class="fa-regular fa-calendar"></i></div><input type="text" placeholder="To" class="date-to"></div>
                        <?php if(current_user_can( 'manage_options' )): ?>
                            <div class="custom-post__dept" style="display: inline-block;">
                                <select id="select-department" class="recon-dept">
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
                        <?php 
                            $dept = 'NURSING';
                            else:
                                $u = wp_get_current_user();
                                /*
                                echo '<input type="hidden" id="author-id" value="'.$u->ID.'">';
                                */

                                echo '<div class="custom-post__dept" style="display: inline-block;">';
                                echo '    <select id="select-department" class="recon-dept">';
                                echo ($u->ID == 7)?'        <option value="NURSING">NURSING</option>':'';
                                echo ($u->ID == 6)?'        <option value="LABORATORY">LABORATORY</option>':'';
                                echo ($u->ID == 4)?'        <option value="PHARMACY">PHARMACY</option>':'';
                                echo ($u->ID == 8)?'        <option value="HOUSEKEEPING">HOUSEKEEPING</option>':'';
                                echo ($u->ID == 8)?'        <option value="MAINTENANCE">MAINTENANCE</option>':'';
                                echo ($u->ID == 5)?'        <option value="RADIOLOGY">RADIOLOGY</option>':'';
                                echo ($u->ID == 9)?'        <option value="BUSINESS OFFICE">BUSINESS OFFICE</option>':'';
                                echo ($u->ID == 10)?'        <option value="INFORMATION / TRIAGE">INFORMATION / TRIAGE</option>':'';
                                echo ($u->ID == 14)?'        <option value="PHYSICAL THERAPY">PHYSICAL THERAPY</option>':'';
                                echo ($u->ID == 11)?'        <option value="KONSULTA PROGRAM">KONSULTA PROGRAM</option>':'';
                                echo ($u->ID == 12)?'        <option value="CLINIC A">CLINIC A</option>':'';
                                echo ($u->ID == 12)?'        <option value="CLINIC B">CLINIC B</option>':'';
                                echo ($u->ID == 12)?'        <option value="CLINIC C">CLINIC C</option>':'';
                                echo ($u->ID == 12)?'        <option value="CLINIC D">CLINIC D</option>':'';
                                echo ($u->ID == 11)?'        <option value="PHILHEALTH - KP">PHILHEALTH - KP</option>':'';
                                echo ($u->ID == 7)?'        <option value="PHILHEALTH - ASC">PHILHEALTH - ASC</option>':'';
                                echo ($u->ID == 12)?'        <option value="PHILHEALTH - CLINIC A">PHILHEALTH - CLINIC A</option>':'';
                                echo ($u->ID == 10)?'        <option value="DSWD">DSWD</option>':'';
                                echo '    </select>';
                                echo '</div>';

                            endif; 
                            
                        ?>
                        <div class="report__filter-btn"><a href="#" class="btn button"><i class="fa-solid fa-filter"></i> Apply Filter</a></div>
                    </form>
                    <div class="filter-show">
                        <div class="filter-show__item"><label><input type="checkbox" class="filter-show__check" checked data-src="filter-serial"> Serial</label></div>
                        <div class="filter-show__item"><label><input type="checkbox" class="filter-show__check" checked data-src="filter-states"> States</label></div>
                        <div class="filter-show__item"><label><input type="checkbox" class="filter-show__check" checked data-src="filter-lot"> Lot #</label></div>
                        <div class="filter-show__item"><label><input type="checkbox" class="filter-show__check" checked data-src="filter-exp"> Expiry Date</label></div>
                        <div class="filter-show__item"><label><input type="checkbox" class="filter-show__check" checked data-src="filter-beg"> Beg Inv</label></div>
                        <div class="filter-show__item"><label><input type="checkbox" class="filter-show__check" checked data-src="filter-purchase"> Purchases</label></div>
                        <div class="filter-show__item"><label><input type="checkbox" class="filter-show__check" checked data-src="filter-total"> Before Total</label></div>
                        <div class="filter-show__item"><label><input type="checkbox" class="filter-show__check" checked data-src="filter-cons"> Consumption</label></div>
                    </div>
                    <div class="report__filter-btn" style="margin-bottom: 35px;"><input type="text" placeholder="Prepared By" name="preparedby" id="preparedby"><a href="#" class="btn button print-btn"><i class="fa-solid fa-print"></i> Print Report</a></div>
                </div>
                <div id="progress-container">
                    <div id="progress"></div>
                    <div id="result"></div>
                </div>
                <div class="report__result init-recon-report" dfrom="<?php echo date('01-m-Y'); ?>" dto="<?php echo date('d-m-Y', strtotime('last day of this month'));?>" ddept="<?php echo $dept; ?>" id="report__result">
                </div>
			</main>
		</div>
	<?php } ?>
<?php else :
	get_template_part( 'templates/content', 'none' );
endif;

get_footer();