<?php
/**
 * Template Name: Statement of Conditions Report
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
                <div class="report__filter">
                    <form action="#" method="POST" id="filter-data" data-report="soc_report">
                        <div class="report__filter-date"><div class="date-icon"><i class="fa-solid fa-calendar"></i></div><input type="text" placeholder="From" class="date-from"></div>
                        <div class="report__filter-date"><div class="date-icon"><i class="fa-regular fa-calendar"></i></div><input type="text" placeholder="To" class="date-to"></div>
                        <div class="report__filter-btn"><a href="#" class="btn button"><i class="fa-solid fa-filter"></i> Apply Filter</a></div>
                    </form>
                </div>
                <div id="progress-container">
                    <div id="progress"></div>
                    <div id="result"></div>
                </div>
                <div class="report__filter-btn" style="margin: 35px 0;"><input type="text" placeholder="Prepared By" name="preparedby" id="preparedby"><a href="#" class="btn button print-btn"><i class="fa-solid fa-print"></i> Print Report</a></div>
                <div class="report__result" id="report__result" dfrom="<?php echo date('01-01-Y'); ?>" dto="<?php echo date('d-m-Y'); ?>">
                <!-- result goes here -->
                <?php echo $theme->getSOCReport(date('01-01-Y'), date('d-m-Y'));?>
                </div>
			</main>
		</div>
	<?php } ?>
<?php else :
	get_template_part( 'templates/content', 'none' );
endif;

get_footer();