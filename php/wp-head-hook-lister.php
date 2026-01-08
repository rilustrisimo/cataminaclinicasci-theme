<?php
/**
 * Simple wp_head hook lister - just shows what's hooked without executing
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_head', function() {
    if (!is_front_page() && !is_home()) {
        return;
    }
    
    global $wp_filter;
    
    if (!isset($wp_filter['wp_head'])) {
        return;
    }
    
    $log_file = WP_CONTENT_DIR . '/wp-head-hooks-list.txt';
    
    $output = "\n=== WP_HEAD HOOKS - " . date('Y-m-d H:i:s') . " ===\n\n";
    
    foreach ($wp_filter['wp_head']->callbacks as $priority => $callbacks) {
        foreach ($callbacks as $callback) {
            $function_name = 'Unknown';
            
            if (is_string($callback['function'])) {
                $function_name = $callback['function'];
            } elseif (is_array($callback['function'])) {
                if (is_object($callback['function'][0])) {
                    $function_name = get_class($callback['function'][0]) . '::' . $callback['function'][1];
                } else {
                    $function_name = $callback['function'][0] . '::' . $callback['function'][1];
                }
            }
            
            $output .= sprintf("[Priority %3d] %s\n", $priority, $function_name);
        }
    }
    
    $output .= "\n";
    
    file_put_contents($log_file, $output, FILE_APPEND | LOCK_EX);
}, -99999); // Run first, before anything else
