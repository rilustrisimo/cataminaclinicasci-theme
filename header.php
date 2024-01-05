<?php
/**
 * Header template part.
 *
 * @author    eyorsogood.com, Rouie Ilustrisimo
 * @package Eyorsogood_Design
 * @version   1.0.0
 */

get_template_part( 'templates/header/header', 'clean' );

$is_sticky_header = qed_get_option( 'sticky_header', 'option' );

if ( $is_sticky_header ) {
//	SD_Js_Client_Script::add_script( 'sticky-header', 'Theme.initStickyHeader();' );
	echo '<div class="header-wrap header-wrap--sticky-header">';
}

$theme = new Theme();
echo 'test deploy';
?>
<div class="page-breadcrumbs" style="<?php echo (!is_user_logged_in())?'display:none;':''; ?>">
	<?php $theme->get_breadcrumb();?>
</div>
<?php if(!is_user_logged_in()): ?>
<div class="login-status__forms">
	<div class="login-status__forms-login"><?php echo do_shortcode('[ultimatemember form_id="97"]'); ?></div>
	<div class="login-status__forms-register"><?php echo do_shortcode('[ultimatemember form_id="96"]'); ?></div>
</div>
<?php endif; ?>
<?php if ( $is_sticky_header ) { echo '</div>'; }
SD_Js_Client_Script::add_script( 'initResizeHandler', 'Theme.initResizeHandler();' );
//SD_Js_Client_Script::add_script( 'initResizeHandler', 'Theme.initResizeHandler(' . wp_json_encode( $js_config ) . ');' );
get_template_part( 'templates/header/header', 'section' );
do_action('eyor_before_main_content');
