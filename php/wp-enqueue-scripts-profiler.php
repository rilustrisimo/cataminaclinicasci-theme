<?php
/**
 * Profile wp_enqueue_scripts hook to find slow callbacks
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Enqueue_Scripts_Profiler {
    private static $log_file;
    private static $timings = array();
    
    public static function init() {
        if (!WP_DEBUG) {
            return;
        }
        
        self::$log_file = WP_CONTENT_DIR . '/wp-enqueue-scripts-timings.log';
        
        // Hook VERY early to profile wp_enqueue_scripts
        add_action('wp_enqueue_scripts', array(__CLASS__, 'start_profiling'), -999999);
        add_action('wp_enqueue_scripts', array(__CLASS__, 'checkpoint_1'), 5);
        add_action('wp_enqueue_scripts', array(__CLASS__, 'checkpoint_2'), 10);
        add_action('wp_enqueue_scripts', array(__CLASS__, 'checkpoint_3'), 15);
        add_action('wp_enqueue_scripts', array(__CLASS__, 'checkpoint_4'), 20);
        add_action('wp_enqueue_scripts', array(__CLASS__, 'end_profiling'), 999999);
    }
    
    public static function start_profiling() {
        self::log_timing('wp_enqueue_scripts START');
    }
    
    public static function checkpoint_1() {
        self::log_timing('After priority 5');
    }
    
    public static function checkpoint_2() {
        self::log_timing('After priority 10 (theme assets should be loaded)');
    }
    
    public static function checkpoint_3() {
        self::log_timing('After priority 15');
    }
    
    public static function checkpoint_4() {
        self::log_timing('After priority 20');
    }
    
    public static function end_profiling() {
        global $wp_filter;
        
        self::log_timing('wp_enqueue_scripts END');
        
        // List all hooked functions
        $report = "\n=== HOOKED TO wp_enqueue_scripts ===\n";
        if (isset($wp_filter['wp_enqueue_scripts'])) {
            foreach ($wp_filter['wp_enqueue_scripts']->callbacks as $priority => $callbacks) {
                foreach ($callbacks as $callback) {
                    $name = self::get_callback_name($callback['function']);
                    $report .= sprintf("[Priority %d] %s\n", $priority, $name);
                }
            }
        }
        
        $output = "\n" . str_repeat('=', 80) . "\n";
        $output .= date('Y-m-d H:i:s') . " - wp_enqueue_scripts PROFILING\n";
        $output .= str_repeat('=', 80) . "\n\n";
        
        $prev_time = null;
        foreach (self::$timings as $timing) {
            if ($prev_time !== null) {
                $delta = $timing['time'] - $prev_time;
                $output .= sprintf("[%7.3fs] (+%7.3fs) %s\n", $timing['time'], $delta, $timing['label']);
            } else {
                $output .= sprintf("[%7.3fs] %s\n", $timing['time'], $timing['label']);
            }
            $prev_time = $timing['time'];
        }
        
        $output .= "\n" . $report . "\n";
        
        file_put_contents(self::$log_file, $output, FILE_APPEND | LOCK_EX);
    }
    
    private static function log_timing($label) {
        self::$timings[] = array(
            'label' => $label,
            'time' => microtime(true)
        );
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
    WP_Enqueue_Scripts_Profiler::init();
}
