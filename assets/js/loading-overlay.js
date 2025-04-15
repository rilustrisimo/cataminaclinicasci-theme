(function($) {
    var LoadingOverlay = {
        init: function() {
            this.pageOverlay = $('.page-loading-overlay');
            this.ajaxOverlay = $('.page-loading-overlay--ajax');
            
            // Track AJAX requests globally
            $(document).ajaxStart(function() {
                $('body').addClass('ajax-loading');
            }).ajaxStop(function() {
                $('body').removeClass('ajax-loading');
            });
            
            // Hide page loading overlay when everything is ready
            $(window).on('load', function() {
                LoadingOverlay.hidePageOverlay();
            });
            
            // Timeout as a fallback in case some assets don't load properly
            setTimeout(function() {
                LoadingOverlay.hidePageOverlay();
            }, 5000);
        },
        
        hidePageOverlay: function() {
            this.pageOverlay.addClass('hidden');
            // Remove from DOM after transition completes
            setTimeout(function() {
                LoadingOverlay.pageOverlay.remove();
            }, 500);
        },
        
        showAjaxOverlay: function() {
            $('body').addClass('ajax-loading');
        },
        
        hideAjaxOverlay: function() {
            $('body').removeClass('ajax-loading');
        }
    };
    
    $(function() {
        LoadingOverlay.init();
    });
    
    // Make accessible globally
    window.LoadingOverlay = LoadingOverlay;
})(jQuery);
