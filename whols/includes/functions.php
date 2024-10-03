<?php
/**
 * Whols Functions
 *
 * Necessary functions of the plugin.
 *
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Get global options value.
 *
 * @since 1.0.0
 *
 * @param string   $option_name Option name.
 * @param null $default Default value.
 *
 * @return string|null
 */
if( !function_exists('whols_get_option') ){
    function whols_get_option( $option_name = '', $default = null ) {
        $options = get_option( 'whols_options' );
    
        return ( isset( $options[$option_name] ) ) ? $options[$option_name] : $default;
    }
}

/**
* Get term meta value.
*
* @since 1.0.0
*
* @param string   $term_id Term ID
* @param null $meta_opt_name Meta key name
*
* @return string
*/
if( !function_exists('whols_get_term_meta') ){
    function whols_get_term_meta( $term_id, $meta_opt_name ){
        $meta_value = get_term_meta( $term_id, $meta_opt_name, true );
    
        return $meta_value;
    }
}

/**
 * List payment gateways.
 *
 * @since 1.0.0
 *
 * @return array
 */
if(!function_exists('whols_get_payment_gateways')){
    function whols_get_payment_gateways(){
        $gateways = (array) get_option( 'woocommerce_gateway_order' );
        $gateway_list = array();
    
        foreach( $gateways as $key => $gateway ) {
            $gateway_info = get_option('woocommerce_'. $key .'_settings');
    
            if( isset($gateway_info['enabled']) && $gateway_info['enabled'] == 'yes' ) {
                $gateway_list[$key] = $key;
                $gateway_list[$key] = isset($gateway_info['title']) ? $gateway_info['title'] : '';
            }
        }
    
        return $gateway_list;
    }
}

/**
 * List given taxonomy terms
 *
 * @since 1.0.0
 *
 * @return array
 */
if( !function_exists('whols_get_taxonomy_terms') ){
    function whols_get_taxonomy_terms( $taxonomy = 'whols_role_cat' ){
        if( class_exists('WP_Term_Query') ){
            $term_query = new WP_Term_Query(array(
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
            ));
    
            $term_list = array();
            foreach ( $term_query->get_terms() as $term ) {
                $term_list[ $term->slug ] = $term->name;
            }
    
            return $term_list;
        }
    
        return array();
    }
}

/**
 * Validate user registration process
 *
 * @since 1.0.0
 *
 * @return true|json_message
 */
if( !function_exists('whols_get_user_reg_validation_status') ){
    function whols_get_user_reg_validation_status( $posted_data ){
        // Empty check
        $empty_fields = array();
        foreach( $posted_data as $key => $value ){
            $key        = str_replace('_whols_', '', $key);
            $fields     = whols_get_registration_fields();
            $field_info = !empty($fields[$key]) ? $fields[$key] : array();
            $required   = !empty($field_info['required']) ? $field_info['required'] : false;
    
            if($field_info){
                if( !$value && $required ){
                    $empty_fields[] = $field_info['label'];
                }
            }
        }
    
        if($empty_fields){
            return json_encode( [ 
                'registerauth'  => false,
                'message'       => implode(', ', $empty_fields) . esc_html__(  ' cannot be empty.', 'whols') 
            ] );
        }
    
        if( !empty( $posted_data['reg_username'] ) ){
    
            if ( 4 > strlen( $posted_data['reg_username'] ) ) {
                return json_encode( [ 
                    'registerauth' =>false,
                    'message'=> esc_html__('Username too short. At least 4 characters is required', 'whols') 
                ] );
            }
    
            if ( username_exists( $posted_data['reg_username'] ) ){
                return json_encode( [ 
                    'registerauth' =>false, 
                    'message'=> esc_html__('Sorry, that username already exists!', 'whols') 
                ] );
            }
    
            if ( !validate_username( $posted_data['reg_username'] ) ) {
                return json_encode( [ 
                    'registerauth' =>false, 
                    'message'=> esc_html__('Sorry, the username you entered is not valid', 'whols') ] 
                );
            }
    
        }
    
        if( !empty( $posted_data['reg_password'] ) ){
    
            if ( 5 > strlen( $posted_data['reg_password'] ) ) {
                return json_encode( [ 
                    'registerauth' =>false, 
                    'message'=> esc_html__('Password length must be greater than 5', 'whols') ] 
                );
            }
    
        }
    
        if( !empty( $posted_data['reg_email'] ) ){
    
            if ( !is_email( $posted_data['reg_email'] ) ) {
                return json_encode( [ 
                    'registerauth' =>false, 
                    'message'=> esc_html__('Email is not valid', 'whols') ] 
                );
            }
    
            if ( email_exists( $posted_data['reg_email'] ) ) {
                return json_encode( [ 
                    'registerauth' =>false, 
                    'message'=> esc_html__('Email Already in Use', 'whols') ] 
                );
            }
    
        }
    
        return true;
    
    }
}

