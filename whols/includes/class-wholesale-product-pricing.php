<?php
namespace Whols;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Wholesale Product Pricing
 * 
 * Handles all wholesale pricing logic for products including price calculation,
 * discounts, tiers, and eligibility.
 * 
 * Designed to streamline development, this class consolidates all previously scattered
 * wholesale price functions and logic into a centralized system to replace the old
 * functions.
 */
class Wholesale_Product_Pricing {
    /**
     * Static instance registry
     *
     * @var array
     */
    private static $instances = [];

    /** 
     * The WooCommerce product object
     *
     * @var \WC_Product
     */
    private $product;

    /**
     * The parent product (for variations)
     *
     * @var \WC_Product|null
     */
    private $parent_product = null;

    /**
     * Variation ID if the product is a variation
     *
     * @var int|null
     */
    private $variation_id = null;

    /**
     * User ID for which to check pricing
     *
     * @var int
     */
    private $user_id;

    /**
     * Pricing model: 'single_role' or 'multiple_role'
     *
     * @var string
     */
    private $pricing_model;

    /**
     * Cached wholesale status information
     *
     * @var array|null
     */
    private $wholesale_status = null;

    /**
     * Cached price tiers
     *
     * @var array|null
     */
    private $price_tiers = null;

    /**
     * Cached user roles
     *
     * @var array|null
     */
    private $user_roles = null;

    /**
     * Constructor
     *
     * @param int|\WC_Product $product Product ID or WC_Product object
     * @param int|null $variation_id Variation ID (if applicable)
     * @param int|null $user_id User ID (defaults to current user)
     */
    public function __construct($product, $variation_id = null, $user_id = null) {
        // Set user ID
        $this->user_id = $user_id ?? get_current_user_id();
        
        // Set pricing model
        $this->pricing_model = $this->get_pricing_model();

        // Set product and variation
        if ($product instanceof \WC_Product) {
            $this->product = $product;
        } else {
            $this->product = wc_get_product($product);
        }

        // Handle variation
        if ($variation_id) {
            $this->variation_id = $variation_id;
            
            if ($this->product->is_type('variable')) {
                $this->parent_product = $this->product;
                $this->product = wc_get_product($variation_id);
            }
        } elseif ($this->product && $this->product->is_type('variation')) {
            $this->variation_id = $this->product->get_id();
            $this->parent_product = wc_get_product($this->product->get_parent_id());
        }
    }

    /**
     * Get instance for product (singleton factory method)
     *
     * @param int|\WC_Product $product Product ID or WC_Product object
     * @param int|null $variation_id Variation ID (if applicable)
     * @param int|null $user_id User ID (defaults to current user)
     * @return self
     */
    public static function getInstance($product, $variation_id = null, $user_id = null) {
        // Get product ID
        $product_id = $product instanceof \WC_Product ? $product->get_id() : intval($product);
        
        // Create a unique key for this product+variation+user combination
        $instance_key = $product_id;
        if ($variation_id) {
            $instance_key .= '_' . $variation_id;
        }
        if ($user_id) {
            $instance_key .= '_' . $user_id;
        } else {
            $instance_key .= '_' . get_current_user_id();
        }
        
        // Return existing instance if available
        if (isset(self::$instances[$instance_key])) {
            return self::$instances[$instance_key];
        }
        
        // Create new instance and store in registry
        self::$instances[$instance_key] = new self($product, $variation_id, $user_id);
        
        return self::$instances[$instance_key];
    }

    /**
     * Clear the instance registry
     *
     * @return void
     */
    public static function clearInstances() {
        self::$instances = [];
    }

    /**
     * Get the pricing model from settings
     *
     * @return string 'single_role' or 'multiple_role'
     */
    private function get_pricing_model() {
        $pricing_model = whols_get_option('pricing_model');
        return ($pricing_model === 'multiple_role') ? 'multiple_role' : 'single_role';
    }

    /**
     * Check if the product has wholesale pricing
     *
     * @return boolean
     */
    public function has_wholesale_pricing() {
        $status = $this->get_wholesale_status();
        return !empty($status['enable_this_pricing']) && !empty($status['price_value']);
    }

    /**
     * Check if the product requires a minimum quantity for wholesale pricing
     *
     * @return boolean
     */
    public function has_minimum_quantity() {
        $status = $this->get_wholesale_status();
        return !empty($status['minimum_quantity']);
    }

    /**
     * Get the minimum quantity required for wholesale pricing
     *
     * @return int Minimum quantity (0 if not set)
     */
    public function get_minimum_quantity() {
        $status = $this->get_wholesale_status();

        // If tiered pricing is enabled, return the minimum quantity from the tiers
        if ($this->has_tiered_pricing()) {
            return min(array_keys($this->get_price_tiers()));
        }
        
        return !empty($status['minimum_quantity']) ? (int)$status['minimum_quantity'] : 0;
    }

