<?php
/**
 * Template Name: Actual Supply
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
                <div class="custom-post__add-form" form-id="127">
				    <?php $theme->createAcfForm(127, 'actualsupplies', '<i class="fa-solid fa-plus"></i> Add Actual Supply'); ?>
                    <div class="supplies-deets">
                        <ul>
                            <li><b>Purchased Date:</b> <span class="res-date"></span></li>
                            <li><b>Room:</b> <span class="res-room"></span></li>
                        </ul>
                    </div>
                </div>
                <div class="custom-post__search"><div class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></div><input type="text" class="search-ajax" placeholder="Search Name"></div>
                <div class="custom-post__list" data-pt="actualsupplies">
                    <?php 
                    $header = array(
                        'supply_name' => 'Equipment / Supply Name',
                        'date_added' => 'Date Added',
                        'quantity' => 'Quantity',
                        'serial' => 'Serial',
                        'states__status' => 'State / Status',
                        'lot_number' => 'Lot #',
                        'expiry_date' => 'Exp. Date',
                    );

                    $theme->createCustomPostListHtml('actualsupplies', 20, $header);
                    
                    ?>
                </div>
			</main>
		</div>
	<?php } ?>
<?php else :
	get_template_part( 'templates/content', 'none' );
endif;

get_footer();