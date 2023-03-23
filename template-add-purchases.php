<?php
/**
 * Template Name: Add Purchases
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
                <div class="custom-post__add-form" form-id="188">
				    <?php $theme->createAcfForm(188, 'purchases', '<i class="fa-solid fa-plus"></i> Add Purchases'); ?>
                </div>
                <div class="custom-post__search"><div class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></div><input type="text" class="search-ajax" placeholder="Search Product"></div>
                <div class="custom-post__list" data-pt="purchases">
                    <?php 
                    $header = array(
                        'product_name' => 'Product Name',
                        'quantity' => 'Quantity',
                        'purchase_date' => 'Purchase Date',
                        'purchase_total' => 'Purchase Total'
                    );

                    $theme->createCustomPostListHtml('purchases', 20, $header);
                    ?>
                </div>
			</main>
		</div>
	<?php } ?>
<?php else :
	get_template_part( 'templates/content', 'none' );
endif;

get_footer();