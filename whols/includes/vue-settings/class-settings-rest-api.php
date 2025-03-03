<?php
namespace Whols\Vue_Settings;

use Whols\Vue_Settings\Settings_Schema;

class Settings_REST_API {
    private static $_instance = null;
    private $option_name = 'whols_options';

    /**
     * Get Instance
     */
    public static function instance(){
        if( is_null( self::$_instance ) ){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     */
    public function __construct() {
        add_action('rest_api_init', array($this, 'register_rest_routes'));
    }

    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        register_rest_route(
            'whols/v1',
            '/settings',
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array($this, 'get_settings'),
                    'permission_callback' => array($this, 'check_permission'),
                ),
                array(
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => array($this, 'update_settings'),
                    'permission_callback' => array($this, 'check_permission'),
                ),
            )
        );

        // To expose the roles
        register_rest_route(
            'whols/v1',
            '/wholesaler-roles',
            array(
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => array($this, 'get_roles'),
                'permission_callback' => array($this, 'check_permission'),
            )
        );

        // To expose the pages
        register_rest_route(
            'whols/v1',
            '/pages',
            array(
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => array($this, 'get_pages'),
                'permission_callback' => array($this, 'check_permission'),
            )
        );

        // To expose the products
        register_rest_route(
            'whols/v1',
            '/products',
            array(
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => array($this, 'get_products'),
                'permission_callback' => array($this, 'check_permission'),
            )
        );

        // To expose the product categories
        register_rest_route(
            'whols/v1',
            '/product-categories',
            array(
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => array($this, 'get_product_categories'),
                'permission_callback' => array($this, 'check_permission'),
            )
        );

        // To expose countries
        register_rest_route(
            'whols/v1',
            '/countries',
            array(
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => array($this, 'get_countries'),
                'permission_callback' => array($this, 'check_permission'),
            )
        );

        // To expose users
        register_rest_route(
            'whols/v1',
            '/users',
            array(
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => array($this, 'get_users'),
                'permission_callback' => array($this, 'check_permission'),
            )
        );

        // To expose payment gateways
        register_rest_route(
            'whols/v1',
            '/payment-gateways',
            array(
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => array($this, 'get_payment_gateways'),
                'permission_callback' => array($this, 'check_permission'),
            )
        );
    }

    /**
     * Check if user has permission
     */
    public function check_permission($request) {
        // First check if user has capability
        if (!current_user_can('manage_options')) {
            return new \WP_Error(
                'rest_forbidden',
                esc_html__('You do not have permissions to manage settings.', 'whols'),
                array('status' => 401)
            );
        }

        // For POST requests, verify nonce
        if ($request->get_method() === 'POST') {
            $nonce = $request->get_header('X-WP-Nonce');

            if (!$nonce || !wp_verify_nonce($nonce, 'wp_rest')) {
                return new \WP_Error(
                    'rest_forbidden',
                    esc_html__('Nonce verification failed.', 'whols'),
                    array('status' => 401)
                );
            }
        }

        return true;
    }

    /**
     * Get settings
     */
    public function get_settings() {
        $cs_options = get_option($this->option_name, []);
        $settings = wp_parse_args($cs_options, Settings_Defaults::get_defaults());

        // Add dynamic roles
        $settings['wholesaler_roles'] = whols_roles_dropdown_options();

        return rest_ensure_response($settings);
    }

    /**
     * Update settings
     */
    public function update_settings($request) {
        $params = $request->get_params();
        $current_settings = get_option($this->option_name, []);

        // Get allowed fields from schema
        $allowed_fields = array_keys(Settings_Defaults::get_defaults());

        // Sanitize and update each allowed field
        foreach ($allowed_fields as $field) {
            if (isset($params[$field])) {
                if (is_array($params[$field])) {
                    $current_settings[$field] = $this->sanitize_array($params[$field]);
                } else {
                    $current_settings[$field] = wp_kses_post($params[$field]); // In the previous version users could use html tags
                }
            }
        }

        update_option($this->option_name, $current_settings);

        return $this->get_settings();
    }

    public function get_roles() {
        return rest_ensure_response(whols_roles_dropdown_options());
    }

    public function get_pages() {
        return rest_ensure_response(whols_pages_dropdown_options('page'));
    }

    public function get_products(){
        return rest_ensure_response(whols_pages_dropdown_options('product'));
    }

    public function get_product_categories(){
        return rest_ensure_response(whols_terms_dropdown_options('product_cat'));
    }

    public function get_countries(){
        return rest_ensure_response(whols_get_countries());
    }

    public function get_users(){
        return rest_ensure_response(whols_users_dropdown_options());
    }

    public function get_payment_gateways() {
        return rest_ensure_response(whols_get_enabled_payment_gateways());
    }

    private function sanitize_array($array) {
        $sanitized_array = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $sanitized_array[$key] = $this->sanitize_array($value);
            } else {
                $sanitized_array[$key] = wp_kses_post($value);
            }
        }
        return $sanitized_array;
    }
}
