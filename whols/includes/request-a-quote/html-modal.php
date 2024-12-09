<?php
    $request_a_quote_label    = whols_get_option('request_a_quote_label', esc_html__( 'Request a Quote', 'whols' ), true);
    $start_conversation_label = whols_get_option('start_conversation_label', esc_html__( 'Start Conversation', 'whols' ), true);
    $submit_button_label      = whols_get_option('submit_button_label', esc_html__( 'Submit', 'whols' ), true);

    // Include defaults
    $defaults = include WHOLS_PATH . '/includes/Admin/defaults.php';
    $form_fields = $defaults['raq_fields'];

    $defaults = array(
        'location'  => '',
    );

    $posted_data = !empty($_REQUEST['fields']) ? wp_parse_args($_REQUEST['fields'], $defaults) : $defaults;
?>
<div class="whols-raq-modal-area whols-raq-modal">
    <div class="whols-raq-modal-overlay"></div>
    <div class="whols-raq-modal-inner">
        <div class="whols-raq-modal-header">
            <div class="whols-raq-modal-title">
                <?php 
                    if( $posted_data['location'] == 'cart' ){
                        echo wp_kses_post($request_a_quote_label);
                    } elseif( $posted_data['location'] == 'conversation' ){
                        echo wp_kses_post($start_conversation_label);
                    }
                ?>         
            </div>
            <span class="whols-raq-modal-dismiss">
                <svg version="1.1" width="18" height="28" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 496.096 496.096" xml:space="preserve">
                    <path d="M259.41,247.998L493.754,13.654c3.123-3.124,3.123-8.188,0-11.312c-3.124-3.123-8.188-3.123-11.312,0L248.098,236.686 L13.754,2.342C10.576-0.727,5.512-0.639,2.442,2.539c-2.994,3.1-2.994,8.015,0,11.115l234.344,234.344L2.442,482.342 c-3.178,3.07-3.266,8.134-0.196,11.312s8.134,3.266,11.312,0.196c0.067-0.064,0.132-0.13,0.196-0.196L248.098,259.31 l234.344,234.344c3.178,3.07,8.242,2.982,11.312-0.196c2.995-3.1,2.995-8.016,0-11.116L259.41,247.998z" fill="#000" data-original="#000000"></path>
                </svg>
            </span>
        </div>
        <div class="whols-raq-modal-body">
            <form class="whols-raq-form">
                <?php
                $products_data = array();
                
                if( $posted_data['location'] == 'cart' ){
                    foreach( WC()->cart->get_cart() as $cart_item ){
                        $product_id = $cart_item['product_id'];
                        $variation_id = $cart_item['variation_id'];
                        $quantity = $cart_item['quantity'];
                        $price = $cart_item['data']->get_price();
                        $name = $cart_item['data']->get_name();

                        $products_data[] = array(
                            'product_id' => $product_id,
                            'variation_id' => $variation_id,
                            'quantity' => $quantity,
                            'price' => $price,
                            'name' => $name,
                        );
                    }

                    $products_data = json_encode($products_data);
                } 

                $form_fields['products_data']['value'] = $products_data;

                if(WC()->customer->get_display_name()){
                    $form_fields['name']['value'] = WC()->customer->get_display_name();
                }

                // Fields
                foreach( $form_fields as $field_key => $field ){
                    if( !empty($field['type']) && $field['type'] == 'email' ){
                        woocommerce_form_field($field_key, $field, WC()->customer->get_email()); // Customer can change their email manually
                    } else if( !empty($field['value']) ) {
                        woocommerce_form_field($field_key, $field, $field['value']);
                    } else {
                        woocommerce_form_field($field_key, $field);
                    }
                }
                ?>
                
                <button type="submit" data-location="<?php echo esc_attr($posted_data['location']); ?>"><?php echo wp_kses_post($submit_button_label); ?></button>

                <div class="whols-raq-form-message"></div>
            </form>
        </div>
    </div>
</div>