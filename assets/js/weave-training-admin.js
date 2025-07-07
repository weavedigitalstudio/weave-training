/**
 * Weave Training Admin JavaScript
 * Handles full-screen seamless iframe display
 */

(function($) {
    'use strict';

    // Document ready
    $(document).ready(function() {
        initTrainingPage();
    });

    /**
     * Initialize full-screen training page
     */
    function initTrainingPage() {
        var $container = $('#weave-training-container');
        var $loading = $('#weave-training-loading');

        // Check if we're on the training page
        if ($container.length === 0) {
            return;
        }

        // Add class to body for full-screen styling
        $('body').addClass('weave-training-active');

        // Get training data from localized script
        if (typeof weaveTraining === 'undefined') {
            console.error('Weave Training: Configuration not found');
            $loading.html('<p>Configuration error. Please contact support.</p>');
            return;
        }

        // Create iframe element
        createFullScreenIframe();

        // Handle iframe communication (if needed)
        handleIframeMessages();
    }

    /**
     * Create full-screen iframe
     */
    function createFullScreenIframe() {
        var $container = $('#weave-training-container');
        var $loading = $('#weave-training-loading');

        // Create iframe
        var iframe = document.createElement('iframe');
        iframe.id = 'weave-training-iframe';
        iframe.src = weaveTraining.url;
        iframe.width = '100%';
        iframe.height = '100%';
        iframe.frameBorder = '0';
        iframe.title = weaveTraining.title;
        
        // Add sandbox attributes for security
        iframe.setAttribute('sandbox', 'allow-scripts allow-same-origin');

        // Handle iframe load event
        $(iframe).on('load', function() {
            // Hide loading indicator
            $loading.fadeOut(300);
            $container.addClass('loaded');
            
            console.log('Weave Training: Iframe loaded successfully');
        });

        // Handle iframe error
        $(iframe).on('error', function() {
            console.error('Weave Training: Iframe failed to load');
            $loading.html('<p>' + weaveTraining.errorText + '</p>');
        });

        // Add fallback content for browsers that don't support iframes
        iframe.innerHTML = '<p>' + weaveTraining.unsupportedText + '</p>';

        // Append iframe to container
        $container.append(iframe);
    }



    /**
     * Handle iframe postMessage communication
     */
    function handleIframeMessages() {
        // Listen for messages from iframe
        window.addEventListener('message', function(event) {
            // Verify origin (you can add specific origin checks here)
            if (event.origin !== window.location.origin) {
                // For now, we'll allow all origins since training content is external
                // In production, you might want to restrict this
            }

            // Handle different message types
            if (event.data && typeof event.data === 'object') {
                switch (event.data.type) {
                    case 'resize':
                        handleIframeResize(event.data);
                        break;
                    case 'ready':
                        handleIframeReady(event.data);
                        break;
                    default:
                        // Log unknown message types for debugging
                        console.log('Weave Training: Unknown message type:', event.data.type);
                }
            }
        });
    }

    /**
     * Handle iframe resize messages
     */
    function handleIframeResize(data) {
        var $iframe = $('#weave-training-iframe');
        
        if (data.height && data.height > 0) {
            $iframe.css('height', data.height + 'px');
        }
    }

    /**
     * Handle iframe ready messages
     */
    function handleIframeReady(data) {
        var $container = $('#weave-training-container');
        var $loading = $('#weave-training-loading');
        
        // Hide loading indicator
        $loading.fadeOut(300);
        $container.addClass('loaded');
        
        console.log('Weave Training: Iframe ready');
    }

})(jQuery); 