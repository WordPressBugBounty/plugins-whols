<?php
/**
 * Custom Posts.
 *
 * Register custom posts.
 *
 * @since 1.0.0
 */

namespace Whols\Admin;

/**
 * Cusotm_Posts class.
 */
class Custom_Posts{
    /**
     * Custom posts constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        add_action( 'init', array( $this, 'custom_post_type' ), 0 );

        // Delete request when an user deleted from the database
        add_action( 'delete_user', array( $this, 'delete_request' ), 10, 3 );
    }
    
    /**
     * Register User Request Custom Post Type.
     *
     * @since 1.0.0
     */
    function custom_post_type() {

        $labels = array(
            'name'                  => esc_html_x( 'Wholsaler Requests', 'Post Type General Name', 'whols' ),
            'singular_name'         => esc_html_x( 'Wholesaler Request', 'Post Type Singular Name', 'whols' ),
            'menu_name'             => esc_html__( 'Wholsaler Requests', 'whols' ),
            'name_admin_bar'        => esc_html__( 'Wholsaler Request', 'whols' ),
            'archives'              => esc_html__( 'Request Archives', 'whols' ),
            'attributes'            => esc_html__( 'Request Attributes', 'whols' ),
            'parent_item_colon'     => esc_html__( 'Parent Request:', 'whols' ),
            'all_items'             => esc_html__( 'Wholesaler Requests', 'whols' ),
            'add_new_item'          => esc_html__( 'Add New Request', 'whols' ),
            'add_new'               => esc_html__( 'Add New', 'whols' ),
            'new_item'              => esc_html__( 'New Request', 'whols' ),
            'edit_item'             => esc_html__( 'Edit Request', 'whols' ),
            'update_item'           => esc_html__( 'Update Request', 'whols' ),
            'view_item'             => esc_html__( 'View Request', 'whols' ),
            'view_items'            => esc_html__( 'View Requests', 'whols' ),
            'search_items'          => esc_html__( 'Search Request', 'whols' ),
            'not_found'             => esc_html__( 'Not found', 'whols' ),
            'not_found_in_trash'    => esc_html__( 'Not found in Trash', 'whols' ),
            'featured_image'        => esc_html__( 'Featured Image', 'whols' ),
            'set_featured_image'    => esc_html__( 'Set featured image', 'whols' ),
            'remove_featured_image' => esc_html__( 'Remove featured image', 'whols' ),
            'use_featured_image'    => esc_html__( 'Use as featured image', 'whols' ),
            'insert_into_item'      => esc_html__( 'Insert into request', 'whols' ),
            'uploaded_to_this_item' => esc_html__( 'Uploaded to this request', 'whols' ),
            'items_list'            => esc_html__( 'Requests list', 'whols' ),
            'items_list_navigation' => esc_html__( 'Requests list navigation', 'whols' ),
            'filter_items_list'     => esc_html__( 'Filter requests list', 'whols' ),
        );

        $args = array(
            'label'                 => esc_html__( 'Wholesaler Request', 'whols' ),
            'description'           => esc_html__( 'Wholsaler Requests', 'whols' ),
            'labels'                => $labels,
            'supports'              => array( 'title' ),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => 'whols-admin', //  set it as a submenu
            'menu_position'         => 5,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'product',
            'map_meta_cap'          => true
        );

        register_post_type( 'whols_user_request', $args );

    }

    /**
     * Delete Wholesaler request
     *
     * @since 1.0.0
     */
    public function delete_request($id, $reassign, $user){
        $request_id = get_user_meta($id, 'whols_request_id', true);

        if($request_id){
            wp_delete_post($request_id);
        }
    }
}