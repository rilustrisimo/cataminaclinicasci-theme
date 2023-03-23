<?php
/**
 * Template Name: Add Capital
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
                <div class="custom-post__add-form" form-id="194">
				    <?php $theme->createAcfForm(194, 'capitals', '<i class="fa-solid fa-plus"></i> Add Capital'); ?>
                </div>
                <div class="custom-post__search"><div class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></div><input type="text" class="search-ajax" placeholder="Search Capital"></div>
                <div class="custom-post__list" data-pt="capitals">
                    <?php 
                    $header = array(
                        'investor_name' => 'Investor Name',
                        'invested_amount' => 'Invested Amount'
                    );

                    $theme->createCustomPostListHtml('capitals', 20, $header);
                    ?>
                </div>
			</main>
		</div>
	<?php } ?>
<?php else :
	get_template_part( 'templates/content', 'none' );
endif;

get_footer();