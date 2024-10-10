<?php
/**
 * Whols Admin.
 *
 * @since 1.0.0
 */

namespace Whols;

/**
 * Admin class.
 */
class Admin {
    public $version = WHOLS_VERSION;

    /**
     * Admin constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        // Debug mode
        if( defined( 'WP_DEBUG' ) && WP_DEBUG ){
            $this->version = time();
        }

        new Admin\Custom_Posts();
        new Admin\Custom_Taxonomies();
        new Admin\Wholesaler_Request_Metabox();
        new Admin\Product_Metabox();
        new Admin\Role_Cat_Metabox();
        new Admin\Product_Category_Metabox();
        new Admin\User_Metabox();
        new Admin\Role_Manager();
        new Admin\Custom_Columns();
        new Admin\Install_Manager();
        new Admin\Menu_Manager();

        // Bind admin page link to the plugin action link.
        add_filter( 'plugin_action_links_whols/whols.php', array($this, 'action_links_add'), 10, 4 );

        // Admin assets hook into action.
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

        // Add page states to the page list table
        add_filter('display_post_states', array( $this, 'filter_post_states' ), 10, 2); 
    }

    /**
     * Action link add.
     *
     * @since 1.0.0
     */
    function action_links_add( $actions, $plugin_file, $plugin_data, $context ){

        $settings_page_link = sprintf(
            /*
             * translators:
             * 1: Settings label
             */
            '<a href="'. esc_url( get_admin_url() . 'admin.php?page=whols-admin' ) .'">%1$s</a>',
            esc_html__( 'Settings', 'whols' )
        );

        array_unshift( $actions, $settings_page_link );

        return $actions;
    }

    /**
     * Enqueue admin assets.
     *
     * @since 1.0.0
     */
    public function enqueue_admin_assets( $hook_suffix ) {
        $current_screen = get_current_screen();

        if ( 
            $current_screen->post_type   == 'whols_user_request' ||
            $current_screen->base        == 'toplevel_page_whols-admin' ||
            $current_screen->base        == 'whols_page_whols-welcome' ||
            $current_screen->post_type   == 'product' || 
            'user-edit'                  == $current_screen->base ||  
            $current_screen->taxonomy    == 'whols_role_cat'
        ) {
            wp_enqueue_style( 'vex', WHOLS_ASSETS . '/css/vex.css', null, $this->version );
            wp_enqueue_style( 'vex-theme-plain', WHOLS_ASSETS . '/css/vex-theme-plain.css', null, $this->version );
            wp_enqueue_style( 'whols-admin', WHOLS_ASSETS . '/css/admin.css', null, $this->version );
            wp_enqueue_script( 'vex', WHOLS_ASSETS . '/js/vex.combined.min.js', array('jquery'), $this->version );
            wp_enqueue_script( 'whols-admin', WHOLS_ASSETS . '/js/admin.js', array('jquery'), $this->version );

            // inline js for the settings submenu
            $is_whols_setting = isset( $_GET['page'] ) ? sanitize_text_field($_GET['page']) : '';
            $is_whols_setting = $is_whols_setting == 'whols-admin' ? 1 : 0;
            wp_add_inline_script( 'whols-admin', 'var whols_is_settings_page = '. esc_js( $is_whols_setting ) .';');
        }

        $css = '#adminmenu li a[href="admin.php?page=whols-welcome"]{display: none;}';
        wp_add_inline_style('common', $css);
    }

    /**
     * It adds a "Whols Registration Page" state to the page that is set as the registration page.
     * 
     * @param post_states (array) An array of post display states.
     * @param post The post object.
     */
    public function filter_post_states( $post_states, $post ){
        if( has_shortcode( $post->post_content, 'whols_registration_form') ||
            (whols_get_option('registration_page') && $post->ID == whols_get_option('registration_page'))
        ){
            $post_states['whols_registration_page'] = __('Whols Registration Page', 'whols');
        }
    
        return $post_states;
    }
}