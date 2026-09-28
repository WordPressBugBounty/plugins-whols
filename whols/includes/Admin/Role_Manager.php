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

            // array_map() on a non-array is a fatal TypeError on PHP 8, and this
            // reads straight from $_POST.
            $posted_meta     = isset( $_POST['whols_user_request_meta'] ) && is_array( $_POST['whols_user_request_meta'] )
                ? wp_unslash( $_POST['whols_user_request_meta'] )
                : array();
            $meta_new        = array_map( 'sanitize_text_field', $posted_meta );

            // A request post with missing or malformed meta must not fatal here.
            if ( ! is_array( $meta_prev ) || empty( $meta_prev['user_id'] ) ) {
                return;
            }

            $user_id         = $meta_prev['user_id'];
            $assign_role     = isset( $meta_new['assign_role'] ) ? $meta_new['assign_role'] : '';
            $assign_role_new = array_key_exists( $assign_role, whols_get_taxonomy_terms( 'whols_role_cat' )) ? $assign_role : 'subscriber';
            $status_new      = isset( $meta_new['status'] ) ? $meta_new['status'] : '';

            // Add or swap only the Whols role. The previous code called
            // set_role('') first, which wipes every role the user holds — an
            // existing WooCommerce `customer` who applied for a wholesale
            // account silently lost the customer role on approval.
            $whols_roles = whols_get_taxonomy_terms( 'whols_role_cat' );

            if( $assign_role_new && $status_new == 'approve' ){

                $user = new \WP_User( $user_id );

                // Drop any other Whols role they hold, leave everything else.
                foreach( array_keys( $whols_roles ) as $whols_role ){
                    if( $whols_role !== $assign_role_new && in_array( $whols_role, (array) $user->roles, true ) ){
                        $user->remove_role( $whols_role );
                    }
                }

                $user->add_role( $assign_role_new );

            } else if( $status_new == 'reject' ||  empty( $assign_role_new ) ){
                $user = new \WP_User( $user_id );

                // Rejection removes the wholesale access only.
                foreach( array_keys( $whols_roles ) as $whols_role ){
                    if( in_array( $whols_role, (array) $user->roles, true ) ){
                        $user->remove_role( $whols_role );
                    }
                }

                // Make sure they are left with at least a basic role.
                if( empty( $user->roles ) ){
                    $user->add_role( 'subscriber' );
                }
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