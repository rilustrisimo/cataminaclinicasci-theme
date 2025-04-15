/**
 * AJAX Debug Helper
 * Helps diagnose AJAX issues by intercepting and logging all AJAX requests
 */
(function($) {
    // Save the original $.ajax function
    var originalAjax = $.ajax;
    
    // Replace the $.ajax function with our own version
    $.ajax = function(options) {
        // Log the request
        console.group('AJAX Request');
        console.log('URL:', options.url);
        console.log('Type:', options.type);
        console.log('Data:', options.data);
        
        // Add our own callbacks to track the request
        var originalBeforeSend = options.beforeSend;
        var originalSuccess = options.success;
        var originalError = options.error;
        var originalComplete = options.complete;
        
        options.beforeSend = function(xhr, settings) {
            console.log('Request sent:', new Date());
            if (originalBeforeSend) {
                return originalBeforeSend.apply(this, arguments);
            }
        };
        
        options.success = function(response, status, xhr) {
            console.log('Request succeeded:', new Date());
            console.log('Status:', status);
            
            // If the response is HTML instead of expected JSON, log it differently
            if (typeof response === 'string' && (response.indexOf('<!DOCTYPE') === 0 || response.indexOf('<html') === 0)) {
                console.error('Received HTML instead of expected data type:');
                // Extract the title if possible
                var titleMatch = response.match(/<title>(.*?)<\/title>/i);
                if (titleMatch && titleMatch[1]) {
                    console.error('Page title:', titleMatch[1]);
                }
                // Extract error messages if any
                var errorMatch = response.match(/(?:Fatal error|Parse error|Warning|Notice|Error):\s*(.+?)<br/i);
                if (errorMatch && errorMatch[1]) {
                    console.error('PHP Error:', errorMatch[1]);
                }
            }
            
            if (originalSuccess) {
                originalSuccess.apply(this, arguments);
            }
        };
        
        options.error = function(xhr, status, error) {
            console.error('Request failed:', new Date());
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('Response Text:', xhr.responseText.substring(0, 500) + '...');
            
            if (originalError) {
                originalError.apply(this, arguments);
            }
        };
        
        options.complete = function(xhr, status) {
            console.log('Request completed:', new Date());
            console.log('Final status:', status);
            console.groupEnd();
            
            if (originalComplete) {
                originalComplete.apply(this, arguments);
            }
        };
        
        // Call the original $.ajax with our modified options
        return originalAjax.call($, options);
    };
    
    console.log('AJAX Debug Helper initialized');
})(jQuery);
