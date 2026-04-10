<?php
/**
 * Polylang Integration for Whols
 *
 * Handles synchronization of Whols metadata when products are translated
 * using Polylang for WooCommerce
 */

namespace Whols\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Class Polylang_Integration
 *
 * Ensures Whols plugin metadata is properly synchronized when products
 * are duplicated/translated using Polylang for WooCommerce
 */
class Polylang_Integration {

    /**
     * List of Whols meta keys that should be synchronized for the free version
     */
    private $whols_meta_keys = [
        '_whols_price_type_1_properties',
        '_whols_price_type_2_properties',
        '_whols_product_visibility',
        '_whols_mark_this_product_as_wholesale_only',
    ];

    /**
     * Initialize the integration
     */
    public function __construct() {
        // Check if Polylang and Polylang for WooCommerce are active
        if ( ! $this->is_polylang_active() ) {
            return;
        }

        $this->init_hooks();
    }

    /**
     * Check if Polylang plugins are active
     */
    private function is_polylang_active() {
        return function_exists( 'pll_get_post' ) && class_exists( 'Polylang_Woocommerce' );
    }

    /**
     * Initialize hooks for Polylang integration
     */
    private function init_hooks() {

        // Register meta keys for Polylang synchronization for products
        add_filter( 'pllwc_copy_post_metas', array( $this, 'add_whols_meta_to_copy_list' ), 10, 1 );

        // Register meta keys for variations (if variations exist)
        add_filter( 'pllwc_copy_variation_metas', array( $this, 'add_whols_variation_meta_to_copy_list' ), 10, 1 );
    }

    /**
     * Add Whols meta keys to the list of metas to copy
     *
     * @param array|mixed $metas Array of meta keys or other value
     * @return array|mixed Modified array of meta keys or original value
     */
    public function add_whols_meta_to_copy_list( $metas ) {
        // Ensure $metas is an array before merging
        if ( ! is_array( $metas ) ) {
            return $metas;
        }
        $whols_meta_keys = [];
        foreach ( $this->whols_meta_keys as $meta_key ) {
            $whols_meta_keys[$meta_key] = $meta_key;
        }
        return array_merge( $metas, $whols_meta_keys );
    }

    /**
     * Add Whols variation meta keys to the list of metas to copy
     *
     * @param array|mixed $metas Array of meta keys or other value
     * @return array|mixed Modified array of meta keys or original value
     */
    public function add_whols_variation_meta_to_copy_list( $metas ) {
        // Ensure $metas is an array before merging
        if ( ! is_array( $metas ) ) {
            return $metas;
        }

        // Variation-specific meta keys for free version
        $variation_meta_keys = [
            '_whols_price_type_1_properties',
            '_whols_price_type_2_properties',
        ];

        $whols_variation_meta = [];
        foreach ( $variation_meta_keys as $meta_key ) {
            $whols_variation_meta[$meta_key] = $meta_key;
        }

        return array_merge( $metas, $whols_variation_meta );
    }
}