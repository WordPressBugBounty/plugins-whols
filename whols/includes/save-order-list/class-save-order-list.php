<?php
namespace Whols;
use const Whols\PL_VERSION;

if (!defined('ABSPATH')) {
    exit;
}

class Save_Order_List {
    public $version = '';
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        if( !$this->config('enable_save_order_list') ){
            return;
        }

        // Set time as the version for development mode.
		if( defined('WP_DEBUG') && WP_DEBUG ){
			$this->version = time();
		} else {
			$this->version = PL_VERSION;
		}

        // Add save list button to cart
        add_action('woocommerce_after_cart_table', array($this, 'add_save_list_button'));

        // Add saved lists section to My Account
        add_filter('woocommerce_account_menu_items', array($this, 'add_lists_menu_item'), 40);
        add_action('init', array($this, 'add_endpoint'));
        add_action('woocommerce_account_whols-saved-lists_endpoint', array($this, 'saved_lists_content'));

        // Register assets
        add_action('wp_enqueue_scripts', array($this, 'register_assets'));
        
        add_action('wp_ajax_whols_save_order_list', array($this, 'save_order_list'));
        add_action('wp_ajax_whols_delete_saved_list', array($this, 'delete_saved_list'));
        add_action('wp_ajax_whols_add_list_to_cart', array($this, 'add_list_to_cart'));
    }

    public function includes() {
        require_once plugin_dir_path(__FILE__) . 'class-save-order-list-admin.php';
    }

    /**
     * Get plugin configuration value based on key
     */
    public function config( $key = '' ) {
        $settings = array(
            'enable_save_order_list'  => whols_get_option($key),
            'max_lists_per_user'      => whols_get_option($key),
            'save_list_button_text'   => whols_get_option($key, 'Save as List', true), // Must show a value even the input field does not
            'add_to_cart_button_text' => whols_get_option($key, 'Add List to Cart', true),
        );

        $value = isset($settings[$key]) ? $settings[$key] : null;

        return $value;
    }
    
    public function register_assets() {
        wp_register_style(
            'whols-save-order-list',
            plugins_url('assets/css/frontend-save-order-list.css', __FILE__),
            array(),
            $this->version
        );

        wp_register_script(
            'whols-save-order-list',
            plugins_url('assets/js/frontend-save-order-list.js', __FILE__),
            array('jquery'),
            $this->version,
            true
        );

        if (is_cart() || is_account_page()) {
            wp_enqueue_style('whols-save-order-list');
            wp_enqueue_script('whols-save-order-list');

            wp_localize_script('whols-save-order-list', 'wholsSaveList', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('whols_save_list_nonce'),
                'i18n' => array(
                    'saveSuccess' => esc_html__('List saved successfully!', 'whols'),
                    'deleteConfirm' => esc_html__('Are you sure you want to delete this list?', 'whols'), 
                    'deleteSuccess' => esc_html__('List deleted successfully!', 'whols'),
                    'addToCartSuccess' => esc_html__('List added to cart!', 'whols'),
                    'noLists' => esc_html__('No lists found', 'whols'),
                    'error' => esc_html__('Something went wrong!', 'whols')
                )
            ));
        }
    }
    
    public function add_save_list_button() {
        // Skip if user is not logged in
        if (!is_user_logged_in()) {
            return;
        }
        
        // Get button text from settings or use default
        $button_text = $this->config('save_list_button_text', esc_html__('Save as List', 'whols'), true);
        $max_lists = (int) $this->config('max_lists_per_user', 5);
        
        // Only show button if cart is not empty
        if (!WC()->cart->is_empty()) {
            $saved_lists_count = $this->get_user_saved_lists_count();
            
            echo '<div class="whols-save-order-list-wrap">';
            
            // Show button only if user hasn't reached max lists limit
            if ($max_lists === 0 || $saved_lists_count < $max_lists) {
                echo '<button type="button" class="button alt whols-save-order-list-btn">' . esc_html($button_text) . '</button>';
                echo '<div class="whols-save-list-form" style="display:none;">
                    <input type="text" name="whols_list_name" placeholder="' . esc_attr__('Enter list name', 'whols') . '">
                    <textarea name="whols_list_description" placeholder="' . esc_attr__('Add a note (optional)', 'whols') . '" rows="3"></textarea>
                    <button type="button" class="button alt whols-save-list-confirm">' . esc_html__('Save', 'whols') . '</button>
                    <button type="button" class="button whols-cancel-save-list">' . esc_html__('Cancel', 'whols') . '</button>
                </div>';
            } else {
                echo '<p class="whols-save-list-max-limit-notice">' . esc_html__('You have reached the maximum number of saved lists.', 'whols') . '</p>';
            }
            
            echo '</div>';
        }
    }

    public function add_endpoint() {
        add_rewrite_endpoint('whols-saved-lists', EP_ROOT | EP_PAGES);

        whols_maybe_flush_rewrite_rules();
    }

    public function add_lists_menu_item($items) {
        $new_items = array();
        
        foreach ($items as $key => $value) {
            $new_items[$key] = $value;
            
            if ($key === 'orders') {
                $new_items['whols-saved-lists'] = esc_html__('Saved Lists', 'whols');
            }
        }
        
        return $new_items;
    }

    public function saved_lists_content() {
        $saved_lists = get_user_meta(get_current_user_id(), 'whols_saved_lists', true);
        if (!is_array($saved_lists)) {
            $saved_lists = array();
        }
        
        wc_get_template(
            'my-account/whols-saved-lists.php',
            array(
                'saved_lists' => $saved_lists,
                'add_to_cart_text' => $this->config('add_to_cart_button_text', esc_html__('Add List to Cart', 'whols'), true)
            ),
            '',
            plugin_dir_path(__FILE__) . 'templates/'
        );
    }
    
    public function save_order_list() {
        check_ajax_referer('whols_save_list_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => esc_html__('Please login to save lists.', 'whols')));
            return;
        }

        $list_name = sanitize_text_field($_POST['whols_list_name']);
        if (empty($list_name)) {
            wp_send_json_error(array('message' => esc_html__('Please enter a list name.', 'whols')));
            return;
        }

        $user_id = get_current_user_id();
        $saved_lists = get_user_meta($user_id, 'whols_saved_lists', true);
        if (!is_array($saved_lists)) {
            $saved_lists = array();
        }

        // Check max lists limit
        $max_lists = (int) $this->config('max_lists_per_user', 5);
        if ($max_lists > 0 && count($saved_lists) >= $max_lists) {
            wp_send_json_error(array('message' => esc_html__('Maximum list limit reached.', 'whols')));
            return;
        }

        // Get current cart items
        $cart_items = array();
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $cart_items[] = array(
                'product_id' => $cart_item['product_id'],
                'variation_id' => $cart_item['variation_id'],
                'quantity' => $cart_item['quantity'],
                'variation' => $cart_item['variation']
            );
        }

        $saved_lists[] = array(
            'id' => uniqid('list_'),
            'name' => $list_name,
            'date' => time(),
            'items' => $cart_items,
            'description' => sanitize_text_field($_POST['whols_list_description'])
        );

        update_user_meta($user_id, 'whols_saved_lists', $saved_lists);

        $my_account_url = get_permalink( get_option('woocommerce_myaccount_page_id') );
        $message        = esc_html__('List saved successfully!', 'whols') . ' <a href="' . esc_url($my_account_url . 'whols-saved-lists/') . '">' . esc_html__('View Lists', 'whols') . '</a>';
        
        wp_send_json_success(array(
            'message' => $message
        ));
    }

    public function delete_saved_list() {
        check_ajax_referer('whols_save_list_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error();
            return;
        }

        $list_id = sanitize_text_field($_POST['whols_list_id']);
        $user_id = get_current_user_id();
        $saved_lists = get_user_meta($user_id, 'whols_saved_lists', true);

        if (!is_array($saved_lists)) {
            wp_send_json_error();
            return;
        }

        $saved_lists = array_filter($saved_lists, function($list) use ($list_id) {
            return $list['id'] !== $list_id;
        });

        update_user_meta($user_id, 'whols_saved_lists', array_values($saved_lists));
        
        wp_send_json_success();
    }

    public function add_list_to_cart() {
        check_ajax_referer('whols_save_list_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error();
            return;
        }

        $list_id = sanitize_text_field($_POST['whols_list_id']);
        $user_id = get_current_user_id();
        $saved_lists = get_user_meta($user_id, 'whols_saved_lists', true);

        if (!is_array($saved_lists)) {
            wp_send_json_error();
            return;
        }

        $list = current(array_filter($saved_lists, function($list) use ($list_id) {
            return $list['id'] === $list_id;
        }));

        if (!$list) {
            wp_send_json_error();
            return;
        }

        // Clear current cart
        WC()->cart->empty_cart();

        // Add items to cart
        foreach ($list['items'] as $item) {
            WC()->cart->add_to_cart(
                $item['product_id'],
                $item['quantity'],
                $item['variation_id'],
                $item['variation']
            );
        }
        
        wp_send_json_success(array(
            'redirect' => wc_get_cart_url()
        ));
    }
    
    private function get_user_saved_lists_count() {
        $user_id = get_current_user_id();
        $saved_lists = get_user_meta($user_id, 'whols_saved_lists', true);
        
        if (empty($saved_lists) || !is_array($saved_lists)) {
            return 0;
        }
        
        return count($saved_lists);
    }
}

Save_Order_List::get_instance();