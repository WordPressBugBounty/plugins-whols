<?php
namespace Whols\Frontend;

/**
 * Woo_Config class.
 */
class Woo_Config {
    /**
     * Constructor.
     */
    public function __construct() {
        // Hide price for guest users
        add_action( 'wp', array( $this, 'hide_prices_for_guest_users') );

        // Apply discount or Adjust price
        add_action( 'woocommerce_before_calculate_totals', array( $this, 'apply_discount') );

        // Alter price html for loop/details to display wholesale price
        add_filter( 'woocommerce_get_price_html', array( $this, 'alter_price_html' ), 10, 2 );

        // Implement Variable product price tier
        add_filter( 'woocommerce_available_variation', array( $this, 'variable_product_price_tier' ), 10, 3 );

        // Disable coupon for wholesale customers
        add_filter( 'woocommerce_coupons_enabled', array( $this, 'hide_coupon_field_on_cart_page' ) );
        // Removing all coupons from the cart.
        add_action('woocommerce_before_cart', array( $this, 'remove_coupons_for_wholesalers' ) );
        add_action('woocommerce_before_checkout_form', array( $this, 'remove_coupons_for_wholesalers') );

        // Exclude products from archive page
        add_filter( 'woocommerce_product_query_meta_query', array( $this, 'exclude_products' ), 10, 2 );

        // Disable Free shipping for wholesaler
        add_filter( 'woocommerce_shipping_free_shipping_is_available', array( $this, 'enable_free_shipping_for_wholesalers' ), 10, 3 );

        // Tax
        add_action( 'woocommerce_before_calculate_totals', array( $this, 'tax_extempt_for_wholesalers' ), 100, 1 );

        // Cart page
        add_filter( 'woocommerce_get_item_data', array( $this, 'filter_get_item_data' ), 99, 2 );
    }

    public function hide_prices_for_guest_users(){
        $hide_price_for_guest_users = whols_get_option( 'hide_price_for_guest_users' );

        // Added filter to hide price for guest users based on conditions, get_queried_object() can be used to get the current page object.
        $hide_price_for_guest_users = apply_filters( 'whols_hide_price_for_guest_users', $hide_price_for_guest_users );
    
        if( $hide_price_for_guest_users && !is_user_logged_in() ){
            // remove cart button from loop
            remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
    
            // remove cart button from product details
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
    
            // disable purchasing products for over protection
            add_filter( 'woocommerce_is_purchasable', array( $this, 'disable_purchasable_guest_users' ) );
    
            // finally add custom message instead of showing price & cart button
            add_filter( 'woocommerce_get_price_html', array( $this, 'filter_woocommerce_get_price_html' ), 10, 2 );
        }
    }

    public function disable_purchasable_guest_users( $purchasable ){
        return false;
    }

    /**
     * Filter woocommerce_get_price_html
     *
     * @since 1.0.0
     */
    public function filter_woocommerce_get_price_html( $price, $product ){
        $lgoin_to_see_price_label = whols_get_option( 'lgoin_to_see_price_label' );
        $my_account_page_id = wc_get_page_id('myaccount');

        if( $my_account_page_id >= 1 && get_post_status($my_account_page_id) == 'publish' ){
            $login_link = get_permalink( $my_account_page_id );
        } else {
            $login_link = wp_login_url();
        }

        if( $lgoin_to_see_price_label ){
            $price = '<a href="'. esc_url( $login_link ) .'">' . esc_html( $lgoin_to_see_price_label ) . '</a>';
        } else {
            $price = '<a href="'. esc_url( $login_link ) .'">'. esc_html__( 'Login to view price', 'whols' ) .'</a>';
        }

        return $price;
    }

