<?php
/**
 * Template Name: Add Before Income Tax
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
                <div class="custom-post__add-form" form-id="19506">
				    <?php $theme->createAcfForm(19506, 'beforeincometax', '<i class="fa-solid fa-plus"></i> Add Before Income Tax'); ?>
                </div>
                <div class="custom-post__search"><div class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></div><input type="text" class="search-ajax" placeholder="Search Before Income Taxes"></div>
                <div class="custom-post__list" data-pt="beforeincometax">
                    <?php 
                    $header = array(
                        'pre-tax_income_amount' => 'Pre-Tax Income Amount',
                        'description' => 'Description',
                        'date_added' => 'Date Added',
                        'applicable_period' => 'Applicable Period'
                    );

                    $theme->createCustomPostListHtml('beforeincometax', 20, $header);
                    ?>
                </div>
			</main>
		</div>
	<?php } ?>
<?php else :
	get_template_part( 'templates/content', 'none' );
endif;

get_footer();