    /**
     * Get the product's wholesale status information
     *
     * @return array Wholesale status information
     */
    public function get_wholesale_status() {
        if ($this->wholesale_status === null) {
            if ($this->product) {
                $p_id = $this->variation_id ? $this->variation_id : $this->product->get_id();
                $this->wholesale_status = $this->calculate_wholesale_status($p_id);
            } else {
                $this->wholesale_status = [
                    'enable_this_pricing' => false,
                    'price_type' => '',
                    'price_value' => '',
                    'minimum_quantity' => '',
                    'tiers' => []
                ];
            }
        }
        
        return $this->wholesale_status;
    }

    /**
     * Calculate the wholesale status for a product
     *
     * @param int $product_id Product ID
     * @return array Wholesale status information
     */
    private function calculate_wholesale_status($product_id) {
        if ($this->pricing_model === 'single_role') {
            return $this->calculate_single_role_status($product_id);
        } else {
            return $this->calculate_multiple_role_status($product_id);
        }
    }

    /**
     * Calculate wholesale status for single role pricing model
     *
     * @param int $product_id Product ID
     * @return array Wholesale status information
     */
    private function calculate_single_role_status($product_id) {
        $result = array(
            'enable_this_pricing' => false,
            'price_type' => '',
            'price_value' => '',
            'minimum_quantity' => '',
            'tiers' => []
        );

        // Start with global settings
        $price_type_1_properties = whols_get_option('price_type_1_properties');

        if( !empty($price_type_1_properties) && $price_type_1_properties['enable_this_pricing'] ){
            $result['enable_this_pricing'] = $price_type_1_properties['enable_this_pricing'];
            $result['price_type'] = $price_type_1_properties['price_type'];
            $result['price_value'] = $price_type_1_properties['price_value'];
            $result['minimum_quantity'] = $price_type_1_properties['minimum_quantity'];
        }

        // Override from category level
        $product_to_check = $this->parent_product ?? $this->product;
        $term_meta = $this->get_category_price_meta($product_to_check);
        
        if ($term_meta) {
            $result['enable_this_pricing'] = $term_meta['enable_this_pricing'] ?? $result['enable_this_pricing'];
            $result['price_type'] = $term_meta['price_type'] ?? $result['price_type'];
            $result['price_value'] = $term_meta['price_value'] ?? $result['price_value'];
            $result['minimum_quantity'] = $term_meta['minimum_quantity'] ?? $result['minimum_quantity'];
        }

        // Override from product level
        $product_meta = $this->get_product_price_meta($this->product);
        
        if ($product_meta) {
            $result['enable_this_pricing'] = true;
            $result['price_type'] = 'flat_rate'; // Product-level overrides always use flat rate
            $result['price_value'] = $product_meta['price_value'];
            $result['minimum_quantity'] = $product_meta['minimum_quantity'];
        }

        // Get price tiers
        $tiers = $this->get_price_tiers();
        $result['tiers'] = $tiers;

        return $result;
    }

    /**
     * Calculate wholesale status for multiple role pricing model
     *
     * @param int $product_id Product ID
     * @return array Wholesale status information
     */
    private function calculate_multiple_role_status($product_id) {
        $user_roles = $this->get_user_roles();
        $active_role = isset($user_roles[0]) ? $user_roles[0] : '';
        
        // Get the appropriate role to use for pricing
        $wholesale_price_visibility = whols_get_option('show_wholesale_price_for');
        $select_role_for_all_users_price = whols_get_option('select_role_for_all_users_price');
        
        // Support for test mode
        if ($wholesale_price_visibility === 'administrator') {
            $select_role_for_all_users_price = 'whols_default_role';
        }

        // Determine which role to use for pricing
        $pricing_role = $active_role;
        if (in_array($wholesale_price_visibility, ['all_users', 'administrator']) && $select_role_for_all_users_price) {
            $pricing_role = $select_role_for_all_users_price;
        }

        // Start with global settings
        $price_type_2_properties = whols_get_option('price_type_2_properties');
        $enable_this_pricing = isset($price_type_2_properties[$pricing_role . '__enable_this_pricing']) ? 
            $price_type_2_properties[$pricing_role . '__enable_this_pricing'] : false;
        $price_type = isset($price_type_2_properties[$pricing_role . '__price_type']) ? 
            $price_type_2_properties[$pricing_role . '__price_type'] : '';
        $price_value = isset($price_type_2_properties[$pricing_role . '__price_value']) ? 
            $price_type_2_properties[$pricing_role . '__price_value'] : '';
        $minimum_quantity = isset($price_type_2_properties[$pricing_role . '__minimum_quantity']) ? 
            $price_type_2_properties[$pricing_role . '__minimum_quantity'] : '';

        // Override from category level
        $product_to_check = $this->parent_product ?? $this->product;
        $term_meta = $this->get_category_price_meta_multiple_role($product_to_check, $pricing_role);
        
        if ($term_meta) {
            $enable_this_pricing = $term_meta['enable_this_pricing'];
            $price_type = $term_meta['price_type'];
            $price_value = $term_meta['price_value'];
            $minimum_quantity = $term_meta['minimum_quantity'];
        }

        // Override from product level
        $product_meta = $this->get_product_price_meta_multiple_role($this->product, $user_roles);
        
        if ($product_meta) {
            $enable_this_pricing = true;
            $price_type = 'flat_rate'; // Product-level overrides always use flat rate
            $price_value = $product_meta['price_value'];
            $minimum_quantity = $product_meta['minimum_quantity'];
        }

        // Get price tiers
        $tiers = $this->get_price_tiers();

        return [
            'enable_this_pricing' => $enable_this_pricing,
            'price_type' => $price_type,
            'price_value' => $price_value,
            'minimum_quantity' => $minimum_quantity,
            'tiers' => $tiers
        ];
    }