/**
 * Return wholesaler price
 *
 * @since 1.0.0
 */
if( !function_exists('whols_get_wholesaler_price') ){
    function whols_get_wholesaler_price( $price_type, $price_value, $product = '' ){
        $product_type   = $product->get_type();
        $retailer_price = $product->get_regular_price();
    
        if( $product_type == 'simple' ){
            if( $price_type == 'flat_rate'){
                $wholesaler_price = wc_price( wc_get_price_to_display( $product, array( 
                    'price' => $price_value
                )) ) . $product->get_price_suffix( $price_value );
            } else {
                $wholesaler_price = whols_get_percent_of( $retailer_price, $price_value );
                $wholesaler_price = wc_price( wc_get_price_to_display( $product, array( 
                    'price' => $wholesaler_price
                )) ) . $product->get_price_suffix( $wholesaler_price );
            }
        } elseif( $product_type = 'variable' ){
    
            if( $price_type == 'flat_rate'){
                $price = explode(':', $price_value);
                if( $price_value && count($price) > 1 ){
                    $min_price        = $price[0];
                    $max_price        = $price[1];
    
                    // When both price is same, show only one price
                    if( $min_price ==  $max_price ){
                        $wholesaler_price = wc_price( $min_price );
                    } else {
                        $wholesaler_price = wc_price( $min_price ).' - '. wc_price( $max_price );
                    }
                } else {
                    $wholesaler_price = wc_price( $price_value );
                }
            } else {
                $min_variation_price = $product->get_variation_price();
                $max_variation_price = $product->get_variation_price( 'max' );
    
                $wholesaler_min_variation_price = whols_get_percent_of( $min_variation_price, $price_value );
                $wholesaler_max_variation_price = whols_get_percent_of( $max_variation_price, $price_value );
    
                if( $wholesaler_min_variation_price ==  $wholesaler_max_variation_price ){
                    $wholesaler_price   = wc_price( $wholesaler_min_variation_price );
                } else{
                    $wholesaler_price   = wc_price( $wholesaler_min_variation_price ).' - '. wc_price( $wholesaler_max_variation_price );
                }
            }
        }
    
        return $wholesaler_price;
    }    
}
/**
 * Check if the current user is wholesaler
 *
 * @since 1.0.0
 *
 * @return true|false
 */
if( !function_exists('whols_is_wholesaler') ){
    function whols_is_wholesaler( $user_id = '' ){
        $show_wholesale_price_for = whols_get_option('show_wholesale_price_for');
        if( $show_wholesale_price_for == 'all_users' ){
            return true;
        }
    
        if( empty($user_id) ){
            $user_id = get_current_user_id();
        }
    
        if( $user_id ){
            $user_obj   = get_user_by( 'id', $user_id );
            $user_roles = array_flip( $user_obj->roles );
    
            if( array_intersect_key( $user_roles, whols_get_taxonomy_terms()) ){
                return true;
            }
        }
    
        return false;
    }
}

