<?php
/**
 * Error Logger - Captures and logs all PHP errors
 */

// Make sure we're not directly accessed
defined('ABSPATH') || die();

/**
 * Set up custom error handler to catch PHP errors
 */
function custom_error_handler($errno, $errstr, $errfile, $errline) {
    // Get the error type as a string
    $error_type = '';
    switch ($errno) {
        case E_ERROR:
            $error_type = 'E_ERROR';
            break;
        case E_WARNING:
            $error_type = 'E_WARNING';
            break;
        case E_PARSE:
            $error_type = 'E_PARSE';
            break;
        case E_NOTICE:
            $error_type = 'E_NOTICE';
            break;
        case E_CORE_ERROR:
            $error_type = 'E_CORE_ERROR';
            break;
        case E_CORE_WARNING:
            $error_type = 'E_CORE_WARNING';
            break;
        case E_COMPILE_ERROR:
            $error_type = 'E_COMPILE_ERROR';
            break;
        case E_COMPILE_WARNING:
            $error_type = 'E_COMPILE_WARNING';
            break;
        case E_USER_ERROR:
            $error_type = 'E_USER_ERROR';
            break;
        case E_USER_WARNING:
            $error_type = 'E_USER_WARNING';
            break;
        case E_USER_NOTICE:
            $error_type = 'E_USER_NOTICE';
            break;
        case E_STRICT:
            $error_type = 'E_STRICT';
            break;
        case E_RECOVERABLE_ERROR:
            $error_type = 'E_RECOVERABLE_ERROR';
            break;
        case E_DEPRECATED:
            $error_type = 'E_DEPRECATED';
            break;
        case E_USER_DEPRECATED:
            $error_type = 'E_USER_DEPRECATED';
            break;
        default:
            $error_type = "Unknown error type: [$errno]";
            break;
    }
    
    // Format the error message
    $error_message = sprintf(
        "[%s] %s: %s in %s on line %d",
        date('Y-m-d H:i:s'),
        $error_type,
        $errstr,
        $errfile,
        $errline
    );
    
    // Log to WordPress error log
    error_log($error_message);
    
    // Check if this is an AJAX request to conditionally handle errors
    if (wp_doing_ajax() && ($errno == E_ERROR || $errno == E_USER_ERROR)) {
        // For fatal errors in AJAX requests, send a JSON response
        @header('HTTP/1.1 500 Internal Server Error');
        @header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'data' => [
                'message' => 'PHP Error: ' . $errstr,
                'file' => $errfile,
                'line' => $errline
            ]
        ]);
        exit;
    }
    
    // Let PHP handle the error normally
    return false;
}

// Register the custom error handler
set_error_handler('custom_error_handler');

// Also register a shutdown function to catch fatal errors
function fatal_error_handler() {
    $error = error_get_last();
    
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        // Format the error message
        $error_message = sprintf(
            "[%s] FATAL ERROR: %s in %s on line %d",
            date('Y-m-d H:i:s'),
            $error['message'],
            $error['file'],
            $error['line']
        );
        
        // Log to WordPress error log
        error_log($error_message);
        
        // If this is an AJAX request, attempt to send a JSON response
        if (wp_doing_ajax()) {
            @header('HTTP/1.1 500 Internal Server Error');
            @header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'data' => [
                    'message' => 'Fatal PHP Error: ' . $error['message'],
                    'file' => $error['file'],
                    'line' => $error['line']
                ]
            ]);
        }
    }
}

register_shutdown_function('fatal_error_handler');
