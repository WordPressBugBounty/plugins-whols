/**
 * Whols Admin JS
 *
 * @since 1.0.0
 */
;( function ( $ ) {
    'use strict';

    $( document ).ready( function () {
    	$('body').on('click', '.whols_button_clone', function() {
    		var id = $(this).data('id');
    	    if( id != undefined ){
    	    	$(this).parent().find('.whols_product_meta_type_2_pricing_wrapper').append('<span class="wrap whols_product_meta_wrap"><span class="whols_field_wrap"><span class="whols_lbl">Role</span><select name="whols_price_type_2_role_'+ id +'[]"><option value="any_role">Any Role</option><option value="whols_default_role">Default Role</option><option value="role-1">Role 1</option><option value="role-2">Role 2</option></select></span><span class="whols_field_wrap"><span class="whols_lbl">Price</span><input name="whols_price_type_2_price_'+ id +'[]" class="" type="number" step="any" min="0" value=""></span><span class="whols_field_wrap"><span class="whols_lbl">Min. Quantity</span><input name="whols_price_type_2_min_quantity_'+ id +'[]" class="" type="number" step="any" min="0" value=""></span><i class="dashicons-before dashicons-no"></i></span>');

    	    	return false; //prevent form submission
    		} else {
    	    	$(this).parent().find('.whols_product_meta_type_2_pricing_wrapper').append('<span class="wrap whols_product_meta_wrap"><span class="whols_field_wrap"><span class="whols_lbl">Role</span><select name="whols_price_type_2_role[]"><option value="any_role">Any Role</option><option value="whols_default_role">Default Role</option><option value="role-1">Role 1</option><option value="role-2">Role 2</option></select></span><span class="whols_field_wrap"><span class="whols_lbl">Price</span><input name="whols_price_type_2_price[]" class="" type="number" step="any" min="0" value=""></span><span class="whols_field_wrap"><span class="whols_lbl">Min. Quantity</span><input name="whols_price_type_2_min_quantity[]" class="" type="number" step="any" min="0" value=""></span><i class="dashicons-before dashicons-no"></i></span>');
    	    }
    	});

        // active settigns page
        if (typeof whols_is_settings_page != "undefined" && whols_is_settings_page === 1){
            $('li.toplevel_page_whols-admin .wp-first-item').addClass('current');
        }

        // help image
        $('.csf-title .dashicons-before').on('mouseover', function(){
            $(this).parent().find('.whols_help_image').show();
        }).on('mouseout',function(){
            $(this).parent().find('.whols_help_image').hide();
        });

        // review fields
        $('body').on('click', '.whols_product_meta_wrap i', function(){
            $(this).parent().remove();
        });

        // pro notice
        $('.whols_pro, .taxonomy-whols_role_cat #addtag input[type="submit"], .whols_pro_opacity').on('click', function(e){
            e.preventDefault();
            var $element = $(this);
            vex.dialog.open({
                unsafeMessage: `
                    <h3>Pro version is required.</h3>
                    <p>Our free version is great, but it doesn't have all our advanced features. The best way to unlock all of the features in our plugin is by purchasing the pro version.</p>
                    <a target="_blank" href="https://hasthemes.com/plugins/whols-woocommerce-wholesale-prices/">Buy Pro</a>`,
                className: "vex-theme-plain",
                showCloseButton: true,
                buttons: [],
                contentClassName: 'whols_pro_notice',
            });
        });

        // 
        // Field Manager
        //
        $('.csf-cloneable-wrapper .csf-cloneable-value').each(function(){
            var $this = $(this),
                $parent = $this.closest('.csf-cloneable-item');

            var default_fields = ['reg_name', 'reg_username', 'reg_email', 'reg_password'];
            if( $.inArray( $this.text(), default_fields ) > -1 ){

                // Remove handlers
                $(this).closest('.csf-cloneable-item').find('.csf-cloneable-helper').remove();

                // Disable fields
                $parent.find('.csf-field-select').hide();
                $parent.find('.csf-field-checkbox').hide();
                
            } else {
                $parent.find('optgroup[label="Default"]').remove();
            }
        });

        // Disable default field options
        $('.csf-cloneable-hidden optgroup[label="Default"]').each(function(){
            var $this = $(this);
                $this.remove();
        });

        $('a.button.button-primary.csf-cloneable-add').off('click');
    });

    
    // On Document ready
    $( document ).ready(function(){
        // Change Field manager can't add more text
        $('.whols_field_manager .csf-cloneable-alert.csf-cloneable-max').text('Adding custom fields requires the Pro version.');

        // Add "New!" ribbon
        var $selector = $('.whols_global_options [data-tab-id="registration-login/fields-manager"], .whols_global_options [data-tab-id="message-email-notifications/custom-thank-you-message"]');
        $selector.append(' <h1 class="whols-ribbon">New!</h1>');
    });

    // Remove event listener and display our notice
    $( window ).load(function(){
        $('.toplevel_page_whols-admin  .csf-cloneable-add').off('click').on('click', function(e){
            e.preventDefault();

            vex.dialog.open({
                unsafeMessage: `
                    <h3>Pro Version is Required.</h3>
                    <p>Adding custom fields requires the Pro version.</p>
                    <p>Our free version is great, but it doesn't have all our advanced features. The best way to unlock all of the features in our plugin is by purchasing the pro version.</p>
                    <a target="_blank" href="https://hasthemes.com/plugins/whols-woocommerce-wholesale-prices/">Buy Pro</a>`,
                className: "vex-theme-plain",
                showCloseButton: true,
                buttons: [],
                contentClassName: 'whols_pro_notice whols_custom_field_notice',
            });
        });
    });

} )( jQuery );