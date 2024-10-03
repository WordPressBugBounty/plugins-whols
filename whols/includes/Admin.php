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

    /**
     * Admin constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
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

        // Bind admin page link to the plugin action link.
        add_filter( 'plugin_action_links_whols/whols.php', array($this, 'action_links_add'), 10, 4 );

        // Admin assets hook into action.
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

        // Set settings page as submenu
        add_action( 'admin_menu', array( $this, 'dashboard_menu_tweaks' ), 30 );

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
            wp_enqueue_style( 'vex', WHOLS_ASSETS . '/css/vex.css', null, WHOLS_VERSION );
            wp_enqueue_style( 'vex-theme-plain', WHOLS_ASSETS . '/css/vex-theme-plain.css', null, WHOLS_VERSION );
            wp_enqueue_style( 'whols-admin', WHOLS_ASSETS . '/css/admin.css', null, WHOLS_VERSION );
            wp_enqueue_script( 'vex', WHOLS_ASSETS . '/js/vex.combined.min.js', array('jquery'), WHOLS_VERSION );
            wp_enqueue_script( 'whols-admin', WHOLS_ASSETS . '/js/admin.js', array('jquery'), WHOLS_VERSION );

            // inline js for the settings submenu
            $is_whols_setting = isset( $_GET['page'] ) ? sanitize_text_field($_GET['page']) : '';
            $is_whols_setting = $is_whols_setting == 'whols-admin' ? 1 : 0;
            wp_add_inline_script( 'whols-admin', 'var whols_is_settings_page = '. esc_js( $is_whols_setting ) .';');
        }

        $css = '#adminmenu li a[href="admin.php?page=whols-welcome"]{display: none;}';
        wp_add_inline_style('common', $css);
    }

    /**
     * Set settings page as submenu
     *
     * @since 1.0.0
     */
    function dashboard_menu_tweaks(){
        global $menu, $submenu;
        $capabilities = whols_get_capabilities();
        
        $query = new \WP_Query(array(
            'post_type' => 'whols_user_request',
            'meta_query' => array(
                'relation' => 'AND',
                 array(
                    'key'     => 'whols_user_request_meta',
                    'value'   => serialize('approve'),
                    'compare' => 'NOT LIKE',
                 ),
                 array(
                    'key'     => 'whols_user_request_meta',
                    'value'   => serialize('reject'),
                    'compare' => 'NOT LIKE',
                 ),
               ),
        ));

        $pending_request_count = $query->post_count;
        wp_reset_postdata();
        
        if($pending_request_count > 0){
            $menu[56][0] = 'Whols <span class="update-plugins whols_request_count"><span>'. $pending_request_count .'</span></span>';
        }

        add_submenu_page( 'whols-admin', esc_html__('Whols Admin', 'whols'), esc_html__( 'Settings', 'whols' ), $capabilities['manage_settings'],'admin.php?page=whols-admin', '', 0);
        add_submenu_page( 'whols-admin', esc_html__('Welcome', 'whols'), esc_html__( 'Welcome', 'whols' ), $capabilities['manage_settings'],'whols-welcome', array( $this, 'quick_recommended_plugin'), 1);
    }

    // Recommended plugin page after activating the plugin
    public function quick_recommended_plugin(){
        wp_enqueue_script('ht-install-manager');

         // $plugin_file = 'woolentor-addons/woolentor_addons_elementor.php';
         $plugin_slug = 'woolentor-addons';
         $plugin_file = 'woolentor-addons/woolentor_addons_elementor.php';

         // Installed but Inactive.
         if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin_file ) && is_plugin_inactive( $plugin_file ) ) {

             $button_classes = 'button ht-activate-now button-primary';
             $button_text    = esc_html__( 'Active Now', 'whols' );

         // Not Installed.
         } elseif ( ! file_exists( WP_PLUGIN_DIR . '/' . $plugin_file ) ) {

             $button_classes = 'button ht-install-now button-primary';
             $button_text    = esc_html__( 'Active Now', 'whols' );

         // Active.
         } else {
             $button_classes = 'button disabled';
             $button_text    = esc_html__( 'Activated', 'whols' );
         }

         $data_attr = array(
             'slug'      => $plugin_slug,
             'location'  => $plugin_file,
             'name'      => '',
         );
        ?>
        <!-- ht-quick-recommended-plugin-area -->
        <div class="ht-qrp-area">
            <div class="ht-qrp">
                <div class="ht-qrp-body">
                    <div class="ht-qrp-logo">
                        <img src="<?php echo esc_url(WHOLS_ASSETS . '/images/woolentor-logo.png') ?>" alt="">
                    </div>
                    <p><?php echo __('<span>Want to have complete control over the dull designs of all WooCommerce default pages and create an eye-catching WooCommerce store?</span> If you are interested, don\'t forget to try out the free version of the WooLentor today!', 'whols') ?></p>
                    <button class="<?php echo esc_attr($button_classes); ?>" 
                        data-slug='<?php echo esc_attr($data_attr['slug']); ?>' 
                        data-location="<?php echo esc_attr($data_attr['location']); ?>"
                        data-progress_message="<?php echo esc_attr__('Activating..', 'whols') ?>" 
                        data-redirect_after_activate="<?php echo esc_url(admin_url('admin.php?page=whols-admin')) ?>">
                        <?php echo esc_html($button_text); ?>
                    </button>
                </div>
            </div>
        </div> <!-- .ht-quick-recommended-plugin-area -->
        <?php
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