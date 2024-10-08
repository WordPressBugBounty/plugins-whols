<?php
namespace Whols\Admin;

/**
 * Install manager class
 */
class Install_Manager {

    /**
     * [$_instance]
     * @var null
     */
    private static $_instance = null;

    /**
     * [instance] Initializes a singleton instance
     * @return [Install_Manager]
     */
    public static function instance( $args = [] ) {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self( $args );
        }
        return self::$_instance;
    }

    public $current_directory_url;

    /**
     * [__construct] Class construct
     */
    public function __construct( $args = [] ) {
        $this->current_directory_url = plugins_url('', __FILE__);

        // Enqueue script
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );

         // Ajax Action
        add_action( 'wp_ajax_htim_activate_plugin', [ $this, 'activate_plugin' ] );
    }

    /**
     * [enqueue_assets]
     * @param  [string] $hook_suffix Current page hook
     * @return [void] 
     */
    public function enqueue_assets( $hook_suffix ) {
        wp_register_script( 'ht-install-manager', $this->current_directory_url. '/js/plugins_install_manager.js', array('jquery','wp-util', 'updates'), WHOLS_VERSION, true );

        $localize_vars['ajaxurl']   = admin_url('admin-ajax.php');
        $localize_vars['i18n'] = array(
            'buynow'        => esc_html__( 'Buy Now', 'whols' ),
            'preview'       => esc_html__( 'Preview', 'whols' ),
            'installing'    => esc_html__( 'Installing..', 'whols' ),
            'activating'    => esc_html__( 'Activating..', 'whols' ),
            'activated'     => esc_html__( 'Activated', 'whols' ),
        );
        wp_localize_script( 'ht-install-manager', 'htim_params', $localize_vars );
    }

    /**
     * [activate_plugin] Plugin activation ajax callable function
     * @return [JSON]
     */
    public function activate_plugin(){
        if ( !current_user_can( 'install_plugins' ) || !isset( $_POST['location'] ) || !$_POST['location'] ) {
            wp_send_json_error(
                array(
                    'success' => false,
                    'message' => esc_html__( 'Plugin Not Found', 'whols' ),
                )
            );
        }

        $plugin_file = ( isset( $_POST['location'] ) ) ? esc_attr( $_POST['location'] ) : '';
        $activate    = activate_plugin( $plugin_file, '', false, true );

        if ( is_wp_error( $activate ) ) {
            wp_send_json_error(
                array(
                    'success' => false,
                    'message' => $activate->get_error_message(),
                )
            );
        }

        wp_send_json_success(
            array(
                'success' => true,
                'message' => esc_html__( 'Plugin Successfully Activated', 'whols' ),
            )
        );
    }
}