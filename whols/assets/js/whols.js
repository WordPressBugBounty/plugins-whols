/*
 * JS for both frontend and backend.
 */

;( function ( $ ) {
    'use strict';

    if ( typeof whols_params === 'undefined' ) {
		return false;
	}

    const whols = {
        selectors: {    
            sendButton: '.whols-message-box button',
            modalForm: '.whols-raq-form',
            loadingIcon: '<i class="dashicons dashicons-update"></i>',
            modalFormSubmit: '.whols-raq-modal button[type="submit"]',
            modalFormMessage: '.whols-raq-modal .whols-raq-form-message',
        },
        conversationID: '',
        sendarType: '',

        init: function() {
            $( document ).on( 'click', '.whols-request-a-quote,.whols-start-conversation', this.openModalAjax );
            $( document ).on( 'submit', whols.selectors.modalForm, this.submitRequestQuoteForm );

            $( document ).on( 'click', '.whols-raq-modal-dismiss', whols.dismissModal );
        },

        submitRequestQuoteForm: function( e ) {
			e.preventDefault();
		
			const $form = $( this );

            const $button = $( whols.selectors.modalFormSubmit );
            const postedData = whols.getFormDataAfterSubmit( $form );
            
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url:  whols_params.ajax_url,
                data: {
                    action: "whols_request_raq_form_submit",
                    nonce: whols_params.nonce,
                    fields: postedData,
                    location: $(whols.selectors.modalFormSubmit).data('location'),
                },
                beforeSend: function(){
                    whols.showLoadingButton( $button );
                },
                success: function( response ){
                    if( response.success ){
                        whols.displayMessage( response.data.message, 'success' );
                        $( whols.selectors.modalForm ).trigger( 'reset' );
                    } else {
                        whols.displayMessage(response.data.message, 'error');
                    }
                },
                complete:function( response ){
                    whols.hideLoadingButton( $button );
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    var errorMessage = "An error occurred during the AJAX request.";

                    if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                        errorMessage = jqXHR.responseJSON.message;
                    } else if (textStatus === "timeout") {
                        errorMessage = "The request timed out. Please try again.";
                    } else if (textStatus === "abort") {
                        errorMessage = "The request was aborted. Please try again.";
                    }

                    whols.displayMessage(errorMessage, 'error');
					console.log( 'error', errorThrown );
                },
            });
		},

        getFormDataAfterSubmit: function( $form ){
            const postedData = {};
			const formData = new FormData($form[0]);
			[...formData.entries()].forEach(([key, value]) => {
				postedData[key] = value;
			});

            return postedData;
        },

		openModalAjax: function( e ) {
			e.preventDefault();

            const location = $(e.target).data('location');
            const target = $(e.target);
            
			$.ajax({
				url: whols_params.ajax_url,
				type: 'POST',
				data: {
					action: 'whols_open_raq_modal',
					nonce: whols_params.nonce,
                    fields: {
                        location: location
                    }
				},
				beforeSend: function() {
					whols.showLoadingButton( target );
				},
				success: function( response ) {
					if( response.success && response.data.modal_content ){
						$('body').append( $(response.data.modal_content) );
					} else {
						console.log( 'error', response );
					}
				},
				complete: function() {
					whols.hideLoadingButton( target );
				},
				error: function( response ) {
					console.log( 'error', response );
				}
			});

			$('body').addClass('whols-raq-modal-open');
		},

        showLoadingButton: function( $button ){
            $button.append( whols.selectors.loadingIcon );
        },

        hideLoadingButton: function( $button ){
            $button.find('.dashicons-update').remove();
        },

        displayMessage: function( message, type ){
            let noticeClass = 'woocommerce-message';
            if( type == 'error' ){
                noticeClass = 'woocommerce-error'
            }

            $( whols.selectors.modalFormMessage ).html('<div class="woocommerce"><div class="woocommerce-notices-wrapper"><div class=" '+ noticeClass +' " role="alert">'+ message +'</div></div></div>');
        },

        dismissModal: function( e ) {
			e.preventDefault();

			$('.whols-raq-modal').remove();
		},

    }

    $(document).ready(function(){
        whols.init();
    });
} )( jQuery );