    /**
     * Get category price meta for single role pricing
     *
     * @param \WC_Product $product Product object
     * @return array|null Price meta or null if not found
     */
    private function get_category_price_meta($product) {
        if (!$product) {
            return null;
        }

        $product_categories = $product->get_category_ids();
        
        foreach ($product_categories as $category_id) {
            $term_meta = whols_get_term_meta($category_id, 'whols_product_category_meta');
            
            if (isset($term_meta['price_type_1_properties']) && $term_meta['price_type_1_properties']) {
                $price_properties = $term_meta['price_type_1_properties'];
                
                if (!empty($price_properties['enable_this_pricing'])) {
                    return [
                        'enable_this_pricing' => $price_properties['enable_this_pricing'],
                        'price_type' => $price_properties['price_type'],
                        'price_value' => $price_properties['price_value'],
                        'minimum_quantity' => $price_properties['minimum_quantity']
                    ];
                }
            }
        }
        
        return null;
    }

    /**
     * Get category price meta for multiple role pricing
     *
     * @param \WC_Product $product Product object
     * @param string $role Role slug
     * @return array|null Price meta or null if not found
     */
    private function get_category_price_meta_multiple_role($product, $role) {
        if (!$product || !$role) {
            return null;
        }

        $product_categories = $product->get_category_ids();
        
        foreach ($product_categories as $category_id) {
            $term_meta = whols_get_term_meta($category_id, 'whols_product_category_meta');
            
            if (isset($term_meta['price_type_2_properties']) && $term_meta['price_type_2_properties']) {
                $price_properties = $term_meta['price_type_2_properties'];
                
                if (isset($price_properties[$role . '__enable_this_pricing']) && 
                    $price_properties[$role . '__enable_this_pricing']) {
                    
                    return [
                        'enable_this_pricing' => $price_properties[$role . '__enable_this_pricing'],
                        'price_type' => $price_properties[$role . '__price_type'],
                        'price_value' => $price_properties[$role . '__price_value'],
                        'minimum_quantity' => $price_properties[$role . '__minimum_quantity']
                    ];
                }
            }
        }
        
        return null;
    }

    /**
     * Get product-level price meta for single role pricing
     *
     * @param \WC_Product $product Product object
     * @return array|null Price meta or null if not found
     */
    private function get_product_price_meta($product) {
        if (!$product) {
            return null;
        }

        $is_variation = $product->is_type('variation');
        $product_id = $product->get_id();

        $meta_value = get_post_meta($product_id, '_whols_price_type_1_properties', true);
        
        if ($meta_value) {
            $meta_parts = explode(':', $meta_value);
            
            if (isset($meta_parts[0]) && $meta_parts[0] !== '') {
                return [
                    'price_value' => $meta_parts[0],
                    'minimum_quantity' => isset($meta_parts[1]) ? $meta_parts[1] : ''
                ];
            }
        }
        
        return null;
    }