    /**
     * Set price based on cart quantity
     */
    public function apply_discount( $cart_object ) {
        // current user role
        $current_user_id     = get_current_user_id();

        if ( (is_admin() && ! defined( 'DOING_AJAX' )) || !whols_is_wholesaler( $current_user_id ) ){
            return;
        }

        $pricing_model           = 'single_role';
        foreach ( $cart_object->get_cart() as $hash => $value ) {
            $price_type_1_properties = whols_get_option( 'price_type_1_properties' );
            $enable_this_pricing     = $price_type_1_properties['enable_this_pricing'];
            $price_type              = $price_type_1_properties['price_type'];
            $price_value             = $price_type_1_properties['price_value'];
            $minimum_quantity        = $price_type_1_properties['minimum_quantity'];
            
            $term_meta           = '';
            
            $product_obj = $value['data'];
            $current_product_category_ids = $product_obj->get_category_ids();

            foreach( $current_product_category_ids as $id ){
                $term_meta = whols_get_term_meta( $id, 'whols_product_category_meta' );

                if( $pricing_model  ==  'single_role' ){
                    $price_type_1_properties = whols_get_option( 'price_type_1_properties' );
                    $enable_this_pricing     = $price_type_1_properties['enable_this_pricing'];
                    $price_type              = $price_type_1_properties['price_type'];
                    $price_value             = $price_type_1_properties['price_value'];
                    $minimum_quantity        = $price_type_1_properties['minimum_quantity'];

                    // override from category level
                    if( isset( $term_meta[ 'price_type_1_properties' ] ) && $term_meta[ 'price_type_1_properties' ] ){
                        $price_type_1_properties = $term_meta[ 'price_type_1_properties' ];
                        if( $price_type_1_properties['enable_this_pricing']  ){
                            $enable_this_pricing = $price_type_1_properties['enable_this_pricing'];
                            $price_type          = $price_type_1_properties['price_type'];
                            $price_value         = $price_type_1_properties['price_value'];
                            $minimum_quantity    = $price_type_1_properties['minimum_quantity'];

                            break;
                        }
                    }
                }
            }

            // override from product level
            if( $pricing_model == 'single_role' ){
                $price_type_1_properties = get_post_meta( $product_obj->get_id(), '_whols_price_type_1_properties', true);
                if( $price_type_1_properties ){
                    $price_type_1_properties_arr = explode( ':', $price_type_1_properties );
                    if( $price_type_1_properties_arr[0] ){
                        $enable_this_pricing = true;
                        $price_type = 'flat_rate';
                        $price_value = $price_type_1_properties_arr[0];
                        $minimum_quantity = $price_type_1_properties_arr[1] ? $price_type_1_properties_arr[1] : 1;
                    }
                }
            }

            // finally apply the new product price into the cart
            if( $enable_this_pricing && $price_value &&  $value['quantity'] >= $minimum_quantity  ){
                if( $price_type == 'flat_rate' ){
                    $new_price = $price_value;
                } else {
                    $new_price = whols_get_percent_of( $value['data']->get_regular_price(), $price_value );
                }

                $value['data']->set_price( $new_price );
            }

        }
    }

