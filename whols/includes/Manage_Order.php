<?php
namespace Whols;

/**
 * Manage Order
 */
class Manage_Order {
    /**
     * Constructor
     */
    public function __construct() {
        // Adding a meta data to the order item
        // It will show on the order received page and order edit page for admin
        add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'add_order_item_meta' ), 10, 4 );

        // Filter thank you message of order received page for wholesales
        add_filter( 'woocommerce_thankyou_order_received_text', array( $this, 'add_custom_thank_you_message') );
    }

    /**
     * It adds the wholesaler role to the order meta
     * 
     * @param order_id The order ID
     * @param posted The  data for the checkout form
     */
    public function add_order_meta( $order_id, $posted ){

        // Save the wholesaler role in the order meta
        if( whols_is_wholesaler() ){
            $wholesaler_roles = whols_get_current_user_roles();
            // $wholesaler_roles = count($wholesaler_roles) > 1 ? implode(',', $wholesaler_roles) : current($wholesaler_roles);

            foreach( $wholesaler_roles as $slug => $role ){
                add_post_meta($order_id, '_whols_order_type', $role);
            }
            
        }
    }

    /**
     * It adds a dropdown to the admin orders page that allows you to filter orders by type
     */
    public function add_filter(){
        global $typenow;
        
        $order_type = isset( $_GET['whols_order_type'] ) ? wc_clean($_GET['whols_order_type']) : '';

        if( $typenow === 'shop_order' ){
            $roles = whols_roles_dropdown_options();
            ?>
            <select name="whols_order_type" id="whols_order_type">
                <?php
                    echo '<optgroup label="'. __('Types', 'whols') .'">';
                        foreach( $this->filter_default_options as $option_name => $option_label ){
                            printf( '<option value="%s" %s>%s</option>',
                                esc_attr( $option_name ),
                                selected( $order_type, $option_name ),
                                esc_html( $option_label )
                            );
                        }
                    echo '</optgroup>';

                    echo '<optgroup label="'. __('Roles', 'whols') .'">';
                        foreach( $roles as $option_name => $option_label ){
                            printf( '<option value="%s" %s>%s</option>',
                                esc_attr( $option_name ),
                                selected( $order_type, $option_name ),
                                esc_html( $option_label )
                            );
                        }
                echo '</optgroup>';

                ?>
            </select>
            <?php
        }
    }

    /**
	 * Filter order type.
	 *
	 * @param WP_Query $wp Query object.
	 */
	public function order_type_custom_field( $wp ) {
        global $pagenow;

        $post_type = !empty($wp->query_vars['post_type']) ? $wp->query_vars['post_type'] : '';

		if ( 'edit.php' !== $pagenow || 'shop_order' !== $post_type || !isset( $_GET['whols_order_type'] ) ) {
			return;
		}

        $order_type = wp_unslash($_GET['whols_order_type']);

        switch ($order_type) {
            case 'all':
                // Do nothing
                break;

            case 'wholesale_only':
                $wp->query_vars['meta_query'] = array(
                    array(
                        'key'     => '_whols_order_type',
                        'compare' => 'EXISTS'
                    )
                );
                break;

            case 'retail_only':
                $wp->query_vars['meta_query'] = array(
                    array(
                        'key'     => '_whols_order_type',
                        'compare' => 'NOT EXISTS'
                    )
                );
                break;
            
            default:
                $wp->query_vars['meta_query'] = array(
                    array(
                        'key' => '_whols_order_type',
                        'value' => $order_type,
                    )
                );
                break;
        }
	}

    /**
     * If the current user is a wholesaler and the product is wholesale priced, add a meta data to the
     * order item
     * 
     * @return void
     */
    function add_order_item_meta( $item, $cart_item_key, $values, $order ) {
        if( !whols_is_wholesaler() ){
            return;
        }     
    
        if ( whols_is_wholesale_priced( $item->get_product_id(), $item->get_quantity()) ) {
            $item->add_meta_data( '_wholesale_priced', 'Yes' );
            
            // Get the matched roles
            $matched_roles = array_intersect_key( array_flip(whols_get_current_user_roles()),  whols_roles_dropdown_options() );
            $matched_roles = array_keys($matched_roles);
    
            // Store only one role into the meta value because multiple value in one key doesn't support here like post meta
            $single_matche_role = current($matched_roles);
            if( $single_matche_role ){
                $item->add_meta_data( '_whols_role', $single_matche_role ); // Added underscore to prevent showin this meta value in the order received page
            }
        }
    }

    /**
     * It adds a custom thank you message to the order received page for wholesales.
     * 
     * @param message The default thank you message.
     * 
     * @return string
     */
    public function add_custom_thank_you_message( $message ){
        $enable         = whols_get_option('enable_custom_thank_you_message');
        $placement      = whols_get_option('thank_you_message_placement');
        $custom_message = whols_get_option('custom_thank_you_message');

        // Determine whether should return the default message
        // so we don't need to go further
        if( !whols_is_wholesaler() || !$enable || !$custom_message ){
            return $message;
        }

        global $wp;
        if( isset($wp->query_vars['order-received']) ){
            // Order id
            $order_id = absint($wp->query_vars['order-received']); // The order ID
            $order    = wc_get_order( $order_id ); // The WC_Order object

            $custom_message = str_replace('{billing_first_name}', $order->get_billing_first_name(), $custom_message);
            $custom_message = str_replace('{billing_last_name}', $order->get_billing_first_name(), $custom_message);
            $custom_message = str_replace('{billing_email}', $order->get_billing_email(), $custom_message);
            $custom_message = '<div class="whols-custom-thank-you-message">'. wpautop($custom_message) .'</div>';
        }

        switch ($placement) {
            case 'before_default_message':
                return $custom_message . $message;
                break;

            case 'after_default_message':
                return $message . $custom_message;
                break;
            
            default:
                return $custom_message;
                break;
        }

        return $message;
    }
}