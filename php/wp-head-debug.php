<?php
/**
 * Debug wp_head hook to identify slow functions
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Head_Debug {
    private static $log_file;
    private static $hook_start_time;
    private static $actions_timing = array();
    
    public static function init() {
        if (!WP_DEBUG) {
            return;
        }
        
        self::$log_file = WP_CONTENT_DIR . '/debug-wp-head.log';
        
        // Track what's hooked to wp_head
        add_action('wp_head', array(__CLASS__, 'start_tracking'), -9999);
        add_action('wp_head', array(__CLASS__, 'end_tracking'), 9999);
    }
    
    public static function start_tracking() {
        global $wp_filter;
        
        self::$hook_start_time = microtime(true);
        
        $report = "\n" . str_repeat('=', 80) . "\n";
        $report .= date('Y-m-d H:i:s') . " - WP_HEAD HOOK ANALYSIS\n";
        $report .= str_repeat('=', 80) . "\n";
        
        // List all functions hooked to wp_head
        if (isset($wp_filter['wp_head'])) {
            $report .= "\nFunctions hooked to wp_head:\n";
            $report .= str_repeat('-', 80) . "\n";
            
            foreach ($wp_filter['wp_head']->callbacks as $priority => $callbacks) {
                foreach ($callbacks as $callback) {
                    $function_name = self::get_callback_name($callback['function']);
                    $report .= sprintf("[Priority %d] %s\n", $priority, $function_name);
                }
            }
        }
        
        $report .= "\n";
        file_put_contents(self::$log_file, $report, FILE_APPEND | LOCK_EX);
    }
    
    public static function end_tracking() {
        $total_time = microtime(true) - self::$hook_start_time;
        
        $report = sprintf("\nwp_head total execution time: %.3f seconds\n", $total_time);
        $report .= str_repeat('=', 80) . "\n\n";
        
        file_put_contents(self::$log_file, $report, FILE_APPEND | LOCK_EX);
    }
    
    private static function get_callback_name($callback) {
        if (is_string($callback)) {
            return $callback;
        }
        
        if (is_array($callback)) {
            if (is_object($callback[0])) {
                return get_class($callback[0]) . '::' . $callback[1];
            }
            return $callback[0] . '::' . $callback[1];
        }
        
        if (is_object($callback)) {
            if ($callback instanceof Closure) {
                return 'Closure';
            }
            return get_class($callback) . '::__invoke';
        }
        
        return 'Unknown';
    }
}

if (WP_DEBUG) {
    WP_Head_Debug::init();
}
