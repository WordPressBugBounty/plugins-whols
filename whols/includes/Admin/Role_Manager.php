<?php
/**
 * Role Manager.
 *
 * Create, Edit & Delete roles.
 *
 * @since 1.0.0
 */

namespace Whols\Admin;

/**
 * Role_Manager class.
 */
class Role_Manager{
    /**
     * Role mannager constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        add_action( 'created_whols_role_cat', array( $this, 'create_role' ), 10, 2 );
        add_action( 'delete_whols_role_cat', array( $this, 'delete_role' ), 10, 4 );
        add_action( 'post_updated', array( $this, 'update_user_role' ), 10, 3 );
    }

    /**
     * Create a new role
     *
     * @since 1.0.0
     */
    public function create_role( $term_id, $tt_id ){
        $term_obj = get_term( $term_id, 'whols_role_cat' );
        add_role( $term_obj->slug, $term_obj->name . esc_html__( ' - Whols Role', 'whols' ), array( 'read' => true, 'level_0' => true ) );
    }

    /**
     * Update user role
     *
     * @since 1.0.0
     */
    public function update_user_role( $post_ID ){
        if( get_post_type( $post_ID ) == 'whols_user_request' ){
            $meta_prev       = get_post_meta( $post_ID, 'whols_user_request_meta', true );
            $meta_new        = isset( $_POST['whols_user_request_meta'] ) ? array_map( 'sanitize_text_field', $_POST['whols_user_request_meta'] ) : array();

            $user_id         = $meta_prev['user_id'];
            $assign_role_new = array_key_exists( $meta_new['assign_role'], whols_get_taxonomy_terms( 'whols_role_cat' )) ? $meta_new['assign_role'] : 'subscriber';
            $status_new      = isset( $meta_new['status'] ) ? $meta_new['status'] : '';

            // update role when previous role & new role is not same
            if( $assign_role_new && $status_new == 'approve' ){
 
                $user = new \WP_User( $user_id );

                // remove all roles
                $user->set_role('');

                // add new role
                $user->set_role( $assign_role_new );
                

            } else if( $status_new == 'reject' ||  empty( $assign_role_new ) ){
                $user = new \WP_User( $user_id );

                // remove all roles
                $user->set_role('');

                // add subscriber role
                $user->set_role('subscriber');
            }
        }
    }

    /**
     * Delete role
     *
     * @since 1.0.0
     */
    public function delete_role( $term_id, $tt_id, $deleted_term_id, $object_ids ){
        $term_obj = get_term( $deleted_term_id, 'whols_role_cat' );
        remove_role( $term_obj->slug );
    }
}