/**
 * It returns an array of the current user's roles
 * 
 * @param user_id The ID of the user you want to get the roles for. If left blank, it will default to
 * the current user.
 * 
 * @return array of the current user's roles with key => role_name pair.
 */
if( !function_exists('whols_get_current_user_roles') ){
    function whols_get_current_user_roles( $user_id = '' ){
        $user_roles = array();
    
        if( empty($user_id) ){
            $user_id = get_current_user_id();
        }
    
        if( $user_id ){
            $user_obj   = get_user_by( 'id', $user_id );
            $user_roles = $user_obj->roles;
        }
    
        return $user_roles;
    }
}

/**
 * Return price saving (discount) info
 *
 * @since 1.0.0
 */
if( !function_exists('whols_get_price_save_info') ){
    function whols_get_price_save_info( $price_type, $price_value, $product = '' ){
        $product_type   = $product->get_type();
        $retailer_price = $product->get_regular_price();
        $save_info      = '';
        $saving_message = '';
    
        if( $product_type  ==  'simple' ){
            if( $price_type == 'flat_rate' ){
                $save_price = (float) $retailer_price - (float) $price_value;
                $save_info  = $save_price >= 1 ? wc_price( $save_price ) . ' ('. round(whols_get_discount_percent( $retailer_price, $price_value )) .'%)' : '';
            } else {
                $new_wholesaler_price = whols_get_percent_of( $retailer_price, $price_value );
                $save_price           = (float) $retailer_price - (float) $new_wholesaler_price;
                $save_info            = $save_price >= 1 ? wc_price( $save_price ) . ' ('. round(whols_get_discount_percent( $retailer_price, $new_wholesaler_price )) .'%)' : '';

            }
        } elseif( $product_type == 'variable' ){
            $old_min_price = $product->get_variation_price();
            $old_max_price = $product->get_variation_price( 'max' );
    
            if( $price_type == 'flat_rate' ){
                $price = explode(':', $price_value);
    
                if( $price_value && count($price) == 1 ){
                    $save_info = '';
                }elseif( $price_value && count($price) > 1 ){
                    $new_min_price = $price[0];
                    $new_max_price = $price[1];
    
                    $discount1 = round(whols_get_discount_percent( $old_min_price, $new_min_price ));
                    $discount2 = round(whols_get_discount_percent( $old_max_price, $new_max_price ));
    
                    $upto_text = apply_filters('whols_label_upto', __('Upto ', 'whols'));
    
                    if( $new_min_price == $new_max_price ){
                        $save_info = $upto_text . $discount2 .'%';
                    } elseif( $discount1 == $discount2 ){
    
                        $save_info = $discount1 < 1 ? '' : $discount1 .'%';
                        
                    } elseif( $discount1 > $discount2 ){
    
                        $save_info = $discount2 < 1 ?
                                    $upto_text . $discount1 .'%' :
                                    $discount2 .'% - '. $discount1 .'%';
    
                    } elseif( $discount2 > $discount1 ) {
    
                        $save_info = $discount1 < 1 ?
                                    $upto_text . $discount2 .'%' :
                                    $discount1 .'% - '. $discount2 .'%';
    
                    }
                } else {
                    $save_info = round(whols_get_discount_percent( $min_variation_price, $price_value )) .'% - '. round(whols_get_discount_percent( $max_variation_price, $price_value )) .'%';
                }
            } else {
                $save_info = (100 - (int)$price_value) . '%';
            }
        }
    
        if( $save_info ){
            $discount_label_options        = whols_get_option( 'discount_label_options' );
            $discount_percent_custom_label = $discount_label_options['discount_percent_custom_label'];
            
            $saving_message .= '<span class="whols_label">';
            $saving_message .= '<span class="whols_label_left">';
            $saving_message .= $discount_percent_custom_label ? esc_html( $discount_percent_custom_label ) : esc_html__( 'Save: ', 'whols' );
            $saving_message .= '</span>';
            $saving_message .= '<span class="whols_label_right">';
            $saving_message .= $save_info;
            $saving_message .= '</span>';
            $saving_message .= '</span>';
        }
    
        return $saving_message;
    }
}