    // Alter price html to show wholesale price
    public function alter_price_html( $price, $product ){
        $current_user_id = get_current_user_id();
        // Added !wp_doing_ajax() condition to fix issue for just tables plugin
        if( (is_admin() && !wp_doing_ajax()) || !whols_is_wholesaler( $current_user_id ) || !$price > 0 ){
            return $price;
        }
    
        // Fix
        // $price returns "Price html containing markups", so when user doesn't enter any price
        // so the product is free product, but wholesale price showing for the free product 
        // because $price variable were having truthy value
        if( $product->is_type('simple') && $product->get_regular_price() <= 0 ){ // Fix decimal pricing issue
            return $price;
        }
    
        $product_type     = $product->get_type();
    
        $wholesale_status    = whols_is_on_wholesale( $product );
        $enable_this_pricing = $wholesale_status['enable_this_pricing'];
        $price_type          = $wholesale_status['price_type'];
        $price_value         = $wholesale_status['price_value'];
        $minimum_quantity    = $wholesale_status['minimum_quantity'];
    
        if( whols_is_wholesaler( $current_user_id ) && $enable_this_pricing && $price_value ){
            $retailer_price                = $product->get_regular_price();
    
            $retailer_price_options        = whols_get_option( 'retailer_price_options' );
            $hide_retailer_price           = $retailer_price_options['hide_retailer_price'];
            $retailer_price_custom_label   = $retailer_price_options['retailer_price_custom_label'];
    
            $wholesaler_price_options      = whols_get_option( 'wholesaler_price_options' );
            $hide_wholesaler_price         = $wholesaler_price_options['hide_wholesaler_price'];
            $wholesaler_price_custom_label = $wholesaler_price_options['wholesaler_price_custom_label'];
    
            $discount_label_options        = whols_get_option( 'discount_label_options' );
            $hide_discount_percent         = $discount_label_options['hide_discount_percent'];
            $discount_percent_custom_label = $discount_label_options['discount_percent_custom_label'];
    
            if( $product_type == 'simple' || $product_type == 'variable' ):
                $disable_del_tag = apply_filters('whols_disable_del_tag', false);
                
                ob_start();
            ?>
            <div class="whols_loop_custom_price">
    
                <!-- retailer price -->
                <?php if( !$hide_retailer_price ): ?>
                <div class="whols_retailer_price">
                    <span class="whols_label">
    
                        <?php if( $retailer_price_custom_label ): ?>
                            <span class="whols_label_left"><?php echo esc_html( $retailer_price_custom_label ); ?></span>
                        <?php else: ?>
                            <span class="whols_label_left"><?php echo esc_html__( 'Retailer Price:', 'whols' ); ?></span>
                        <?php endif; ?>
    
                        <?php
                        if( !$disable_del_tag ){
                            echo '<del>';
                        }
                        ?>
                            <?php if( $product_type == 'simple' ): ?>
                                <?php echo wc_price( $retailer_price ); ?>
                            <?php elseif( $product_type == 'variable' ):
                                    $min_variation_price = $product->get_variation_price();
                                    $max_variation_price = $product->get_variation_price( 'max' );
                                ?>
                                <span class="whols_price">
                                    <?php
                                        echo wc_price($min_variation_price);
    
                                        if( $min_variation_price != $max_variation_price ){
                                            echo '–';
                                            echo wc_price($max_variation_price);
                                        }
                                    ?>
                                </span>
                            <?php endif; ?>
                        <?php
                        if( !$disable_del_tag ){
                            echo '</del>';
                        }
                        ?>
                    </span>
                </div>
                <?php endif; ?>
    
                <!-- wholesaler price -->
                <?php if( !$hide_wholesaler_price ): ?>
                <div class="whols_wholesaler_price">
                    <span class="whols_label">
                        <?php if( $wholesaler_price_custom_label ): ?>
                            <span class="whols_label_left"><?php echo esc_html( $wholesaler_price_custom_label ); ?></span>
                        <?php else: ?>
                            <span class="whols_label_left"><?php echo esc_html__( 'Wholesaler Price:', 'whols' ); ?></span>
                        <?php endif; ?>
                        <span class="whols_label_right"><?php echo whols_get_wholesaler_price( $price_type, $price_value, $product ); ?></span>
                    </span>
                </div>
                <?php endif; ?>
    
                <!-- price save info -->
                <?php if( !$hide_discount_percent ): ?>
                <div class="whols_save_amount">
                    <?php echo whols_get_price_save_info( $price_type, $price_value, $product ); ?>
                </div>
                <?php endif; ?>
            </div> <!-- .whols_loop_custom_price -->
    
            <?php if( $minimum_quantity ): 
                    $default = esc_html__('Wholesale price will apply for minimum quantity of {qty} products.', 'whols');
                    $notce_text = whols_get_option('min_qty_notice_custom_text');
                    $notce_text = $notce_text ? $notce_text : $default;
                    $notce_text = str_replace('{qty}', $minimum_quantity, $notce_text);
                ?>
            <div class="whols_minimum_quantity_notice">
                <span><?php echo wp_kses_post($notce_text); ?></span>
            </div>
            <?php
            endif;
            $price = ob_get_clean();
            endif;
        }
    
        return $price;
    }

