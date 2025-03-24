jQuery(function($) {
    'use strict';

    // Show save list form
    $(document).on('click', '.whols-save-order-list-btn', function(e) {
        e.preventDefault();
        $(this).hide();
        $('.whols-save-list-form').slideDown();
    });

    // Hide save list form
    $(document).on('click', '.whols-cancel-save-list', function(e) {
        e.preventDefault();
        $('.whols-save-list-form').slideUp(function() {
            $('.whols-save-order-list-btn').show();
        });
    });

    // Save list
    $(document).on('click', '.whols-save-list-confirm', function(e) {
        e.preventDefault();
        
        var $form = $(this).closest('.whols-save-list-form');
        var listName = $form.find('input[name="whols_list_name"]').val();
        var listDescription = $form.find('textarea[name="whols_list_description"]').val();
        
        if (!listName) {
            alert(wholsSaveList.i18n.emptyName);
            return;
        }
        
        $.ajax({
            url: wholsSaveList.ajaxUrl,
            type: 'POST',
            data: {
                action: 'whols_save_order_list',
                whols_list_name: listName,
                whols_list_description: listDescription,
                nonce: wholsSaveList.nonce
            },
            beforeSend: function() {
                $form.closest('.whols-save-order-list-wrap').addClass('processing').block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });
            },
            success: function(response) {
                if (response.success) {
                    $form.find('input[name="whols_list_name"]').val('');
                    $form.find('textarea[name="whols_list_description"]').val('');
                    $('.whols-save-list-form').slideUp(function() {
                        $('.whols-save-order-list-btn').show();
                    });
                    
                    // Show success message
                    const $message = $('<div class="woocommerce-message" role="alert">' + response.data.message + '</div>');
                    $form.closest('.whols-save-order-list-wrap').before($message);
                } else {
                    alert(response.data.message || wholsSaveList.i18n.error);
                }
            },
            error: function() {
                alert(wholsSaveList.i18n.error);
            },
            complete: function() {
                $form.closest('.whols-save-order-list-wrap').removeClass('processing').unblock();
            }
        });
    });

    // Delete saved list
    $(document).on('click', '.whols-delete-saved-list', function(e) {
        e.preventDefault();
        
        if (!confirm(wholsSaveList.i18n.deleteConfirm)) {
            return;
        }
        
        const $row = $(this).closest('tr');
        const listId = $(this).data('list-id');
        
        $.ajax({
            url: wholsSaveList.ajaxUrl,
            type: 'POST',
            data: {
                action: 'whols_delete_saved_list',
                nonce: wholsSaveList.nonce,
                whols_list_id: listId
            },
            beforeSend: function() {
                $row.addClass('processing').block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });
            },
            success: function(response) {
                if (response.success) {
                    $row.fadeOut(function() {
                        $(this).remove();
                        
                        // Show empty message if no lists remain
                        if ($('.whols-saved-lists-table tbody tr').length === 0) {
                            $('.whols-saved-lists-table').replaceWith(
                                '<p class="woocommerce-message">' + wholsSaveList.i18n.noLists + '</p>'
                            );
                        }
                    });
                } else {
                    alert(wholsSaveList.i18n.error);
                }
            },
            error: function() {
                alert(wholsSaveList.i18n.error);
            },
            complete: function() {
                $row.removeClass('processing').unblock();
            }
        });
    });

    // Add list to cart
    $(document).on('click', '.whols-add-list-to-cart', function(e) {
        e.preventDefault();
        
        const $button = $(this);
        const listId = $button.data('list-id');
        
        $.ajax({
            url: wholsSaveList.ajaxUrl,
            type: 'POST',
            data: {
                action: 'whols_add_list_to_cart',
                nonce: wholsSaveList.nonce,
                whols_list_id: listId
            },
            beforeSend: function() {
                $button.addClass('loading');
                $('.whols-saved-lists-table').block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });
            },
            success: function(response) {
                if (response.success && response.data.redirect) {
                    window.location.href = response.data.redirect;
                } else {
                    alert(wholsSaveList.i18n.error);
                }
            },
            error: function() {
                alert(wholsSaveList.i18n.error);
            },
            complete: function() {
                $button.removeClass('loading');
                $('.whols-saved-lists-table').unblock();
            }
        });
    });
});