<?php
/**
 * Whols ajax actions.
 *
 * All functions of ajax action of the plugin.
 *
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
/**
 * Ajax taxonomy filter.
 *
 * @since 1.0.0
 */
if( !function_exists('whols_ajax_user_register') ){
    function whols_ajax_user_register(){
        $post_data = wp_unslash($_POST);
    
        // Verify nonce
        $nonce = sanitize_text_field($_REQUEST['nonce']);
    
        if ( !wp_verify_nonce( $nonce, 'whols_register_nonce' ) ) {
            wp_send_json_error(array(
                'message' => esc_html__( 'No naughty business please!', 'whols' )
            ));
        }
    
        // posted data
        $posted_data = $post_data['fields'];
        $additional_fields = array();
    
        foreach( $posted_data as $field_name => $value ){
            if( str_starts_with($field_name, '_whols_') ){
                $additional_fields[$field_name] = $value;
            }
        }  
    
        $role                                = 'subscriber';
        $pricing_model                       = 'single_role';
        $default_wholesale_role              = 'whols_default_role';
        $enable_auto_approve                 = false;
        $redirect_page_customer_registration = whols_get_option('redirect_page_customer_registration');
    
        $successful_message_for_auto_approve   = whols_get_option('registration_successful_message_for_auto_approve');
        $successful_message_for_manual_approve = whols_get_option('registration_successful_message_for_manual_approve');
       
        $success_message = $successful_message_for_manual_approve? $successful_message_for_manual_approve : esc_html__( 'Thank you for registering. Your account will be reviewed by us & approve manually. Please wait to be approved.', 'whols' );
    
        $user_data = array(
            'first_name' => !empty( $posted_data['reg_name'] ) ? sanitize_text_field( $posted_data['reg_name'] ) : "",
            'user_login' => !empty( $posted_data['reg_username'] ) ? sanitize_text_field( $posted_data['reg_username'] ) : "",
            'user_email' => !empty( $posted_data['reg_email'] ) ? sanitize_email( $posted_data['reg_email'] ) : "",
            'user_pass'  => !empty( $posted_data['reg_password'] ) ? sanitize_user( $posted_data['reg_password'] ) : "",
            'role'       => $role
    
        );
    
        if( whols_get_user_reg_validation_status( $posted_data ) !== true ){
    
            echo whols_get_user_reg_validation_status( $posted_data );
    
        } else {
    
            $user_id = wp_insert_user( $user_data );
    
            if ( is_wp_error( $user_id ) ){
                echo json_encode( [ 
                    'registerauth' => false, 
                    'message'=> esc_html__('Something is wrong please check again!', 'whols') ] 
                );
            } else {
                // add custom fields for the user
                foreach( $additional_fields as $key => $value ){
                    add_user_meta( $user_id, $key, $value, true );
                }
    
                $status = $enable_auto_approve ? 'approve' : '';
                $post_arr = array(
                    'post_title'   => $user_data['user_login'],
                    'post_status'  => 'publish',
                    'post_type'    => 'whols_user_request',
                    'meta_input'   => array(
                        'whols_user_request_meta' => array (
                            'user_id'       => $user_id,
                            'current_role'  => $role,
                            'assign_role'   => $role,
                            'status'        => $status,
                        )
                    ),
                );
    
                $post_id = wp_insert_post( $post_arr );
    
                if(!is_wp_error($post_id) && $post_id){
                    update_user_meta( $user_id, 'whols_request_id', $post_id );
                }
    
                echo json_encode( [ 
                    'registerauth' => true, 
                    'redirect_url' => $redirect_page_customer_registration, 
                    'message'      => $success_message
                    ] 
                );
    
                do_action('whols_user_registration_success', $user_id);
            }
        }
    
        wp_die();
    }
}

add_action( 'wp_ajax_nopriv_whols_ajax_user_register', 'whols_ajax_user_register' );
add_action( 'wp_ajax_whols_ajax_user_register', 'whols_ajax_user_register' );