/**
 * Calculate discount compared by old & new price
 *
 * @since 1.0.0
 */
if( !function_exists('whols_get_discount_percent') ){
    function whols_get_discount_percent( $old_price, $new_price ){
        $decrease =  (float) $old_price - (float) $new_price;
    
        if( $decrease > 0 ){
            $percent = $decrease / (float) $old_price * 100;
            return (float) $percent;
        }
    
        return 0;
    }
}

/**
 * Calculate percent of an amount. e.g: 50 percent of 30 = 15 
 *
 * @since 1.0.0
 */
if( !function_exists('whols_get_percent_of') ){
    function whols_get_percent_of( $x, $percent_limit ){
        if($x){
            $percent = (float) $percent_limit / 100;
            return $x * $percent;
        }
    
        return 0;
    }
}

if( !function_exists('whols_get_registration_fields') ){
    function whols_get_registration_fields(){
        $default_fields = array(
            'reg_name' => array(
                'label'         => __('Name', 'whols'),
                'type'          => 'text',
                'required'      => true,
                'placeholder'   => __('Name', 'whols'),
                'value'         => '',
                'priority'         => 10,
            ),
            'reg_username' => array(
                'label'         => __('Username', 'whols'),
                'type'          => 'text',
                'required'      => true,
                'placeholder'   => __('Username', 'whols'),
                'value'         => '',
                'priority'         => 20,
            ),
            'reg_email' => array(
                'label'         => __('Email', 'whols'),
                'type'          => 'email',
                'required'      => true,
                'placeholder'   => __('Your Email', 'whols'),
                'value'         => '',
                'priority'         => 30,
            ),
            'reg_password' => array(
                'label'         => __('Password', 'whols'),
                'type'          => 'password',
                'required'      => true,
                'placeholder'   => __('Your Password', 'whols'),
                'value'         => '',
                'priority'         => 40,
            ),
        );

        // When the plugin updated to the version 1.1.7 the registration fields value doesn't updated unless click on the save button.
        $fields = array();
        if( whols_get_option('registration_fields') && is_array(whols_get_option('registration_fields')) ){
            $registration_fields = (array) whols_get_option('registration_fields');

            // Prepare and array of the fields with field key as key
            foreach ($registration_fields as $key => $field) {
                $field_name = $field['field'];
                unset($field['field']);

                if( isset($default_fields[$field_name]) ){
                    $fields[$field_name] = wp_parse_args( $field, $default_fields[$field_name] );
                }

                $i = (int) $key + 1;
                $fields[$field_name]['priority'] = 10 * $i;
                $fields[$field_name]['class'] = !empty($fields[$field_name]['class']) ? explode(' ', $fields[$field_name]['class']) : array();
            }
        } else {
            $fields = $default_fields;
        }
    
        // required field properties are label, type, order, is_additional
        $fields = apply_filters( 'whols_registration_fields', $fields );

        // Ordering support
        // Each field must have the order value otherwise ordering support won't work
        $order = array_column($fields, 'priority');
        if(count($order) == count($fields)){
           array_multisort($order, SORT_ASC, $fields); 
        }
        
        return $fields;
    }
}


