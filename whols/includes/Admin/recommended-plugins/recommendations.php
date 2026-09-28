<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Constructor Parameters
 *
 * @param string    $text_domain your plugin text domain.
 * @param string    $parent_menu_slug the menu slug name where the "Recommendations" submenu will appear.
 * @param string    $submenu_label To change the submenu name.
 * @param string    $submenu_page_name an unique page name for the submenu.
 * @param int       $priority Submenu priority adjust.
 * @param string    $hook_suffix use it to load this library assets only to the recommedded plugins page. Not into the whol admin area.
 *
 */

 add_action( 'init', 'whols_recommendations_init' );
 function whols_recommendations_init() {
    if( class_exists('Hasthemes\Whols\HTRP_Recommended_Plugins') ){
        $recommendations = new Hasthemes\Whols\HTRP_Recommended_Plugins(
            array( 
                'text_domain'       => 'whols',
                'parent_menu_slug'  => 'whols-admin', 
                'menu_capability'   => 'manage_options', 
                'menu_page_slug'    => 'whols_recommendations',
                'priority'          => '',
                'assets_url'        => '',
                'hook_suffix'       => 'whols_page_whols_recommendations',
            )
        );

        // ShopLentor is a WooCommerce plugin — only worth featuring in the
        // primary "Recommended Plugins" tab when WooCommerce is actually
        // active; otherwise it stays discoverable under the "WooCommerce" tab.
        $woocommerce_active = class_exists( 'WooCommerce' );
        $shoplentor_entry   = array(
            'slug'      => 'woolentor-addons',
            'location'  => 'woolentor_addons_elementor.php',
            'name'      => esc_html__( 'ShopLentor – All-in-One WooCommerce Growth & Store Enhancement Plugin', 'whols' )
        );

        $recommendations->add_new_tab(array(
            'title' => esc_html__( 'Recommended Plugins', 'whols' ),
            'active' => true,
            'plugins' => array_merge(
                array(
                    array(
                        'slug'      => 'whols-pro',
                        'location'  => 'whols-pro.php',
                        'name'      => esc_html__( 'Whols Pro – WooCommerce Wholesale Prices', 'whols' ),
                        'link'      => 'https://wpwhols.com/pricing/',
                        'details_link'=> 'https://wpwhols.com/',
                        'author_link'=> 'https://hasthemes.com/',
                        'description'=> esc_html__( 'Whols is an outstanding WordPress plugin for WooCommerce that allows store owners to set wholesale prices for the products of their online stores. This plugin enables you to show special wholesale prices to the wholesaler. Users can easily request to become a wholesale customer by filling out a simple online registration form. Once the registration is complete, the owner of the store will be able to review the request and approve the request either manually or automatically.', 'whols' ),
                    ),
                ),
                $woocommerce_active ? array( $shoplentor_entry ) : array(),
                array(
                    array(
                        'slug'      => 'support-genix-lite',
                        'location'  => 'support-genix-lite.php',
                        'name'      => esc_html__( 'Support Genix – Helpdesk, AI Chatbot, Knowledge Base & Customer Support Ticketing System', 'whols' )
                    ),
                    array(
                        'slug'      => 'hashbar-wp-notification-bar',
                        'location'  => 'init.php',
                        'name'      => esc_html__( 'HashBar – Announcement, Notification Bar & Popup Campaign', 'whols' )
                    ),
                    array(
                        'slug'      => 'wp-plugin-manager',
                        'location'  => 'plugin-main.php',
                        'name'      => esc_html__( 'WP Plugin Manager – Deactivate plugins per page', 'whols' )
                    ),
                    array(
                        'slug'      => 'ht-contactform',
                        'location'  => 'contact-form-widget-elementor.php',
                        'name'      => esc_html__( 'HT Contact Form – Drag & Drop Form Builder for WordPress', 'whols' )
                    ),
                    array(
                        'slug'      => 'cookieray',
                        'location'  => 'cookieray.php',
                        'name'      => esc_html__( 'CookieRay – Cookie Banner for Cookie Consent (GDPR/CCPA Compliant)', 'whols' )
                    ),
                    array(
                        'slug'      => 'kelune-crm',
                        'location'  => 'kelune-crm.php',
                        'name'      => esc_html__( 'Kelune CRM – Contact Management, Email Marketing, Newsletter & Marketing Automation', 'whols' )
                    ),
                )
            )
        ));

        $recommendations->add_new_tab(array(
            'title' => esc_html__( 'WooCommerce', 'whols' ),
            'plugins' => array_merge(
                $woocommerce_active ? array() : array( $shoplentor_entry ),
                array(
                    array(
                        'slug'      => 'recurio',
                        'location'  => 'recurio.php',
                        'name'      => esc_html__( 'Recurio – Ultimate Subscription for WooCommerce', 'whols' )
                    ),
                )
            )
        ));

        $recommendations->add_new_tab(array(
            'title' => esc_html__( 'Popular', 'whols' ),
            'plugins' => array(
                array(
                    'slug'      => 'wp-plugin-manager',
                    'location'  => 'plugin-main.php',
                    'name'      => esc_html__( 'WP Plugin Manager – Deactivate plugins per page', 'whols' )
                ),
                array(
                    'slug'      => 'ht-easy-google-analytics',
                    'location'  => 'ht-easy-google-analytics.php',
                    'name'      => esc_html__( 'HT Easy GA4 – Google Analytics WordPress Plugin', 'whols' )
                ),
                array(
                    'slug'      => 'cookieray',
                    'location'  => 'cookieray.php',
                    'name'      => esc_html__( 'CookieRay – Cookie Banner for Cookie Consent (GDPR/CCPA Compliant)', 'whols' )
                ),
                array(
                    'slug'      => 'insert-headers-and-footers-script',
                    'location'  => 'init.php',
                    'name'      => esc_html__( 'Insert Headers and Footers Code – HT Script', 'whols' )
                ),
                array(
                    'slug'      => 'pixelavo',
                    'location'  => 'pixelavo.php',
                    'name'      => esc_html__( 'Pixelavo – Server Side Tracking & Pixel + AI Ads Tools', 'whols' )
                ),
                array(
                    'slug'      => 'courseglade-lms',
                    'location'  => 'courseglade-lms.php',
                    'name'      => esc_html__( 'CourseGlade LMS – Online Course & eLearning Platform', 'whols' )
                ),
            )
        ));
    }
 }

