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

        // Dashboard data endpoint
        register_rest_route(
            'whols/v1',
            '/dashboard-data',
            array(
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => array($this, 'get_dashboard_data'),
                'permission_callback' => array($this, 'check_permission'),
                'args'                => array(
                    'date_from' => array(
                        'required'          => false,
                        'type'              => 'string',
                        'description'       => __('Start date for filtering orders (YYYY-MM-DD format)', 'whols'),
                        'validate_callback' => function($param) {
                            return empty($param) || preg_match('/^\d{4}-\d{2}-\d{2}$/', $param);
                        }
                    ),
                    'date_to' => array(
                        'required'          => false,
                        'type'              => 'string',
                        'description'       => __('End date for filtering orders (YYYY-MM-DD format)', 'whols'),
                        'validate_callback' => function($param) {
                            return empty($param) || preg_match('/^\d{4}-\d{2}-\d{2}$/', $param);
                        }
                    ),
                    'force_refresh' => array(
                        'required'          => false,
                        'type'              => 'integer',
                        'description'       => __('Force refresh data by clearing transient cache', 'whols'),
                        'validate_callback' => function($param) {
                            return is_numeric($param);
                        }
                    ),
                ),
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

    /**
     * Get dashboard metrics and data
     *
     * @param WP_REST_Request $request The REST API request.
     * @return WP_REST_Response The response.
     */
    public function get_dashboard_data($request) {
        // Get date parameters with defaults (last 30 days if not provided)
        $date_to = $request->get_param('date_to');
        if (empty($date_to)) {
            $date_to = date('Y-m-d');
        }
        
        $date_from = $request->get_param('date_from');
        if (empty($date_from)) {
            // Default to 30 days before date_to
            $date_from = date('Y-m-d', strtotime($date_to . ' -30 days'));
        }
        
        // Check if force refresh is requested
        $force_refresh = $request->get_param('force_refresh');
        
        // Create cache key based on date range
        $cache_key = 'whols_dashboard_' . md5($date_from . '_' . $date_to);
        
        // Clear cache if force refresh is requested
        if (!empty($force_refresh)) {
            delete_transient($cache_key);
        }
        
        // Try to get cached data (managed it from client-side)
        // $cached_data = get_transient($cache_key);
        // if (false !== $cached_data && empty($force_refresh)) {
        //     return rest_ensure_response($cached_data);
        // }
        
        // If not cached or force refresh requested, process the data
        $data = $this->process_dashboard_data($date_from, $date_to);
        
        // Cache the data for 1 hour
        // set_transient($cache_key, $data, HOUR_IN_SECONDS);
        
        return rest_ensure_response($data);
    }

    /**
     * Process and aggregate dashboard data
     *
     * @param string $date_from Start date (YYYY-MM-DD).
     * @param string $date_to End date (YYYY-MM-DD).
     * @return array Processed dashboard data.
     */
    private function process_dashboard_data($date_from, $date_to) {
        // Get filtered orders
        $orders = $this->get_filtered_orders($date_from, $date_to);
        
        // Prepare previous period for trend calculation
        $current_period_days = (strtotime($date_to) - strtotime($date_from)) / DAY_IN_SECONDS;
        $prev_date_to = date('Y-m-d', strtotime($date_from . ' -1 day'));
        $prev_date_from = date('Y-m-d', strtotime($prev_date_to . ' -' . $current_period_days . ' days'));
        $prev_orders = $this->get_filtered_orders($prev_date_from, $prev_date_to);
        
        // Process data
        $metrics = $this->calculate_metrics($orders, $prev_orders);
        $top_products = $this->get_top_products($orders);
        $top_categories = $this->get_top_categories($orders);
        
        // Generate time-series data with appropriate granularity
        $granularity = $this->determine_optimal_granularity($date_from, $date_to);
        $time_series_data = $this->generate_time_series_data($orders, $granularity);
        
        // Get action items count (pending orders, registrations, etc.)
        $action_items = $this->count_action_items();
        
        return array(
            'metrics' => $metrics,
            'topProducts' => $top_products,
            'topCategories' => $top_categories,
            'chartData' => array(
                'timeSeriesData' => $time_series_data,
                'totalRevenue' => $metrics['sales']['total'] ?? 0,
                'totalOrders' => $metrics['orders']['total'] ?? 0,
                'averageOrderValue' => ($metrics['orders']['total'] > 0) ? ($metrics['sales']['total'] / $metrics['orders']['total']) : 0,
            ),
            'actionItems' => $action_items,
        );
    }

    /**
     * Get filtered WooCommerce orders by date range
     *
     * @param string $date_from Start date (YYYY-MM-DD).
     * @param string $date_to End date (YYYY-MM-DD).
     * @return array Filtered WooCommerce orders.
     */
    private function get_filtered_orders($date_from, $date_to) {
        // Check if WooCommerce is active
        if (!function_exists('wc_get_orders')) {
            return array();
        }
        
        // Query arguments for WooCommerce orders
        $args = array(
            'limit' => -1,
            'type' => 'shop_order',
            'status' => array('wc-completed', 'wc-processing'),
            'date_created' => strtotime($date_from) . '...' . strtotime($date_to . ' 23:59:59'),

            // Only Wholesale orders 
            'meta_query' => array(
                array(
                    'key' => '_whols_order_type',
                    'compare' => 'EXISTS'
                )
            )
        );
        
        // Execute the query
        $orders = wc_get_orders($args);
        
        return $orders;
    }

    /**
    * Calculate dashboard metrics
    *
    * @param array $orders Current period orders.
    * @param array $prev_orders Previous period orders.
    * @return array Calculated metrics with trends.
    */
    private function calculate_metrics($orders, $prev_orders) {
        // Initialize metrics
        $total_orders = count($orders);
        $total_revenue = 0;
        $total_products_sold = 0;
        $customers = array();
        
        // Process current period orders
        foreach ($orders as $order) {
            $total_revenue += $order->get_total();
            $customer_id = $order->get_customer_id();
            if ($customer_id && !in_array($customer_id, $customers)) {
                $customers[] = $customer_id;
            }
            
            foreach ($order->get_items() as $item) {
                $total_products_sold += $item->get_quantity();
            }
        }
        
        // Calculate average order value and items per order
        $average_order_value = $total_orders > 0 ? $total_revenue / $total_orders : 0;
        $items_per_order = $total_orders > 0 ? $total_products_sold / $total_orders : 0;
        
        // Process previous period for trend calculation
        $prev_total_orders = count($prev_orders);
        $prev_total_revenue = 0;
        $prev_total_products_sold = 0;
        $prev_customers = array();
        
        foreach ($prev_orders as $order) {
            $prev_total_revenue += $order->get_total();
            $customer_id = $order->get_customer_id();
            if ($customer_id && !in_array($customer_id, $prev_customers)) {
                $prev_customers[] = $customer_id;
            }
            
            foreach ($order->get_items() as $item) {
                $prev_total_products_sold += $item->get_quantity();
            }
        }
        
        $prev_average_order_value = $prev_total_orders > 0 ? $prev_total_revenue / $prev_total_orders : 0;
        $prev_items_per_order = $prev_total_orders > 0 ? $prev_total_products_sold / $prev_total_orders : 0;
        
        // Calculate trends (percentage change)
        $orders_trend = $this->calculate_trend($total_orders, $prev_total_orders);
        $sales_trend = $this->calculate_trend($total_revenue, $prev_total_revenue);
        $aov_trend = $this->calculate_trend($average_order_value, $prev_average_order_value);
        $customers_trend = $this->calculate_trend(count($customers), count($prev_customers));
        $products_sold_trend = $this->calculate_trend($total_products_sold, $prev_total_products_sold);
        $items_per_order_trend = $this->calculate_trend($items_per_order, $prev_items_per_order);
        
        // Return metrics with trends
        return array(
            'orders' => array(
                'total' => $total_orders,
                'trend' => $orders_trend
            ),
            'sales' => array(
                'total' => round($total_revenue, 2),
                'trend' => $sales_trend
            ),
            'aov' => array(
                'total' => round($average_order_value, 2),
                'trend' => $aov_trend
            ),
            'customers' => array(
                'total' => count($customers),
                'trend' => $customers_trend
            ),
            'productsSold' => array(
                'total' => $total_products_sold,
                'trend' => $products_sold_trend
            ),
            'itemsPerOrder' => array(
                'total' => round($items_per_order, 1),
                'trend' => $items_per_order_trend
            )
        );
    }

    /**
     * Calculate percentage trend between two values
     *
     * @param float $current Current value.
     * @param float $previous Previous value.
     * @return float Percentage change (positive or negative).
     */
    private function calculate_trend($current, $previous) {
        if ($previous === 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Get top-selling products
     *
     * @param array $orders WooCommerce orders.
     * @param int $limit Maximum number of products to return.
     * @return array Top products with sales data.
     */
    private function get_top_products($orders, $limit = 5) {
        $products = array();
        
        // Process orders to extract product data
        foreach ($orders as $order) {
            foreach ($order->get_items() as $item) {
                $product_id = $item->get_product_id();
                $quantity = $item->get_quantity();
                $total = $item->get_total();
                
                if (!isset($products[$product_id])) {
                    $product = wc_get_product($product_id);
                    if (!$product) continue;
                    
                    $products[$product_id] = array(
                        'id' => $product_id,
                        'name' => $product->get_name(),
                        'itemsSold' => 0,
                        'netSales' => 0
                    );
                }
                
                $products[$product_id]['itemsSold'] += $quantity;
                $products[$product_id]['netSales'] += $total;
            }
        }
        
        // Sort by net sales (descending)
        usort($products, function($a, $b) {
            return $b['netSales'] <=> $a['netSales'];
        });
        
        // Return limited number of products
        return array_slice(array_values($products), 0, $limit);
    }

    /**
     * Get top-selling product categories
     *
     * @param array $orders WooCommerce orders.
     * @param int $limit Maximum number of categories to return.
     * @return array Top categories with sales data.
     */
    private function get_top_categories($orders, $limit = 5) {
        $categories = array();
        
        // Process orders to extract category data
        foreach ($orders as $order) {
            foreach ($order->get_items() as $item) {
                $product_id = $item->get_product_id();
                $product = wc_get_product($product_id);
                if (!$product) continue;
                
                $quantity = $item->get_quantity();
                $total = $item->get_total();
                
                // Get product categories
                $terms = get_the_terms($product_id, 'product_cat');
                if (empty($terms) || is_wp_error($terms)) continue;
                
                foreach ($terms as $term) {
                    $category_id = $term->term_id;
                    
                    if (!isset($categories[$category_id])) {
                        $categories[$category_id] = array(
                            'id' => $category_id,
                            'name' => $term->name,
                            'itemsSold' => 0,
                            'netSales' => 0
                        );
                    }
                    
                    $categories[$category_id]['itemsSold'] += $quantity;
                    $categories[$category_id]['netSales'] += $total;
                }
            }
        }
        
        // Sort by net sales (descending)
        usort($categories, function($a, $b) {
            return $b['netSales'] <=> $a['netSales'];
        });
        
        // Return limited number of categories
        return array_slice(array_values($categories), 0, $limit);
    }

    /**
     * Determine optimal data granularity based on date range
     *
     * @param string $date_from Start date (YYYY-MM-DD).
     * @param string $date_to End date (YYYY-MM-DD).
     * @return string Granularity: 'daily', 'weekly', or 'monthly'.
     */
    private function determine_optimal_granularity($date_from, $date_to) {
        $start = new \DateTime($date_from);
        $end = new \DateTime($date_to);
        $interval = $start->diff($end);
        $days = $interval->days + 1; // Include both start and end days
        
        if ($days <= 31) {
            return 'daily';
        } elseif ($days <= 90) {
            return 'weekly';
        } else {
            return 'monthly';
        }
    }

    /**
     * Generate time-series data for dashboard charts
     *
     * @param array $orders WooCommerce orders.
     * @param string $granularity Data granularity ('daily', 'weekly', 'monthly').
     * @return array Time-series data.
     */
    private function generate_time_series_data($orders, $granularity = 'daily') {
        $time_series = array();
        $order_data = array();
        
        // Group orders by date (or week/month)
        foreach ($orders as $order) {
            $date = $order->get_date_created()->format('Y-m-d');
            $total = $order->get_total();
            
            // Group by appropriate granularity
            switch ($granularity) {
                case 'weekly':
                    $date_obj = new \DateTime($date);
                    $week = $date_obj->format('W');
                    $year = $date_obj->format('Y');
                    $period_key = $year . '-W' . $week; // YYYY-WW
                    break;
                    
                case 'monthly':
                    $period_key = substr($date, 0, 7); // YYYY-MM
                    break;
                    
                case 'daily':
                default:
                    $period_key = $date;
                    break;
            }
            
            if (!isset($order_data[$period_key])) {
                $order_data[$period_key] = array(
                    'date' => $period_key,
                    'orders' => 0,
                    'revenue' => 0,
                    'items' => 0
                );
            }
            
            $order_data[$period_key]['orders']++;
            $order_data[$period_key]['revenue'] += $total;
            
            // Count items
            foreach ($order->get_items() as $item) {
                $order_data[$period_key]['items'] += $item->get_quantity();
            }
        }
        
        // Fill in any missing periods and calculate averages
        $order_data = $this->fill_missing_periods($order_data, $granularity);
        
        // Format the data for charts
        foreach ($order_data as $period_key => $data) {
            // Calculate average order value
            $aov = $data['orders'] > 0 ? $data['revenue'] / $data['orders'] : 0;
            
            $time_series[] = array(
                'date' => $data['date'],
                'orders' => $data['orders'],
                'revenue' => round($data['revenue'], 2),
                'aov' => round($aov, 2)
            );
        }
        
        // Sort by date
        usort($time_series, function($a, $b) {
            return strcmp($a['date'], $b['date']);
        });
        
        return $time_series;
    }

    /**
     * Fill in missing periods in time-series data
     *
     * @param array $order_data Existing order data grouped by period.
     * @param string $granularity Data granularity ('daily', 'weekly', 'monthly').
     * @return array Completed order data with all periods.
     */
    private function fill_missing_periods($order_data, $granularity) {
        if (empty($order_data)) {
            return array();
        }
        
        // Get min and max dates
        $period_keys = array_keys($order_data);
        sort($period_keys);
        $min_period = reset($period_keys);
        $max_period = end($period_keys);
        
        $complete_data = array();
        
        // Fill in periods based on granularity
        switch ($granularity) {
            case 'weekly':
                list($min_year, $min_week) = explode('-W', $min_period);
                list($max_year, $max_week) = explode('-W', $max_period);
                
                $current = new \DateTime();
                $current->setISODate($min_year, $min_week);
                $end = new \DateTime();
                $end->setISODate($max_year, $max_week);
                
                while ($current <= $end) {
                    $year = $current->format('Y');
                    $week = $current->format('W');
                    $period_key = $year . '-W' . $week;
                    
                    if (isset($order_data[$period_key])) {
                        $complete_data[$period_key] = $order_data[$period_key];
                    } else {
                        $complete_data[$period_key] = array(
                            'date' => $period_key,
                            'orders' => 0,
                            'revenue' => 0,
                            'items' => 0
                        );
                    }
                    
                    $current->modify('+1 week');
                }
                break;
                
            case 'monthly':
                $start = new \DateTime($min_period . '-01');
                $end = new \DateTime($max_period . '-01');
                
                while ($start <= $end) {
                    $period_key = $start->format('Y-m');
                    
                    if (isset($order_data[$period_key])) {
                        $complete_data[$period_key] = $order_data[$period_key];
                    } else {
                        $complete_data[$period_key] = array(
                            'date' => $period_key,
                            'orders' => 0,
                            'revenue' => 0,
                            'items' => 0
                        );
                    }
                    
                    $start->modify('+1 month');
                }
                break;
                
            case 'daily':
            default:
                $start = new \DateTime($min_period);
                $end = new \DateTime($max_period);
                
                while ($start <= $end) {
                    $period_key = $start->format('Y-m-d');
                    
                    if (isset($order_data[$period_key])) {
                        $complete_data[$period_key] = $order_data[$period_key];
                    } else {
                        $complete_data[$period_key] = array(
                            'date' => $period_key,
                            'orders' => 0,
                            'revenue' => 0,
                            'items' => 0
                        );
                    }
                    
                    $start->modify('+1 day');
                }
                break;
        }
        
        return $complete_data;
    }

    /**
     * Count action items for dashboard
     *
     * @return array Action items count.
     */
    private function count_action_items() {
        $action_items = array(
            'pendingRegistrations' => 0,
            'conversationsNeedingReply' => 0,
            'pendingOrders' => 0
        );
        
        // Count pending wholesale registrations
        $args = array(
            'post_type' => 'whols_user_request',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'whols_user_request_meta',
                    'value' => 'status";s:0:""',
                    'compare' => 'LIKE'
                )
            )
        );
        $requests = get_posts($args);

        $action_items['pendingRegistrations'] = count($requests);
        
        // Count pending orders (if WooCommerce is active)
        if (function_exists('wc_get_orders')) {
            $status = array('wc-pending', 'wc-on-hold', 'wc-failed');
            $status = apply_filters('whols_dashboard_pending_orders_status', $status);

            $args = array(
                'limit' => -1,
                'status' => $status,
                'return' => 'ids',

                // Get orders with _whols_order_type meta key exists
                'meta_query' => array(
                    array(
                        'key' => '_whols_order_type',
                        'compare' => 'EXISTS'
                    )
                )
            );
            $pending_orders = wc_get_orders($args);
            $action_items['pendingOrders'] = count($pending_orders);
        }
        
        // Count conversations needing reply
        $args = array(
            'post_type' => 'whols_conversation',
            'posts_per_page' => -1,
            'post_status' => 'pending'
        );
        $conversations = get_posts($args);
        $action_items['conversationsNeedingReply'] = count($conversations);
        
        return $action_items;
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
