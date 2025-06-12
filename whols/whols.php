<?php
/**
 * Plugin Name: Whols - Wholesale Prices and B2B Store Solution for WooCommerce
 * Plugin URI:  https://wpwhols.com/
 * Description: This plugin provides all the necessary features that you will ever need to sell wholesale products from your WooCommerce online store.
 * Version:     2.2.0
 * Author:      HasThemes
 * Author URI:  https://hasthemes.com
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: whols
 * Domain Path: /languages
 */

use const Whols\PL_VERSION;
use const Whols\PL_FILE;
use const Whols\PL_PATH;
use const Whols\PL_URL;

// If this file is accessed directly, exit
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main Whols class
 *
 * @since 1.0.0
 */

final class Whols_Lite {

    /**
     * Whols version
     *
     * @since 1.0.0
     */
    public $version = '2.2.0';

    /**
     * The single instance of the class
     *
     * @since 1.0.0
     */
    protected static $_instance = null;

    /**
     * Main Whols Instance
     *
     * Ensures only one instance of Whols is loaded or can be loaded
     *
     * @since 1.0.0
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Whols Constructor
     *
     * @since 1.0.0
     */
    private function __construct() {
        $this->define_constants();
        $this->includes();
        $this->run();
    }

    /**
     * Define the required constants
     *
     * @since 1.0.0
     */
    private function define_constants() {
        define( 'WHOLS_VERSION', $this->version );
        define( 'WHOLS_FILE', __FILE__ );
        define( 'WHOLS_PATH', __DIR__ );
        define( 'WHOLS_URL', plugins_url( '', WHOLS_FILE ) );
        define( 'WHOLS_ASSETS', WHOLS_URL . '/assets' );

		define( 'Whols\PL_VERSION', $this->version );
		define( 'Whols\PL_FILE', __FILE__ );
		define( 'Whols\PL_PATH', __DIR__ );
		define( 'Whols\PL_URL', plugins_url( '', PL_FILE ) );
		define( 'Whols\PL_ASSETS', PL_URL . '/assets' );
    }

    /**
     * Include files
     *
     * @since 1.0.0
     */
    public function includes() {
		/**
		 * Including plugin file for secutiry purpose
		 */
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( ! function_exists( 'get_current_screen' ) ) {
			require_once ABSPATH . '/wp-admin/includes/screen.php';
		}

        /**
         * Load files
         */
        require_once WHOLS_PATH . '/includes/functions.php';
        require_once WHOLS_PATH . '/includes/ajax-actions.php';

        if ( ! class_exists( 'CSF' ) ) {
            require_once WHOLS_PATH .'/includes/Admin/csf-settings-custom/classes/setup.class.php';
            require_once WHOLS_PATH . '/includes/Admin/csf-fields/CSF_Field_registration_details.php';
        }

        // Assets
        require_once WHOLS_PATH . '/includes/class-assets-manager.php';

        require_once WHOLS_PATH . '/includes/Admin.php';

        require_once WHOLS_PATH . '/includes/Admin/Custom_Posts.php';
        require_once WHOLS_PATH . '/includes/Admin/Custom_Taxonomies.php';
        require_once WHOLS_PATH . '/includes/Admin/Wholesaler_Request_Metabox.php';
        require_once WHOLS_PATH . '/includes/Admin/Product_Metabox.php';
        require_once WHOLS_PATH . '/includes/Admin/User_Metabox.php';
        require_once WHOLS_PATH . '/includes/Admin/Role_Cat_Metabox.php';
        require_once WHOLS_PATH . '/includes/Admin/Product_Category_Metabox.php';
        require_once WHOLS_PATH . '/includes/Admin/Role_Manager.php';
        require_once WHOLS_PATH . '/includes/Admin/Custom_Columns.php';
        require_once WHOLS_PATH . '/includes/Admin/CSF_Field_whols_image.php';
        require_once WHOLS_PATH . '/includes/Admin/recommended-plugins/class.recommended-plugins.php';
        require_once WHOLS_PATH . '/includes/Admin/recommended-plugins/recommendations.php';
        require_once WHOLS_PATH . '/includes/Admin/install-manager/class-install-manager.php';
        require_once WHOLS_PATH . '/includes/Admin/Diagnostic_Data.php';
        require_once WHOLS_PATH . '/includes/Admin/Trial.php';
        require_once WHOLS_PATH . '/includes/Admin/class-menu-manager.php';
        require_once WHOLS_PATH . '/includes/Admin/Notice_Handler.php';

        require_once WHOLS_PATH . '/includes/Frontend.php';
        require_once WHOLS_PATH . '/includes/Frontend/Wholesaler_Login_Register.php';
        require_once WHOLS_PATH . '/includes/Frontend/Woo_Config.php';

        whols_include_plugin_file('includes/class-wholesale-product-pricing.php');

        // Popup
        whols_include_plugin_file('includes/popup/class-whols-popup.php');

        // Quote
        require_once WHOLS_PATH . '/includes/request-a-quote/class-request-quote.php';
        
        // Save Order List
        whols_include_plugin_file('includes/save-order-list/class-save-order-list.php');
        
        require_once WHOLS_PATH . '/includes/Email_Notifications.php';
        require_once WHOLS_PATH . '/includes/Manage_Order.php';
        require_once WHOLS_PATH . '/includes/Compatibility.php';

        // Vue settings
		require_once PL_PATH . '/includes/vue-settings/class-init.php';
    }