    /**
     * Get product-level price meta for multiple role pricing
     *
     * @param \WC_Product $product Product object
     * @param array $user_roles User roles
     * @return array|null Price meta or null if not found
     */
    private function get_product_price_meta_multiple_role($product, $user_roles) {
        if (!$product || empty($user_roles)) {
            return null;
        }

        $product_id = $product->get_id();
        $meta_value = get_post_meta($product_id, '_whols_price_type_2_properties', true);
        
        if ($meta_value) {
            $roles_data_list = explode(';', $meta_value);
            
            foreach ($roles_data_list as $role_data) {
                $role_parts = explode(':', $role_data);
                $role_match = false;
                
                // Check if "any_role" is present or if user roles match
                if (in_array('any_role', $role_parts)) {
                    $role_match = true;
                } else {
                    // Check if any of the user's roles match
                    foreach ($user_roles as $user_role) {
                        if (in_array($user_role, $role_parts)) {
                            $role_match = true;
                            break;
                        }
                    }
                }
                
                if ($role_match && isset($role_parts[1]) && $role_parts[1] !== '') {
                    return [
                        'price_value' => $role_parts[1],
                        'minimum_quantity' => isset($role_parts[2]) ? $role_parts[2] : ''
                    ];
                }
            }
        }
        
        return null;
    }

    /**
     * Get the current user's roles
     *
     * @return array User roles
     */
    public function get_user_roles() {
        if ($this->user_roles === null) {
            $this->user_roles = whols_get_current_user_roles($this->user_id);
            
            // For public mode, add the default role
            $wholesale_price_visibility = whols_get_option('show_wholesale_price_for');
            $select_role_for_all_users_price = whols_get_option('select_role_for_all_users_price');
            
            if ($wholesale_price_visibility === 'all_users' && 
                $select_role_for_all_users_price && 
                !is_user_logged_in()) {
                $this->user_roles[] = $select_role_for_all_users_price;
            }
        }
        
        return $this->user_roles;
    }

    /**
     * Get price tiers for the product
     *
     * @param bool $prepared_prices Whether to return prepared prices (qty => price pairs)
     * @return array Price tiers
     */
    public function get_price_tiers($prepared_prices = true) {
        if ($this->price_tiers === null) {
            $this->price_tiers = whols_get_product_price_tiers($this->product, $prepared_prices);
        }
        
        return $this->price_tiers;
    }

    /**
     * Check if the product has tiered pricing
     *
     * @return boolean
     */
    public function has_tiered_pricing() {
        $tiers = $this->get_price_tiers();
        return count($tiers) > 1;
    }

    /**
     * Get the price type
     *
     * @return string 'flat_rate' or 'percentage'
     */
    public function get_price_type() {
        $status = $this->get_wholesale_status();
        return $status['price_type'];
    }

    /**
     * Check if pricing is flat rate
     *
     * @return boolean
     */
    public function is_flat_rate() {
        return $this->get_price_type() === 'flat_rate';
    }

    /**
     * Check if pricing is percentage-based
     *
     * @return boolean
     */
    public function is_percentage() {
        return $this->get_price_type() === 'percentage';
    }

    /**
     * Get the wholesale price for a given quantity
     *
     * @param int $quantity Product quantity
     * @param bool $raw Whether to return the raw price value
     * @return float|array|string Price value, price range array, or price HTML
     */
    public function get_wholesale_price($quantity = 1, $raw = true) {
        if (!$this->product || !$this->has_wholesale_pricing()) {
            return $raw ? 0 : '';
        }

        $status = $this->get_wholesale_status();
        $tiers = $this->get_price_tiers();
        
        // If product has tiered pricing, display as a price range
        if (count($tiers) > 1) {
            return $this->get_tiered_price_range($tiers, $raw);
        }
        
        $price = 0;
        
        // If no tiers exist, use standard pricing
        if ($this->is_flat_rate()) {
            $price = $this->get_flat_rate_price($quantity);
        } else {
            $price = $this->get_percentage_price($quantity);
        }

        if ($raw) {
            return $price;
        } else {
            return $this->format_price($price);
        }
    }
    
    /**
     * Get price range for tiered pricing
     *
     * @param array $tiers Price tiers
     * @param bool $raw Whether to return raw price values
     * @return float|array|string Price range as array or formatted HTML
     */
    private function get_tiered_price_range($tiers, $raw = true) {
        if (empty($tiers)) {
            return $raw ? 0 : '';
        }
        
        // Get min and max prices from tiers
        $min_price = min($tiers);
        $max_price = max($tiers);
        
        if ($raw) {
            return array(
                'min' => $min_price,
                'max' => $max_price
            );
        } else {
            // Format as price range
            if ($min_price === $max_price) {
                return wc_price(wc_get_price_to_display($this->product, ['price' => $min_price])) . 
                       $this->product->get_price_suffix($min_price);
            } else {
                return wc_format_price_range(
                    wc_get_price_to_display($this->product, ['price' => $min_price]),
                    wc_get_price_to_display($this->product, ['price' => $max_price])
                ) . $this->product->get_price_suffix($min_price);
            }
        }
    }

