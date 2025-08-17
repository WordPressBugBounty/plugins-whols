<?php
/**
 * Product Quick Edit Fields Handler
 */

namespace Whols\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Class Product_Quick_Edit_Fields  
 * 
 * Handles custom fields for products in admin area (quick edit, bulk edit)
 */
class Product_Quick_Edit_Fields {

    /**
     * Initialize the class
     */
    public function __construct() {
        if (!$this->config('enable_wholesale_price_quick_edit')) {
            return;
        }

        $this->init_hooks();
        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
    }

    /**
     * Retrieves bulk order form configuration options
     * 
     * Serves as the centralized access point for all bulk order form settings.
     * Benefits:
     * - Prevents scattered direct calls to whols_get_option()
     * - Isolates option name changes to a single location
     * - Provides consistent default values
     * - Simplifies testing with static values
     *
     * @param string $option_name The option key to retrieve
     * @param mixed $default Value to return if option doesn't exist
     * @return mixed The option value
     */
    public function config($option_name, $default = null) {
        // Static configuration values for testing
        $config = array(
            // Feature toggle
            'enable_wholesale_price_quick_edit' => whols_get_option('enable_wholesale_price_quick_edit', true),
        );
        
        // Return the requested option or default
        return isset($config[$option_name]) ? $config[$option_name] : $default;
    }

    
    /**
     * Enqueue admin styles
     */
    public function enqueue_styles() {
        global $typenow;
        if ($typenow !== 'product') return;
        
        wp_register_style('whols-admin-styles', false);
        wp_enqueue_style('whols-admin-styles');
        
        // Add inline styles
        wp_add_inline_style('whols-admin-styles', '
            .whols-hidden { display: none; }
            .whols-visible { display: block; }
            .whols-section-heading { margin: 10px 0 5px; font-weight: 600; color: #2271b1; }
            .whols-full-width-label { width: 100%; margin-bottom: 10px; }
            .whols-bold-title { font-weight: 600; }
            .whols-pricing-container {}
            .whols-mt-10 { margin-top: 10px; }
            .whols-pricing-row { display: flex; gap: 10px; margin-bottom: 10px; align-items: center; padding: 5px; border: 1px solid #e1e1e1; border-radius: 3px; }
            .whols-role-select { width: 120px; }
            .whols-price-input { width: 80px; }
            .whols-qty-input { width: 70px; }
            .whols-remove-btn { color: #a00; }
            .whols-bulk-pricing-fields { border-left: 3px solid #2271b1; padding-left: 15px; margin-top: 10px; }
            .whols-price-input, .whols-qty-input { min-width: 100px; }
            #wpbody-content #posts-filter .whols-quick-edit-section legend {
                font-weight: bold;
                padding: 0 5px;
            }
            #wpbody-content #posts-filter .whols-quick-edit-section fieldset {
                border: 1px solid #8c8f94;
                padding: 15px;
                padding-top: 0;
            }
            .whols-bulk-edit-section .whols-bulk-pricing-row {
                display: flex;
                gap: 10px;
                margin-bottom: 3px;
            }
            .whols-bulk-edit-section .whols-bulk-pricing-row .title {
                min-width: 125px;
            }
        ');
    }

    /**
     * Initialize hooks
     */
    public function init_hooks() {        
        // Whols Quick Edit panel
        add_action( 'woocommerce_product_quick_edit_end', array( $this, 'add_quick_edit_whols_fields' ) );
        add_action( 'woocommerce_product_quick_edit_save', array( $this, 'save_quick_edit_whols_fields' ) );
        
        // Whols Bulk Edit panel
        add_action( 'woocommerce_product_bulk_edit_end', array( $this, 'add_bulk_edit_whols_fields' ) );
        add_action( 'woocommerce_product_bulk_edit_save', array( $this, 'save_bulk_edit_whols_fields' ), 10, 1 );
        
        // Output hidden data for Quick Edit
        add_action( 'manage_product_posts_custom_column', array( $this, 'add_hidden_whols_for_quick_edit' ), 10, 2 );
        
        // JS for Quick Edit
        add_action( 'admin_footer', array( $this, 'quick_edit_whols_script' ) );
    }

    /**
     * Add Whols fields to Quick Edit panel
     */
    public function add_quick_edit_whols_fields() {
        $pricing_model = whols_get_option( 'pricing_model' );
        $roles = whols_get_taxonomy_terms();
        ?>
        <div class="inline-edit-group whols-quick-edit-section">
            <fieldset>
            <legend><?php echo esc_html__('Whols', 'whols') ?></legend>
            
            <!-- Product Visibility -->
            <div class="whols-product-visibility-field">
                <span class="title"><?php echo esc_html__('Product Visibility', 'whols') ?></span>
                <span class="input-text-wrap">
                    <select name="whols_product_visibility" class="whols_product_visibility">
                        <option value=""><?php echo esc_html__('Everyone', 'whols') ?></option>
                        <option value="wholesaler_only"><?php echo esc_html__('Wholesalers Only', 'whols') ?></option>
                    </select>
                </span>
            </div>

            <?php if ( $pricing_model == 'single_role' ): ?>
            <!-- Single Role Pricing -->
            <div>
                <span class="title"><?php echo esc_html__('Wholesale Price', 'whols') ?> (<?php echo wp_kses_post(get_woocommerce_currency_symbol()); ?>)</span>
                <span class="input-text-wrap">
                    <input type="text" name="whols_price_type_1_price" class="text wc_input_price whols_price_type_1_price" value="" placeholder="<?php echo esc_html__('Price', 'whols') ?>">
                </span>
            </div>
            
            <div>
                <span class="title"><?php echo esc_html__('Wholesale Min. Qty', 'whols') ?></span>
                <span class="input-text-wrap">
                    <input type="number" name="whols_price_type_1_min_quantity" class="text whols_price_type_1_min_quantity" value="" placeholder="<?php echo esc_html__('Min. Quantity', 'whols') ?>" step="1" min="0">
                </span>
            </div>
            <?php endif; ?>
            </fieldset>
        </div>
        <?php
    }

    /**
     * Save Whols fields from Quick Edit panel
     *
     * @param \WC_Product $product Product object
     */
    public function save_quick_edit_whols_fields( $product ) {
        $pricing_model = whols_get_option( 'pricing_model' );
        
        // Save Product Visibility
        if ( isset( $_POST['whols_product_visibility'] ) ) {
            $visibility = sanitize_text_field( $_POST['whols_product_visibility'] );
            $product->update_meta_data( '_whols_product_visibility', $visibility );
            
            // Backward compatibility
            switch ( $visibility ) {
                case 'wholesaler_only':
                    $product->update_meta_data( '_whols_mark_this_product_as_wholesale_only', 'yes' );
                    break;
                default:
                    $product->update_meta_data( '_whols_mark_this_product_as_wholesale_only', '' );
                    break;
            }
        }

        // Save Pricing based on model
        if ( $pricing_model == 'single_role' ) {
            $price = isset( $_POST['whols_price_type_1_price'] ) ? sanitize_text_field( $_POST['whols_price_type_1_price'] ) : '';
            $min_qty = isset( $_POST['whols_price_type_1_min_quantity'] ) ? sanitize_text_field( $_POST['whols_price_type_1_min_quantity'] ) : '';
            
            if ( !empty( $price ) ) {
                $meta_value = wc_format_decimal( $price ) . ':' . $min_qty;
                $product->update_meta_data( '_whols_price_type_1_properties', $meta_value );
            }
            
        }
        
        $product->save();
    }

    /**
     * Add Whols fields to Bulk Edit panel
     */
    public function add_bulk_edit_whols_fields() {
        $pricing_model = whols_get_option( 'pricing_model' );
        $roles = whols_get_taxonomy_terms();
        ?>
        <div class="inline-edit-group whols-bulk-edit-section">
            <fieldset>
            <h4 class="whols-section-heading"><?php esc_html_e( 'Whols', 'whols' ); ?></h4>
            
            <!-- Product Visibility -->
            <div>
                <span class="title"><?php esc_html_e( 'Product Visibility', 'whols' ); ?></span>
                <span class="input-text-wrap">
                    <select name="whols_product_visibility_bulk" class="whols_product_visibility_bulk">
                        <option value="no_change"><?php esc_html_e( '— No change —', 'whols' ); ?></option>
                        <option value=""><?php esc_html_e( 'Everyone', 'whols' ); ?></option>
                        <option value="wholesaler_only"><?php esc_html_e( 'Wholesalers Only', 'whols' ); ?></option>
                    </select>
                </span>
            </div>

            <!-- Pricing Update Control -->
            <div>
                <span class="title"><?php esc_html_e( 'Wholesale Pricing', 'whols' ); ?></span>
                <span class="input-text-wrap">
                    <select name="whols_pricing_action" class="whols_pricing_action" onchange="toggleWholsPricingFields(this)">
                        <option value="no_change"><?php esc_html_e( '— No change —', 'whols' ); ?></option>
                        <option value="update"><?php esc_html_e( 'Update pricing', 'whols' ); ?></option>
                        <option value="clear"><?php esc_html_e( 'Clear all pricing', 'whols' ); ?></option>
                    </select>
                </span>
            </div>

            <!-- Pricing Fields (Hidden by default) -->
            <div class="whols-bulk-pricing-fields hidden">
                <?php if ( $pricing_model == 'single_role' ): ?>
                <!-- Single Role Pricing -->
                <div class="whols-bulk-pricing-row">
                    <span class="title"><?php esc_html_e( 'Wholesale Price', 'whols' ); ?> (<?php echo wp_kses_post(get_woocommerce_currency_symbol()); ?>)</span>
                    <span class="input-text-wrap">
                        <input type="text" name="whols_price_type_1_price_bulk" class="text wc_input_price whols_price_type_1_price_bulk" value="" placeholder="<?php esc_html_e( 'Price', 'whols' ); ?>">
                    </span>
                </div>
                
                <div class="whols-bulk-pricing-row">
                    <span class="title"><?php esc_html_e( 'Wholesale Min. Qty', 'whols' ); ?></span>
                    <span class="input-text-wrap">
                        <input type="number" name="whols_price_type_1_min_quantity_bulk" class="text whols_price_type_1_min_quantity_bulk" value="" placeholder="<?php esc_attr_e( 'Min. Quantity', 'whols' ); ?>" step="1" min="0">
                    </span>
                </div>
                
                <?php endif; ?>
            </div>
            </fieldset>
        </div>

        <script type="text/javascript">
            function toggleWholsPricingFields(select) {
                var fieldsContainer = select.closest('.whols-bulk-edit-section').querySelector('.whols-bulk-pricing-fields');
                if (select.value === 'update') {
                    fieldsContainer.classList.remove('whols-hidden');
                    fieldsContainer.classList.add('whols-visible');
                } else {
                    fieldsContainer.classList.add('whols-hidden');
                    fieldsContainer.classList.remove('whols-visible');
                }
            }
            
            // Function to add a new pricing rule in bulk edit
            function addPricingRuleBulk() {
                var $container = jQuery('.whols-existing-rules-bulk');
                var $template = jQuery('.whols-pricing-row-template-bulk .whols-pricing-row').clone();
                $container.append($template);
            }
            
            // Add pricing rule button for bulk edit
            jQuery(document).on('click', '.whols-add-pricing-rule-bulk', function() {
                addPricingRuleBulk();
            });
            
            // Remove pricing rule button for bulk edit
            jQuery(document).on('click', '.whols-remove-pricing-rule-bulk', function() {
                var $container = jQuery('.whols-existing-rules-bulk');
                jQuery(this).closest('.whols-pricing-row').remove();
                
                // Ensure at least one rule exists
                if ($container.children().length === 0) {
                    addPricingRuleBulk();
                }
            });
        </script>
        <?php
    }

    /**
     * Save Whols fields from Bulk Edit
     *
     * @param \WC_Product $product Product object
     */
    public function save_bulk_edit_whols_fields( $product ) {
        $pricing_model = whols_get_option( 'pricing_model' );
        
        // Save Product Visibility
        if ( isset( $_REQUEST['whols_product_visibility_bulk'] ) && $_REQUEST['whols_product_visibility_bulk'] !== 'no_change' ) {
            $visibility = sanitize_text_field( $_REQUEST['whols_product_visibility_bulk'] );
            $product->update_meta_data( '_whols_product_visibility', $visibility );
            
            // Backward compatibility
            switch ( $visibility ) {
                case 'wholesaler_only':
                    $product->update_meta_data( '_whols_mark_this_product_as_wholesale_only', 'yes' );
                    break;
                case 'retailer_only':
                    $product->update_meta_data( '_whols_mark_this_product_as_wholesale_only', 'no' );
                    break;
                default:
                    $product->update_meta_data( '_whols_mark_this_product_as_wholesale_only', '' );
                    break;
            }
        }

        // Handle Pricing Actions
        if ( isset( $_REQUEST['whols_pricing_action'] ) ) {
            $pricing_action = sanitize_text_field( $_REQUEST['whols_pricing_action'] );
            
            if ( $pricing_action === 'clear' ) {
                // Clear all pricing
                $product->update_meta_data( '_whols_price_type_1_properties', '' );
                
            } elseif ( $pricing_action === 'update' ) {
                // Update pricing based on model
                if ( $pricing_model == 'single_role' ) {
                    $price = isset( $_REQUEST['whols_price_type_1_price_bulk'] ) ? sanitize_text_field( $_REQUEST['whols_price_type_1_price_bulk'] ) : '';
                    $min_qty = isset( $_REQUEST['whols_price_type_1_min_quantity_bulk'] ) ? sanitize_text_field( $_REQUEST['whols_price_type_1_min_quantity_bulk'] ) : '';
                    
                    if ( !empty( $price ) ) {
                        $meta_value = wc_format_decimal( $price ) . ':' . $min_qty;
                        $product->update_meta_data( '_whols_price_type_1_properties', $meta_value );
                    }
                    
                }
            }
        }
        
        $product->save();
    }

    /**
     * Output hidden Whols data for Quick Edit (used in JS)
     *
     * @param string $column  Column name
     * @param int    $post_id Post ID
     */
    public function add_hidden_whols_for_quick_edit( $column, $post_id ) {
        if ( $column === 'name' ) {
            // Product Visibility
            $visibility = get_post_meta( $post_id, '_whols_product_visibility', true );
            echo "<div class='hidden whols-visibility-value whols-hidden' id='whols_visibility_".esc_attr($post_id)."'>".esc_html($visibility)."</div>";
            
            // Pricing Type 1 Properties
            $price_type_1 = get_post_meta( $post_id, '_whols_price_type_1_properties', true );
            echo "<div class='hidden whols-price-type-1-value whols-hidden' id='whols_price_type_1_".esc_attr($post_id)."'>".esc_html($price_type_1)."</div>";
        }
    }

    /**
     * JS to populate Whols Quick Edit fields with existing values
     */
    public function quick_edit_whols_script() {
        global $typenow;
        if ( $typenow !== 'product' ) return;
        
        // Add inline styles for hidden and visible classes
        wp_add_inline_style('whols-admin-styles', '
            .whols-hidden { display: none; }
            .whols-visible { display: block; }
        ');
        
        $pricing_model = whols_get_option( 'pricing_model' );
        $roles = whols_get_taxonomy_terms();
        ?>
        <script>
            jQuery(function($) {
                var $edit = inlineEditPost.edit;
                inlineEditPost.edit = function(id) {
                    $edit.apply(this, arguments);
                    var postId = 0;
                    if (typeof(id) === 'object') {
                        postId = parseInt(this.getId(id));
                    }
                    if (postId > 0) {
                        // Populate Product Visibility
                        var visibilityVal = $('#whols_visibility_' + postId).text();
                        $('.whols_product_visibility').val(visibilityVal);
                        
                        <?php if ( $pricing_model == 'single_role' ): ?>
                        // Populate Single Role Pricing
                        var priceType1Val = $('#whols_price_type_1_' + postId).text();
                        if (priceType1Val) {
                            var priceType1Parts = priceType1Val.split(':');
                            if (priceType1Parts.length >= 2) {
                                $('.whols_price_type_1_price').val(priceType1Parts[0]);
                                $('.whols_price_type_1_min_quantity').val(priceType1Parts[1]);
                            }
                        }
                        <?php endif; ?>
                    }
                };
            });
        </script>
        <?php
    }
}