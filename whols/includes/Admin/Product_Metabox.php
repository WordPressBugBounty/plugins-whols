<?php
/**
 * Product Metabox
 *
 * @since 1.0.0
 */

namespace Whols\Admin;

/**
 * Product Metabox
 */
class Product_Metabox {

    /**
     * Metabox constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        // Add extra metabox tab to woocommerce
        add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_wc_extra_metabox_tab' ) );

        // Add metabox to general tab
        add_action( 'woocommerce_product_data_panels', array( $this, 'add_metabox_to_previous_metabox_panel' ) );

        // Save meta data
        add_action( 'woocommerce_process_product_meta', array( $this, 'save_metabox_of_general_tab') );

        // Create meta fields for simple product
        add_action( 'woocommerce_product_options_pricing', array( $this, 'simple_product_meta_fields' ), 99 );

        // Save simple product meta fields
        add_action( 'woocommerce_process_product_meta', array( $this, 'simple_product_meta_fields_save' ) );

        // Create meta fields for variable product
        add_action( 'woocommerce_variation_options_pricing', array( $this, 'product_data_variation_fields' ), 99, 3 );

        // Save meta fields for variable product
        add_action( 'woocommerce_save_product_variation', array( $this, 'product_data_variation_fields_save' ), 10, 2 );
    }

    /**
     * Add extra metabox tab to woocommerce
     */
    public function add_wc_extra_metabox_tab($tabs){
        $whols_tab = array(
            'label'    => esc_html__( 'Whols', 'whols' ),
            'target'   => 'whols_product_data',
            'class'    => '',
            'priority' => 80,
        );

        $tabs[] = $whols_tab;

        return $tabs;
    }

    /**
     * Add metabox to general tab
     */
    public function add_metabox_to_previous_metabox_panel(){

        echo '<div id="whols_product_data" class="panel woocommerce_options_panel hidden">';
        echo '<h4 class="whols_visibility">'. esc_html__('Product Visibility', 'whols') .'</h4>';
        woocommerce_wp_checkbox(array(
            'id'          => '_whols_mark_this_product_as_wholesale_only',
            'label'       => esc_html__( 'Wholesaler Only', 'whols' ),
            'description' => __( 'Yes, Customers other than <b>Wholesale Role</b> cannot see this product', 'whols' ),
        ));
        echo '</div>';

    }

    /**
     * Save meta data
     */
    public function save_metabox_of_general_tab( $post_id ){
        $whols_mark_this_product_as_wholesale = isset( $_POST['_whols_mark_this_product_as_wholesale_only'] ) ? 'yes' : 'no';

        update_post_meta( $post_id, '_whols_mark_this_product_as_wholesale_only', $whols_mark_this_product_as_wholesale );
    }


    /**
     * Create meta fields for simple product
     */
    public function simple_product_meta_fields() {
        global $post;

        $pricing_model = 'single_role';
        $roles         = whols_get_taxonomy_terms();
        if( $pricing_model == 'single_role' ){
            ?>

            <p class="form-field whols_product_meta_type_1_pricing">
                <label><?php echo esc_html__( 'Wholesale Price', 'whols' ).' ('.get_woocommerce_currency_symbol().')'; ?></label>
                <?php 
                $price_type_1_properties = get_post_meta( $post->ID, '_whols_price_type_1_properties', true);
                $price_type_1_properties_arr = explode(':', $price_type_1_properties);
                $price_type_1_price = isset($price_type_1_properties_arr[0]) ? $price_type_1_properties_arr[0] : '';
                $price_type_1_min_quantity = isset($price_type_1_properties_arr[1]) ? $price_type_1_properties_arr[1] : '';
                ?>
                <span class="wrap whols_product_meta_wrap">

                    <span class="whols_field_wrap">
                        <input name="whols_price_type_1_price" placeholder="<?php echo esc_html__('Price', 'whols') ?>" class="wc_input_price" type="text" step="any" min="0" value="<?php echo wc_format_localized_price( $price_type_1_price ); ?>" />
                    </span>

                    <span class="whols_field_wrap">
                        <input name="whols_price_type_1_min_quantity" placeholder="<?php echo esc_html__('Min. Quantity', 'whols') ?>" class="" type="number" step="any" min="0" value="<?php echo esc_attr( $price_type_1_min_quantity ); ?>" />
                    </span>

                </span>
                <?php
                ?>
            </p>

            <?php
        }
    }