    /**
     * First initialization of the plugin
     *
     * @since 1.0.0
     */
    private function run() {
        register_activation_hook( __FILE__, array( $this, 'register_activation_hook_cb' ) );

        if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
            add_action( 'admin_notices', array( $this, 'build_dependencies_notice' ) );
        } else {
            // Set up localisation.
            add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

            // Finally initialize this plugin
            add_action( 'plugins_loaded', array( $this, 'plugins_loaded_cb' ) );

            // Redirect to welcome page after activate the plugin
            // $plugin_file = 'woolentor-addons/woolentor_addons_elementor.php';
			// if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin_file ) && is_plugin_inactive( $plugin_file ) ) {
			// 	add_action('admin_init', array( $this, 'redirect_after_activate') );
			// }

            // Redirect after activation
            add_action('admin_init', array( $this, 'do_activation_redirect') );
        }
    }

    /**
     * Do stuff upon plugin activation
     *
     * @since 1.0.0
     */
    public function register_activation_hook_cb() {
        // deactivate the pro plugin if active
        if ( ! function_exists('is_plugin_active') ){ 
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }

        if( is_plugin_active('whols-pro/whols-pro.php') ){
            add_action('update_option_active_plugins', function(){
                deactivate_plugins('whols-pro/whols-pro.php');
            });
        }

        $installed = get_option( 'whols_installed' );

        if ( ! $installed ) {
            update_option( 'whols_installed', time() );
        }

        update_option( 'whols_version', WHOLS_VERSION );
        update_option( 'whols_do_activation_redirect', 'yes' );

        // Save initial settings if not already.
		$existing_settings = get_option('whols_options');
		if ( empty($existing_settings) ) {
			$defaults = Whols\Vue_Settings\Settings_Defaults::get_defaults();
			update_option( 'whols_options', $defaults );
		}
    }

    /**
	 * Maybe redirect to the admin page
	 * 
	 * Checks if the activation redirect flag is set and redirects to the admin page if needed
	 * 
	 * @return void
	 */
	public function do_activation_redirect() {
		// Only run on admin page load
		if ( ! is_admin() || wp_doing_ajax() ) {
			return;
		}

		// Check if we should redirect
		$redirect = get_option( 'whols_do_activation_redirect' );
		
		if ( 'yes' === $redirect ) {
			// Delete the redirect option to prevent multiple redirects
			delete_option( 'whols_do_activation_redirect' );

			$redirect_link = admin_url( 'admin.php?page=whols-admin' );

			if( whols_should_show_onboarding() ){
				$redirect_link = admin_url( 'admin.php?page=whols-admin#onboarding' );
			}
			
			// Redirect to the admin page
			wp_safe_redirect( $redirect_link );
			exit;
		}
	}

    /**
     * Load the plugin textdomain
     *
     * @since 1.0.0
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain( 'whols', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    /**
     * Initialize this plugin
     *
     * @since 1.0.0
     */
    public function plugins_loaded_cb() {
        // Both admin + frontend
        new Whols\Assets_Manager();
        new Whols\Manage_Order();
        new Whols\Compatibility();
        new Whols\Admin\Custom_Posts();

        // Prior this was instantiate only in the Frontend.php file
        // The reason why moved it here is to load it on both frontend & admin because for using the has_shortcode function in the admin area too.
        new Whols\Frontend\Wholesaler_Login_Register();

        // Frontend
        new Whols\Frontend();
        new Whols\Frontend\Woo_Config();
        new Whols\Request_Quote();

        if ( is_admin() ) {
            new Whols\Admin();
            new Whols\Admin\Menu_Manager();
        }

        // Insert default role
        add_action('init', array( $this, 'insert_default_term'), 12);

        // Test mode
        $this->init_test_mode();

        // Notice
        add_action( 'admin_head', function(){
            $remote_banner_data = whols_get_plugin_remote_data();

            if (!empty($remote_banner_data) && is_array($remote_banner_data)) {
                foreach ($remote_banner_data as $banner) {
                    if (empty($banner['disable'])) {
                        Whols\Admin\Notice_Handler::set_notice($banner);
                    }
                }
            }
        } );
    }

    /**
     * Output a admin notice when build dependencies not met
     *
     * @since 1.0.0
     */
    public function build_dependencies_notice() {
        $message = sprintf(
            /*
             * translators:
             * 1: Whols.
             * 2: WooCommerce.
             */
            esc_html__( '%1$s plugin requires the %2$s plugin to be installed and activated in order to work.', 'whols' ),
            '<strong>' . esc_html__( 'Whols', 'whols' ) . '</strong>',
            '<strong>' . esc_html__( 'WooCommerce', 'whols' ) . '</strong>'
        );

        printf( '<div class="notice notice-warning"><p>%1$s</p></div>', $message );
    }

    /**
     * Create and set default role
     */
    public function insert_default_term(){
        // check if category(term) exists
        $cat_exists = term_exists('whols_default_role', 'whols_role_cat');

        if ( !$cat_exists ) {
            // if term is not exist, insert it
            $new_cat = wp_insert_term(
                esc_html__( 'Default Role', 'whols' ),
                'whols_role_cat',
                array(
                    'description'   =>  esc_html__( 'Default Wholesale Role', 'whols' ),
                    'slug'          =>  'whols_default_role',
                )
            );
            // wp_insert_term returns an array on success so we need to get the term_id from it
            $default_cat_id = ($new_cat && is_array($new_cat)) ? $new_cat['term_id'] : false;
        } else {
            //if default category is already inserted, term_exists will return it's term_id
            $default_cat_id = $cat_exists;
        }

        // Setting default_{$taxonomy} option value as our default term_id to make them default and non-removable (like default uncategorized WP category)
        $stored_default_cat = get_option( 'whols_default_role' );

        if ( empty( $stored_default_cat ) && $default_cat_id )
            update_option( 'whols_default_role', $default_cat_id );
    }

    /**
     * Implement test mode feature
     * If test mode is enabled, it adds whols_default_role to the current logged in administrator user
     * and if the test mode is disabled, it removes the whols_default_role from the administrator
     */
    public function init_test_mode(){
        if( is_user_logged_in() && current_user_can('manage_options') ){

            $current_user   = wp_get_current_user();
            $is_test_mode   = whols_get_option('show_wholesale_price_for') === 'administrator' ? true : false;

            $test_mode_meta = get_user_meta(get_current_user_id(), 'whols_test_mode', true );

            if( $is_test_mode && !$test_mode_meta && !array_intersect($current_user->roles, array('whols_default_role')) ){
                $current_user->add_role('whols_default_role');
                update_user_meta( get_current_user_id(), 'whols_test_mode', 1 );
            } elseif( !$is_test_mode && $test_mode_meta && array_intersect($current_user->roles, array('whols_default_role') ) ){
                $current_user->remove_role('whols_default_role');
                delete_user_meta( get_current_user_id(), 'whols_test_mode' );
            }
         }
    }

}

/**
 * Returns the main instance of Whols
 *
 * @since 1.0.0
 */

function whols_lite() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
    return Whols_Lite::instance();
}

// Kick-off the plugin
whols_lite();
