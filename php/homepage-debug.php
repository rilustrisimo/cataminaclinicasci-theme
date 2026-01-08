<?php
/**
 * Homepage Performance Debug Logger
 * Tracks timing of various WordPress hooks and operations
 */

if (!defined('ABSPATH')) {
    exit;
}

class Homepage_Performance_Debug {
    private static $start_time;
    private static $checkpoints = array();
    private static $log_file;
    
    public static function init() {
        if (!WP_DEBUG) {
            return; // Only run in debug mode
        }
        
        self::$start_time = microtime(true);
        self::$log_file = WP_CONTENT_DIR . '/debug-homepage-performance.log';
        
        // Clear old log if it's too large (over 5MB)
        if (file_exists(self::$log_file) && filesize(self::$log_file) > 5242880) {
            unlink(self::$log_file);
        }
        
        // Track early WordPress hooks
        add_action('init', array(__CLASS__, 'checkpoint_init'), 1);
        add_action('wp_loaded', array(__CLASS__, 'checkpoint_wp_loaded'), 1);
        add_action('template_redirect', array(__CLASS__, 'checkpoint_template_redirect'), 1);
        add_action('wp_enqueue_scripts', array(__CLASS__, 'checkpoint_enqueue_scripts'), 1);
        add_action('wp_head', array(__CLASS__, 'checkpoint_wp_head_start'), 1);
        add_action('wp_head', array(__CLASS__, 'checkpoint_wp_head_end'), 999);
        add_action('wp_footer', array(__CLASS__, 'checkpoint_wp_footer'), 1);
        add_action('shutdown', array(__CLASS__, 'write_report'), 1);
        
        self::log_checkpoint('WordPress Started');
    }
    
    public static function log_checkpoint($label) {
        $time = microtime(true);
        $elapsed = $time - self::$start_time;
        $memory = memory_get_usage(true) / 1024 / 1024; // MB
        
        self::$checkpoints[] = array(
            'label' => $label,
            'time' => $elapsed,
            'memory' => $memory
        );
    }
    
    public static function checkpoint_init() {
        self::log_checkpoint('init hook');
    }
    
    public static function checkpoint_wp_loaded() {
        self::log_checkpoint('wp_loaded hook');
    }
    
    public static function checkpoint_template_redirect() {
        self::log_checkpoint('template_redirect hook');
        
        // Only track homepage
        if (!is_front_page() && !is_home()) {
            return;
        }
        
        self::log_checkpoint('HOMEPAGE DETECTED');
    }
    
    public static function checkpoint_enqueue_scripts() {
        self::log_checkpoint('wp_enqueue_scripts hook');
    }
    
    public static function checkpoint_wp_head_start() {
        self::log_checkpoint('wp_head start');
    }
    
    public static function checkpoint_wp_head_end() {
        self::log_checkpoint('wp_head end');
    }
    
    public static function checkpoint_wp_footer() {
        self::log_checkpoint('wp_footer hook');
    }
    
    public static function write_report() {
        if (empty(self::$checkpoints)) {
            return;
        }
        
        // Only log homepage or pages taking more than 2 seconds
        $total_time = microtime(true) - self::$start_time;
        $is_homepage = is_front_page() || is_home();
        
        if (!$is_homepage && $total_time < 2) {
            return; // Skip fast non-homepage requests
        }
        
        $report = "\n" . str_repeat('=', 80) . "\n";
        $report .= date('Y-m-d H:i:s') . " - ";
        $report .= $is_homepage ? "HOMEPAGE" : ($_SERVER['REQUEST_URI'] ?? 'Unknown');
        $report .= "\n" . str_repeat('=', 80) . "\n";
        
        $prev_time = 0;
        foreach (self::$checkpoints as $checkpoint) {
            $delta = $checkpoint['time'] - $prev_time;
            $report .= sprintf(
                "[%7.3fs] (+%6.3fs) %6.2f MB - %s\n",
                $checkpoint['time'],
                $delta,
                $checkpoint['memory'],
                $checkpoint['label']
            );
            $prev_time = $checkpoint['time'];
        }
        
        // Add query information
        global $wpdb;
        if (isset($wpdb->num_queries)) {
            $report .= sprintf("\nTotal Queries: %d\n", $wpdb->num_queries);
        }
        
        // Add loaded plugins/themes info
        $report .= sprintf("Peak Memory: %.2f MB\n", memory_get_peak_usage(true) / 1024 / 1024);
        $report .= sprintf("Total Time: %.3f seconds\n", $total_time);
        
        // Identify slow sections
        $report .= "\n--- SLOW OPERATIONS (>0.5s) ---\n";
        $prev_time = 0;
        foreach (self::$checkpoints as $checkpoint) {
            $delta = $checkpoint['time'] - $prev_time;
            if ($delta > 0.5) {
                $report .= sprintf("⚠️  %.3fs - %s\n", $delta, $checkpoint['label']);
            }
            $prev_time = $checkpoint['time'];
        }
        
        $report .= "\n";
        
        // Write to log file
        file_put_contents(self::$log_file, $report, FILE_APPEND | LOCK_EX);
    }
}

// Initialize if on homepage or if page is slow
if (WP_DEBUG) {
    Homepage_Performance_Debug::init();
}