    /**
     * Save simple product meta fields
     */
    public function simple_product_meta_fields_save( $post_id ){
        $meta_field_value = '';
        $pricing_model = 'single_role';

        if( $pricing_model == 'single_role' ){
            if ( isset($_POST['whols_price_type_1_min_quantity']) ){
                $min_quantity = absint( $_POST['whols_price_type_1_min_quantity'] ); 
            } else {
                $min_quantity = '';
            }

            if ( isset($_POST['whols_price_type_1_price']) ){
                $price = $_POST['whols_price_type_1_price'];
            } else {
                $price = '';
            }

            if ( !empty( $price ) ){
                $meta_field_value .= wc_format_decimal($price). ':' .$min_quantity;
            }

            update_post_meta( $post_id, '_whols_price_type_1_properties', $meta_field_value);
        }
    }

    /**
     * Create meta fields for variable product
     */
    public function product_data_variation_fields( $loop, $variation_data, $variation ){
        global $post;

        $pricing_model = 'single_role';
        $roles = whols_get_taxonomy_terms();
        if( $pricing_model == 'single_role' ){
            ?>

            <p class="form-field whols_product_meta_type_1_pricing">
                <?php 
                $price_type_1_properties = get_post_meta( $variation->ID, '_whols_price_type_1_properties', true);
                $price_type_1_properties_arr = explode(':', $price_type_1_properties);
                $price_type_1_price = isset($price_type_1_properties_arr[0]) ? $price_type_1_properties_arr[0] : '';
                $price_type_1_min_quantity = isset($price_type_1_properties_arr[1]) ? $price_type_1_properties_arr[1] : '';
                ?>
                <span class="wrap whols_product_meta_wrap">

                    <span class="form-row form-field whols_field_wrap">
                        <span class="whols_lbl"><?php echo esc_html__( 'Wholesale Price', 'whols' ).' ('.get_woocommerce_currency_symbol().')'; ?></span>
                        <input name="whols_price_type_1_price_<?php echo esc_attr($variation->ID); ?>" placeholder="<?php echo esc_attr__( 'Price', 'whols' ); ?>" class="wc_input_price" type="text" step="any" min="0" value="<?php echo wc_format_localized_price( $price_type_1_price ); ?>" />
                    </span>

                    <span class="form-row form-field whols_field_wrap">
                        <span class="whols_lbl"><?php echo esc_html__('Wholesale Min. Quantity', 'whols'); ?></span>
                        <input name="whols_price_type_1_min_quantity_<?php echo esc_attr($variation->ID); ?>" placeholder="<?php echo esc_attr__( 'Min. Quantity', 'whols' ); ?>" class="" type="number" step="any" min="0" value="<?php echo esc_attr( $price_type_1_min_quantity ); ?>" />
                    </span>

                </span>
                <?php
                ?>
            </p>

            <?php
        }  
    }

    /**
     * Save meta fields for variable product
     */
    public function product_data_variation_fields_save( $post_id ){
        $meta_field_value = '';
        $pricing_model = 'single_role';

        if( $pricing_model == 'single_role' ){
            if ( isset($_POST['whols_price_type_1_min_quantity_'. $post_id]) ){
                $min_quantity = absint( $_POST['whols_price_type_1_min_quantity_'. $post_id] ); 
            } else {
                $min_quantity = '';
            }

            if ( isset($_POST['whols_price_type_1_price_'. $post_id]) ){
                $price = $_POST['whols_price_type_1_price_'. $post_id];
            } else {
                $price = '';
            }

            if ( !empty( $price ) ){
                $meta_field_value .= wc_format_decimal($price). ':' .$min_quantity;
            }

            update_post_meta( $post_id, '_whols_price_type_1_properties', $meta_field_value );
        }
    }
}