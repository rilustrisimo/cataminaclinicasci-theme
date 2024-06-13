<?php
/**
 * Template Name: Income / Expenses Goods Report
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
                    <form action="#" method="POST" id="filter-data" data-report="goods_report">
                        <div class="report__filter-date"><div class="date-icon"><i class="fa-solid fa-calendar"></i></div><input type="text" placeholder="From" class="date-from"></div>
                        <div class="report__filter-date"><div class="date-icon"><i class="fa-regular fa-calendar"></i></div><input type="text" placeholder="To" class="date-to"></div>
                        <div class="report__filter-btn"><a href="#" class="btn button"><i class="fa-solid fa-filter"></i> Apply Filter</a></div>
                    </form>
                </div>
                <div class="report__filter-btn" style="margin: 35px 0;"><input type="text" placeholder="Prepared By" name="preparedby" id="preparedby"><a href="#" class="btn button print-btn"><i class="fa-solid fa-print"></i> Print Report</a></div>
                <div class="report__result" id="report__result">
                <!-- result goes here -->
                <?php echo $theme->getGoodsReport(date('01-m-Y'), date('d-m-Y', strtotime('last day of this month')));?>
                </div>
			</main>
		</div>
	<?php } ?>
<?php else :
	get_template_part( 'templates/content', 'none' );
endif;

get_footer();