<?php
/**
 * Template Name: Supplies List
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
                <div class="custom-post__add-form" form-id="108">
				    <?php $theme->createAcfForm(108, 'supplies', '<i class="fa-solid fa-plus"></i> Add Supply'); ?>
                </div>
                <div class="custom-post__search"><div class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></div><input type="text" class="search-ajax" placeholder="Search Name"></div>
                <div class="custom-post__list" data-pt="supplies">
                    <?php 
                    $header = array(
                        'supply_name' => 'Equipment / Supply Name',
                        'department' => 'Department',
                        'section' => 'Section',
                        'type' => 'Type',
                        'purchased_date' => 'Purchased Date',
                        'serial' => 'Serial',
                        'states__status' => 'State / Status',
                        'lot_number' => 'Lot #',
                        'expiry_date' => 'Exp. Date',
                        'date_added' => 'Date Added',
                        'price_per_unit' => 'Price Per Unit'
                    );

                    $theme->createCustomPostListHtml('supplies', 20, $header);
                    
                    ?>
                </div>
			</main>
		</div>
	<?php } ?>
<?php else :
	get_template_part( 'templates/content', 'none' );
endif;

get_footer();