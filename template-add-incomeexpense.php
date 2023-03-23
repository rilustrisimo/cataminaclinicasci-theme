<?php
/**
 * Template Name: Add Income / Expenses
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
                <div class="custom-post__add-form" form-id="167">
				    <?php $theme->createAcfForm(167, 'incomeexpenses', '<i class="fa-solid fa-plus"></i> Add Income / Expense'); ?>
                </div>
                <div class="custom-post__search"><div class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></div><input type="text" class="search-ajax" placeholder="Search Description"></div>
                <div class="custom-post__list" data-pt="incomeexpenses">
                    <?php 
                    $header = array(
                        'description' => 'Description',
                        'amount' => 'Amount',
                        'date_added' => 'Date Added',
                        'type' => 'Type',
                        'income_category' => 'Income Category',
                        'expense_category' => 'Expense Category',
                        'voucher_number' => 'Voucher Number',
                        'remarks' => 'Remarks'
                    );

                    $theme->createCustomPostListHtml('incomeexpenses', 20, $header);
                    ?>
                </div>
			</main>
		</div>
	<?php } ?>
<?php else :
	get_template_part( 'templates/content', 'none' );
endif;

get_footer();