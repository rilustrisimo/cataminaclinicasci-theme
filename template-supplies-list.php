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

 $dept = false;
 $u = wp_get_current_user();

get_header();
if ( have_posts() ) : ?>
	<?php while ( have_posts() ) { the_post(); ?>
        <?php if(!current_user_can( 'manage_options' )): ?>
        <input type="hidden" id="useraccid" value="<?php echo $u->ID; ?>">
        <?php endif; ?>
		<div class="page-single">
			<main class="page-single__content" role="main">
                <div class="custom-post__add-form" form-id="108">
				    <?php $theme->createAcfForm(108, 'supplies', '<i class="fa-solid fa-plus"></i> Add Supply'); ?>
                </div>
                <div class="custom-post__search"><div class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></div><input type="text" class="search-ajax" placeholder="Search Name"></div>
                <?php if(current_user_can( 'manage_options' )): ?>
                <div class="custom-post__dept">
                    <select id="select-department">
                        <option value="NURSING">NURSING</option>
                        <option value="LABORATORY">LABORATORY</option>
                        <option value="PHARMACY">PHARMACY</option>
                        <option value="HOUSEKEEPING">HOUSEKEEPING</option>
                        <option value="MAINTENANCE">MAINTENANCE</option>
                        <option value="RADIOLOGY">RADIOLOGY</option>
                        <option value="BUSINESS OFFICE">BUSINESS OFFICE</option>
                        <option value="INFORMATION / TRIAGE">INFORMATION / TRIAGE</option>
                        <option value="PHYSICAL THERAPY">PHYSICAL THERAPY</option>
                        <option value="KONSULTA PROGRAM">KONSULTA PROGRAM</option>
                        <option value="CLINIC A">CLINIC A</option>
                        <option value="CLINIC B">CLINIC B</option>
                        <option value="CLINIC C">CLINIC C</option>
                        <option value="CLINIC D">CLINIC D</option>
                        <option value="PHILHEALTH - KP">PHILHEALTH - KP</option>
                        <option value="PHILHEALTH - ASC">PHILHEALTH - ASC</option>
                        <option value="PHILHEALTH - CLINIC A">PHILHEALTH - CLINIC A</option>
                        <option value="DSWD">DSWD</option>
                    </select>
                </div>
                <?php 
                    $dept = 'NURSING';
                    endif; 
                    
                ?>
                <div class="custom-post__list" data-pt="supplies">
                    <?php 
                    $header = array(
                        'supply_name' => 'Equipment / Supply Name',
                        'department' => 'Department',
                        'section' => 'Section',
                        'type' => 'Type',
                        'purchased_date' => 'Purchased Date',
                        'date_added' => 'Date Added',
                        'price_per_unit' => 'Price Per Unit'
                    );

                    $theme->createCustomPostListHtml('supplies', 20, $header, false, $dept);
                    
                    ?>
                </div>
			</main>
		</div>
	<?php } ?>
<?php else :
	get_template_part( 'templates/content', 'none' );
endif;

get_footer();