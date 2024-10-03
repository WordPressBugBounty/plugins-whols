<?php
/**
 * Wholesaler Registration.
 *
 * @since 1.0.0
 */
namespace Whols\Frontend;

/**
 * Wholesaler_Login_Register class.
 */
class Wholesaler_Login_Register{

    /**
     * Wholesaler_Login_Register constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        // registration form shortcode
        add_shortcode( 'whols_registration_form', array( $this, 'registration_form_shortcode' ) );

        // redirect after login
        if( whols_get_option('redirect_page_customer_login') ){
            add_filter( 'woocommerce_login_redirect', array( $this, 'wholesaler_wc_login_redirect' ), 9999, 2 );
            add_filter('login_redirect', array( $this, 'wholesaler_wp_login_redirect' ), 9999, 3);
        }
    }

    /**
     * Register user shotcode callback function
     *
     * @since 1.0.0
     */
    public function registration_form_shortcode( $atts ){
        ob_start();
        ?>
            <?php if( !is_user_logged_in() ): ?>

                <div id="whols_user_reg_message" class="whols_user_reg_message">&nbsp;</div>
                <div class="whols_registration_form">
                    <form action="whols_registration_action">
                        <?php
                            $fields = whols_get_registration_fields();

                            foreach( $fields as $key => $field ){
                                $type        = !empty($field['type']) ? $field['type'] : '';
                                $value       = !empty($field['value']) ? $field['value'] : null;
                                $label       = !empty($field['label']) ? $field['label'] : '';
                                $placeholder = !empty($field['placeholder']) ? $field['placeholder'] : '';
                                $description = !empty($field['description']) ? $field['description'] : '';
                                $required    = !empty($field['required']) ? 'required' : '';
                                $options     = !empty($field['options']) ? $field['options'] : array();
                                $is_additional    = !empty($field['is_additional']) && $field['is_additional'] ? true : false;
                                $class            =  !empty($field['class']) ? $field['class'] : array();
                                
                                whols_form_field( $key, array( 
                                    'type'          => $type, 
                                    'label'         => $label, 
                                    'placeholder'   => $placeholder, 
                                    'description'   => $description, 
                                    'required'      => $required, 
                                    'is_additional' => $is_additional, 
                                    'options'       => $options,
                                    'class'         => $class,
                                ), $value );
                            }
                        ?>

                        <input type="submit" name="reg_submit" id="whols_reg_submit" value="<?php echo apply_filters( 'whols_registration_submit_label', __( 'Register As Wholesaler', 'whols' ) ); ?>">
                    </form>
                </div>

            <?php else:
                global $wp;
                $current_user = wp_get_current_user();
                $current_url = home_url( add_query_arg( array(), $wp->request ) );
            ?>
                <div class="whols_reg_logged_in">
                    <?php echo esc_html__( 'You are Logged in as: ', 'whols' ) . $current_user->display_name. ' (<a href="'. esc_url( wp_logout_url( $current_url )  ) .'">'. esc_html__( 'Logout', 'whols' ) .'</a>)';
                    ?>
                </div>
            <?php endif; ?>

            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    "use strict";

                    var button_id       = '#whols_reg_submit',
                        nonce           = '<?php echo wp_create_nonce( 'whols_register_nonce' ) ?>',
                        loading_message = '<?php echo esc_html__( 'Please Wait...','whols' ); ?>';

                    $( 'body' ).on('click', '#whols_reg_submit', function( event ){
                        event.preventDefault();

                        $( '#whols_user_reg_message' ).html( '<span class="whols_lodding_msg">'+ loading_message +'</span>' ).fadeIn();

                        $.ajax({
                            type: 'POST',
                            dataType: 'json',  
                            url:  woocommerce_params.ajax_url,
                            data: {
                                action: "whols_ajax_user_register",
                                nonce: nonce,
                                fields: $('.whols_registration_form form').find(':input').serializeJSON({checkboxUncheckedValue: "0"}),
                            },
                            beforeSend: function(){
                            },
                            success: function( response ){
                                if ( response.registerauth == true ){
                                    var redirect_url = response.redirect_url;
                                    $('#whols_user_reg_message').html('<div class="woocommerce"><div class="woocommerce-notices-wrapper"><div class="whols_success_msg woocommerce-message" role="alert">'+ response.message +'</div></div></div>').fadeIn();
                                    $.scroll_to_notices( $( '[role="alert"]' ) );

                                    if( redirect_url.length ){
                                        document.location.href = redirect_url;
                                    }
                                }else{
                                    $('#whols_user_reg_message').html('<div class="woocommerce"><div class="woocommerce-notices-wrapper"><div class="whols_invalid_msg woocommerce-error" role="alert">'+ response.message +'</div></div></div>').fadeIn();
                                    $.scroll_to_notices( $( '[role="alert"]' ) );
                                }
                            },
                            error: function( errorThrown ) {
                                console.log(errorThrown);
                            },
                        });
                    });
                }); // document ready
            </script>
        <?php
        return ob_get_clean();
    }

    /**
     * WooCommerce Redirect After login
     *
     * @since 1.0.0
     */
    public function wholesaler_wc_login_redirect( $redirect, $user ) {
        $current_user_id = $user->ID;
        if ( whols_is_wholesaler( $current_user_id ) ) {
            $redirect_page_customer_login = whols_get_option('redirect_page_customer_login');
            if( $redirect_page_customer_login ){
                $redirect = $redirect_page_customer_login;
            }
        }
      
        return $redirect;
    }

    /**
     * WordPress Redirect After login
     *
     * @since 1.0.0
     */
    public function wholesaler_wp_login_redirect( $redirect_to, $requested_redirect_to, $user ){
        if ( !is_wp_error($user) &&  whols_is_wholesaler( $user->ID ) ) {
            $redirect_page_customer_login = whols_get_option('redirect_page_customer_login');
            if( $redirect_page_customer_login ){
                $redirect_to = $redirect_page_customer_login;
            }
        }
        
        return $redirect_to;
    }
}