    /**
     * Get the flat rate price
     *
     * @param int $quantity Product quantity
     * @return float Price value
     */
    private function get_flat_rate_price($quantity = 1) {
        $status = $this->get_wholesale_status();
        $price_value = $status['price_value'];
        
        // Handle variable products (price range)
        if ($this->product->is_type('variable')) {
            $prices = explode(':', $price_value);
            
            if (count($prices) > 1) {
                // Return min price for simplicity (can be changed to return range)
                return (float)$prices[0];
            }
        }
        
        return (float)$price_value;
    }

    /**
     * Get the percentage-based price
     *
     * @param int $quantity Product quantity
     * @return float Price value
     */
    private function get_percentage_price($quantity = 1) {
        $status = $this->get_wholesale_status();
        $percentage = (float)$status['price_value'];
        $retail_price = (float)$this->product->get_regular_price();
        
        if ($retail_price > 0 && $percentage > 0) {
            return $this->calculate_percentage_of($retail_price, $percentage);
        }
        
        return 0;
    }

    /**
     * Calculate the percentage of a price
     * Example: calculate_percentage_of(100, 10) = 10 (10% of 100)
     *
     * @param float $price Base price
     * @param float $percentage Percentage value
     * @return float Calculated price
     */
    private function calculate_percentage_of($price, $percentage) {
        return $price * ($percentage / 100);
    }

    /**
     * Format a price for display
     *
     * @param float $price Price value
     * @return string Formatted price HTML
     */
    private function format_price($price) {
        if ($this->product->is_type('variable')) {
            $status = $this->get_wholesale_status();
            $price_value = $status['price_value'];
            $prices = explode(':', $price_value);
            
            if (count($prices) > 1) {
                $min_price = (float)$prices[0];
                $max_price = (float)$prices[1];
                
                if ($min_price === $max_price) {
                    return wc_price(wc_get_price_to_display($this->product, ['price' => $min_price]));
                } else {
                    return wc_format_price_range(
                        wc_get_price_to_display($this->product, ['price' => $min_price]),
                        wc_get_price_to_display($this->product, ['price' => $max_price])
                    ) . $this->product->get_price_suffix($min_price);
                }
            }
        }
        
        return wc_price(wc_get_price_to_display($this->product, ['price' => $price])) . 
               $this->product->get_price_suffix($price);
    }

    /**
     * Get the HTML for the wholesale price
     *
     * @param int $quantity Product quantity
     * @return string Price HTML
     */
    public function get_wholesale_price_html($quantity = 1) {
        return $this->get_wholesale_price($quantity, false);
    }

    /**
     * Get discount information
     *
     * @return array Discount information
     */
    public function get_discount_info() {
        if (!$this->product || !$this->has_wholesale_pricing()) {
            return [
                'amount' => 0,
                'percentage' => 0,
                'html' => ''
            ];
        }

        $tiers = $this->get_price_tiers();
        
        // Check if product has tiered pricing
        if (count($tiers) > 1) {
            // Get the maximum discount percentage across all tiers
            $max_discount_percent = $this->get_max_tier_discount();
            
            return [
                'amount' => 0, // Not applicable for tiered pricing
                'percentage' => $max_discount_percent,
                'html' => $this->get_discount_html(),
                'is_tiered' => true,
                'max_discount' => $max_discount_percent
            ];
        }
        
        // Standard discount calculation for non-tiered pricing
        $retail_price = (float)$this->product->get_regular_price();
        $wholesale_price = (float)$this->get_wholesale_price();
        $discount_amount = $retail_price - $wholesale_price;
        $discount_percentage = 0;
        
        if ($retail_price > 0) {
            $discount_percentage = ($discount_amount / $retail_price) * 100;
        }
        
        return [
            'amount' => $discount_amount,
            'percentage' => round($discount_percentage, 2),
            'html' => $this->get_discount_html(),
            'is_tiered' => false
        ];
    }
    
    /**
     * Calculate the maximum discount percentage across all tiers
     *
     * @return float Maximum discount percentage
     */
    private function get_max_tier_discount() {
        $tiers = $this->get_price_tiers();
        if (empty($tiers)) {
            return 0;
        }
        
        $max_discount_percent = 0;
        $retail_price = 0;
        
        // Get the retail price based on product type
        if ($this->product->is_type('variable')) {
            // For variable products, get the minimum price from variations
            $variations = $this->product->get_children();
            if (!empty($variations)) {
                foreach ($variations as $variation_id) {
                    $variation = wc_get_product($variation_id);
                    if ($variation && $variation->get_regular_price()) {
                        $variation_price = (float)$variation->get_regular_price();
                        if ($variation_price > 0) {
                            // Use the lowest retail price for maximum discount calculation
                            if ($retail_price === 0 || $variation_price < $retail_price) {
                                $retail_price = $variation_price;
                            }
                        }
                    }
                }
            }
        } else {
            // For simple products
            $retail_price = (float)$this->product->get_regular_price();
        }
        
        if ($retail_price <= 0) {
            return 0;
        }
        
        // Find the lowest tier price (highest discount)
        $min_tier_price = min($tiers);
        
        // Calculate the maximum discount percentage
        $discount_amount = $retail_price - (float)$min_tier_price;
        $max_discount_percent = round(($discount_amount / $retail_price) * 100);
        
        return $max_discount_percent;
    }

