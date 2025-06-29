<?php
namespace Whols\Admin;

class Menu_Manager {
	public function __construct() {
        // Hook run orders: init -> admin_menu -> custom_menu_order -> parent_file
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );

        // Set the taxonomy as submenu.
        add_action( 'admin_menu', array( $this, 'customer_roles_submenu') );

        // custom_menu_order action hook
        add_action( 'custom_menu_order', array( $this, 'custom_menu_order') );

        // Highlight the submenu when active this page.
        add_action( 'parent_file', array( $this, 'fix_submenu_hilight') );
	}

    /**
     * Register admin menu
     *
     * @return void
     */
    public function admin_menu() {
        add_menu_page(
            __( 'Whols', 'whols' ),
            __( 'Whols', 'whols' ),
            'manage_options',
            'whols-admin',
            [ $this, 'plugin_page' ],
            'dashicons-money-alt',
            '55.8'
        );
        
        // After initialization of the post type, the post type menu added in 0 index.
        // Fixed that issue by registering the submenu page.
        add_submenu_page( 'whols-admin', esc_html__('Whols Admin', 'whols'), esc_html__( 'Settings', 'whols' ), 'manage_options','admin.php?page=whols-admin', '', 0);

    }

    /**
     * Plugin page
     *
     * @return void
     */
    public function plugin_page() {
        ?>
        <div class="wrap">
            <div id="whols-vue-settings-app"></div>
        </div>
        <?php
    }

    /**
     * Create submenu for customer roles taxonomy.
     */
    public function customer_roles_submenu() {
        $capabilities = whols_get_capabilities();
        
        add_submenu_page( 'whols-admin', esc_html__('Wholesaler Roles', 'whols'), esc_html__('Wholesaler Roles', 'whols'), $capabilities['manage_roles'], 'edit-tags.php?taxonomy=whols_role_cat', '', null);
    }

    /**
     * Custom menu order.
     */
    public function custom_menu_order( $menu_order ) {
        global $menu, $submenu;

        $enable_conversation = whols_get_option('enable_conversation');

        // No need change menu order.
        if( !$enable_conversation ){
            return;
        }

        global $submenu;

        $conversation_menu = $submenu['whols-admin'][2];
        $wholesaler_roles_menu = $submenu['whols-admin'][3];

        // Add counter badge to conversation menu.
        $conversation_menu[0] = $conversation_menu[0] . ' <span class="awaiting-mod">' . whols_get_conversation_count( 'unread' ) . '</span>';

        // Swap order of conversation and wholesaler roles menu.
        $submenu['whols-admin'][2] = $wholesaler_roles_menu;
        $submenu['whols-admin'][3] = $conversation_menu;
    }

    public function get_pending_request_count(){
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

        return $pending_request_count;
    }

    /**
    * Highlight the submenu when active this page.
    */
    public function fix_submenu_hilight( $parent_file ) {
        global $current_screen, $parent_file, $submenu_file, $submenu; // Defined in wp-admin/menu-header.php _wp_menu_output function.

        // Fix Wholesaler Roles submenu does not highlight.
        $taxonomy = $current_screen->taxonomy;
        if ( $taxonomy == 'whols_role_cat' ) {
            $parent_file = 'whols-admin';
        }
 
        return $parent_file;
    }
}