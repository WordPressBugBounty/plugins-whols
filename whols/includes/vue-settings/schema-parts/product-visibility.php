<?php
return ['product-visibility_route' => [
    'title' => __('Product Visibility', 'whols'),
    'sections' => [
        'for_retailers' => [
            'title' => __('For Retailers / General Customers', 'whols')
        ],

        'for_wholesalers' => [
            'title' => __('For Wholesalers', 'whols')
        ]
    ],
    'texts' => [
        'notice' => __('Since you set the "Wholesale Price Visibility" to "Public Mode", so the "Product Visibility" settings are not effective and they are hidden. <br>The options will be available once you set the visibility to "Testing Mode" or "Private Mode".', 'whols')
    ],

    'fields' => [
        // 'product_visibility_retailer_heading' => [
        //     'id' => 'product_visibility_retailer_heading',
        //     'type' => 'subheading',
        // 'product_visibility_retailer_heading' => [
        //     'id' => 'product_visibility_retailer_heading',
        //     'type' => 'subheading',
        //     'content' => __('For Retailers / General Customers', 'whols')
        // ],
        'hide_wholesale_only_products_from_other_customers' => [
            'section' => 'for_retailers',
            'id' => 'hide_wholesale_only_products_from_other_customers',
            'type' => 'switch',
            'title' => __('Hide "Wholesaler Only" Products', 'whols'),
            'label' => __('Yes', 'whols'),
            'help' => __('Control product visibility for retailers:<br>• ON: Hide wholesale-only products from retail customers<br>• OFF: Show all available products<br>• Note: Product visibility can be set in the product edit page under "WHOLS Options"', 'whols'),
            'default' => '1'
        ],
        // 'product_visibility_wholesaler_heading' => [
        //     'id' => 'product_visibility_wholesaler_heading',
        //     'type' => 'subheading',
        //     'content' => __('For Wholesalers', 'whols')
        // ],
        'hide_general_products_from_wholesalers' => [
            'section' => 'for_wholesalers',
            'id' => 'hide_general_products_from_wholesalers',
            'type' => 'switch',
            'title' => __('Show only "Wholesaler Only" products', 'whols'),
            'label' => __('Yes', 'whols'),
            'help' => __('Filter products for wholesalers:<br>• ON: Show only wholesale-specific products<br>• OFF: Show all available products<br>• Note: Product visibility can be set in WooCommerce product settings under "WHOLS Options" <br>• Useful for focused wholesale catalogs', 'whols'),
            'default' => '0'
        ],
        'hide_retailer_only_products_from_wholesalers' => [
            'section' => 'for_wholesalers',
            'id' => 'hide_retailer_only_products_from_wholesalers',
            'type' => 'switch',
            'title' => __('Hide "Retailer Only" Products', 'whols'),
            'label' => __('Yes', 'whols'),
            'help' => __('Control retail product visibility:<br>• ON: Hide retail-only products from wholesalers<br>• OFF: Show all products to wholesalers<br>• Affects products marked as "Retailers Only" in the product edit page under "WHOLS Options"', 'whols'),
            'default' => '1',
            'is_pro' => true
        ]
    ]
]];