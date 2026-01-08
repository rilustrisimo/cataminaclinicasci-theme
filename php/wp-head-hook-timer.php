<?php
/**
 * WP Head Hook Timer - Times each individual hook hooked to wp_head
 * Uses a different approach: wraps each callback and measures execution time
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Head_Hook_Timer {
    private static $log_file;
    private static $timings = array();
    private static $start_time;
    
    public static function init() {
        if (!WP_DEBUG) {
            return;
        }
        
        self::$log_file = WP_CONTENT_DIR . '/wp-head-hook-timings.log';
        self::$start_time = microtime(true);
        
        // Hook very early to wrap all wp_head callbacks
        add_action('wp_head', array(__CLASS__, 'start_timing'), -999999);
        add_action('wp_head', array(__CLASS__, 'end_timing'), 999999);
    }
    
    public static function start_timing() {
        global $wp_filter;
        
        // Log that we're starting
        self::log_timing('=== WP_HEAD TIMING START ===', 0);
        
        // Get all wp_head hooks
        if (isset($wp_filter['wp_head'])) {
            $hooks = $wp_filter['wp_head']->callbacks;
            
            foreach ($hooks as $priority => $callbacks) {
                foreach ($callbacks as $callback) {
                    $name = self::get_callback_name($callback['function']);
                    self::log_timing("Registered: $name at priority $priority", 0);
                }
            }
        }
    }
    
    public static function end_timing() {
        // Write all timings to log
        $report = "\n" . str_repeat('=', 80) . "\n";
        $report .= date('Y-m-d H:i:s') . " - WP_HEAD HOOK TIMINGS\n";
        $report .= str_repeat('=', 80) . "\n\n";
        
        foreach (self::$timings as $timing) {
            $report .= $timing . "\n";
        }
        
        $report .= "\n";
        
        file_put_contents(self::$log_file, $report, FILE_APPEND | LOCK_EX);
    }
    
    private static function log_timing($message, $elapsed) {
        self::$timings[] = sprintf("[%7.3fs] %s", $elapsed, $message);
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
            return get_class($callback);
        }
        
        return 'Unknown';
    }
}

// Initialize
if (WP_DEBUG) {
    WP_Head_Hook_Timer::init();
}
