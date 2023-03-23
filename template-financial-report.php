<?php
/**
 * Template Name: Financial Report
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
                    <!--
                    <form action="#" method="POST" id="filter-data" data-report="financial_report">
                        <div class="report__filter-date"><div class="date-icon"><i class="fa-solid fa-calendar"></i></div><input type="text" placeholder="From" class="date-from"></div>
                        <div class="report__filter-date"><div class="date-icon"><i class="fa-regular fa-calendar"></i></div><input type="text" placeholder="To" class="date-to"></div>
                        <div class="report__filter-btn"><a href="#" class="btn button"><i class="fa-solid fa-filter"></i> Apply Filter</a></div>
                    </form>
                    -->
                </div>
                <div class="report__result" id="report__result">
                <!-- result goes here -->
                <?php echo $theme->getFinancialReport();?>
                </div>
			</main>
		</div>
	<?php } ?>
<?php else :
	get_template_part( 'templates/content', 'none' );
endif;

get_footer();