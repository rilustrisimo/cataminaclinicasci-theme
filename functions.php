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
 * Load custom error logger
 */
if (file_exists(get_template_directory() . '/php/error-logger.php')) {
    require_once(get_template_directory() . '/php/error-logger.php');
}

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

/**
 * Enqueue AJAX debug script in development environments
 */
function enqueue_ajax_debug_script() {
    // Only load in development environments
    if (WP_DEBUG) {
        wp_enqueue_script(
            'ajax-debug',
            get_template_directory_uri() . '/assets/js/ajax-debug.js',
            array('jquery'),
            '1.0.0',
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'enqueue_ajax_debug_script', 999); // Load after all other scripts

/**
 * Enqueue DOM structure fix stylesheet
*/
function enqueue_dom_fix_styles() {
    wp_enqueue_style(
        'dom-fixes',
        get_template_directory_uri() . '/assets/css/dom-fixes.css',
        array(),
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'enqueue_dom_fix_styles', 100); // Add after other styles

include_once get_template_directory() . '/php/supplies-overview-link.php';
include_once get_template_directory() . '/php/supply-corrector-link.php';

/**
 * Register AJAX handlers for supplies overview and corrector
 * Only load these handlers when doing AJAX requests to improve performance
 */
function include_supplies_handlers() {
    // Only load AJAX handlers during AJAX requests
    if (!defined('DOING_AJAX') || !DOING_AJAX) {
        return;
    }
    
    $overview_handler_path = get_template_directory() . '/php/supplies-ajax-handler.php';
    $corrector_handler_path = get_template_directory() . '/php/supply-corrector-ajax.php';
    $analytics_handler_path = get_template_directory() . '/php/supplies-analytics-handler.php';
    
    if (file_exists($overview_handler_path)) {
        require_once($overview_handler_path);
    }
    
    if (file_exists($corrector_handler_path)) {
        require_once($corrector_handler_path);
    }
    
    if (file_exists($analytics_handler_path)) {
        require_once($analytics_handler_path);
    }
}
add_action('init', 'include_supplies_handlers');
