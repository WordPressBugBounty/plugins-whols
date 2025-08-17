<?php
namespace Whols\Admin;

class Menu_Manager {
	public function __construct() {
        add_action( 'admin_menu', [ $this, 'register_main_menu' ] );

        // Set settings page as submenu
        add_action( 'admin_menu', array( $this, 'add_submenu_pages' ), 30 );

        // custom_menu_order action hook
        // add_action( 'custom_menu_order', array( $this, 'custom_menu_order') );

        // Highlight the submenu when active this page.
        add_action( 'parent_file', array( $this, 'fix_submenu_hilight') );

        // Set active class using js
        add_action('admin_footer', [ $this, 'menu_item_active_js' ]);
	}

    /**
     * Register admin menu
     *
     * @return void
     */
    public function register_main_menu() {
        add_menu_page(
            __( 'Whols', 'whols' ),
            __( 'Whols', 'whols' ),
            'manage_options',
            'whols-admin',
            [ $this, 'plugin_page' ],
            'dashicons-money-alt',
            '58.5'
        );
    }

    /**
     * Set settings page as submenu
     *
     * @since 1.0.0
     */
    function add_submenu_pages(){
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
        
        // Add pending request count to menu
        if($pending_request_count > 0){
            $menu['58.5'][0] = 'Whols <span class="update-plugins whols_request_count"><span>'. $pending_request_count .'</span></span>';
        }

        // Dashboard
        add_submenu_page( 'whols-admin', esc_html__('Dashboard', 'whols'), esc_html__('Dashboard', 'whols'), $capabilities['manage_settings'], 'admin.php?page=whols-admin#/dashboard', '', 0);
        
        // Settings
        add_submenu_page( 'whols-admin', 'Whols Admin', esc_html__( 'Settings', 'whols' ), $capabilities['manage_settings'], 'admin.php?page=whols-admin#/settings/general', '', 1);

        // Wholesaler requestes (reststered from post type)
        
        // Wholesaler Roles
        add_submenu_page( 'whols-admin', esc_html__('Wholesaler Roles', 'whols'), esc_html__('Wholesaler Roles', 'whols'), $capabilities['manage_roles'], 'edit-tags.php?taxonomy=whols_role_cat', '', 3);
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
     * Custom menu order.
     */
    public function custom_menu_order( $menu_order ) {
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

    /**
    * Highlight the submenu when active this page.
    */
    public function fix_submenu_hilight( $parent_file ) {
        global $current_screen, $parent_file, $submenu_file; // Defined in wp-admin/menu-header.php _wp_menu_output function.

        // Fix Settings submenu does not highlight.
        // if( $current_screen->base === 'toplevel_page_whols-admin' ){
        //     $submenu_file = 'admin.php?page=whols-admin';
        // }

        // Fix Wholesaler Roles submenu does not highlight.
        $taxonomy = $current_screen->taxonomy;
        if ( $taxonomy == 'whols_role_cat' ) {
            $parent_file = 'whols-admin';
        }
 
        return $parent_file;
    }

    public function menu_item_active_js(){
        $submenu_items = 'li.toplevel_page_whols-admin ul.wp-submenu li:nth-child(2), li.toplevel_page_whols-admin ul.wp-submenu li:nth-child(3)';
        ?>
        <script>
            jQuery(document).ready(function($) {
                const $subMenuItems = $('<?php echo wp_kses_post($submenu_items); ?>');
            
                // Function to handle menu activation
                const activateMenuItem = (hash) => {
                    // Remove active class from all menu items first
                    $subMenuItems.removeClass('current active');
                    
                    // Find and activate the matching menu item
                    $subMenuItems.each(function() {
                        const subMenuLink = $(this).find('a').attr('href');
                        if (hash && subMenuLink.indexOf(hash) > -1) {
                            $(this).addClass('current active');
                        }
                    });
                };
                
                // Initialize for page load
                activateMenuItem(window.location.hash);
                
                // Add click event handler to menu items
                $subMenuItems.on('click', function() {
                    const clickedItemHref = $(this).find('a').attr('href');
                    const hashPart = clickedItemHref.split('#')[1];
                    
                    if (hashPart) {
                        // Use timeout to ensure this runs after the default navigation
                        setTimeout(() => {
                            activateMenuItem('#' + hashPart);
                        }, 50);
                    }
                });
                
                // Listen for hash changes to handle browser navigation
                $(window).on('hashchange', function() {
                    activateMenuItem(window.location.hash);
                });
            });
        </script>
        <?php
    }
}