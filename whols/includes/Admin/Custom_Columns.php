<?php
/**
 * Whols Post Columns.
 *
 * Filter Requested user post columns of Whols.
 *
 * @since 1.0.0
 */

namespace Whols\Admin;

/**
 * Post columns class.
 */
class Custom_Columns {

    /**
     * Post columns constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        add_filter( 'manage_whols_user_request_posts_columns', array( $this, 'filter_posts_columns' ) );
        add_action( 'manage_whols_user_request_posts_custom_column', array( $this, 'status_column_content' ), 10, 2 );

        // taxonomy column
        add_filter( 'manage_edit-whols_role_cat_columns', array( $this, 'register_role_category_columns' ) );
        add_filter ( 'manage_whols_role_cat_custom_column', array( $this, 'render_role_categroy_column'), 10, 3 );

        // Wholesale Price column into the Product list table
        add_filter( 'manage_product_posts_columns', array( $this, 'add_wholesale_price_column' ), 50 );
        add_action( 'manage_product_posts_custom_column', array( $this, 'wholesale_price_column_content' ), 10, 2 );
    }

    /**
     * Filter posts columns.
     *
     * @since 1.0.0
     *
     * @return array Columns array.
     */
    public function filter_posts_columns( $columns ) {
        $columns = array(
            'cb'        => $columns['cb'],
            'title'     => $columns['title'],
            'shortcode' => esc_html__( 'Status', 'whols' ),
            'date'      => $columns['date'],
        );

        return $columns;
    }

    /**
     * Status column content.
     *
     * @since 1.0.0
     */
    public function status_column_content( $column, $post_id ) {
        $post_id = absint( $post_id );
        $meta    = get_post_meta( $post_id, 'whols_user_request_meta', true );

        if( !empty($meta['status']) && $meta['status'] == 'approve' ){
            printf( 
                __( '%1$s Approved %2$s', 'whols' ),
                '<span class="whols_approved">',
                '</span>' 
            );
        } else if( !empty($meta['status']) && $meta['status'] == 'reject' ){
            printf( 
                __( '%1$s Rejected %2$s', 'whols' ),
                '<span class="whols_rejected">',
                '</span>' 
            );
        } else {
            printf( 
                __( '%1$s Pending %2$s', 'whols' ),
                '<span class="whols_pending">',
                '</span>' 
            );
        }
    }

    public function register_role_category_columns($columns){
        // Remove Old count column
        unset($columns['posts']);

         // add count column title
        $columns['count'] = esc_html__('Count', 'whols');

        return $columns;
    }

    public function render_role_categroy_column( $deprecated, $column_name, $term_id ){
        if ( $column_name == 'count' ) {
            $role = get_term_field( 'slug', $term_id, '', 'display' );
            $user_query = new \WP_User_Query( array( 'role' => $role ) );

            echo '<a href="users.php?role='. esc_attr($role) .'">'. esc_html( count($user_query->get_results()) ) .'</a>';
        }
    }

    // Add Wholesale Price column into the Product list table
    public function add_wholesale_price_column( $columns ) {
        // Insert wholesale price column after the price column
        $columns = whols_insert_element_after_specific_array_key( $columns, 'price', 
            array(
                'key'   => 'wholesale_price',
                'value' => __( 'Wholesale Price', 'whols' )
            )
        );

        return $columns;
    }

    // Wholesale Price column content
    public function wholesale_price_column_content( $column, $post_id ) {
        if ( $column == 'wholesale_price' ) {
            $post_id = absint( $post_id );
            
            $pricing = new \Whols\Wholesale_Product_Pricing( $post_id );
            
            if( $pricing->has_wholesale_pricing() ){
                $price = $pricing->get_wholesale_price();
                
                if( !is_array( $price ) ){
                    echo wc_price( $price );
                } elseif( $price['min'] && $price['max'] ){
                    // Display price range
                    echo wc_price( $price['min'] ) . ' - ' . wc_price( $price['max'] );
                }
            }
        }
    }
    
} // Class