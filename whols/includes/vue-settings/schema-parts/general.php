<?php
return array(
    'pricing_model' => [
        'id' => 'pricing_model',
        'type' => 'select',
        'title' => __('Wholesaler Type Setup', 'whols'),
        'options' => [
            'single_role' => __('Single Role Based', 'whols'),
            'multiple_role' => __('Multiple Role Based', 'whols')
        ],
        'default' => 'single_role',
        'help' => __('<b>Single Role:</b> One fixed wholesale price for all wholesale customers.<br><b>Multiple Role:</b> Different price tiers for different types of wholesalers (e.g., small, medium, large volume buyers).', 'whols'),
        'is_pro' => true
    ],
    'show_wholesale_price_for' => [
        'id' => 'show_wholesale_price_for',
        'type' => 'select',
        'title' => __('Wholesale Price Visibility', 'whols'),
        'options' => [
            'administrator' => __('Testing Mode (Admin & Wholesalers Only)', 'whols'),
            'all_users' => __('Public Mode (Visible to Everyone)', 'whols'),
            'only_wholesalers' => __('Private Mode (Wholesalers Only)', 'whols')
        ],
        'default' => 'administrator',
        'help' => __('If you select "Test Mode", Admin & Wholesalers will be able to access pricing.', 'whols')
    ],
    'select_role_for_all_users_price' => [
        'id' => 'select_role_for_all_users_price',
        'type' => 'select',
        'title' => __('Default Price Tier Selection', 'whols'),
        'help' => __('Choose which wholesale price tier should be displayed to all visitors when "Public Access" is enabled. <br> This price tier will be shown to everyone, regardless of their role.', 'whols'),
        'options' => 'whols_roles', // This will be populated from whols_get_taxonomy_terms()
        'condition' => [
            [
                'key' => 'pricing_model|show_wholesale_price_for',
                'operator' => '==|==',
                'value' => 'multiple_role|all_users'
            ]
        ],
        'default' => ''
    ],
    'purchase_permission' => [
        'id' => 'purchase_permission',
        'type' => 'select',
        'title' => __('Purchase Permission', 'whols'),
        'options' => [
            'yes' => __('Open Purchase - Everyone Can Buy', 'whols'),
            'no' => __('Restricted Purchase - Wholesalers Only', 'whols')
        ],
        'default' => 'yes',
        'condition' => [
            [
                'key' => 'show_wholesale_price_for',
                'operator' => '==',
                'value' => 'all_users'
            ]
        ]
    ],
    'price_type_1_properties' => [
        'id' => 'price_type_1_properties',
        'type' => 'fieldset',
        'title' => __('Global Wholesale Pricing - Single Role Setup', 'whols'),
        'condition' => [
            [
                'key' => 'pricing_model',
                'operator' => '==',
                'value' => 'single_role'
            ]
        ],
        'help' => __('(These options can be overridden for each category & product individually.)', 'whols'),
        'fields' => [
            'enable_this_pricing' => [
                'type' => 'switch',
                'title' => __('Enable', 'whols'),
                'default' => '0'
            ],
            'price_type' => [
                'type' => 'select',
                'title' => __('Price Type', 'whols'),
                'help' => __('Choose how to calculate wholesale prices:<br>• <b>Flat Rate:</b> Set a fixed wholesale price (e.g., $10 per item)<br>• <b>Percentage:</b> Set price as a percentage of retail price', 'whols'),
                'options' => [
                    'flat_rate' => __('Flat Rate', 'whols'),
                    'percent' => __('Percentage', 'whols')
                ],
                'default' => 'flat_rate'
            ],
            'price_value' => [
                'type' => 'number',
                'title' => __('Price Value', 'whols'),
                'help' => __('<b>For Percentage:</b> Enter a value between 1-100 to set the final price as a percentage of retail price.<br>Example: Entering 75 means wholesalers pay 75% of retail price (25% discount).<br><b>For Flat Rate:</b> Enter the fixed wholesale price you want to charge.', 'whols'),
                'default' => ''
            ],
            'minimum_quantity' => [
                'type' => 'number',
                'title' => __('Minimum Quantity', 'whols'),
                'help' => __('Define how many items a customer must buy to get wholesale prices:<br>• This threshold applies per product<br>• Customers will see this requirement displayed on product pages<br>• Leave empty to remove quantity restrictions', 'whols'),
                'default' => ''
            ]
        ]
    ],
    'price_type_2_properties' => [
        'id' => 'price_type_2_properties',
        'type' => 'tabbed',
        'title' => __('Global Wholesale Pricing - Multi Role Setup', 'whols'),
        'after' => esc_html__( '(These options can be overridden for each category & product individually.)', 'whols'  ),
        'tabs' => [], // Will be populated dynamically in the component
        'fields' => [
            '__enable_this_pricing' => [
                'id' => '__enable_this_pricing',
                'type' => 'switch',
                'title' => __('Enable', 'whols'),
                'label' => __('Yes', 'whols'),
                'default' => true,
            ],
            '__price_type' => [
                'id' => '__price_type',
                'type' => 'select',
                'title' => __('Price Type', 'whols'),
                'help' => __('Choose how to calculate wholesale prices:<br>• <b>Flat Rate:</b> Set a fixed wholesale price (e.g., $10 per item)<br>• <b>Percentage:</b> Set price as a percentage of retail price', 'whols'),
                'options' => [
                    'flat_rate' => __('Flat Rate', 'whols'),
                    'percent' => __('Percentage', 'whols'),
                ],
                'default' => 'flat_rate',
            ],
            '__price_value' => [
                'id' => '__price_value',
                'type' => 'number',
                'title' => __('Price Value', 'whols'),
                'help' => __('<b>For Percentage:</b> Enter a value between 1-100 to set the final price as a percentage of retail price.<br>Example: Entering 75 means wholesalers pay 75% of retail price (25% discount).<br><b>For Flat Rate:</b> Enter the fixed wholesale price you want to charge.', 'whols'),
                'attributes' => [
                    'min' => '0',
                ],
                'default' => '',
            ],
            '__minimum_quantity' => [
                'id' => '__minimum_quantity',
                'type' => 'number',
                'title' => __('Minimum Quantity', 'whols'),
                'help' => __('Define how many items a customer must buy to get wholesale prices:<br>• This threshold applies per product<br>• Customers will see this requirement displayed on product pages<br>• Leave empty to remove quantity restrictions', 'whols'),
                'attributes' => [
                    'min' => '1',
                ],
                'default' => '',
            ],
        ],
        'condition' => [
            [
                'key' => 'pricing_model',
                'operator' => '==',
                'value' => 'multiple_role'
            ]
        ],
        'default' => []
    ],
    'retailer_price_options' => [
        'id' => 'retailer_price_options',
        'type' => 'fieldset',
        'title' => __('Retailer Price Label', 'whols'),
        'fields' => [
            'hide_retailer_price' => [
                'type' => 'switch',
                'title' => __('Hide Retailer Price', 'whols'),
                'help' => __('Control visibility of retail prices:<br>• Affects both shop listings and product pages<br>• Useful when you want to show only wholesale prices', 'whols'),
                'default' => '0'
            ],
            'retailer_price_custom_label' => [
                'type' => 'text',
                'title' => __('Retailer Price Custom Label', 'whols'),
                'help' => __('Customize the retail price label:<br>• Displays before the retail price<br>• Shows on both product lists and detail pages', 'whols'),
                'condition' => [
                    [
                        'key' => 'hide_retailer_price',
                        'operator' => '!=',
                        'value' => '1'
                    ]
                ],
                'default' => __('Retailer Price:', 'whols')
            ]
        ]
    ],
    'wholesaler_price_options' => [
        'id' => 'wholesaler_price_options',
        'type' => 'fieldset',
        'title' => __('Wholesaler Price Label', 'whols'),
        'fields' => [
            'hide_wholesaler_price' => [
                'type' => 'switch',
                'title' => __('Hide Wholesaler Price', 'whols'),
                'help' => __('Control visibility of wholesale prices:<br>• Affects both shop listings and product pages', 'whols'),
                'default' => '0'
            ],
            'wholesaler_price_custom_label' => [
                'type' => 'text',
                'title' => __('Wholesaler Price Custom Label', 'whols'),
                'help' => __('Customize the wholesale price label:<br>• Displays before the wholesale price<br>• Shows on both product lists and detail pages', 'whols'),
                'condition' => [
                    [
                        'key' => 'hide_wholesaler_price',
                        'operator' => '!=',
                        'value' => '1'
                    ]
                ],
                'default' => __('Wholesaler Price:', 'whols')
            ]
        ]
    ],
    'discount_label_options' => [
        'id' => 'discount_label_options',
        'type' => 'fieldset',
        'title' => __('Discount Label', 'whols'),
        'fields' => [
            'hide_discount_percent' => [
                'type' => 'switch',
                'title' => __('Hide Discount %', 'whols'),
                'help' => __('Control discount percentage visibility:<br>• Hide/show savings percentage on all pages<br>• Affects both product listings and detail pages', 'whols'),
                'default' => '0'
            ],
            'discount_percent_custom_label' => [
                'type' => 'text',
                'title' => __('Discount Percentage Custom Label', 'whols'),
                'help' => __('Customize the retail price label:<br>• Displays before the retail price<br>• Shows on both product lists and detail pages', 'whols'),
                'default' => '',
                'group' => 'discount_label_options',
                'condition' => [
                    [
                        'key' => 'hide_discount_percent',
                        'operator' => '!=',
                        'value' => '1'
                    ]
                    ],
                'default' => __('Save:', 'whols')
            ]
        ]
    ],
    'min_qty_notice_custom_text' => [
        'id' => 'min_qty_notice_custom_text',
        'type' => 'textarea',
        'title' => __('Min Qty Notice Text', 'whols'),
        'help' => __('Customize minimum quantity notice:<br>• Use {qty} to show required quantity<br>• Example: "Buy {qty} or more for wholesale price"<br>• Leave empty to use default text', 'whols'),
        'default' => __('Wholesale price will apply for minimum quantity of {qty} products.', 'whols')
    ]
);