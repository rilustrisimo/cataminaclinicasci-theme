<?php
/**
 * Template Name: Add Products
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
                <div class="custom-post__add-form" form-id="177">
				    <?php $theme->createAcfForm(177, 'products', '<i class="fa-solid fa-plus"></i> Add Product'); ?>
                </div>
                <div class="custom-post__search"><div class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></div><input type="text" class="search-ajax" placeholder="Search Name"></div>
                <div class="custom-post__list" data-pt="products">
                    <?php 
                    $header = array(
                        'product_name' => 'Product Name',
                        'product_description' => 'Product Description',
                        'date_added' => 'Date Added',
                        'price_per_unit' => 'Price Per Unit (SRP)',
                        'cost_of_goods_sold' => 'Cost of Goods Sold'
                    );

                    $theme->createCustomPostListHtml('products', 20, $header);
                    ?>
                </div>
			</main>
		</div>
	<?php } ?>
<?php else :
	get_template_part( 'templates/content', 'none' );
endif;

get_footer();