    /**
     * Get discount HTML
     *
     * @return string Discount HTML
     */
    public function get_discount_html() {
        if (!$this->product || !$this->has_wholesale_pricing()) {
            return '';
        }
        
        // Get the discount label
        $discount_label_options = whols_get_option('discount_label_options');
        $custom_label = !empty($discount_label_options['discount_percent_custom_label']) ? 
            $discount_label_options['discount_percent_custom_label'] : 
            __('Save:', 'whols');
        
        // Start building the HTML wrapper
        $html = '<span class="whols_label">';
        $html .= '<span class="whols_label_left">' . esc_html($custom_label) . '</span>';
        $html .= '<span class="whols_label_right">';
        
        // Check for tiered pricing first
        $tiers = $this->get_price_tiers();
        if (count($tiers) > 1) {
            // Get the maximum discount percentage across all tiers
            $max_discount_percent = $this->get_max_tier_discount();
            $html .= __('Up to', 'whols') . ' ' . $max_discount_percent . '%';
        } 
        // Handle different product types without tiers
        elseif ($this->product->is_type('simple')) {
            $status = $this->get_wholesale_status();
            $retail_price = $this->product->get_regular_price();
            
            if ($this->is_flat_rate()) {
                $wholesale_price = $this->get_wholesale_price();
                $save_amount = (float)$retail_price - (float)$wholesale_price;
                $discount_percent = round(($save_amount / (float)$retail_price) * 100);
                $html .= wc_price($save_amount) . ' (' . $discount_percent . '%)';
            } else {
                // For percentage pricing
                $percent = (float)$status['price_value'];
                $discount_percent = 100 - $percent;
                $html .= $discount_percent . '%';
            }
        } elseif ($this->product->is_type('variable')) {
            $status = $this->get_wholesale_status();
            
            if ($this->is_flat_rate()) {
                $price_value = $status['price_value'];
                $prices = explode(':', $price_value);
                
                if (count($prices) > 1) {
                    $min_price = (float)$prices[0];
                    $max_price = (float)$prices[1];
                    
                    // Get min and max retail prices from variations
                    $min_retail = PHP_FLOAT_MAX;
                    $max_retail = 0;
                    $variations = $this->product->get_children();
                    
                    foreach ($variations as $variation_id) {
                        $variation = wc_get_product($variation_id);
                        if ($variation && $variation->get_regular_price()) {
                            $variation_price = (float)$variation->get_regular_price();
                            if ($variation_price > 0) {
                                $min_retail = min($min_retail, $variation_price);
                                $max_retail = max($max_retail, $variation_price);
                            }
                        }
                    }
                    
                    // If no valid price found, reset min_retail
                    if ($min_retail === PHP_FLOAT_MAX) {
                        $min_retail = 0;
                    }
                    
                    $min_discount = ($min_retail > 0) ? round((($min_retail - $min_price) / $min_retail) * 100) : 0;
                    $max_discount = ($max_retail > 0) ? round((($max_retail - $max_price) / $max_retail) * 100) : 0;
                    
                    if ($min_discount == $max_discount) {
                        $html .= $min_discount . '%';
                    } else {
                        $html .= __('Up to', 'whols') . ' ' . max($min_discount, $max_discount) . '%';
                    }
                }
            } else {
                // For percentage pricing on variable products
                $percent = (float)$status['price_value'];
                $discount_percent = 100 - $percent;
                $html .= $discount_percent . '%';
            }
        }
        
        $html .= '</span>';
        $html .= '</span>';
        
        return $html;
    }

    /**
     * Check if the user is a wholesaler
     *
     * @return boolean
     */
    public function is_user_wholesaler() {
        return whols_is_wholesaler($this->user_id);
    }

    /**
     * Check if the product is wholesale priced for the given quantity
     *
     * @param int $quantity Product quantity
     * @return boolean
     */
    public function is_wholesale_priced($quantity = 1) {
        if (!$this->product || !$this->has_wholesale_pricing()) {
            return false;
        }
        
        $min_qty = $this->get_minimum_quantity();
        
        return $this->is_user_wholesaler() && $quantity >= $min_qty;
    }

