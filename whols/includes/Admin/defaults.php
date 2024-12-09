<?php
return array(
    'success_message' => esc_html__( 'Your message has been submitted successfully. We will get back to you soon.', 'whols' ),
	'submit_button_label' => esc_html__( 'Submit', 'whols' ),
    'raq_data_defaults' => array(
        'sendar'      => '',
        'sendar_type' => '',
        'name'        => '',
        'email'       => '',
        'message'     => '',
        'time'        => '',
        'products'    => '',
    ),

    'raq_fields' => array(
        'name' => array(
            'type'       => 'text',
            'label'       => esc_html__( 'Name', 'whols_pro' ),
            'placeholder' => esc_html__( 'Enter your name', 'whols_pro' ),
            'required'    => true,
            'class'             => array('whols-form-row'),
            'label_class'       => array('label_classs'),
            'input_class'       => array('input_classs'),
            'custom_attributes' => array('required' => 'required'),
        ),
        'email' => array(
            'type'       => 'email',
            'label'       => esc_html__( 'Email', 'whols_pro' ),
            'placeholder' => esc_html__( 'Enter your email', 'whols_pro' ),
            'required'    => true,
            'class'             => array('whols-form-row'),
            'label_class'       => array('label_classs'),
            'input_class'       => array('input_classs'),
            'custom_attributes' => array(
                'required' => 'required',
                'pattern' => '[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$'
            ),
        ),
        'subject' => array(
            'type'       => 'text',
            'label'       => esc_html__( 'Subject', 'whols_pro' ),
            'placeholder' => esc_html__( 'Enter your subject', 'whols_pro' ),
            'required'    => true,
            'class'             => array('whols-form-row'),
            'label_class'       => array('label_classs'),
            'input_class'       => array('input_classs'),
            'custom_attributes' => array('required' => 'required'),
        ),
        'products_data' => array(
            'type'              => 'hidden',
            'label'             => esc_html__( 'Products', 'whols_pro' ),
            'class'             => array('whols-form-row type--hidden'),
            'label_class'       => array('label_classs'),
            'input_class'       => array('input_classs'),
        ),
        'message' => array(
            'type'        => 'textarea',
            'label'       => esc_html__( 'Message', 'whols_pro' ),
            'placeholder' => esc_html__( 'Enter your message', 'whols_pro' ),
            'required'    => true,
            'class'             => array('whols-form-row'),
            'label_class'       => array('label_classs'),
            'input_class'       => array('input_classs'),
            'custom_attributes' => array('required' => 'required'),
        ),
    ),

    'global_settings' => array(
        'pricing_model'           => 'single_role',
        'price_type_1_properties' => array(
            'enable_this_pricing' => '',
            'price_type'          => 'flat_rate',
            'price_value'         => '',
            'minimum_quantity'    => '',
        ),
        'price_type_2_properties'   => array(
            'whols_default_role__enable_this_pricing' => '',
            'whols_default_role__price_type'          => 'flat_rate',
            'whols_default_role__price_value'         => '',
            'whols_default_role__minimum_quantity'    => '',
        ),
        'retailer_price_options' => array(
            'hide_retailer_price'         => '',
            'retailer_price_custom_label' => '',
        ),
        'wholesaler_price_options'  => array(
            'hide_wholesaler_price'         => '',
            'wholesaler_price_custom_label' => '',
        ),
        'discount_label_options' => array(
            'hide_discount_percent'         => '',
            'discount_percent_custom_label' => ''
        ),
        'lgoin_to_see_price_label'                                 => '',
        'auto_apply_minimum_quantity'                             => 0,
        'hide_wholesale_only_products_from_other_customers'             => '',
        'hide_general_products_from_wholesalers'                   => '',
        'default_wholesale_role'                                   => 'whols_default_role',
        'enable_auto_approve_customer_registration'                => '',
        'registration_successful_message_for_auto_approve'         => 'Thank you for registering.',
        'registration_successful_message_for_manual_approve'       => 'Thank you for registering. Your account will be reviewed by us & approve manually. Please wait to be approved.',
        'redirect_page_customer_registration'                      => '',
        'redirect_page_customer_login'                             => '',
        'hide_price_for_guest_users'                               => '',
        'enable_website_restriction'                               => '',
        'who_can_access_shop'                                      => 'everyone',
        'who_can_access_entire_website'                            => 'everyone',
        'disable_coupon_for_wholesale_customers'                   => '',
        'disable_specific_payment_gateway_for_wholesale_customers' => '',
        'allow_free_shipping_for_wholesale_customers'              => '',
    
        'show_wholesale_price_for'            => 'only_wholesalers',
        'exclude_tax_for_wholesale_customers' => '',
        'enable_wholesale_store'              => '1',
        'registration_notification_recipients' => '',

        // Request a quote
        'enable_request_a_quote' => false,
        'request_a_quote_label' => '',
        'create_conversation_when_request_a_quote' => false,
    )
);