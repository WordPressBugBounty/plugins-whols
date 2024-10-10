<?php
/**
 * Whols Global_Settings
 *
 * @since 1.0.0
 */

namespace Whols\Admin;

/**
 * Global_Settings class
 */
class Global_Settings {

    /**
     * Global_Settings constructor
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->plugin_global_settings();
    }

    /**
     * All the global settings of this plugin
     *
     * @since 1.0.0
     */
    public function plugin_global_settings() {
        $prefix = 'whols_options';

        $roles = whols_get_taxonomy_terms();
        $price_type_2_tabs_arr = array();

        foreach( $roles as $role_slug => $role ){
            $price_type_2_tabs_arr[] =  array(
                'title'  => $role,
                'fields' => array(
                    // enable this pricing
                    array(
                        'id'         => $role_slug. '__enable_this_pricing',
                        'type'       => 'switcher',
                        'title'      => esc_html__( 'Enable This Pricing', 'whols'),
                        'text_on'    => esc_html__( 'Yes', 'whols' ),
                        'text_off'   => esc_html__( 'No', 'whols' ),
                    ),

                    // price type
                    array(
                        'id'          => $role_slug. '__price_type',
                        'type'        => 'select',
                        'title'       => esc_html__( 'Price Type', 'whols'),
                        'options'     => array(
                            'flat_rate'     => esc_html__( 'Flat Rate', 'whols' ),
                            'percent'       => esc_html__( 'Percentage', 'whols' ),
                        ),
                    ),

                    // price value
                    array(
                        'id'    => $role_slug. '__price_value',
                        'type'  => 'text',
                        'title' => esc_html__( 'Price Value', 'whols' ),
                        'attributes'  => array(
                            'type'      => 'number',
                        ),
                    ),

                    // minimum quantity
                    array(
                        'id'    => $role_slug. '__minimum_quantity',
                        'type'  => 'text',
                        'title' => esc_html__( 'Minimum Quantity', 'whols' ),
                        'attributes'  => array(
                            'type'      => 'number',
                        ),
                    ),
                ),
            );
        }

        // titles with help image
        $retailer_price_options_title = sprintf(
            /*
             * translators:
             * 1: label
             */
            '%1$s <i class="dashicons-before dashicons-editor-help"></i><img class="whols_help_image" src="'. WHOLS_ASSETS .'/images/retailer-price-help-image.jpg">',
            esc_html__( 'Retailer Price Label', 'whols' )
        );

        $wholesaler_price_options_title = sprintf(
            /*
             * translators:
             * 1: label
             */
            '%1$s <i class="dashicons-before dashicons-editor-help"></i><img class="whols_help_image" src="'. WHOLS_ASSETS .'/images/wholesale-price-help-image.jpg">',
            esc_html__( 'Wholesaler Price Label', 'whols' )
        );

        $discount_label_options_title = sprintf(
            /*
             * translators:
             * 1: label
             */
            '%1$s <i class="dashicons-before dashicons-editor-help"></i><img class="whols_help_image" src="'. WHOLS_ASSETS .'/images/save-price-help-image.jpg">',
            esc_html__( 'Discount Label', 'whols' )
        );

        $capabilities = whols_get_capabilities();

        // Create Settings Wrapper
        \CSF::createOptions( $prefix, array(
            'menu_title'         => esc_html__( 'Whols', 'whols' ),
            'menu_slug'          => 'whols-admin',
            'menu_icon'          => 'dashicons-money-alt',
            'menu_capability'    => $capabilities['manage_settings'],
            'framework_title'    => esc_html__( 'WooCommerce Wholesale Price Settings', 'whols' ),
            'theme'              => 'light',
            'sticky_header'      => false,
            'class'              => 'whols_global_options',
            'show_sub_menu'      => false,
            'menu_position'      => 56,
            'show_search'        => false,
            'show_reset_all'     => true,
            'show_reset_section' => true,
            'show_bar_menu'      => false,
            'footer_text'      => esc_html__('Made with Love by HasThemes', 'whols'),
            'defaults'           => array(
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
            )
        ) );

        // General Settings Tab
        \CSF::createSection( $prefix, array(
            'title'  => esc_html__( 'General Settings', 'whols' ),
            'fields' => array(

                // enable_wholesale_store
                array(
                    'id'          => 'enable_wholesale_store',
                    'type'        => 'checkbox',
                    'title'       => esc_html__( 'Enable Wholesale Store', 'whols' ),
                    'label'       => esc_html__( 'Yes', 'whols' ),
                ),

                // role type
                array(
                    'id'          => 'pricing_model',
                    'type'        => 'select',
                    'title'       => esc_html__( 'Pricing Model', 'whol' ),
                    'placeholder' => '',
                    'options'     => array(
                        'single_role'       => esc_html__( 'Single Role Based', 'whols' ),
                        'multiple_role'     => esc_html__( 'Multiple Role Based', 'whols' ),
                    ),
                    'after'       => __( '<b>Single Role</b> is useful when you have only one type of wholesaler. <br> <b>Multiple Role</b> is useful when you have different kind of wholesaler and you want different price for each kind of wholesaler.', 'whols' ),
                    'class'       => 'whols_pro',
                ),

                // show wholesale price for
                array(
                    'id'          => 'show_wholesale_price_for',
                    'type'        => 'radio',
                    'title'       => esc_html__( 'Wholesale Pricing Mode', 'whols' ),
                    'placeholder' => '',
                    'options'     => array(
                        'administrator'    => esc_html__( 'Test Mode', 'whols' ),
                        'all_users'        => esc_html__( 'Make pricing available to everyone', 'whols' ),
                        'only_wholesalers' => esc_html__( 'Make pricing available to wholesalers only', 'whols' ),
                    ),
                    'default'    => 'administrator',
                    'desc'       => __( 'If you select "Test Mode", Admin & Wholesalers will be able to access pricing.
                    
                    ', 'whols' ),
                ),


                // price type 1 properties
                array(
                  'id'         => 'price_type_1_properties',
                  'type'       => 'fieldset',
                  'title'      => esc_html__( 'Price Options For Pricing Model: Single Role', 'whols' ),
                  'dependency' => array( 'pricing_model', '==', 'single_role' ),
                  'fields'     => array(
                        // enable this pricing
                        array(
                          'id'         => 'enable_this_pricing',
                          'type'       => 'switcher',
                          'title'      => esc_html__( 'Enable This Pricing', 'whols'),
                          'text_on'    => esc_html__( 'Yes', 'whols' ),
                          'text_off'   => esc_html__( 'No', 'whols' ),
                        ),

                        // price type
                        array(
                          'id'          => 'price_type',
                          'type'        => 'select',
                          'title'       => esc_html__( 'Price Type', 'whols'),
                          'options'     => array(
                            'flat_rate'     => esc_html__( 'Flat Rate', 'whols' ),
                            'percent'       => esc_html__( 'Percentage', 'whols' ),
                          ),
                        ),

                        // price value
                        array(
                          'id'    => 'price_value',
                          'type'  => 'text',
                          'title' => esc_html__( 'Price Value', 'whols' ),
                          'attributes'  => array(
                            'type'      => 'number',
                          ),
                          'desc'  => esc_html__('Example: If "Price Type" is set to "Percentage" & if you enter 75. Then product price will be 75% of the existing price & wholesaler will get 25% discount.', 'whols')
                        ),

                        // minimum quantity
                        array(
                          'id'    => 'minimum_quantity',
                          'type'  => 'text',
                          'title' => esc_html__( 'Minimum Quantity', 'whols' ),
                          'attributes'  => array(
                            'type'      => 'number',
                          ),
                          'desc'  => esc_html__('Minimum quantity to purchase to qualify the price. A notice with the "Minimum Quantity" value will be shown with the products', 'whols')
                        ),
                    ),
                  'after' => esc_html__( '(These options can be overridden for each category & product individually.)', 'whols'  ),
                ),

                array(
                    'id'     => 'retailer_price_options',
                    'type'   => 'fieldset',
                    'title'  => $retailer_price_options_title,
                    'fields' => array(
                        // hide retailer price
                        array(
                            'id'       => 'hide_retailer_price',
                            'type'     => 'switcher',
                            'title'    => esc_html__( 'Hide Retailer Price', 'whols'),
                            'text_on'  => esc_html__( 'Yes', 'whols' ),
                            'text_off' => esc_html__( 'No', 'whols' ),
                            'label'    => esc_html__( 'This label will be shown in the product listing/loop of your shop page & product details page.', 'whols'  ),
                        ),
                        // retailer price custom label
                        array(
                            'id'    => 'retailer_price_custom_label',
                            'type'  => 'text',
                            'title' => esc_html__( 'Retailer Price Custom Label', 'whols' ),
                            'after' => esc_html__( 'This label will be shown in the product listing/loop of your shop page & product details page.', 'whols'  ),
                            'dependency'  => array( 'hide_retailer_price', '==', 'false' ),
                        ),
                    ),
                ),

                array(
                    'id'     => 'wholesaler_price_options',
                    'type'   => 'fieldset',
                    'title'  => $wholesaler_price_options_title,
                    'fields' => array(
                        // hide wholesaler price
                        array(
                            'id'       => 'hide_wholesaler_price',
                            'type'     => 'switcher',
                            'title'    => esc_html__( 'Hide Wholesaler Price', 'whols'),
                            'text_on'  => esc_html__( 'Yes', 'whols' ),
                            'text_off' => esc_html__( 'No', 'whols' ),
                            'label'    => esc_html__( 'Hide the wholesaler price which appear into the product listing/loop of your shop page & product details page.', 'whols'  ),
                        ),
                        // wholesaler price custom label
                        array(
                            'id'    => 'wholesaler_price_custom_label',
                            'type'  => 'text',
                            'title' => esc_html__( 'Wholesaler Price Custom Label', 'whols' ),
                            'after' => esc_html__( 'This label will be shown in the product listing/loop of your shop page & product details page.', 'whols'  ),
                            'dependency'  => array( 'hide_wholesaler_price', '==', 'false' ),
                        ),
                    ),
                ),

                array(
                    'id'     => 'discount_label_options',
                    'type'   => 'fieldset',
                    'title'  => $discount_label_options_title,
                    'fields' => array(

                        // show discount %
                        array(
                            'id'       => 'hide_discount_percent',
                            'type'     => 'switcher',
                            'title'    => esc_html__( 'Hide Discount %', 'whols'),
                            'text_on'  => esc_html__( 'Yes', 'whols' ),
                            'text_off' => esc_html__( 'No', 'whols' ),
                            'label'    => esc_html__( 'Enabling this option will hide the discount amount (%) from the product listing/loop of your shop page & product details page', 'whols'  ),
                        ),

                        // discount percent custom label
                        array(
                            'id'    => 'discount_percent_custom_label',
                            'type'  => 'text',
                            'title' => esc_html__( 'Discount Percentage Custom Label', 'whols' ),
                            'after' => esc_html__( 'This label will be shown in the product listing/loop of your shop page & product details page.', 'whols'  ),
                            'dependency'  => array( 'hide_discount_percent', '==', 'false' ),
                        ),
                    ),
                ),

                array(
                    'id'     => 'min_qty_notice_custom_text',
                    'type'   => 'text',
                    'title'  => esc_html__( 'Min Qty Notice Text', 'whols' ),
                    'default' => esc_html__('Wholesale price will apply for minimum quantity of {qty} products.', 'whols'),
                    'after'   => __( 'To display the Minimum Quantity amount in the notice, use the placeholder <code>{qty}</code><br>Leave it empty for default.', 'whols'  ),
                ),
            )
        ) );

        // Registration Settings Tab
        \CSF::createSection( $prefix, array(
            'id'    => 'registration_tab',
            'title' => esc_html__( 'Registration & Login',  'whols' ),
        ) );

        \CSF::createSection( $prefix, array(
            'parent' => 'registration_tab',
            'title'  => esc_html__( 'General Options',  'whols' ),
            'fields' => array(
                // registration_page
                 array(
                    'id'          => 'registration_page',
                    'type'        => 'select',
                    'title'       => esc_html__( 'Set up The Registration Page', 'whols' ),
                    'options'     => 'page',
                    'placeholder' => __( '---Select a page---', 'whols' ),
                    'after'       => __( 'The wholesaler registration form will appear on the selected page. Or<br> Use this shortcode <code>[whols_registration_form]</code> anywhere on a page, to show the wholesaler registration form.', 'whols' ),
                 ),

                // default wholesale role
                array(
                    'id'          => 'default_wholesale_role',
                    'type'        => 'select',
                    'title'       => esc_html__( 'Default Wholesaler Role', 'whols' ),
                    'options'     => whols_get_taxonomy_terms(),
                    'after'       => esc_html__( 'Select the default role of a wholesaler that will be used for the new wholesaler registration. Note: This option will work when the "Pricing Model" is set to "Multiple Role".', 'whols' ),
                    'class'       => 'whols_pro'
                ),

                // enable auto approve
                array(
                    'id'       => 'enable_auto_approve_customer_registration',
                    'type'     => 'checkbox',
                    'title'    => esc_html__( 'Enable Auto Approve', 'whols'),
                    'label'    => esc_html__( 'Automatically approve new user registration.', 'whols'  ),
                    'class'       => 'whols_pro whols_enable_auto_approve_customer_registration'
                ),

                // successful message
                array(
                    'id'       => 'registration_successful_message_for_auto_approve',
                    'type'     => 'textarea',
                    'title'    => esc_html__( 'Successful Registration Message', 'whols'),
                    'after'    => esc_html__( 'If "Auto Approve" is enabled. Then this message will show into the customer registration page, after a successful wholesaler registration. HTML is allowed.', 'whols'  ),
                    'dependency' => array(
                        'enable_auto_approve_customer_registration', '==', '1'
                    )
                ),

                // successful message
                array(
                    'id'       => 'registration_successful_message_for_manual_approve',
                    'type'     => 'textarea',
                    'title'    => esc_html__( 'Registration Successful Message', 'whols'),
                    'default'  => esc_html__( 'Thank you very much for completing the registration. Your account will be reviewed by us & approved manually. Please wait for a while.', 'whols' ),
                    'after'    => esc_html__( 'Insert a custom message for a successful registration. HTML tags are also allowed. When the "Auto Approve" option is disabled, this message will be shown on the customer registration page after a successful wholesaler registration.', 'whols'  ),
                    'dependency' => array(
                        'enable_auto_approve_customer_registration', '==', '0'
                    )
                ),

                // redirect page
                array(
                    'id'       => 'redirect_page_customer_registration',
                    'type'     => 'text',
                    'title'    => esc_html__( 'Redirect Page URL (After Registration)', 'whols'),
                    'after'    => esc_html__( 'Insert a page URL where you want the users to be redirected after a successful registration. Leave empty if you don\'t want to redirect the users.', 'whols'  ),
                ),

                // redirect page after login
                array(
                    'id'       => 'redirect_page_customer_login',
                    'type'     => 'text',
                    'title'    => esc_html__( 'Redirect Page URL (After Login)', 'whols'),
                    'after'    => esc_html__( 'Insert a page URL where you want the users to be redirected after a successful login. Leave empty for default redirection. Note: Make sure you have entered a page URL of the same domain.', 'whols'  ),
                ),
            )
        ));

        // Registration Fields Editor
        \CSF::createSection( $prefix, array(
            'parent' => 'registration_tab',
            'title'  => esc_html__( 'Fields Manager', 'whols' ),
            'fields' => array(
                array(
                    'id'     => 'registration_fields',
                    'type'   => 'group',
                    'title'  => __( 'Registration Fields Manager', 'whols' ),
                    'class'  => 'whols_field_manager',
                    'button_title'=> '<i class="fa fa-plus"></i>',
                    'fields' => array(
                        // Field name
                        array(
                            'id'    => 'field',
                            'type'  => 'select',
                            'title' => __( 'Field', 'whols' ),
                            'options' => array(
                                'custom'        => __( 'Custom Field', 'whols' ),
                                'WooCommerce'   => array(
                                    'billing_company'    => __( 'Billing Company', 'whols' ),
                                    'billing_address_1'  => __( 'Billing Address', 'whols' ),
                                    'billing_city'       => __( 'Billing City', 'whols' ),
                                    'billing_postcode'   => __( 'Billing Postcode', 'whols' ),
                                    'billing_country'    => __( 'Billing Country', 'whols' ),
                                    'billing_state'      => __( 'Billing State', 'whols' ),
                                    'billing_phone'      => __( 'Billing Phone', 'whols' ),
                                ),
                                'Default'   => array(
                                    'reg_name'      => __( 'Name', 'whols' ),
                                    'reg_username'  => __( 'Username', 'whols' ),
                                    'reg_email'     => __( 'Email', 'whols' ),
                                    'reg_password'  => __( 'Password', 'whols' ),
                                ),
                            ),
                        ),

                        // Field status
                        array(
                            'id'    => 'enable',
                            'type'  => 'checkbox',
                            'title' => __( 'Enable', 'whols' ),
                            'label' => __( 'Yes', 'whols' ),
                            'default' => true,
                        ),
                        
                        // Field type
                        array(
                            'id'    => 'type',
                            'type'  => 'select',
                            'title' => __( 'Field Type', 'whols' ),
                            'options' => array(
                                'text'      => __( 'Text', 'whols' ),
                                'textarea'  => __( 'Textarea', 'whols' ),
                                'select'    => __( 'Select', 'whols' ),
                                'checkbox'  => __( 'Checkbox', 'whols' ),
                                'radio'     => __( 'Radio', 'whols' ),
                            ),
                            'dependency' => array( 'field', '==', 'custom' ),
                        ),

                        // Field id
                        array(
                            'id'    => 'custom_field_name',
                            'type'  => 'text',
                            'title' => __( 'Field Unique Name', 'whols' ),
                            'desc'  => __( 'It will be used to save the field value in the database. It must be unique.', 'whols' ),
                            'placeholder' => __( 'e.g: company_name', 'whols' ),
                            'dependency' => array( 'field', '==', 'custom' ),
                        ),

                        // Field label
                        array(
                            'id'    => 'label',
                            'type'  => 'text',
                            'title' => __( 'Label', 'whols' ),
                        ),

                        // Field options
                        array(
                            'id'    => 'options',
                            'type'  => 'textarea',
                            'title' => __( 'Field Options', 'whols' ),
                            'desc'  => __( 'Enter each option on a new line with key|value pair . For example: <br> <code>option-1|Option 1</code> <br> <code>option-2|Option 2</code> <br> <code>option-3|Option 3</code>', 'whols' ),
                            'dependency' => array( 'field|type', '==|any', 'custom|select,radio' ),
                        ),

                        // Field placeholder
                        array(
                            'id'    => 'placeholder',
                            'type'  => 'text',
                            'title' => __( 'Placeholder', 'whols' ),
                        ),

                        // Field description
                        array(
                            'id'    => 'description',
                            'type'  => 'text',
                            'title' => __( 'Description', 'whols' ),
                        ),

                        // Field default value
                        array(
                            'id'    => 'value',
                            'type'  => 'text',
                            'title' => __( 'Default Value', 'whols' ),
                        ),

                        // Field required
                        array(
                            'id'    => 'required',
                            'type'  => 'checkbox',
                            'title' => __( 'Required?', 'whols' ),
                            'label' => __( 'Yes', 'whols' ),
                        ),

                        // Field class
                        array(
                            'id'    => 'class',
                            'type'  => 'text',
                            'title' => __( 'Class', 'whols' ),
                        ),

                    ),
                    'max'  => 4,
                    'accordion_title_prefix' => 'Field: ',
                    'accordion_title_number' => true,
                    'default' => array(
                        array(
                            'field'       => 'reg_name',
                            'enable'      => true,
                            'label'       => __( 'Name', 'whols' ),
                            'placeholder' => __( 'Your Name', 'whols' ),
                            'required'    => true,
                        ),
                        array(
                            'field'       => 'reg_username',
                            'enable'      => true,
                            'label'       => __( 'Username', 'whols' ),
                            'placeholder' => __( 'Your Username', 'whols' ),
                            'required'    => true,
                        ),
                        array(
                            'field'       => 'reg_email',
                            'enable'      => true,
                            'label'       => __( 'Email', 'whols' ),
                            'placeholder' => __( 'Your Email', 'whols' ),
                            'required'    => true,
                        ),
                        array(
                            'field'       => 'reg_password',
                            'enable'      => true,
                            'label'       => __( 'Password', 'whols' ),
                            'placeholder' => __( 'Your Password', 'whols' ),
                            'required'    => true,
                        ),
                    ),
                ),
            )
        ));

        // Product Visibility Settings Tab
        \CSF::createSection( $prefix, array(
            'id'    => 'product_visibility_tab',
            'title' => esc_html__( 'Product Settings',  'whols' ),
        ) );

        \CSF::createSection( $prefix, array(
            'parent' => 'product_visibility_tab',
            'title'  => esc_html__( 'Product Visibility',  'whols' ),
            'fields' => array(
                array(
					'id'         => 'product_visibility_tab_heading_1',
					'type'       => 'subheading',
					'content' 	 => __('For Retailers / General Customers', 'whols')
				),

				// hide wholesale products for from other customers
				array(
					'id'         => 'hide_wholesale_only_products_from_other_customers',
					'type'       => 'checkbox',
					'title'      => esc_html__( 'Hide "Wholesaler Only" Products', 'whols' ),
					'label'      => esc_html__( 'Yes'  , 'whols' ),
					'after'      => __( 'Products with <b>Wholesalers Only</b> visibility will be hidden from Retailers.'  , 'whols' ),
				),

				array(
					'id'         => 'product_visibility_tab_heading_2',
					'type'       => 'subheading',
					'content' 	 => __('For Wholesalers', 'whols')
				),

				// hide general products from wholesalers
				array(
					'id'         => 'hide_general_products_from_wholesalers',
					'type'       => 'checkbox',
					'title'      => __( 'Show only "Wholesaler Only" products', 'whols' ),
					'label'      => __( 'Yes'  , 'whols' ),
					'after'      => __( 'Products with <b>Wholesalers Only</b> visibility will only be displayed to wholesalers and rest will be hidden.'  , 'whols' ),
				),
            )
        ) );

        \CSF::createSection( $prefix, array(
            'parent' => 'product_visibility_tab',
            'title'  => esc_html__( 'Wholesaler Only Categories',  'whols' ),
            'class'  => 'whols-ribbon-new',
            'fields' => array(
                // include_children
                array(
                    'id'         => 'include_children',
                    'type'       => 'checkbox',
                    'title'      => esc_html__( 'Include Children Categories', 'whols'),
                    'desc'       => __( 'If checked, all the child categories within a parent category will be selected as well. <br>If not checked, parents or children relationship will not be considered in the selection of the category.', 'whols'),
                    'label'      => esc_html__( 'Yes', 'whols' ),
                    'class'      => 'whols_pro'
                ),

                // wholesale_only_categories
                array(
                    'id'          => 'wholesale_only_categories',
                    'type'        => 'repeater',
                    'title'       => esc_html__( '', 'whols' ),
                    'class'       => 'whols_wholesale_only_categories whols_pro_opacity',
                    'fields'      => array(
                        array(
                            'id'          => 'categories',
                            'type'        => 'select',
                            'title'       => __( 'Category(s)', 'whols' ) . '<div class="csf-help"><span class="csf-help-text">'. __('Products in the following categories will be available only for the assigned Roles.', 'whols') .'</span><i class="fas fa-question-circle"></i></div>',
                            'placeholder' => __( 'Select', 'whols' ),
                            'options'     => whols_product_category_dropdown_options(),
                            'multiple'    => true,
                            'chosen'      => true,
                        ),
                        array(
                            'id'          => 'roles',
                            'type'        => 'select',
                            'title'       => __( 'Assign Role(s)', 'whols' ) . '<div class="csf-help"><span class="csf-help-text">'. __('These are the roles that have access to the categories selected. <br>Leaving it empty will restrict the categories to All Wholesalers.', 'whols') .'</span><i class="fas fa-question-circle"></i></div>',
                            'placeholder' => __( 'Leave it empty, to assign all roles.', 'whols' ),
                            'options'     => whols_roles_dropdown_options(),
                            'multiple'    => true,
                            'chosen'      => true,
                        ),
                    ),
                    'button_title'      => __('Add New', 'whols'),
                    'default'   => array(
                        array(
                          'categories' => array(),
                          'roles' => array()
                        ),
                    )
                ),
            )
        ) );

        // Guest Access Restriction Tab
        \CSF::createSection( $prefix, array(
            'title'  => esc_html__( 'Guest Access Restriction',  'whols' ),
            'fields' => array(

                // hide price for guest users
                array(
                    'id'       => 'hide_price_for_guest_users',
                    'type'     => 'switcher',
                    'title'    => esc_html__( 'Hide Price For Guest Users', 'whols'),
                    'text_on'  => esc_html__( 'Yes', 'whols' ),
                    'text_off' => esc_html__( 'No', 'whols' ),
                    'label'    => esc_html__( 'Enable this option to show the price only for those users who are logged in.'  , 'whols'  ),
                ),

                // login to see price label
                array(
                    'id'       => 'lgoin_to_see_price_label',
                    'type'  => 'text',
                    'title' => esc_html__( '"Login To See Price" Label', 'whols' ),
                    'after' => esc_html__( 'This label will be shown in the product listing/loop of your shop page & product details page.', 'whols'  ),
                    'dependency'  => array( 'hide_price_for_guest_users', '==', 'true' ),
                ),

                // enable website restiction for
                array(
                    'id'          => 'enable_website_restriction',
                    'title'       => esc_html__( 'Enable Website Restriction Type', 'whols'),
                    'type'        => 'radio',
                    'options'     => array(
                        ''                   => esc_html__('No Restriction', 'whols'),
                        'for_only_shop'      => esc_html__('For Only Shop', 'whols'),
                        'for_entire_wbesite' => esc_html__('For Entire Website', 'whols')
                    ),
                    'class'       => 'whols_pro'
                ),

                // who can access shop
                array(
                    'id'          => 'who_can_access_shop',
                    'title'       => esc_html__( 'Who Can Access Shop', 'whols'),
                    'type'        => 'select',
                    'options'     => array(
                        'everyone'                          => esc_html__('Everyone', 'whols'),
                        'logedin_users'                     => esc_html__('Logged In Users', 'whols'),
                        'logedin_users_with_wholesale_role' => esc_html__('Wholesalers Only', 'whols')
                    ),
                    'after'       => esc_html__( 'Define who can access the shop.'  , 'whols' ),
                    'class'       => 'whols_pro_opacity whols_who_can_access_shop'

                ),

                // who can access entire website
                array(
                    'id'          => 'who_can_access_entire_website',
                    'title'       => esc_html__( 'Who Can Access Entire Website', 'whols'),
                    'type'        => 'select',
                    'options'     => array(
                        'everyone'                          => esc_html__('Everyone', 'whols'),
                        'logedin_users'                     => esc_html__('Loged In Users', 'whols'),
                        'logedin_users_with_wholesale_role' => esc_html__('Wholesalers Only', 'whols')
                    ),
                    'after'       => esc_html__( 'Define who can access the entire website.'  , 'whols' ),
                    'dependency'  => array(
                        'enable_website_restriction', '==', 'for_entire_wbesite'
                    ),
                    'class'       => 'whols_pro whols_who_can_access_entire_website'
                ),

            )
        ) );

        // Registration Settings Tab
        \CSF::createSection( $prefix, array(
            'id'    => 'message_and_email_notifications_tab',
            'title' => esc_html__( 'Message & Email Notifications',  'whols' ),
        ) );

        // Email Notification
        \CSF::createSection( $prefix, array(
            'parent' => 'message_and_email_notifications_tab',
            'title'  => esc_html__( 'Email Notification',  'whols' ),
            'fields' => array(
                // Heading
                array(
                'type'    => 'heading',
                'content' => esc_html__('Wholesaler Request Notification', 'whols'),
                ),
                // enable_registration_notification_for_admin
                array(
                'id'         => 'enable_registration_notification_for_admin',
                'type'       => 'checkbox',
                'title'      => esc_html__( 'Wholesaler Request Notification', 'whols' ),
                'label'      => esc_html__( 'Enable', 'whols' ),
                'desc'       => esc_html__( 'If Enabled, The site admin will get an email about the new wholesaler registration request.' , 'whols' ),
                ),
                // registration_notification_recipients
                array(
                    'id'       => 'registration_notification_recipients',
                    'type'     => 'text',
                    'title'    => esc_html__( 'Recipient Email(s)', 'whols'),
                    'desc'     => esc_html__( 'Leave it empty for default admin email. You can enter multiple emails separated by commas.' , 'whols' ),
                    'placeholder' => get_option('admin_email'),
                    'dependency' => array(
                        'enable_registration_notification_for_admin', '==', '1'
                    )
                ),
                // email_subject
                array(
                    'id'       => 'registration_notification_subject_for_admin',
                    'type'     => 'text',
                    'title'    => esc_html__( 'Email Subject', 'whols'),
                    'dependency' => array(
                        'enable_registration_notification_for_admin', '==', '1'
                    )
                ),
                // message
                array(
                    'id'       => 'registration_notification_message_for_admin',
                    'type'     => 'wp_editor',
                    'title'    => esc_html__( 'Successful Registration Message', 'whols'),
                    'desc'     => esc_html__( 'Use these  {name}, {email}, {date}, {time} placeholder tags to get dynamic content.', 'whols'  ),
                    'dependency' => array(
                        'enable_registration_notification_for_admin', '==', '1'
                    )
                ),
                // Heading
                array(
                'type'    => 'heading',
                'content' => esc_html__('Wholesaler Registration Notification', 'whols'),
                ),
                // enable_registration_notification_for_user
                array(
                'id'         => 'enable_registration_notification_for_user',
                'type'       => 'checkbox',
                'title'      => esc_html__( 'Wholesaler Registration Notification', 'whols' ),
                'label'      => esc_html__( 'Enable', 'whols' ),
                'desc'       => esc_html__( 'If Enabled, The registered wholesale customer will get an email about the registration.' , 'whols' ),
                ),
                // email_subject
                array(
                    'id'       => 'registration_notification_subject_for_user',
                    'type'     => 'text',
                    'title'    => esc_html__( 'Email Subject', 'whols'),
                    'dependency' => array(
                        'enable_registration_notification_for_user', '==', '1'
                    )
                ),
                // message
                array(
                    'id'       => 'registration_notification_message_for_user',
                    'type'     => 'wp_editor',
                    'title'    => esc_html__( 'Successful Registration Message', 'whols'),
                    'desc'     => esc_html__( 'Use these  {name}, {email}, {date}, {time} placeholder tags to get dynamic content.', 'whols'  ),
                    'dependency' => array(
                        'enable_registration_notification_for_user', '==', '1'
                    )
                ),
                // Heading
                array(
                'type'    => 'heading',
                'content' => esc_html__('Approved Notification', 'whols'),
                ),
                // enable_approved_notification
                array(
                'id'         => 'enable_approved_notification',
                'type'       => 'checkbox',
                'title'      => esc_html__( 'Enable Approved Notification', 'whols' ),
                'label'      => esc_html__( 'Enable', 'whols' ),
                'desc'       => esc_html__( 'If Enabled, The registered wholesale customer will get an email if the wholesaler request is approved.' , 'whols' ),
                'class'      => 'whols_pro'
                ),
                // Heading
                array(
                'type'    => 'heading',
                'content' => esc_html__('Rejection Notification', 'whols'),
                ),
                // enable_rejection_notification
                array(
                'id'         => 'enable_rejection_notification',
                'type'       => 'checkbox',
                'title'      => esc_html__( 'Enable Rejection Notification', 'whols' ),
                'label'      => esc_html__( 'Enable', 'whols' ),
                'desc'       => esc_html__( 'If Enabled, The registered wholesale customer will get an email if the wholesaler request is rejected.' , 'whols' ),
                'class'      => 'whols_pro'
                ),
            )
        ));

        // Custom thank you message
        \CSF::createSection( $prefix, array(
            'parent'  => 'message_and_email_notifications_tab',
            'title'  => esc_html__( 'Custom Thank You Message',  'whols' ),
            'fields' => array(
                // enable_custom_thank_you_message
                array(
                    'id'         => 'enable_custom_thank_you_message',
                    'type'       => 'checkbox',
                    'title'      => esc_html__( 'Custom Thank You Message', 'whols' ),
                    'label'      => esc_html__( 'Enable', 'whols' ),
                    'desc'       => esc_html__( 'Enable to customize thank you message for the wholesalers.' , 'whols' ),
                ),
                // thank_you_message_placement
                array(
                    'id'          => 'thank_you_message_placement',
                    'title'       => esc_html__( 'Placement', 'whols'),
                    'type'        => 'select',
                    'options'     => array(
                        'before_default_message'  => esc_html__('Before default "Thank You" message', 'whols'),
                        'after_default_message'   => esc_html__('After default "Thank You" message', 'whols'),
                        'replace_default_message' => esc_html__('Replace default "Thank You" message', 'whols')
                    ),
                    'default'     => 'replace_default_message',
                    'after'       => esc_html__( 'Define how the message should be displayed.'  , 'whols' ),
                    'dependency'  => array(
                        'enable_custom_thank_you_message', '==', '1'
                    ),
                ),
                // message
                array(
                    'id'       => 'custom_thank_you_message',
                    'type'     => 'wp_editor',
                    'title'    => esc_html__( 'Message', 'whols'),
                    'desc'     => __( 'Use the placeholder tags below to get dynamic content. <br><span class="whols_pre">{billing_first_name}, {billing_last_name}, {billing_email}</pre>', 'whols'  ),
                    'default'  => __( 'Thank you <strong>{billing_first_name}</strong>. Your order has been received.', 'whols' ),
                    'dependency' => array(
                        'enable_custom_thank_you_message', '==', '1'
                    )
                ),
            )
        ));

        // Others Options Tab
        \CSF::createSection( $prefix, array(
            'title'  => esc_html__( 'Others Settings',  'whols' ),
            'fields' => array(
                // exclude_tax_for_wholesale_customers
                array(
                    'id'         => 'exclude_tax_for_wholesale_customers',
                    'type'       => 'checkbox',
                    'title'      => esc_html__( 'Exclude Tax For Wholesale Customers', 'whols'),
                    'label'      => esc_html__( 'Yes', 'whols'  ),
                ),

                // disable coupon for wholesale customers
                array(
                    'id'         => 'disable_coupon_for_wholesale_customers',
                    'type'       => 'checkbox',
                    'title'      => esc_html__( 'Disable Coupons For Wholesale Customers', 'whols'),
                    'label'      => esc_html__( 'Yes', 'whols'  ),
                    'after'      => esc_html__( '(This option can be overridden for each role individually)', 'whols'  ),
                ),

                // disable specific payment gateway for wholesale customers
                array(
                    'id'          => 'disable_specific_payment_gateway_for_wholesale_customers',
                    'title'       => esc_html__( 'Disable Payment Gateway For Wholesale Customers', 'whols'),
                    'type'        => 'select',
                    'options'     => whols_get_payment_gateways(),
                    'placeholder' => esc_html__( 'Select Gateways' ),
                    'chosen'      => true,
                    'multiple'    => true,
                    'after'       => esc_html__( '(Theis option can be overridden for each role individually)', 'whols'  ),
                    'class'       => 'whols_pro whols_disable_gateway'
                ),

                // enable free shipping for wholsale customers
                array(
                    'id'         => 'allow_free_shipping_for_wholesale_customers',
                    'type'       => 'checkbox',
                    'title'      => esc_html__( 'Allow Free Shipping For Wholesale Customers', 'whols'),
                    'label'      => esc_html__( 'Yes', 'whols'),
                    'after'      => __( '<strong>Note: </strong>Free Shipping will not work unless you have enabled & configured free shipping from the "WooCommerce > Settings > Shipping Zones" </br>  (This option can be overridden for role individually) '  , 'whols' ),
                ),
            )
        ) );

        // Design
        \CSF::createSection( $prefix, array(
            'id'     => 'design',
            'title'  => esc_html__( 'Design', 'whols' ),
            'fields' => array(
                // retailer_price_label
                array(
                    'id'         => 'retailer_price_label',
                    'type'       => 'fieldset',
                    'title'      => esc_html__('Retailer Price Label', 'whols'),
                    'fields'     => array(
                        array(
                            'id'         => 'retailer_price_label_color',
                            'type'       => 'color',
                            'title'      => esc_html__('Color', 'whols'),
                            'output'     => '.whols_retailer_price .whols_label_left'
                        ),
                        array(
                            'id'         => 'retailer_price_label_font_size',
                            'type'       => 'number',
                            'title'      => esc_html__('Font size', 'whols'),
                            'unit'       => 'px',
                            'output'     => '.whols_retailer_price .whols_label_left',
                            'output_mode' => 'font-size'
                        ),
                        array(
                            'id'         => 'retailer_price_label_line_height',
                            'type'       => 'number',
                            'title'      => esc_html__('Line Height', 'whols'),
                            'unit'       => 'px',
                            'output'     => '.whols_retailer_price .whols_label_left',
                            'output_mode' => 'line-height'
                        ),
                        array(
                            'id'         => 'retailer_price_label_font_weight',
                            'type'       => 'number',
                            'title'      => esc_html__('Font Weight', 'whols'),
                            'unit'       => ' ',
                            'output'     => '.whols_loop_custom_price .whols_label .whols_label_left',
                            'output_mode' => 'font-weight',
                            'desc'        => esc_html__('Default: 700', 'whols')
                        ),
                    )
                ),

                // retailer_price_label
                array(
                    'id'         => 'retailer_price',
                    'type'       => 'fieldset',
                    'title'      => esc_html__('Retailer Price', 'whols'),
                    'fields'     => array(
                        array(
                            'id'         => 'retailer_price_color',
                            'type'       => 'color',
                            'title'      => esc_html__('Color', 'whols'),
                            'output'     => '.whols_retailer_price del'
                        ),
                        array(
                            'id'         => 'retailer_price_font_size',
                            'type'       => 'number',
                            'title'      => esc_html__('Font size', 'whols'),
                            'unit'       => 'px',
                            'output'     => '.whols_retailer_price del',
                            'output_mode' => 'font-size'
                        ),
                        array(
                            'id'         => 'retailer_price_line_height',
                            'type'       => 'number',
                            'title'      => esc_html__('Line Height', 'whols'),
                            'unit'       => 'px',
                            'output'     => '.whols_retailer_price del',
                            'output_mode' => 'line-height'
                        ),
                        array(
                            'id'         => 'retailer_price_font_weight',
                            'type'       => 'number',
                            'title'      => esc_html__('Font Weight', 'whols'),
                            'unit'       => ' ',
                            'output'     => '.whols_retailer_price del',
                            'output_mode' => 'font-weight',
                            'desc'        => esc_html__('Default: 400', 'whols')
                        ),
                    )
                ),

                // retailer_price_margin
                array(
                    'id'          => 'retailer_price_margin',
                    'type'        => 'spacing',
                    'title'       => esc_html__('Retailer Price Area Margin', 'whols'),
                    'output'      => '.whols_retailer_price',
                    'output_mode' => 'margin',
                    'top_icon'    => '',
                    'right_icon'  => '',
                    'bottom_icon' => '',
                    'left_icon'   => '',
                ),

                // wholesaler_price_label
                array(
                    'id'         => 'wholesaler_price_label',
                    'type'       => 'fieldset',
                    'title'      => esc_html__('Wholesaler Price Label', 'whols'),
                    'fields'     => array(
                        array(
                            'id'         => 'wholesaler_price_label_color',
                            'type'       => 'color',
                            'title'      => esc_html__('Color', 'whols'),
                            'output'     => '.whols_wholesaler_price .whols_label_left'
                        ),
                        array(
                            'id'         => 'wholesaler_price_label_font_size',
                            'type'       => 'number',
                            'title'      => esc_html__('Font size', 'whols'),
                            'unit'       => 'px',
                            'output'     => '.whols_wholesaler_price .whols_label_left',
                            'output_mode' => 'font-size'
                        ),
                        array(
                            'id'         => 'wholesaler_price_label_line_height',
                            'type'       => 'number',
                            'title'      => esc_html__('Line Height', 'whols'),
                            'unit'       => 'px',
                            'output'     => '.whols_wholesaler_price .whols_label_left',
                            'output_mode' => 'line-height'
                        ),
                        array(
                            'id'         => 'wholesaler_price_label_font_weight',
                            'type'       => 'number',
                            'title'      => esc_html__('Font Weight', 'whols'),
                            'unit'       => ' ',
                            'output'     => '.whols_loop_custom_price .whols_label .whols_label_left',
                            'output_mode' => 'font-weight',
                            'desc'        => esc_html__('Default: 700', 'whols')
                        ),
                    )
                ),

                // wholesaler_price
                array(
                    'id'         => 'wholesaler_price',
                    'type'       => 'fieldset',
                    'title'      => esc_html__('Wholesaler Price', 'whols'),
                    'fields'     => array(
                        array(
                            'id'         => 'wholesaler_price_color',
                            'type'       => 'color',
                            'title'      => esc_html__('Color', 'whols'),
                            'output'     => ':is(bodoy,.products) .product .whols_wholesaler_price .whols_label_right, :is(body,.products) .product .whols_wholesaler_price .whols_label_right *'
                        ),
                        array(
                            'id'         => 'wholesaler_price_font_size',
                            'type'       => 'number',
                            'title'      => esc_html__('Font size', 'whols'),
                            'unit'       => 'px',
                            'output'     => ':is(bodoy,.products) .product .whols_wholesaler_price .whols_label_right, :is(body,.products) .product .whols_wholesaler_price .whols_label_right *',
                            'output_mode' => 'font-size'
                        ),
                        array(
                            'id'         => 'wholesaler_price_line_height',
                            'type'       => 'number',
                            'title'      => esc_html__('Line Height', 'whols'),
                            'unit'       => 'px',
                            'output'     => ':is(bodoy,.products) .product .whols_wholesaler_price .whols_label_right, :is(body,.products) .product .whols_wholesaler_price .whols_label_right *',
                            'output_mode' => 'line-height'
                        ),
                        array(
                            'id'         => 'wholesaler_price_font_weight',
                            'type'       => 'number',
                            'title'      => esc_html__('Font Weight', 'whols'),
                            'unit'       => ' ',
                            'output'     => ':is(bodoy,.products) .product .whols_wholesaler_price .whols_label_right, :is(body,.products) .product .whols_wholesaler_price .whols_label_right *',
                            'output_mode' => 'font-weight',
                            'desc'        => esc_html__('Default: 400', 'whols')
                        ),
                    )
                ),

                // wholesaler_price_margin
                array(
                    'id'          => 'wholesaler_price_margin',
                    'type'        => 'spacing',
                    'title'       => esc_html__('Wholesaler Price Area Margin', 'whols'),
                    'output'      => '.whols_wholesaler_price',
                    'output_mode' => 'margin',
                    'top_icon'    => '',
                    'right_icon'  => '',
                    'bottom_icon' => '',
                    'left_icon'   => '',
                ),

                // wholesaler_price_label
                array(
                    'id'         => 'save_amount_label',
                    'type'       => 'fieldset',
                    'title'      => esc_html__('Save Amount Label', 'whols'),
                    'fields'     => array(
                        array(
                            'id'         => 'save_amount_label_color',
                            'type'       => 'color',
                            'title'      => esc_html__('Color', 'whols'),
                            'output'     => '.whols_save_amount .whols_label_left'
                        ),
                        array(
                            'id'         => 'save_amount_label_font_size',
                            'type'       => 'number',
                            'title'      => esc_html__('Font size', 'whols'),
                            'unit'       => 'px',
                            'output'     => '.whols_save_amount .whols_label_left',
                            'output_mode' => 'font-size'
                        ),
                        array(
                            'id'         => 'save_amount_label_line_height',
                            'type'       => 'number',
                            'title'      => esc_html__('Line Height', 'whols'),
                            'unit'       => 'px',
                            'output'     => '.whols_save_amount .whols_label_left',
                            'output_mode' => 'line-height'
                        ),
                        array(
                            'id'         => 'save_amount_label_font_weight',
                            'type'       => 'number',
                            'title'      => esc_html__('Font Weight', 'whols'),
                            'unit'       => ' ',
                            'output'     => '.whols_save_amount .whols_label .whols_label_left',
                            'output_mode' => 'font-weight',
                            'desc'        => esc_html__('Default: 700', 'whols')
                        ),
                    )
                ),

                // save_amount_price
                array(
                    'id'         => 'save_amount_price',
                    'type'       => 'fieldset',
                    'title'      => esc_html__('Save Amount Price', 'whols'),
                    'fields'     => array(
                        array(
                            'id'         => 'save_amount_price_color',
                            'type'       => 'color',
                            'title'      => esc_html__('Color', 'whols'),
                            'output'     => '.whols_save_amount .whols_label_right'
                        ),
                        array(
                            'id'         => 'save_amount_price_font_size',
                            'type'       => 'number',
                            'title'      => esc_html__('Font size', 'whols'),
                            'unit'       => 'px',
                            'output'     => '.whols_save_amount .whols_label_right',
                            'output_mode' => 'font-size'
                        ),
                        array(
                            'id'         => 'save_amount_price_line_height',
                            'type'       => 'number',
                            'title'      => esc_html__('Line Height', 'whols'),
                            'unit'       => 'px',
                            'output'     => '.whols_save_amount .whols_label_right',
                            'output_mode' => 'line-height'
                        ),
                        array(
                            'id'         => 'save_amount_price_font_weight',
                            'type'       => 'number',
                            'title'      => esc_html__('Font Weight', 'whols'),
                            'unit'       => ' ',
                            'output'     => '.whols_save_amount .whols_label_right',
                            'output_mode' => 'font-weight',
                            'desc'        => esc_html__('Default: 400', 'whols')
                        ),
                    )
                ),

                // save_amount_margin
                array(
                    'id'          => 'save_amount_margin',
                    'type'        => 'spacing',
                    'title'       => esc_html__('Wholesaler Save Amount Margin', 'whols'),
                    'output'      => '.whols_save_amount',
                    'output_mode' => 'margin',
                    'top_icon'    => '',
                    'right_icon'  => '',
                    'bottom_icon' => '',
                    'left_icon'   => '',
                ),

                // minimum_quantity_notice
                array(
                    'id'         => 'minimum_quantity_notice',
                    'type'       => 'fieldset',
                    'title'      => esc_html__('Minimum Quantity Notice', 'whols'),
                    'fields'     => array(
                        array(
                            'id'         => 'retailer_price_color',
                            'type'       => 'color',
                            'title'      => esc_html__('Color', 'whols'),
                            'output'     => '.whols_minimum_quantity_notice'
                        ),
                        array(
                            'id'         => 'retailer_price_font_size',
                            'type'       => 'number',
                            'title'      => esc_html__('Font size', 'whols'),
                            'unit'       => 'px',
                            'output'     => '.whols_minimum_quantity_notice',
                            'output_mode' => 'font-size'
                        ),
                        array(
                            'id'         => 'retailer_price_line_height',
                            'type'       => 'number',
                            'title'      => esc_html__('Line Height', 'whols'),
                            'unit'       => 'px',
                            'output'     => '.whols_minimum_quantity_notice',
                            'output_mode' => 'line-height'
                        ),
                        array(
                            'id'         => 'retailer_price_font_weight',
                            'type'       => 'number',
                            'title'      => esc_html__('Font Weight', 'whols'),
                            'unit'       => ' ',
                            'output'     => '.whols_minimum_quantity_notice',
                            'output_mode' => 'font-weight',
                            'desc'        => esc_html__('Default: 400', 'whols')
                        ),
                    )
                ),
            )
        ));

        // Import/Export Tab
        \CSF::createSection( $prefix, array(
            'title'  => esc_html__( 'Import & Export',  'whols' ),
            'fields' => array(

                // backup
                array(
                    'id'    => 'backup',
                    'title' => esc_html__('Import / Export Settings', 'whols'),
                    'type'  => 'backup',
                ),

            )
        ) );    
    }

}