    /**
     * Check if the product is wholesaler-only
     *
     * @return boolean
     */
    public function is_wholesaler_only() {
        $product_id = $this->product->get_id();
        $parent_id = $this->product->is_type('variation') ? $this->product->get_parent_id() : $product_id;
        
        // Check product meta 
        $meta_value = get_post_meta($parent_id, '_whols_product_visibility', true);
        
        return $meta_value === 'wholesaler_only';
    }

    /**
     * Check if the product is retailer-only
     *
     * @return boolean
     */
    public function is_retailer_only() {
        $product_id = $this->product->get_id();
        $parent_id = $this->product->is_type('variation') ? $this->product->get_parent_id() : $product_id;
        
        // Check product meta
        $meta_value = get_post_meta($parent_id, '_whols_product_visibility', true);
        
        return $meta_value === 'retailer_only';
    }

    /**
     * Get price HTML with discount information
     *
     * @param int $quantity Product quantity
     * @return string Complete price HTML
     */
    public function get_complete_price_html($quantity = 1) {
        if (!$this->product || !$this->has_wholesale_pricing()) {
            return $this->product ? $this->product->get_price_html() : '';
        }
        
        $min_qty = $this->get_minimum_quantity();
        
        if ($quantity < $min_qty) {
            return $this->product->get_price_html();
        }
        
        $html = '';
        
        // Retailer price options
        $retailer_price_options = whols_get_option('retailer_price_options');
        $hide_retailer_price = !empty($retailer_price_options['hide_retailer_price']) ? 
            $retailer_price_options['hide_retailer_price'] : false;
        
        // Show retailer price if not hidden
        if (!$hide_retailer_price) {
            $retailer_label = !empty($retailer_price_options['retailer_price_custom_label']) ? 
                $retailer_price_options['retailer_price_custom_label'] : 
                __('Retailer Price:', 'whols');
            
            $html .= '<div class="whols_retailer_price">';
            $html .= '<span class="whols_label">';
            $html .= '<span class="whols_label_left">' . esc_html($retailer_label) . '</span>';
            $html .= '<span class="whols_label_right">';
            $html .= '<del>' . wc_price($this->product->get_regular_price()) . '</del>';
            $html .= '</span>';
            $html .= '</span>';
            $html .= '</div>';
        }
        
        // Wholesaler price options
        $wholesaler_price_options = whols_get_option('wholesaler_price_options');
        $hide_wholesaler_price = !empty($wholesaler_price_options['hide_wholesaler_price']) ? 
            $wholesaler_price_options['hide_wholesaler_price'] : false;
        
        // Show wholesaler price if not hidden
        if (!$hide_wholesaler_price) {
            $wholesaler_label = !empty($wholesaler_price_options['wholesaler_price_custom_label']) ? 
                $wholesaler_price_options['wholesaler_price_custom_label'] : 
                __('Wholesaler Price:', 'whols');
            
            $html .= '<div class="whols_wholesaler_price">';
            $html .= '<span class="whols_label">';
            $html .= '<span class="whols_label_left">' . esc_html($wholesaler_label) . '</span>';
            $html .= '<span class="whols_label_right">';
            $html .= $this->get_wholesale_price_html($quantity);
            $html .= '</span>';
            $html .= '</span>';
            $html .= '</div>';
        }
        
        // Discount options
        $discount_label_options = whols_get_option('discount_label_options');
        $hide_discount_percent = !empty($discount_label_options['hide_discount_percent']) ? 
            $discount_label_options['hide_discount_percent'] : false;
        
        // Show discount if not hidden
        if (!$hide_discount_percent) {
            $html .= '<div class="whols_save_amount">';
            $html .= $this->get_discount_html();
            $html .= '</div>';
        }
        
        // Minimum quantity notice
        if ($min_qty > 1) {
            $notice_text = whols_get_option('min_qty_notice_custom_text', 
                __('Wholesale price will apply for minimum quantity of {qty} products.', 'whols'));
            $notice_text = str_replace('{qty}', $min_qty, $notice_text);
            
            $html .= '<div class="whols_minimum_quantity_notice">';
            $html .= esc_html($notice_text);
            $html .= '</div>';
        }
        
        return $html;
    }