    public function variable_product_price_tier( $data, $product, $variation ){
        if( !whols_is_wholesaler() ){
            return $data;
        }
    
        $pricing_model  = 'single_role';
        $has_price_tier = false;
    
        if( $pricing_model  == 'single_role' ){
            $price_type_1_properties_meta     = get_post_meta( $variation->get_id(), '_whols_price_type_1_properties', true);
            $price_type_1_properties_meta_arr = explode(':', $price_type_1_properties_meta);

            $price_per_unit   = (float) $price_type_1_properties_meta_arr[0];
            $minimum_quantity = isset($price_type_1_properties_meta_arr[1]) ? $price_type_1_properties_meta_arr[1] : 1;
            $minimum_quantity = $minimum_quantity ? $minimum_quantity : 1;
            
            if( $price_per_unit ){
                $has_price_tier = true;
            }
        }
    
        ob_start();
    
        if( $has_price_tier ){
        ?>
        <table class="shop_table shop_table_responsive whols_shop_table">
            <thead>
                <tr>
                    <th><?php echo esc_html__('Minimum Quantity','whols'); ?></th>
                    <th><?php echo esc_html__('Price Per Unit','whols'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo esc_html($minimum_quantity); ?></td>
                    <td><?php echo wc_price( $price_per_unit ); ?></td>
                </tr>
            </tbody>
        </table>
        <?php
        } else{
            $wholesale_status    = whols_is_on_wholesale( $variation );
            $enable_this_pricing = $wholesale_status['enable_this_pricing'];
            $price_type          = $wholesale_status['price_type'];
            $price_value         = $wholesale_status['price_value'];
            $minimum_quantity    = $wholesale_status['minimum_quantity'];
    
            if( $enable_this_pricing && $price_value ){
                if($price_type == 'flat_rate'){
                    $price_per_unit = $price_value;
                } elseif($price_type == 'percent'){
                    $price_per_unit = whols_get_percent_of( $variation->get_regular_price(), $price_value );
                }
    
                $data['price_html'] = '<span class="price">' .  wc_price( $price_per_unit ) . '</span>';
            }
        }
    
        $new_html = ob_get_clean();
        $previous_availability = $data['availability_html'];
    
        $data['availability_html'] = $new_html . $previous_availability;
        return $data;
    }

    function whols_recalculate_cart_price( $cart_object ) {
        // current user role
        $current_user_id     = get_current_user_id();
    
        $price_type_1_properties = whols_get_option( 'price_type_1_properties' );
        $enable_this_pricing     = $price_type_1_properties['enable_this_pricing'];
        $price_type              = $price_type_1_properties['price_type'];
        $price_value             = $price_type_1_properties['price_value'];
        $minimum_quantity        = $price_type_1_properties['minimum_quantity'];
        
        $term_meta           = '';
    
        if ( (is_admin() && ! defined( 'DOING_AJAX' )) || !whols_is_wholesaler( $current_user_id ) ){
            return;
        }
    
        $pricing_model           = 'single_role';
        foreach ( $cart_object->get_cart() as $hash => $value ) {
            $product_obj = $value['data'];
            $current_product_category_ids = $product_obj->get_category_ids();
    
            foreach( $current_product_category_ids as $id ){
                $term_meta = whols_get_term_meta( $id, 'whols_product_category_meta' );
    
                if( $pricing_model  ==  'single_role' ){
                    $price_type_1_properties = whols_get_option( 'price_type_1_properties' );
                    $enable_this_pricing     = $price_type_1_properties['enable_this_pricing'];
                    $price_type              = $price_type_1_properties['price_type'];
                    $price_value             = $price_type_1_properties['price_value'];
                    $minimum_quantity        = $price_type_1_properties['minimum_quantity'];
    
                    // override from category level
                    if( isset( $term_meta[ 'price_type_1_properties' ] ) && $term_meta[ 'price_type_1_properties' ] ){
                        $price_type_1_properties = $term_meta[ 'price_type_1_properties' ];
                        if( $price_type_1_properties['enable_this_pricing']  ){
                            $enable_this_pricing = $price_type_1_properties['enable_this_pricing'];
                            $price_type          = $price_type_1_properties['price_type'];
                            $price_value         = $price_type_1_properties['price_value'];
                            $minimum_quantity    = $price_type_1_properties['minimum_quantity'];
    
                            break;
                        }
                    }
                }
            }
    
            // override from product level
            if( $pricing_model == 'single_role' ){
                $price_type_1_properties = get_post_meta( $product_obj->get_id(), '_whols_price_type_1_properties', true);
                if( $price_type_1_properties ){
                    $price_type_1_properties_arr = explode( ':', $price_type_1_properties );
                    if( $price_type_1_properties_arr[0] ){
                        $enable_this_pricing = true;
                        $price_type = 'flat_rate';
                        $price_value = $price_type_1_properties_arr[0];
                        $minimum_quantity = $price_type_1_properties_arr[1] ? $price_type_1_properties_arr[1] : 1;
                    }
                }
            }
    
            // finally apply the new product price into the cart
            if( $enable_this_pricing && $price_value &&  $value['quantity'] >= $minimum_quantity  ){
                if( $price_type == 'flat_rate' ){
                    $new_price = $price_value;
                } else {
                    $new_price = whols_get_percent_of( $value['data']->get_regular_price(), $price_value );
                }
    
                $value['data']->set_price( $new_price );
            }
    
        }
    }

    // Disable coupon for wholesale customers
    public function hide_coupon_field_on_cart_page( $enabled ) {
        if( !whols_is_wholesaler() ){
            return $enabled;
        }
    
        // globally disable coupon
        $disable_coupon_for_wholesale_customers = whols_get_option( 'disable_coupon_for_wholesale_customers' );
        if ( $disable_coupon_for_wholesale_customers && whols_is_wholesaler() ) {
            if( is_cart() || is_checkout() ){
                $enabled = false;
            }
        }
    
        // override role level disable coupon
        if( class_exists('\WP_Term_Query') ){
            $term_query = new \WP_Term_Query(array(
                'taxonomy'   => 'whols_role_cat',
                'hide_empty' => false,
            ));
    
            foreach ( $term_query->get_terms() as $term ) {
                if( in_array($term->slug, whols_get_current_user_roles()) ){
                    $meta = get_term_meta( $term->term_id, 'whols_role_tax_meta', true );

                    if( isset($meta['disable_coupon']) && $meta['disable_coupon'] == 'yes' ){
                        $enabled = false;
                    } elseif( isset($meta['disable_coupon']) && $meta['disable_coupon'] == 'no' ){
                        $enabled = true;
                    }

                    break;
                }
            }
        }
    
        return $enabled;
    }

    function remove_coupons_for_wholesalers(){
        // globally disable coupon
        $disable_coupon_for_wholesale_customers = whols_get_option( 'disable_coupon_for_wholesale_customers' );
        if ( $disable_coupon_for_wholesale_customers && whols_is_wholesaler() ) {
            WC()->cart->remove_coupons(); // remove coupon
        }
    }

    public function exclude_products( $meta_query, $query ) {
        $current_user_id = get_current_user_id();
    
        // hide wholesale products from other customers
        $hide_wholesale_only_products_from_other_customers = whols_get_option('hide_wholesale_only_products_from_other_customers');
    
        if( $hide_wholesale_only_products_from_other_customers && !whols_is_wholesaler( $current_user_id ) ){
            $meta_query[] = array(
                'relation' => 'OR',
                array(
                    'key'     => '_whols_mark_this_product_as_wholesale_only',
                    'value'   => 'yes',
                    'compare' => '!='
                ),
                array(
                    'key'     => '_whols_mark_this_product_as_wholesale_only',
                    'compare' => 'NOT EXISTS',
                )
            );
        }
    
        // hide general products from wholesalers
        $hide_general_products_from_wholesalers = whols_get_option('hide_general_products_from_wholesalers');
        if( $hide_general_products_from_wholesalers && whols_is_wholesaler( $current_user_id ) ){
            $meta_query[] = array(
                'key'     => '_whols_mark_this_product_as_wholesale_only',
                'value'   => 'yes',
                'compare' => '='
            );
        }
    
        return $meta_query;
    }

    public function enable_free_shipping_for_wholesalers( $is_available, $package, $shipping_method ){
        $has_free_shipping = 0;
        $allow_free_shipping = '';
    
        if( whols_is_wholesaler(get_current_user_id()) ){
            $has_free_shipping = whols_get_option( 'allow_free_shipping_for_wholesale_customers' );
        }
    
        if( $allow_free_shipping ){
            $has_free_shipping = true;
        }
    
        if( $has_free_shipping ){
            return true;
        } else {
            return $is_available;
        } 
    
        return true;
    }

    public function tax_extempt_for_wholesalers(){
        global $woocommerce;
        $new_tax_exempt_status = (bool) whols_get_option('exclude_tax_for_wholesale_customers');
    
        if( $new_tax_exempt_status && whols_is_wholesaler() ){
            $woocommerce->customer->set_is_vat_exempt(true);
    
        } else {
            $woocommerce->customer->set_is_vat_exempt(false);
        }
    }

    public function filter_get_item_data( $item_data, $cart_item ){
        $product_data = $cart_item['data'];
        $product_id   = $product_data->get_id(); // Any product ID
        $variation_id = '';

        if( $product_data->is_type('variation') ){
            $product_id     = $product_data->get_parent_id(); // The variable product ID
            $variation_id   = $product_data->get_id();
        }
    
        $wholesale_status    = whols_is_on_wholesale( $product_id, $variation_id );
        $enable_this_pricing = $wholesale_status['enable_this_pricing'];
        $price_type          = $wholesale_status['price_type'];
        $price_value         = $wholesale_status['price_value'];
        $minimum_quantity    = $wholesale_status['minimum_quantity'];

        $should_show_wholesale_status       = whols_is_wholesaler(get_current_user_id()) && $enable_this_pricing && $price_value &&  $cart_item['quantity'] >= $minimum_quantity;
        $show_wholesale_status_in_item_data = apply_filters('whols_show_wholesale_status_in_item_data', $should_show_wholesale_status, $item_data, $cart_item );

        if( $show_wholesale_status_in_item_data  ){
            $item_data[] = array(
                'name'      => 'Wholesale',
                'display'   => '<span class="whols_wholesale_status_label">'. esc_html__('Yes', 'whols') .'</span>',
                'value'     => '',
            );
        }
    
        return $item_data;
    }
}