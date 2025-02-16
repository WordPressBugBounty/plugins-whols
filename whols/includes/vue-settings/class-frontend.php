<?php
namespace Whols\Vue_Settings;

class Frontend {
    private static $_instance = null;

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
        $this->init();
    }

    /**
     * Initialize components
     */
    public function init() {
        add_action('wp_head', array($this, 'print_dynamic_styles'));
        add_action('admin_head', array($this, 'print_dynamic_styles'));
    }

    /**
     * Generate dynamic CSS from settings
     *
     * @return string Generated CSS
     */
    private function generate_dynamic_css() {
        $options = get_option('whols_options', array());

        $css = '';

        // Retailer Price Label Styling
        $fields = [
            ['retailer_price_label', 'color', '.whols_retailer_price .whols_label_left'],
            ['retailer_price_label', 'font_size', '.whols_retailer_price .whols_label_left'],
            ['retailer_price_label', 'line_height', '.whols_retailer_price .whols_label_left'],
            ['retailer_price_label', 'font_weight', '.whols_loop_custom_price .whols_label .whols_label_left'],
            
            // Retailer Price Styling
            ['retailer_price', 'color', '.whols_retailer_price del'],
            ['retailer_price', 'font_size', '.whols_retailer_price del'],
            ['retailer_price', 'line_height', '.whols_retailer_price del'],
            ['retailer_price', 'font_weight', '.whols_retailer_price del'],
            
            // Wholesaler Price Label Styling
            ['wholesaler_price_label', 'color', '.products .product .price .whols_wholesaler_price .whols_label_left'],
            ['wholesaler_price_label', 'font_size', '.products .product .price .whols_wholesaler_price .whols_label_left'],
            ['wholesaler_price_label', 'line_height', '.products .product .price .whols_wholesaler_price .whols_label_left'],
            ['wholesaler_price_label', 'font_weight', '.products .product .price .whols_wholesaler_price .whols_label_left'],
            
            // Wholesaler Price Styling
            ['wholesaler_price', 'color', '.whols_wholesaler_price .whols_label_right'],
            ['wholesaler_price', 'font_size', '.whols_wholesaler_price .whols_label_right'],
            ['wholesaler_price', 'line_height', '.whols_wholesaler_price .whols_label_right'],
            ['wholesaler_price', 'font_weight', '.whols_wholesaler_price .whols_label_right'],
            
            // Save Amount Label Styling
            ['save_amount_label', 'color', '.whols_save_amount .whols_label_left'],
            ['save_amount_label', 'font_size', '.whols_save_amount .whols_label_left'],
            ['save_amount_label', 'line_height', '.whols_save_amount .whols_label_left'],
            ['save_amount_label', 'font_weight', '.whols_save_amount .whols_label .whols_label_left'],

            // save_amount_price
            ['save_amount_price', 'color', '.whols_save_amount .whols_label_right'],
            ['save_amount_price', 'font_size', '.whols_save_amount .whols_label_right'],
            ['save_amount_price', 'line_height', '.whols_save_amount .whols_label_right'],
            ['save_amount_price', 'font_weight', '.whols_save_amount .whols_label_right'],

            // Minimum Quantity Notice
            ['minimum_quantity_notice', 'color', '.whols_minimum_quantity_notice'],
            ['minimum_quantity_notice', 'font_size', '.whols_minimum_quantity_notice'],
            ['minimum_quantity_notice', 'line_height', '.whols_minimum_quantity_notice'],
            ['minimum_quantity_notice', 'font_weight', '.whols_minimum_quantity_notice'],
            
        ];

        foreach ($fields as $field) {
            list($section, $property, $selector) = $field;

            $option_key = "{$section}_{$property}";

            // Exceptional case
            if($section == 'minimum_quantity_notice'){
                $option_key = "retailer_price_{$property}";
            }
            
            if (!empty($options[$section][$option_key])) {
                $value = $options[$section][$option_key];
                $unit = $property === 'color' ? '' : ($property === 'font_weight' ? '' : 'px');
                $css_property = str_replace('_', '-', $property);
                $css .= "$selector { $css_property: " . esc_attr($value) . $unit . "; }";
            }
        }

        // Handle margin spacing fields
        $margin_fields = [
            'retailer_price_margin' => '.whols_retailer_price',
            'wholesaler_price_margin' => '.whols_wholesaler_price'
        ];

        foreach ($margin_fields as $margin_field => $selector) {
            if (!empty($options[$margin_field])) {
                $margin = $options[$margin_field];
                if (is_array($margin)) {
                    $directions = ['top', 'right', 'bottom', 'left'];
                    $margin_values = [];
                    
                    foreach ($directions as $direction) {
                        $value = !empty($margin[$direction]) ? $margin[$direction] : '0';
                        $unit = !empty($margin['unit']) ? $margin['unit'] : 'px';
                        $margin_values[] = $value . $unit;
                    }
                    
                    if (!empty($margin_values)) {
                        $css .= "$selector { margin: " . implode(' ', $margin_values) . "; }";
                    }
                }
            }
        }

        return $css;
    }

    /**
     * Print dynamic styles in header
     */
    public function print_dynamic_styles() {
        $css = $this->generate_dynamic_css();

        if (!empty($css)) {
            echo "\n<style type='text/css'>\n" . $css . "\n</style>\n";
        }
    }
}