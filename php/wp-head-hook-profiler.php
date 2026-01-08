<?php
/**
 * Profile each function hooked to wp_head to identify slow operations
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Head_Hook_Profiler {
    private static $log_file;
    private static $hook_times = array();
    private static $current_hook_start = 0;
    
    public static function init() {
        if (!WP_DEBUG) {
            return;
        }
        
        self::$log_file = WP_CONTENT_DIR . '/debug-wp-head-hooks.log';
        
        // Clear old log if too large
        if (file_exists(self::$log_file) && filesize(self::$log_file) > 5242880) {
            unlink(self::$log_file);
        }
        
        // Hook right before wp_head executes
        add_action('wp_head', array(__CLASS__, 'start_profiling'), -9999);
        add_action('wp_head', array(__CLASS__, 'end_profiling'), 9999);
    }
    
    public static function start_profiling() {
        global $wp_filter;
        
        if (!isset($wp_filter['wp_head'])) {
            return;
        }
        
        // Get all functions hooked to wp_head
        $report = "\n" . str_repeat('=', 80) . "\n";
        $report .= date('Y-m-d H:i:s') . " - wp_head Hook Analysis\n";
        $report .= "Page: " . ($_SERVER['REQUEST_URI'] ?? 'Unknown') . "\n";
        $report .= str_repeat('=', 80) . "\n\n";
        
        $report .= "Functions hooked to wp_head:\n";
        $report .= str_repeat('-', 80) . "\n";
        
        foreach ($wp_filter['wp_head']->callbacks as $priority => $callbacks) {
            foreach ($callbacks as $callback) {
                $function_name = self::get_callback_name($callback['function']);
                $report .= sprintf("[Priority %3d] %s\n", $priority, $function_name);
            }
        }
        
        file_put_contents(self::$log_file, $report, FILE_APPEND | LOCK_EX);
        
        // Now we'll manually time each one
        self::profile_each_hook();
    }
    
    public static function profile_each_hook() {
        global $wp_filter;
        
        if (!isset($wp_filter['wp_head'])) {
            return;
        }
        
        $report = "\nTiming each wp_head hook:\n";
        $report .= str_repeat('-', 80) . "\n";
        
        foreach ($wp_filter['wp_head']->callbacks as $priority => $callbacks) {
            foreach ($callbacks as $idx => $callback) {
                $function_name = self::get_callback_name($callback['function']);
                $start_time = microtime(true);
                
                try {
                    // Execute the callback
                    call_user_func_array($callback['function'], array(''));
                } catch (Exception $e) {
                    $report .= sprintf("ERROR in %s: %s\n", $function_name, $e->getMessage());
                }
                
                $elapsed = microtime(true) - $start_time;
                
                if ($elapsed > 0.1) { // Only log if > 100ms
                    $report .= sprintf(
                        "[%7.3fs] Priority %3d - %s %s\n",
                        $elapsed,
                        $priority,
                        $function_name,
                        $elapsed > 1 ? '⚠️ SLOW!' : ''
                    );
                }
            }
        }
        
        file_put_contents(self::$log_file, $report, FILE_APPEND | LOCK_EX);
    }
    
    public static function end_profiling() {
        $report = str_repeat('=', 80) . "\n\n";
        file_put_contents(self::$log_file, $report, FILE_APPEND | LOCK_EX);
    }
    
    private static function get_callback_name($callback) {
        if (is_string($callback)) {
            return $callback;
        } elseif (is_array($callback)) {
            if (is_object($callback[0])) {
                return get_class($callback[0]) . '::' . $callback[1];
            } else {
                return $callback[0] . '::' . $callback[1];
            }
        } elseif (is_object($callback)) {
            if ($callback instanceof Closure) {
                return 'Closure';
            }
            return get_class($callback);
        }
        return 'Unknown';
    }
}

if (WP_DEBUG) {
    WP_Head_Hook_Profiler::init();
}
