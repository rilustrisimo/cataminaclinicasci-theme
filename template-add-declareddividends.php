<?php
/**
 * Template Name: Add Declared Dividends
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
                <div class="custom-post__add-form" form-id="200">
				    <?php $theme->createAcfForm(200, 'declareddividends', '<i class="fa-solid fa-plus"></i> Add Dividend'); ?>
                </div>
                <div class="custom-post__search"><div class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></div><input type="text" class="search-ajax" placeholder="Search Dividends"></div>
                <div class="custom-post__list" data-pt="declareddividends">
                    <?php 
                    $header = array(
                        'declared_dividends' => 'Declared Dividends',
                        'date_added' => 'Date Added'
                    );

                    $theme->createCustomPostListHtml('declareddividends', 20, $header);
                    ?>
                </div>
			</main>
		</div>
	<?php } ?>
<?php else :
	get_template_part( 'templates/content', 'none' );
endif;

get_footer();