<?php
namespace Whols;

class Request_Quote {
    public function __construct() {
        $enable_request_a_quote = whols_get_option('enable_request_a_quote');

        if( !$enable_request_a_quote ){
            return;
        }

        // Add custom quote button to the cart page.
        add_action( 'woocommerce_cart_actions', array( $this, 'add_reqest_quote_button' ) );
        add_action( 'woolentor_cart_actions', array( $this, 'add_reqest_quote_button' ) );

        // Add js template for the quote modal.
        add_action( 'wp_footer', array( $this, 'add_modal_markup' ) );

        // Ajax - Request a quote
        add_action( 'wp_ajax_whols_open_raq_modal', array( $this, 'open_raq_modal') );
        add_action( 'wp_ajax_nopriv_whols_open_raq_modal', array( $this, 'open_raq_modal') );

        add_action( 'wp_ajax_whols_request_raq_form_submit', array( $this, 'raq_form_submit') );
        add_action( 'wp_ajax_nopriv_whols_request_raq_form_submit', array( $this, 'raq_form_submit') );
    }

    public function add_reqest_quote_button(){
        // @todo don't show if the cart value is less than 1
        $request_a_quote_label = whols_get_option('request_a_quote_label', esc_html__( 'Request a Quote', 'whols' ), true);
        ?>
        <a href="#" data-location="cart" class="whols-request-a-quote button alt"><?php echo esc_html($request_a_quote_label); ?></a>
        <?php
    }

    public function add_modal_markup(){
        wp_enqueue_style('dashicons');
    }

    public function raq_form_submit(){
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        // Default values
        $defaults = include WHOLS_PATH . '/includes/Admin/defaults.php';
        $raq_fields = $defaults['raq_fields'];
        $raq_data_defaults = $defaults['raq_data_defaults'];

        if ( !wp_verify_nonce( $nonce, 'whols_nonce' ) ) {
            wp_send_json_error(array(
                'message' => esc_html__( 'Oops! Something went wrong while checking the security token. Please try again or refresh the page, and if the issue persists, get in touch with us!', 'whols' )
            ));
        }

        $posted_data = !empty($_REQUEST['fields']) ? $_REQUEST['fields'] : array();
        $post_data = wp_parse_args($posted_data, $raq_data_defaults );

        $validation_result = $this->get_user_inputs_validation_status( $raq_fields, $post_data );

        if( !$validation_result['success'] ){
            wp_send_json_error(array(
                'message' => $validation_result['message']
            ));
        }

        // No need to validate location
        $posted_data['location'] = !empty($_REQUEST['location']) ? sanitize_text_field($_REQUEST['location']) : '';

		// For sending email
        do_action( 'whols_after_raq_form_submit', $posted_data );

        wp_send_json_success(array(
            'message' => esc_html__('Your request has been submitted successfully. We will get back to you soon.', 'whols')
        ));
    }


    public function open_raq_modal(){
        ob_start();
        include_once( WHOLS_PATH . '/includes/request-a-quote/html-modal.php' );
        $modal_content = ob_get_clean();

        wp_send_json_success(array(
            'modal_content' => $modal_content
        ));
    }

    /**
     * Validate each fields of the submitted
     *
     * @param $form_fields
     * @param $posted_data
     *
     * @return array
     */
    public function get_user_inputs_validation_status( $form_fields, $posted_data ){
        $msg_arr = array(
            'success' => false,
            'message' => ''
        );

        // Loop through each field
        foreach( $form_fields as $field_key => $field_info ){
            $required = !empty($field_info['required']) ? $field_info['required'] : false;

            if( $field_info ){
                // Check if the field is required
                if( $required && empty($posted_data[$field_key]) ){
                    $msg_arr['message'] = sprintf(
                        '%1$s %2$s %3$s',
                        $field_info['label'],
                        esc_html__('Field is required.', 'whols'),
                        esc_html__('Please fill it up.', 'whols')
                    );

                    break;
                }

                // Email field validation
                if( $field_info['type'] == 'email' && !empty($posted_data[$field_key]) ){
                    if( !is_email($posted_data[$field_key]) ){
                        $msg_arr['message'] = esc_html__('Invalid email address.', 'whols');
                        break;
                    }
                }
            }
        }

        if( empty($msg_arr['message']) ){ // No validation issue found
            $msg_arr['success'] = true;
        }

        return $msg_arr;
    }
}