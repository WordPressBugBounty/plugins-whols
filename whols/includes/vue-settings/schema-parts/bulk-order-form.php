<?php
/**
 * Bulk Order Form settings schema
 */

return [
    'bulk-order-form_route' => [
        'title' => __('Bulk Order Form', 'whols-pro'),
        'sections' => [],
        'fields' => [
            'bof_enabled' => [
                'type' => 'switch',
                'title' => __('Enable', 'whols-pro'),
                'help' => __('• Adds a bulk order form to the My Account page.<br>• Use shortcode [whols_bulk_order_form] to add it to other pages.<br>• Provides wholesalers with a quick way to order multiple products at once.', 'whols-pro'),
                'default' => false,
                'is_pro' => true
            ],
            'bof_shortcode' => [
                'type' => 'clipboard',
                'title' => __('Shortcode', 'whols-pro'),
                'help' => __('Copy this shortcode to add the bulk order form to any page or post.', 'whols-pro'),
                'default' => '[whols_bulk_order_form]',
                'class' => 'whols-pro-field-opacity'
            ],
            'bof_menu_title' => [
                'type' => 'text',
                'title' => __('Menu Title', 'whols-pro'),
                'help' => __('Custom title for the Bulk Order Form menu item in My Account page.', 'whols-pro'),
                'default' => 'Bulk Order',
                'class' => 'whols-pro-field-opacity'
            ],
            'bof_search_results_limit' => [
                'type' => 'number',
                'title' => __('Search Results Limit', 'whols-pro'),
                'help' => __('• Maximum number of search results to display. <br>• Use -1 or 0 for no limit. <br>• Lower values improve search performance, especially for sites with many products.', 'whols-pro'),
                'default' => 20,
                'class' => 'whols-pro-field-opacity'
            ],
            'bof_title_text' => [
                'type' => 'text',
                'title' => __('Form Title', 'whols-pro'),
                'help' => __('Custom title for the Bulk Order Form.', 'whols-pro'),
                'default' => 'Bulk / Quick Order Form',
                'class' => 'whols-pro-field-opacity'
            ],
            'bof_redirect_after_add' => [
                'type' => 'radio',
                'title' => __('Redirect After Add to Cart', 'whols-pro'),
                'help' => __('Where to redirect users after adding products to cart.', 'whols-pro'),
                'default' => '',
                'options' => [
                    '' => __('Stay on page', 'whols-pro'),
                    'cart' => __('Go to cart', 'whols-pro'),
                    'checkout' => __('Go to checkout', 'whols-pro'),
                ],
                'class' => 'whols-pro-field-opacity'
            ],
        ],
    ],
];
