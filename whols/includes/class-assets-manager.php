<?php
namespace Whols;

/**
 * Manage all of the assets of the plugin.
 */
class Assets_Manager {
    public $version = '';

    public function __construct() {
        // Set time as the version for development mode.
		if( defined('WP_DEBUG') && WP_DEBUG ){
			$this->version = time();
		} else {
			$this->version = WHOLS_VERSION;
		}
        
        // Register all scripts.
        add_action( 'wp_loaded', array( $this, 'register_all_scripts' ) );

        // Frontend Assets.
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );

        // Admin Assets.
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_backend_assets' ) );
    }

    public function is_plugin_screen(){

    }

    public function register_all_scripts(){
        // Styles
        wp_register_style( 'whols-common', WHOLS_URL . '/assets/css/common.css', '', $this->version );
        wp_register_style( 'whols-frontend', WHOLS_URL . '/assets/css/frontend.css', null, $this->version );
        
        // Scripts
        wp_register_script( 'whols', WHOLS_URL . '/assets/js/whols.js', array( 'jquery' ), $this->version, true );
    }

    /**
     * Enqueue frontend assets.
     */
    public function enqueue_frontend_assets() {
        // Styles
        wp_enqueue_style( 'whols-common' );
        wp_enqueue_style( 'whols-frontend' );
        
        // Scripts
        wp_enqueue_script( 'whols' );
        wp_localize_script( 'whols', 'whols_params', $this->get_localized_vars() );
    }

    /**
     * Enqueue backend assets.
     */
    public function enqueue_backend_assets() {

    }

    public function get_localized_vars(){
        $localize = array(
            'ajax_url'                    => admin_url( 'admin-ajax.php' ),
            'nonce'                       => wp_create_nonce( 'whols_nonce' ),
            'auto_apply_minimum_quantity' => whols_get_option('auto_apply_minimum_quantity') ? 1 : 0,
        );

        return $localize;
    }
}