if ( !function_exists( 'whols_form_field' ) ) {

    /**
     * Outputs registration form field.
     *
     * @param string $key Key.
     * @param mixed  $args Arguments.
     * @param string $value (default: null).
     * @return string
     */
    function whols_form_field( $key, $args, $value = null ) {
        // add __ with the name to detect the field as additional
        if ( !empty($args['is_additional']) && $args['is_additional'] ) {
            $key = '_whols_' . $key;
        }

        $defaults = array(
            'type'              => 'text',
            'label'             => '',
            'description'       => '',
            'placeholder'       => '',
            'maxlength'         => false,
            'required'          => false,
            'autocomplete'      => false,
            'id'                => $key,
            'class'             => array(),
            'label_class'       => array(),
            'input_class'       => array(),
            'return'            => false,
            'options'           => array(),
            'custom_attributes' => array(),
            'validate'          => array(),
            'default'           => '',
            'autofocus'         => '',
            'priority'          => '99',
            'is_additional'     => false,
        );

        $args = wp_parse_args( $args, $defaults );
        
        if ( $args['required'] ) {
            $args['class'][] = 'validate-required';
            $required        = '&nbsp;<abbr class="required" title="' . esc_attr__( 'required', 'whols' ) . '">*</abbr>';
        } else {
            $required = '&nbsp;<span class="optional">(' . esc_html__( 'optional', 'whols' ) . ')</span>';
        }

        if ( is_string( $args['label_class'] ) ) {
            $args['label_class'] = array( $args['label_class'] );
        }

        if ( is_null( $value ) ) {
            $value = $args['default'];
        }

        // Custom attribute handling.
        $custom_attributes         = array();
        $args['custom_attributes'] = array_filter( (array) $args['custom_attributes'], 'strlen' );

        if ( $args['maxlength'] ) {
            $args['custom_attributes']['maxlength'] = absint( $args['maxlength'] );
        }

        if ( ! empty( $args['autocomplete'] ) ) {
            $args['custom_attributes']['autocomplete'] = $args['autocomplete'];
        }

        if ( true === $args['autofocus'] ) {
            $args['custom_attributes']['autofocus'] = 'autofocus';
        }

        if ( $args['description'] ) {
            $args['custom_attributes']['aria-describedby'] = $args['id'] . '-description';
        }

        if ( ! empty( $args['custom_attributes'] ) && is_array( $args['custom_attributes'] ) ) {
            foreach ( $args['custom_attributes'] as $attribute => $attribute_value ) {
                $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
            }
        }

        if ( ! empty( $args['validate'] ) ) {
            foreach ( $args['validate'] as $validate ) {
                $args['class'][] = 'validate-' . $validate;
            }
        }

        $field           = '';
        $label_id        = $args['id'];
        $sort            = $args['priority'] ? $args['priority'] : '';
        $field_container = '<p class="whols-form-row %1$s" id="%2$s" data-priority="' . esc_attr( $sort ) . '">%3$s</p>';

        switch ( $args['type'] ) {
            case 'textarea':
                $field .= '<textarea name="' . esc_attr( $key ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" ' . ( empty( $args['custom_attributes']['rows'] ) ? ' rows="2"' : '' ) . ( empty( $args['custom_attributes']['cols'] ) ? ' cols="5"' : '' ) . implode( ' ', $custom_attributes ) . '>' . esc_textarea( $value ) . '</textarea>';

                break;
            case 'text':
            case 'password':
            case 'datetime':
            case 'datetime-local':
            case 'date':
            case 'month':
            case 'time':
            case 'week':
            case 'number':
            case 'email':
            case 'url':
            case 'tel':
                $field .= '<input type="' . esc_attr( $args['type'] ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '"  value="' . esc_attr( $value ) . '"   required="' . esc_attr( $args['required'] ) . '"  ' . implode( ' ', $custom_attributes ) . ' />';

                break;
            case 'hidden':
                $field .= '<input type="' . esc_attr( $args['type'] ) . '" class="input-hidden ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="' . esc_attr( $value ) . '" ' . implode( ' ', $custom_attributes ) . ' />';

                break;
            case 'select':
                $field   = '';
                $options = '';

                if ( ! empty( $args['options'] ) ) {
                    foreach ( $args['options'] as $option_key => $option_text ) {
                        if ( '' === $option_key ) {
                            // If we have a blank option, select2 needs a placeholder.
                            if ( empty( $args['placeholder'] ) ) {
                                $args['placeholder'] = $option_text ? $option_text : __( 'Choose an option', 'whols' );
                            }
                            $custom_attributes[] = 'data-allow_clear="true"';
                        }
                        $options .= '<option value="' . esc_attr( $option_key ) . '" ' . selected( $value, $option_key, false ) . '>' . esc_html( $option_text ) . '</option>';
                    }

                    $field .= '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="select ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . ' data-placeholder="' . esc_attr( $args['placeholder'] ) . '">
                            ' . $options . '
                        </select>';
                }

                break;
            case 'radio':
                $label_id .= '_' . current( array_keys( $args['options'] ) );

                if ( ! empty( $args['options'] ) ) {
                    foreach ( $args['options'] as $option_key => $option_text ) {
                        $field .= '<input type="radio" class="input-radio ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" value="' . esc_attr( $option_key ) . '" name="' . esc_attr( $key ) . '" ' . implode( ' ', $custom_attributes ) . ' id="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '"' . checked( $value, $option_key, false ) . ' />';
                        $field .= '<label for="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '" class="radio ' . implode( ' ', $args['label_class'] ) . '">' . esc_html( $option_text ) . '</label>';
                    }
                }

                break;

            case 'checkbox':
                $field = '<input type="' . esc_attr( $args['type'] ) . '" class="input-checkbox ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="1" ' . checked( $value, 1, false ) . ' /> ';

                break;

        }

        if ( ! empty( $field ) ) {
            $field_html = '';

            if ( $args['label'] ) {
                $field_html .= '<label for="' . esc_attr( $label_id ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) . '">' . wp_kses_post( $args['label'] ) . $required . '</label>';
            }

            $field_html .= '<span class="whols-input-wrapper">' . $field;

            if ( $args['description'] ) {
                $field_html .= '<span class="description" id="' . esc_attr( $args['id'] ) . '-description" aria-hidden="true">' . wp_kses_post( $args['description'] ) . '</span>';
            }

            $field_html .= '</span>';

            
            $args['class'][] = 'type--'. $args['type'];
            $container_class = esc_attr( implode( ' ', $args['class'] ) );
            $container_id    = esc_attr( $args['id'] ) . '_field';
            $field           = sprintf( $field_container, $container_class, $container_id, $field_html );
        }

        if ( $args['return'] ) {
            return $field;
        } else {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $field;
        }
    }
}

/**
 * backrward compatibility for str_starts_with function, since it is introduced in php 8.0
 * @return string
 */
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}

if( !function_exists('whols_is_on_wholesale') ){
    function whols_is_on_wholesale( $product_id = '', $variation_id = '' ){
        $status = false;
    
        $p_id = $variation_id ? $variation_id : $product_id;
        $product = wc_get_product($p_id);
    
        $product_type     = $product->get_type();
        $pricing_model    = 'single_role';
        $price_type       = '';
        $price_value      = '';
        $minimum_quantity = '';
    
        if( $pricing_model == 'single_role' ){
            $price_type_1_properties = whols_get_option( 'price_type_1_properties' );
            $enable_this_pricing     = $price_type_1_properties['enable_this_pricing'];
            $price_type              = $price_type_1_properties['price_type'];
            $price_value             = $price_type_1_properties['price_value'];
            $minimum_quantity        = $price_type_1_properties['minimum_quantity'];
    
            // override from category level
            $term_meta = '';
            $current_product_category_ids = $product->get_category_ids();
            foreach( $current_product_category_ids as $id ){
                $term_meta = whols_get_term_meta( $id, 'whols_product_category_meta' );
    
                if( isset( $term_meta[ 'price_type_1_properties' ] ) && $term_meta[ 'price_type_1_properties' ] ){
                    $price_type_1_properties = $term_meta[ 'price_type_1_properties' ];
                    if( $price_type_1_properties['enable_this_pricing'] ){
                        $enable_this_pricing = $price_type_1_properties['enable_this_pricing'];
                        $price_type          = $price_type_1_properties['price_type'];
                        $price_value         = $price_type_1_properties['price_value'];
                        $minimum_quantity    = $price_type_1_properties['minimum_quantity'];
    
                        break;
                    }
                }
            }
    
            // override from simple product level
            $price_type_1_properties = get_post_meta( $product->get_id(), '_whols_price_type_1_properties', true);
            if( ($price_type_1_properties && $product_type == 'simple') ){
    
                $price_type_1_properties_arr = explode( ':', $price_type_1_properties );
                if( isset( $price_type_1_properties_arr[0] ) ){
    
                    $enable_this_pricing = true;
                    $price_type = 'flat_rate';
                    $price_value = $price_type_1_properties_arr[0];
                    $minimum_quantity = !empty($price_type_1_properties_arr[1]) ? $price_type_1_properties_arr[1] : '';
    
                }
            }
    
            // override from variation product level
            if( $product_type == 'variation' ){
                $new_min_price = 9999999999;
                $new_max_price = 0;
                $has_price     = false;
    
                $regular_price = (float) get_post_meta( $p_id, '_price', true );
                $meta_info = get_post_meta( $p_id, '_whols_price_type_1_properties', true );
                $meta_arr = explode( ':', $meta_info );
                $meta_price = (float) $meta_arr[0];
                $minimum_quantity = '';
                
                if( $meta_price ){
                    $has_price = true;
                }
    
                if( $regular_price && $meta_price ){
                    if( $meta_price && $meta_price != 0 &&  $meta_price < $new_min_price ){
                        $new_min_price = $meta_price;
                    }
    
                    if( $meta_price &&  $meta_price > $new_max_price ){
                        $new_max_price = $meta_price;
                    }
                } else{
                    if( $regular_price < $new_min_price ){
                        $new_min_price = $regular_price;
                    }
    
                    if( $regular_price > $new_max_price ){
                        $new_max_price = $regular_price;
                    }
                }
    
                if( $has_price ){
                    $enable_this_pricing = true;
                    $price_type          = 'flat_rate';
                    $price_value         = "$new_min_price:$new_max_price";
                    $minimum_quantity    = !empty($meta_arr['1']) ? (int) $meta_arr['1'] : 0;
                }
            }else if( $product_type == 'variable' ){
                $old_min_price = $product->get_variation_price('min');
                $old_max_price = $product->get_variation_price('max');
                $current_product_variations = $product->get_available_variations();
    
                $new_min_price = 9999999999;
                $new_max_price = 0;
                $has_price = false;
    
                foreach( $current_product_variations as $variation ){
                    $regular_price = (float) get_post_meta( $variation['variation_id'], '_price', true );
                    $meta_info = get_post_meta( $variation['variation_id'], '_whols_price_type_1_properties', true );
                    $meta_arr = explode( ':', $meta_info );
                    $meta_price = (float) $meta_arr[0];
                    if( $meta_price ){
                        $has_price = true;
                    }
    
                    if( $regular_price && $meta_price ){
                        if( $meta_price && $meta_price != 0 &&  $meta_price < $new_min_price ){
                            $new_min_price = $meta_price;
                        }
    
                        if( $meta_price &&  $meta_price > $new_max_price ){
                            $new_max_price = $meta_price;
                        }
                    } else{
                        if( $regular_price < $new_min_price ){
                            $new_min_price = $regular_price;
                        }
    
                        if( $regular_price > $new_max_price ){
                            $new_max_price = $regular_price;
                        }
                    }
                }
    
                if( $has_price ){
                    $enable_this_pricing = true;
                    $price_type          = 'flat_rate';
                    $price_value         = "$new_min_price:$new_max_price";
                    $minimum_quantity    = '';
                }
            } // product type
    
            return array(
                'enable_this_pricing' => $enable_this_pricing,
                'price_type'          => $price_type,
                'price_value'         => $price_value,
                'minimum_quantity'    => $minimum_quantity
            );
        } // pricing model
    }
}

/**
 * Sanitize checkbox
 *
 * @since 1.0.0
 */
if( !function_exists('whols_sanitize_checkbox') ){
    function whols_sanitize_checkbox( $input ){
        //returns true if checkbox is checked
         return ( isset( $input ) ? 'yes' : 'no' );
    }
}

/**
 * Capabilities
 */
if( !function_exists('whols_get_capabilities') ){
    function whols_get_capabilities(){
        $capabilities = array(
            'manage_settings' => 'manage_options',
            'manage_roles'    => 'manage_options',
            'manage_requests' => 'manage_options'
        );

        return apply_filters('whols_capabilities', $capabilities );
    }
}

if( !function_exists('whols_insert_element_after_specific_array_key') ){

    /**
     * It takes an array, a specific key, and a new element, and inserts the new element after the
     * specific key
     * 
     * @param array arr The array where you want to insert the new element.
     * @param string specific_key The key of the array element you want to insert after.
     * @param array new_element The new element to be inserted.
     * 
     * @return array
     */
    function whols_insert_element_after_specific_array_key( $arr, $specific_key, $new_element ){
        if( !is_array($arr) || !is_array($new_element) ){
            return $arr;
        }

        if( !array_key_exists( $specific_key, $arr ) ){
            return $arr;
        }
    
        $array_keys = array_keys( $arr );
        $start      = (int) array_search( $specific_key, $array_keys, true ) + 1; // Offset
    
        $spliced_arr                = array_splice( $arr, $start );
        $new_element_key            = $new_element['key'];
        $arr[$new_element_key]      = $new_element['value'];
        $new_arr                    = array_merge( $arr, $spliced_arr );
    
        return $new_arr;
    }
}

if( !function_exists('whols_product_category_dropdown_options') ){
    function whols_product_category_dropdown_options(){
        $query  = new WP_Term_Query( array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
        ) );
    
        $options = array();
    
        if ( ! is_wp_error( $query ) && !empty( $query->terms ) ) {
            foreach ( $query->terms as $item ) {
              $options[$item->slug] = $item->name;
            }
        }
    
        return $options;
    }
}

