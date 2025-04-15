/**
 * Whols Popup functionality
 * 
 * Handles all popup-related functionality including:
 * - Opening and closing popups
 * - Loading content via AJAX
 * - Managing popup events
 * - Programmatic popup control
 */
;(function($) {
    'use strict';
    
    // Check if the required parameters exist
    if (typeof whols_popup === 'undefined') {
        return false;
    }
    
    /**
     * Centralized selectors grouped by functional area
     */
    const SELECTORS = {
        popup: {
            container: '.whols-popup',
            active: '.whols-popup.active',
            content: '.whols-popup__content',
            title: '.whols-popup__title',
            close: '.whols-popup__close',
            loading: '.whols-popup__loading',
            error: '.whols-popup__error'
        },
        overlay: {
            container: '#whols-popup-overlay',
            active: '.whols-popup-overlay--active'
        },
        triggers: {
            button: '[data-role="popup-trigger"]'
        }
    };
    
    /**
     * Main Popup controller
     */
    const WholsPopup = {
        /**
         * Initialize the module
         */
        init: function() {
            this.bindEvents();
        },
        
        /**
         * Bind all event listeners
         */
        bindEvents: function() {
            // Document-level event delegation for dynamic elements
            $(document)
                // Trigger and close buttons
                .on('click', SELECTORS.triggers.button, Events.triggerPopup)
                .on('click', SELECTORS.popup.close, Events.closePopup)
                
                // Close popup on overlay click if enabled
                .on('click', SELECTORS.overlay.container, function(e) {
                    if (e.target !== this) return;
                    
                    var $activePopup = $(SELECTORS.popup.active);
                    if ($activePopup.data('close-overlay') === 'true') {
                        Events.closePopup();
                    }
                })
                
                // Close on ESC key
                .on('keyup', function(e) {
                    if (e.key === "Escape" && $(SELECTORS.popup.active).length > 0) {
                        Events.closePopup();
                    }
                });
        }
    };
    
    /**
     * Event Handlers
     * Handles all popup-related events
     */
    const Events = {
        /**
         * Trigger popup open
         */
        triggerPopup: function(e) {
            e.preventDefault();
            
            var $trigger = $(this);
            var popupName = $trigger.data('popup-name');
            var popupId = 'whols-popup-' + popupName;
            var data = {};
            
            // Collect all data attributes
            $.each($trigger.data(), function(key, value) {
                if (key !== 'popupType') {
                    data[key] = value;
                }
            });
            
            // Hide all popups
            $(SELECTORS.popup.container).removeClass('active');
            
            // Show overlay
            $(SELECTORS.overlay.container).addClass('whols-popup-overlay--active');
            
            // Show specific popup
            $('#' + popupId).addClass('active');
            
            // Trigger before content load event
            $(document).trigger('whols/popup/before_load', [popupName, data]);
            
            // Get any content via AJAX if needed
            Content.loadContent(popupName, data);
            
            // Trigger after open event
            $(document).trigger('whols/popup/opened', [popupName, data]);
            
            return false;
        },
        
        /**
         * Close active popup
         */
        closePopup: function() {
            var $activePopup = $(SELECTORS.popup.active);
            var popupName = $activePopup.data('popup-name');
            
            // Trigger before close event
            $(document).trigger('whols/popup/before_close', [popupName]);
            
            // Hide all popups and overlay
            $activePopup.removeClass('active');
            $(SELECTORS.overlay.container).removeClass('whols-popup-overlay--active');
            
            // Clear content after animation completes
            setTimeout(function() {
                if (!$(SELECTORS.popup.container).hasClass('active')) {
                    $(SELECTORS.popup.content).empty();
                }
            }, 300);
            
            // Trigger after close event
            $(document).trigger('whols/popup/closed', [popupName]);
            
            return false;
        }
    };
    
    /**
     * Content Management
     * Handles loading and managing popup content
     */
    const Content = {
        /**
         * Load popup content
         */
        loadContent: function(popupName, data) {
            var $content = $('#whols-popup-' + popupName + '-content');
            
            // Default loading state
            $content.html('<div class="whols-popup__loading">Loading...</div>');
            
            // Trigger load event - extensions can hook into this to load content
            var evt = $.Event('whols/popup/load_content');
            $(document).trigger(evt, [popupName, data, $content]);
            
            // If event was not prevented, do default AJAX loading
            if (!evt.isDefaultPrevented()) {
                // Additional data for the AJAX request
                var ajaxData = {
                    action: 'load_' + popupName + '_popup',
                    nonce: whols_popup.nonce,
                    popup_name: popupName
                };
                
                // Merge with custom data
                $.extend(ajaxData, data);
                
                // Make AJAX request
                $.ajax({
                    url: whols_popup.ajax_url,
                    type: 'POST',
                    data: ajaxData,
                    success: function(response) {
                        if (response.success) {
                            $content.html(response.data.content);
                            
                            if (response.data.title) {
                                $('#whols-popup-' + popupName + '-title').text(response.data.title);
                            }
                            
                            // Trigger content loaded event
                            $(document).trigger('whols/popup/content_loaded', [popupName, response.data]);
                        } else {
                            $content.html('<div class="whols-popup__error">' + 
                                          (response.data.message || 'Error loading content') + 
                                          '</div>');
                        }
                    },
                    error: function() {
                        $content.html('<div class="whols-popup__error">Error loading content. Please try again.</div>');
                    }
                });
            }
        },
        
        /**
         * Set popup title
         */
        setTitle: function(popupName, title) {
            $('#whols-popup-' + popupName + '-title').text(title);
        },
        
        /**
         * Set popup content directly (no AJAX)
         */
        setContent: function(popupName, content) {
            $('#whols-popup-' + popupName + '-content').html(content);
            $(document).trigger('whols/popup/content_loaded', [popupName, { content: content }]);
        }
    };
    
    /**
     * API - Public methods for external use
     */
    const API = {
        
        /**
         * Open a popup programmatically
         */
        openPopup: function(popupName, data) {
            data = data || {};
            
            // Hide all popups
            $(SELECTORS.popup.container).removeClass('active');
            
            // Show overlay
            $(SELECTORS.overlay.container).addClass('whols-popup-overlay--active');
            
            // Show specific popup
            $('#whols-popup-' + popupName).addClass('active');
            
            // Trigger before content load event
            $(document).trigger('whols/popup/before_load', [popupName, data]);
            
            // Get any content via AJAX if needed
            Content.loadContent(popupName, data);
            
            // Trigger after open event
            $(document).trigger('whols/popup/opened', [popupName, data]);
        },
        
        /**
         * Close the currently active popup
         */
        closePopup: function() {
            Events.closePopup();
        },
        
        /**
         * Set the title of a popup
         */
        setTitle: function(popupName, title) {
            Content.setTitle(popupName, title);
        },
        
        /**
         * Set popup content directly (no AJAX)
         */
        setContent: function(popupName, content) {
            Content.setContent(popupName, content);
        }
    };
    
    // Make API globally accessible
    window.WholsPopup = API;
    
    // Initialize on document ready
    $(document).ready(function() {
        WholsPopup.init();
    });
})(jQuery);