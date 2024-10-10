<?php
namespace Whols\Admin;

class Menu_Manager {
	public function __construct() {
        add_action( 'admin_menu', function(){
            global $menu;

            // Add Settings page as submenu.
            add_submenu_page( 'whols-admin', esc_html__('Whols Admin', 'whols'), esc_html__( 'Settings', 'whols' ), 'manage_options','admin.php?page=whols-admin', '', 0);

            // Set the taxonomy as submenu.
            $capabilities = whols_get_capabilities();
            add_submenu_page( 'whols-admin', esc_html__('Wholesaler Roles', 'whols'), esc_html__('Wholesaler Roles', 'whols'), $capabilities['manage_roles'], 'edit-tags.php?taxonomy=whols_role_cat', '', 20);

            // Add pending request count to the menu.
            $pending_request_count = $this->get_pending_request_count();
            if($pending_request_count > 0){
                $menu[56][0] = 'Whols <span class="awaiting-mod">'. $pending_request_count .'</span></span>';
            }
        }, 30 );

        // Highlight the submenu when active this page.
        add_action( 'parent_file', array( $this, 'fix_submenu_hilight') );
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