if( !function_exists('whols_roles_dropdown_options') ){
    function whols_roles_dropdown_options(){
        $query  = new WP_Term_Query( array(
            'taxonomy'   => 'whols_role_cat',
            'hide_empty' => false,
        ) );
    
        $options = array();
    
        if ( ! is_wp_error( $query ) && !empty( $query->terms ) ) {
            foreach ( $query->terms as $item ) {
              $options[$item->slug] = $item->name;
            }
        }
    
        return $options;
    }
}

if( !function_exists('whols_is_wholesale_priced') ){
    /**
     * Check wheather the give products is applicable for wholesale price or not.
     * 
     * @param product_id The product ID of the item being added to the cart.
     * @param qty The quantity of the product being added to the cart.
     * 
     * @return array|bool
     */
    function whols_is_wholesale_priced( $product_id, $qty ){
        $product_data = wc_get_product($product_id);
    
        if($product_data->is_type('simple')){
            $product_id     = $product_data->get_id();
            $variation_id   = '';
        } elseif( $product_data->is_type('variation') ){
            $product_id     = $product_data->get_parent_id();
            $variation_id   = $product_data->get_id();
        }
    
        $wholesale_status    = whols_is_on_wholesale( $product_id, $variation_id );
        $enable_this_pricing = $wholesale_status['enable_this_pricing'];
        $price_value         = $wholesale_status['price_value'];
        $minimum_quantity    = $wholesale_status['minimum_quantity'];
    
        if( whols_is_wholesaler(get_current_user_id()) && $enable_this_pricing && $price_value &&  $qty >= $minimum_quantity  ){
            // Returned array in case we need to pass any other data in the future
            return array(
                'wholesale_priced' => 'yes'
            );
        }
    
        return false;
    }
}