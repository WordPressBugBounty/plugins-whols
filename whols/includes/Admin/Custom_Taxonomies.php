<?php
/**
 * Whols Taxonomies.
 *
 * Registers taxonomies.
 *
 * @since 1.0.0
 */

namespace Whols\Admin;

/**
 * Custom_Taxonomies class.
 */
class Custom_Taxonomies{

    /**
     * Custom taxonomies constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        // create taxonomy
        add_action( 'init', array( $this, 'register_taxonomies' ) );
    }

    /**
     * Register taxonomies
     *
     * @since 1.0.0
     */
    public function register_taxonomies(){

        $labels = array(
            'name'                       => esc_html_x( 'Roles', 'Role General Name', 'whols' ),
            'singular_name'              => esc_html_x( 'Role', 'Role Singular Name', 'whols' ),
            'menu_name'                  => esc_html__( 'Role', 'whols' ),
            'all_items'                  => esc_html__( 'All Roles', 'whols' ),
            'parent_item'                => esc_html__( 'Parent Role', 'whols' ),
            'parent_item_colon'          => esc_html__( 'Parent Role:', 'whols' ),
            'new_item_name'              => esc_html__( 'New Role Name', 'whols' ),
            'add_new_item'               => esc_html__( 'Add New Role', 'whols' ),
            'edit_item'                  => esc_html__( 'Edit Role', 'whols' ),
            'update_item'                => esc_html__( 'Update Role', 'whols' ),
            'view_item'                  => esc_html__( 'View Role', 'whols' ),
            'separate_items_with_commas' => esc_html__( 'Separate roles with commas', 'whols' ),
            'add_or_remove_items'        => esc_html__( 'Add or remove roles', 'whols' ),
            'choose_from_most_used'      => esc_html__( 'Choose from the most used', 'whols' ),
            'popular_items'              => esc_html__( 'Popular Roles', 'whols' ),
            'search_items'               => esc_html__( 'Search Roles', 'whols' ),
            'not_found'                  => esc_html__( 'Not Found', 'whols' ),
            'no_terms'                   => esc_html__( 'No roles', 'whols' ),
            'items_list'                 => esc_html__( 'Roles list', 'whols' ),
            'items_list_navigation'      => esc_html__( 'Roles list navigation', 'whols' ),
        );

        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => false,
            'show_in_menu'               => false,
            'show_tagcloud'              => false,
        );

        register_taxonomy( 'whols_role_cat', array( 'post' ), $args );
    }
}