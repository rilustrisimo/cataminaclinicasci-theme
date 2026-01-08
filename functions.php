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

/**
 * Load homepage performance debugger (only in debug mode)
 */
if (WP_DEBUG && file_exists(PARENT_DIR . '/php/homepage-debug.php')) {
    require_once PARENT_DIR . '/php/homepage-debug.php';
}

/**
 * Load wp_head debugger (only in debug mode)
 */
if (WP_DEBUG && file_exists(PARENT_DIR . '/php/wp-head-debug.php')) {
    require_once PARENT_DIR . '/php/wp-head-debug.php';
}

/**
 * Load wp_head hook lister (safe - just lists, doesn't execute)
 */
if (WP_DEBUG && file_exists(PARENT_DIR . '/php/wp-head-hook-lister.php')) {
    require_once PARENT_DIR . '/php/wp-head-hook-lister.php';
}

require PARENT_DIR . '/includes/core.php';
require PARENT_DIR . '/php/class-main.php';

/**
 * Load WP Enqueue Scripts Profiler (debug mode only)
 */
if (WP_DEBUG && file_exists(get_template_directory() . '/php/wp-enqueue-scripts-profiler.php')) {
    require_once(get_template_directory() . '/php/wp-enqueue-scripts-profiler.php');
}

/**
 * Load custom error logger
 */
if (file_exists(get_template_directory() . '/php/error-logger.php')) {
    require_once(get_template_directory() . '/php/error-logger.php');
}

/**
 * Disable Ultimate Member wp_head hooks on non-homepage pages to improve performance
 */
add_action('init', function() {
    // Only load UM head hooks on homepage
    if (!is_front_page() && !is_home()) {
        // Remove Ultimate Member wp_head hooks
        remove_action('wp_head', 'um_add_form_honeypot_css', 10);
        remove_action('wp_head', 'um_profile_dynamic_meta_desc', 20);
    }
}, 999); // Run late to ensure UM has already added its hooks

/**
 * PERFORMANCE FIX: Test disabling theme asset loading
 */
add_action('wp_enqueue_scripts', function() {
    if (class_exists('Homepage_Performance_Debug')) {
        Homepage_Performance_Debug::log_checkpoint('Testing: BEFORE theme asset functions');
    }
}, 9);

// Temporarily disable theme asset functions to test
remove_action('wp_enqueue_scripts', 'qed_init_theme_assets');

add_action('wp_enqueue_scripts', function() {
    if (class_exists('Homepage_Performance_Debug')) {
        Homepage_Performance_Debug::log_checkpoint('Testing: AFTER removing qed_init_theme_assets');
    }
}, 11);

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