    /**
     * Get the wholesale price HTML for a variation, including support for price tiers
     * @return string Price HTML
     */
    public function get_price_html_with_labels() {
        if (!$this->product || !$this->has_wholesale_pricing()) {
            return $this->product ? $this->product->get_price_html() : '';
        }
        
        // Get price labels from settings
        $price_labels = whols_get_option('price_labels');
        $retail_label = !empty($price_labels['retail_price_label']) ? 
            $price_labels['retail_price_label'] : 
            __('Retailer Price:', 'whols');
        $wholesale_label = !empty($price_labels['wholesale_price_label']) ? 
            $price_labels['wholesale_price_label'] : 
            __('Wholesaler Price:', 'whols');
        
        // Start building HTML
        $html = '<div class="whols_price_wrap">';
        
        // Retail price section
        $html .= '<div class="whols_retail_price">';
        $html .= '<span class="whols_price_label">' . esc_html($retail_label) . '</span>';
        $html .= '<span class="whols_price_value"><del>' . wc_price($this->product->get_regular_price()) . '</del></span>';
        $html .= '</div>';
        
        // Wholesale price section
        $html .= '<div class="whols_wholesale_price">';
        $html .= '<span class="whols_price_label">' . esc_html($wholesale_label) . '</span>';
        $html .= '<span class="whols_price_value">';
        
        // Check if the product has tiered pricing
        if ($this->has_tiered_pricing()) {
            $tiers = $this->get_price_tiers();
            $price_range = $this->get_tiered_price_range($tiers, false);
            $html .= $price_range;
        } else {
            $wholesale_price = $this->get_wholesale_price(false);
            $html .= $wholesale_price;
        }
        
        $html .= '</span>';
        $html .= '</div>';
        
        // Discount information
        $hide_discount_percent = whols_get_option('hide_discount_percent');
        if (!$hide_discount_percent) {
            $html .= '<div class="whols_save_amount">';
            $html .= $this->get_discount_html();
            $html .= '</div>';
        }
        
        $html .= '</div>'; // Close whols_price_wrap
        
        return $html;
    }
    
    /**
     * Get the applicable price tier for a specific quantity
     *
     * @param int $quantity Product quantity
     * @return array|null Price tier information or null if not found
     */
    public function get_applicable_tier($quantity) {
        $tiers = $this->get_price_tiers();
        
        if (empty($tiers)) {
            return null;
        }
        
        // Sort tiers by quantity in descending order
        krsort($tiers);
        
        foreach ($tiers as $tier_qty => $tier_price) {
            if ($quantity >= $tier_qty) {
                return [
                    'min_qty' => $tier_qty,
                    'price' => $tier_price
                ];
            }
        }
        
        return null;
    }
    
    /**
     * Check if the product is visible to the current user
     *
     * @return boolean
     */
    public function is_visible_to_user() {
        // Admins can see everything
        if (current_user_can('manage_options')) {
            return true;
        }
        
        // Check visibility settings
        $hide_wholesale_only = whols_get_option('hide_wholesale_only_products_from_other_customers', '1');
        $hide_general_products = whols_get_option('hide_general_products_from_wholesalers', '0');
        $hide_retailer_only = whols_get_option('hide_retailer_only_products_from_wholesalers', '1');
        
        $is_wholesaler = $this->is_user_wholesaler();
        $is_wholesaler_only = $this->is_wholesaler_only();
        $is_retailer_only = $this->is_retailer_only();
        
        // For non-wholesalers
        if (!$is_wholesaler) {
            return !($is_wholesaler_only && $hide_wholesale_only === '1');
        }
        
        // For wholesalers
        if ($is_retailer_only && $hide_retailer_only === '1') {
            return false;
        }
        
        if (!$is_wholesaler_only && !$is_retailer_only && $hide_general_products === '1') {
            return false;
        }
        
        return true;
    }
    
    /**
     * Get the source of the pricing (global, category, product, variation)
     *
     * @return string Pricing source
     */
    public function get_price_source() {
        if (!$this->product || !$this->has_wholesale_pricing()) {
            return 'none';
        }
        
        // Check variation level first
        if ($this->variation_id) {
            $meta_key = $this->pricing_model === 'single_role' ? 
                '_whols_price_type_1_properties' : '_whols_price_type_2_properties';
            
            if (get_post_meta($this->variation_id, $meta_key, true)) {
                return 'variation';
            }
        }
        
        // Check product level
        $product_id = $this->product->is_type('variation') ? 
            $this->product->get_parent_id() : $this->product->get_id();
        
        $meta_key = $this->pricing_model === 'single_role' ? 
            '_whols_price_type_1_properties' : '_whols_price_type_2_properties';
        
        if (get_post_meta($product_id, $meta_key, true)) {
            return 'product';
        }
        
        // Check category level
        $product_to_check = $this->parent_product ?? $this->product;
        
        if ($this->pricing_model === 'single_role') {
            if ($this->get_category_price_meta($product_to_check)) {
                return 'category';
            }
        } else {
            $user_roles = $this->get_user_roles();
            $active_role = isset($user_roles[0]) ? $user_roles[0] : '';
            
            if ($this->get_category_price_meta_multiple_role($product_to_check, $active_role)) {
                return 'category';
            }
        }
        
        // Default to global
        return 'global';
    }
}