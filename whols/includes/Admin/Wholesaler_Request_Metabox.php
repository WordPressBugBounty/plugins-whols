<?php
/**
 * Wholesaler Request Metabox.
 *
 * @since 1.0.0
 */

namespace Whols\Admin;

/**
 * Wholesaler Request metabox class.
 */
class Wholesaler_Request_Metabox {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->metabox_options();
	}

	/**
	 * Metabox Options.
	 *
	 * @since 1.0.0
	 */
	public function metabox_options() {
		$prefix           = 'whols_user_request_meta';
		$post_id          = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : '';
		$user_id          = '';
		$user_name        = '';
		$profile_edit_url = '';

		if( get_post_type( $post_id ) ==  'whols_user_request' ){
			$meta = get_post_meta( $post_id, $prefix, true );

			$user_id = $user_name = '';
			if( $post_id ){
				$user_id       = $meta['user_id'];
				$user_data_obj = get_userdata( $meta['user_id'] );
				$user_name     = get_userdata( $meta['user_id'] ) ? $user_data_obj->user_login : '';
			}
			
			$profile_edit_url = get_admin_url() . '/user-edit.php?user_id='. $user_id;
		}

		// Create metabox
		\CSF::createMetabox( $prefix, array(
			'title'     => esc_html__( 'Edit Request', 'whols' ),
			'post_type' => 'whols_user_request',
			'theme'		=> 'light',
			'data_type'	=> 'serialize'
		) );
		

		// Create a section
		\CSF::createSection( $prefix, array(
			'title'  => '',
			'fields' => array(

				array(
					'type'    => 'notice',
					'style'   => 'info',
					'content' => __( '<a href="'. $profile_edit_url .'">Click Here</a> to edit/update profile informations like (Shipping Address, Billing Address etc) of this user', 'whols'),
				),

				array(
					'id'    => 'registration_details',
					'type'  => 'registration_details',
					'title' => __('Registration Details', 'whols'),
				),

				array(
					'id'         => 'user_id',
					'type'       => 'text',
					'title'      => esc_html__( 'User ID', 'whols' ),
					'attributes' => array(
						'readonly' => 'readonly'
					),
					'default'    => $user_id,
					'class' => 'whols_userid',
				),

				array(
					'id'         => 'user_name',
					'type'       => 'text',
					'title'      => esc_html__( 'Username', 'whols' ),
					'attributes' => array(
						'readonly' => 'readonly'
					),
					'default'    => $user_name,
					'class' => 'whols_username',
				),

				array(
					'id'          => 'assign_role',
					'type'        => 'select',
					'title'       => esc_html__( 'Assign A Role', 'whols' ),
					'placeholder' => esc_html__( 'Select role', 'whols' ),
					'options'     => whols_get_taxonomy_terms(),
				),

				array(
					'id'      => 'status',
					'type'    => 'radio',
					'title'   => esc_html__( 'Approve / Reject', 'whols' ),
					'options' => array(
						'approve'   => esc_html__( 'Approve', 'whols'),
						'reject'    => esc_html__( 'Reject', 'whols' )
					),
					'default' => '',
				),

			)

		) );
	}

}