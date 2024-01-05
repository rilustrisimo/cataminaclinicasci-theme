<?php
/**
 * Template Name: Add Cash / Cheques
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
                <div class="custom-post__add-form" form-id="144">
				    <?php $theme->createAcfForm(144, 'cashcheques', '<i class="fa-solid fa-plus"></i> Add Cash / Cheques'); ?>
                </div>
                <div class="custom-post__search"><div class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></div><input type="text" class="search-ajax" placeholder="Search Name"></div>
                <div class="custom-post__list" data-pt="cashcheques">
                    <?php 
                    $header = array(
                        'name_of_bank' => 'Name of Bank',
                        'type_of_account' => 'Account Type',
                        'account_number' => 'Account Number',
                        'amount' => 'Amount',
                        'date_added' => 'Date Added'
                    );

                    $theme->createCustomPostListHtml('cashcheques', 20, $header);
                    ?>
                </div>
			</main>
		</div>
	<?php } ?>
<?php else :
	get_template_part( 'templates/content', 'none' );
endif;

get_footer();