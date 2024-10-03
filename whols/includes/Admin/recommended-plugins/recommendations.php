<?php
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

    $recommendations->add_new_tab(array(
        'title' => esc_html__( 'Pro Version', 'whols' ),
        'plugins_type' => 'pro',
        'active' => true,
        'plugins' => array(
            array(
                'slug'      => 'whols-pro',
                'location'  => 'whols-pro.php',
                'name'      => esc_html__( 'Whols Pro – WooCommerce Wholesale Prices', 'whols' ),
                'link'      => 'https://hasthemes.com/plugins/whols-woocommerce-wholesale-prices/',
                'author_link'=> 'https://hasthemes.com/',
                'description'=> esc_html__( 'Whols is an outstanding WordPress plugin for WooCommerce that allows store owners to set wholesale prices for the products of their online stores. This plugin enables you to show special wholesale prices to the wholesaler. Users can easily request to become a wholesale customer by filling out a simple online registration form. Once the registration is complete, the owner of the store will be able to review the request and approve the request either manually or automatically.', 'whols' ),
            ),
        )
    ));

    $recommendations->add_new_tab(array(
        'title' => esc_html__( 'Recommended Plugins', 'whols' ),
        'plugins_type' => 'free',
        'plugins' => array(
            array(
                'slug'      => 'wishsuite',
                'location'  => 'wishsuite.php',
                'name'      => esc_html__( 'WishSuite', 'whols' )
            ),
            array(
                'slug'      => 'ever-compare',
                'location'  => 'ever-compare.php',
                'name'      => esc_html__( 'EverCompare', 'whols' )
            ),
            array(
                'slug'      => 'just-tables',
                'location'  => 'just-tables.php',
                'name'      => esc_html__( 'JustTables', 'whols' )
            ),
            array(
                'slug'      => 'wc-multi-currency',
                'location'  => 'wcmilticurrency.php',
                'name'      => esc_html__( 'Multi Currency', 'whols' )
            ),
        )
    ));

    $recommendations->add_new_tab(array(
        'title' => esc_html__( 'You May Also Like', 'whols' ),
        'plugins_type' => 'pro',
        'plugins' => array(
            array(
                'slug'      => 'just-tables-pro',
                'location'  => 'just-tables-pro.php',
                'name'      => esc_html__( 'JustTables Pro', 'whols' ),
                'link'      => 'https://hasthemes.com/wp/justtables/',
                'author_link'=> 'https://hasthemes.com/',
                'description'=> esc_html__( 'JustTables is an incredible WordPress plugin that lets you showcase all your WooCommerce products in a sortable and filterable table view. It allows your customers to easily navigate through different attributes of the products and compare them on a single page. This plugin will be of great help if you are looking for an easy solution that increases the chances of landing a sale on your online store.', 'whols' ),
            ),

            array(
                'slug'      => 'multicurrencypro',
                'location'  => 'multicurrencypro.php',
                'name'      => esc_html__( 'Multi Currency Pro for WooCommerce', 'whols' ),
                'link'      => 'https://hasthemes.com/plugins/multi-currency-pro-for-woocommerce/',
                'author_link'=> 'https://hasthemes.com/',
                'description'=> esc_html__( 'Multi-Currency Pro for WooCommerce is a prominent currency switcher plugin for WooCommerce. This plugin allows your website or online store visitors to switch to their preferred currency or their country’s currency.', 'whols' ),
            ),

            array(
                'slug'      => 'email-candy-pro',
                'location'  => 'email-candy-pro.php',
                'name'      => esc_html__( 'Email Candy Pro - Email customizer for WooCommerce', 'whols' ),
                'link'      => 'https://hasthemes.com/plugins/email-candy-pro/',
                'author_link'=> 'https://hasthemes.com/',
                'description'=> esc_html__( 'Email Candy is an outstanding WordPress plugin that allows you to customize the default WooCommerce email templates and give a professional look to your WooCommerce emails. If you are tired of using the boring design of WooCommerce emails and want to create customized emails, then this plugin will come in handy.', 'whols' ),
            ),

            array(
                'slug'      => 'hashbar-pro',
                'location'  => 'init.php',
                'name'      => esc_html__( 'HashBar - Notification Bar', 'whols' ),
                'link'      => 'https://hasthemes.com/plugins/wordpress-notification-bar-plugin/',
                'author_link'=> 'https://hasthemes.com/',
                'description'=> esc_html__( 'HashBar is a WordPress Notification / Alert / Offer Bar plugin which allows you to create unlimited notification bars to notify your customers. This plugin has option to show email subscription form (sometimes it increases up to 500% email subscriber), Offer text and buttons about your promotions. This plugin has the options to add unlimited background colors and images to make your notification bar more professional.', 'whols' ),
            ),

            array(
                'slug'      => 'wp-plugin-manager-pro',
                'location'  => 'plugin-main.php',
                'name'      => esc_html__( 'WP Plugin Manager Pro', 'whols' ),
                'link'      => 'https://hasthemes.com/plugins/wp-plugin-manager-pro/',
                'author_link'=> 'https://hasthemes.com/',
                'description'=> esc_html__( 'WP Plugin Manager Pro is a specialized WordPress Plugin that helps you to deactivate unnecessary WordPress Plugins page wise and boosts the speed of your WordPress site to improve the overall site performance.', 'whols' ),
            ),
        )
    ));
}