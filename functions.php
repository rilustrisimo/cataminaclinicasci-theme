<?php
/**
 * Swish Design functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Eyorsogood_Design
 */

if ( ! defined( 'THEME_IS_DEV_MODE' ) ) {
	define( 'THEME_IS_DEV_MODE', true );
}

define( 'QED_VERSION', '1.0.0' );
define( 'PARENT_DIR', get_template_directory() );
define( 'PARENT_URL', get_template_directory_uri() );

require PARENT_DIR . '/includes/core.php';
require PARENT_DIR . '/php/class-main.php';


/**
 * 
 *  Instantiate classes
 */

$theme = new Theme();


add_action( 'admin_menu', 'isa_remove_menus', 999 ); 
function isa_remove_menus() { 
     remove_menu_page( 'branding' );
     remove_menu_page( 'wpmudev' );
 }

/**
 * Enqueue loading overlay scripts and styles
 */
function catamina_loading_overlay_assets() {
    // Enqueue the CSS
    wp_enqueue_style(
        'loading-overlay-css',
        get_template_directory_uri() . '/assets/css/loading-overlay.css',
        array(),
        '1.0.0'
    );
    
    // Enqueue the JavaScript (before other scripts that might use it)
    wp_enqueue_script(
        'loading-overlay-js',
        get_template_directory_uri() . '/assets/js/loading-overlay.js',
        array('jquery'),
        '1.0.0',
        false  // Load in header instead of footer to ensure it's available early
    );
}
add_action('wp_enqueue_scripts', 'catamina_loading_overlay_assets', 5);  // Priority 5 to load before other scripts