<?php
return array(
    'dynamic_rules' => [
        'id' => 'dynamic_rules',
        'type' => 'group',
        'class' => 'whols_dynamic_rules',
        'title' => __('All Rules', 'whols'),
        'button_title' => __('Add Rule', 'whols'),
        'accordion_title_by' => ['rule_label', 'status'],
        'accordion_title_by_prefix' => ' | ',
        'fields' => [
            'status' => [
                'id' => 'status',
                'type' => 'switch',
                'title' => __('Enable', 'whols'),
                'label' => __('Yes', 'whols'),
                'default' => '',
            ],
            'rule_label' => [
                'id' => 'rule_label',
                'type' => 'text',
                'title' => __('Label', 'whols'),
                'desc' => __('Enter a name to help you identify this rule in your admin panel', 'whols'),
                'default' => '',
            ],
            'private_note' => [
                'id' => 'private_note',
                'type' => 'text',
                'title' => __('Private Note', 'whols'),
                'desc' => __('Add internal notes about this rule (only visible to administrators)', 'whols'),
                'default' => '',
            ],
            'action' => [
                'id' => 'action',
                'type' => 'select',
                'title' => __('Choose Action', 'whols'),
                'options' => [
                    'apply_cart_discount' => __('Cart Discount - Apply discount to entire cart', 'whols'),
                    'apply_extra_charge' => __('Additional Fee - Add extra charge to cart', 'whols'),
                    'apply_bogo_discount' => __('BOGO Offer - Create Buy One Get One offer', 'whols'),
                ],
                'default' => 'apply_cart_discount',
            ],
            'amount_type' => [
                'id' => 'amount_type',
                'type' => 'select',
                'title' => __('Amount Type', 'whols'),
                'desc' => __('Choose how you want to apply the amount', 'whols'),
                'options' => [
                    'percentage' => __('Percentage', 'whols'),
                    'fixed' => __('Fixed', 'whols'),
                ],
                'default' => 'percentage',
            ],
            'discount_or_fee_value' => [
                'id' => 'discount_or_fee_value',
                'type' => 'number',
                'title' => __('Amount', 'whols'),
                'placeholder' => __('50', 'whols'),
                'desc' => __('For Cart Discount: Amount to reduce from cart total <br>For Additional Fee: Amount to add to cart total <br>For BOGO: Discount on Get/Discounted product', 'whols'),
                'attributes' => [
                    'min' => 1,
                ],
                'default' => '50',
            ],
            'bogo_based_on' => [
                'id' => 'bogo_based_on',
                'type' => 'select',
                'title' => __('Buy Products', 'whols'),
                'desc' => __('The products to buy to qualify BOGO discount', 'whols'),
                'options' => [
                    '' => __('Any Products', 'whols'),
                    'specific_products' => __('Specific Products', 'whols'),
                ],
                'group' => 'dynamic_rules',
                'condition' => [
                    [
                        'key' => 'action',
                        'operator' => '==',
                        'value' => 'apply_bogo_discount'
                    ]
                ],
                'default' => '',
            ],
            'bogo_operator' => [
                'id' => 'bogo_operator',
                'type' => 'select',
                'title' => __('Compare Operator', 'whols'),
                'options' => [
                    'matches_any_of' => __('Matches any of selected', 'whols'),
                ],
                'group' => 'dynamic_rules',
                'condition' => [
                    [
                        'key' => 'target_based_on',
                        'operator' => '==',
                        'value' => 'specific_products'
                    ]
                ],
                'default' => '',
            ],
            'bogo_products_to_buy' => [
                'id' => 'bogo_products_to_buy',
                'type' => 'select',
                'title' => __('Select Product(s)', 'whols'),
                'placeholder' => __('Select a Product(s)', 'whols'),
                'chosen' => true,
                'ajax' => true,
                'multiple' => true,
                'options' => 'products',
                'filterable' => true,
                'group' => 'dynamic_rules',
                'condition' => [
                    [
                        'key' => 'action|bogo_based_on',
                        'operator' => '==|==',
                        'value' => 'apply_bogo_discount|specific_products'
                    ]
                ],
                'default' => [],
            ],
            'buy_items_quantity' => [
                'id' => 'buy_items_quantity',
                'type' => 'number',
                'title' => __('Min Quantity to Buy', 'whols'),
                'desc' => __('How many items customer must buy to qualify for the BOGO offer', 'whols'),
                'attributes' => [
                    'type' => 'number',
                    'min' => 1,
                ],
                'group' => 'dynamic_rules',
                'condition' => [
                    [
                        'key' => 'action',
                        'operator' => '==',
                        'value' => 'apply_bogo_discount'
                    ]
                ],
                'default' => 1,
            ],
            'get_items_quantity' => [
                'id' => 'get_items_quantity',
                'type' => 'number',
                'title' => __('Quantity will Get', 'whols'),
                'desc' => __('Number of items customer will receive at the discounted price or free. <br> <i>This value will be customizable in future updates.</i>', 'whols'),
                'attributes' => [
                    'type' => 'number',
                    'min' => 1,
                    'readonly' => true,
                ],
                'group' => 'dynamic_rules',
                'condition' => [
                    [
                        'key' => 'action',
                        'operator' => '==',
                        'value' => 'apply_bogo_discount'
                    ]
                ],
                'default' => 1,
            ],
            'discounted_items' => [
                'id' => 'discounted_items',
                'type' => 'select',
                'title' => __('Discounted Product', 'whols'),
                'desc' => __('Discounted / Free products', 'whols'),
                'placeholder' => __('Search a product', 'whols'),
                'chosen' => true,
                'ajax' => true,
                'multiple' => false,
                'options' => 'products',
                'filterable' => true,
                'query_args' => [
                    'post_type' => 'product'
                ],
                'group' => 'dynamic_rules',
                'condition' => [
                    [
                        'key' => 'action',
                        'operator' => '==',
                        'value' => 'apply_bogo_discount'
                    ]
                ],
                'default' => '',
            ],
            'target_item_based_on' => [
                'id' => 'target_item_based_on',
                'type' => 'select',
                'title' => __('Choose Products', 'whols'),
                'desc' => __('The cart should have these products to qualify this rule', 'whols'),
                'options' => [
                    '' => __('Any Products', 'whols'),
                    'specific_products' => __('Specific Products', 'whols'),
                    'product_category' => __('All Products of given category', 'whols'),
                ],
                'group' => 'dynamic_rules',
                'condition' => [
                    [
                        'key' => 'action',
                        'operator' => 'any',
                        'value' => 'apply_cart_discount,apply_extra_charge'
                    ]
                ],
                'default' => '',
            ],
            'target_item_operator' => [
                'id' => 'target_item_operator',
                'type' => 'select',
                'title' => __('Compare Operator', 'whols'),
                'desc' => __('Product matching criterias', 'whols'),
                'options' => [
                    'matches_any_of' => __('Matches any of selected', 'whols'),
                    'matches_all_of' => __('Matches all of selected', 'whols'),
                    'matches_none_of' => __('Matches none of selected', 'whols'),
                ],
                'group' => 'dynamic_rules',
                'condition' => [
                    [
                        'key' => 'action|target_item_based_on',
                        'operator' => 'any|any',
                        'value' => 'apply_cart_discount,apply_extra_charge|specific_products,product_category'
                    ]
                ],
                'default' => 'matches_any_of',
            ],
            'products' => [
                'id' => 'products',
                'type' => 'select',
                'title' => __('Select Product(s)', 'whols'),
                'placeholder' => __('Select a Product(s)', 'whols'),
                'desc' => __('Select Products', 'whols'),
                'chosen' => true,
                'ajax' => true,
                'multiple' => true,
                'options' => 'products',
                'filterable' => true,
                'query_args' => [
                    'post_type' => 'product'
                ],
                'group' => 'dynamic_rules',
                'condition' => [
                    [
                        'key' => 'action|target_item_based_on',
                        'operator' => 'any|==',
                        'value' => 'apply_cart_discount,apply_extra_charge|specific_products'
                    ]
                ],
                'default' => [],
            ],
            'product_categories' => [
                'id' => 'product_categories',
                'type' => 'select',
                'title' => __('Select category(s)', 'whols'),
                'placeholder' => __('Select a category', 'whols'),
                'desc' => __('All of the products of the given categories will be selected.', 'whols'),
                'chosen' => true,
                'ajax' => true,
                'multiple' => true,
                'options' => 'product_cat',
                'filterable' => true,
                'query_args' => [
                    'taxonomy' => 'product_cat'
                ],
                'group' => 'dynamic_rules',
                'condition' => [
                    [
                        'key' => 'action|target_item_based_on',
                        'operator' => 'any|==',
                        'value' => 'apply_cart_discount,apply_extra_charge|product_category'
                    ]
                ],
                'default' => [],
            ],
            'user_condition_name' => [
                'id' => 'user_condition_name',
                'type' => 'select',
                'title' => __('Choose Condition', 'whols'),
                'options' => [
                    '' => __('Any Customers', 'whols'),
                    'all_registered_users' => __('Registered customers only', 'whols'),
                    'b2b_roles' => __('B2B customers only', 'whols'),
                    'specific_roles' => __('Selected user roles only', 'whols'),
                    'specific_user' => __('Selected customers only', 'whols'),
                ],
                'default' => '',
            ],
            'user_condition_specific_role' => [
                'id' => 'user_condition_specific_role',
                'type' => 'select',
                'title' => __('Select Role(s)', 'whols'),
                'placeholder' => __('Select a Role(s)', 'whols'),
                'chosen' => true,
                'ajax' => false,
                'multiple' => true,
                'options' => 'whols_roles',
                'group' => 'dynamic_rules',
                'condition' => [
                    [
                        'key' => 'user_condition_name',
                        'operator' => '==',
                        'value' => 'specific_roles'
                    ]
                ],
                'default' => [],
            ],
            'user_condition_specific_user' => [
                'id' => 'user_condition_specific_user',
                'type' => 'select',
                'title' => __('Select User(s)', 'whols'),
                'placeholder' => __('Select a User(s)', 'whols'),
                'chosen' => true,
                'ajax' => false,
                'multiple' => true,
                'options' => 'users',
                'filterable' => true,
                'group' => 'dynamic_rules',
                'condition' => [
                    [
                        'key' => 'user_condition_name',
                        'operator' => '==',
                        'value' => 'specific_user'
                    ]
                ],
                'default' => [],
            ],
            'additional_conditons' => [
                'id' => 'additional_conditons',
                'type' => 'group',
                'title' => '',
                'button_title' => 'Add Condition',
                'class' => 'whols_additional_conditons',
                'accordion_title_by' => ['condition_name', 'condition_operator', 'condition_value'],
                'accordion_title_by_prefix' => ' | ',
                'fields' => [
                    'condition_name' => [
                        'id' => 'condition_name',
                        'type' => 'select',
                        'title' => __('Condition Name', 'whols'),
                        'options' => [
                            'cart_subtotal' => __('Cart - subtotal', 'whols'),
                            'cart_total_qunatity' => __('Cart - total quantity', 'whols'),
                            'cart_item_count' => __('Cart - item count', 'whols'),
                            'checkout_shipping_country' => __('Checkout - shipping country', 'whols'),
                        ],
                        'default' => 'cart_subtotal',
                    ],
                    'condition_operator' => [
                        'id' => 'condition_operator',
                        'type' => 'select',
                        'title' => __('Compare Operator', 'whols'),
                        'options' => [
                            'at_least' => __('At least', 'whols'),
                            'less_than' => __('Less than', 'whols'),
                            'equal' => __('Exactly equals', 'whols'),
                        ],
                        'group' => 'additional_conditons',
                        'condition' => [
                            [
                                'key' => 'condition_name',
                                'operator' => 'any',
                                'value' => 'cart_subtotal,cart_total_qunatity,cart_item_count'
                            ]
                        ],
                        'default' => 'at_least',
                    ],
                    'condition_operator_3' => [
                        'id' => 'condition_operator_3',
                        'type' => 'select',
                        'title' => __('Compare Operator', 'whols'),
                        'options' => [
                            'matches_any_of' => __('Matches any of selected', 'whols'),
                            'matches_none_of' => __('Matches none of selected', 'whols'),
                        ],
                        'group' => 'additional_conditons',
                        'condition' => [
                            [
                                'key' => 'condition_name',
                                'operator' => '==',
                                'value' => 'checkout_shipping_country'
                            ]
                        ],
                        'default' => 'matches_any_of',
                    ],
                    'condition_value' => [
                        'id' => 'condition_value',
                        'type' => 'number',
                        'title' => __('Value', 'whols'),
                        'attributes' => [
                            'type' => 'number',
                            'min' => 1,
                        ],
                        'group' => 'additional_conditons',
                        'condition' => [
                            [
                                'key' => 'condition_name',
                                'operator' => 'any',
                                'value' => 'cart_subtotal,cart_total_qunatity,cart_item_count'
                            ]
                        ],
                        'default' => '50',
                    ],
                    'countries' => [
                        'id' => 'countries',
                        'type' => 'select',
                        'title' => __('Country', 'whols'),
                        'placeholder' => __('Select Country(s)', 'whols'),
                        'chosen' => true,
                        'ajax' => true,
                        'multiple' => true,
                        'options' => 'countries',
                        'filterable' => true,
                        'group' => 'additional_conditons',
                        'condition' => [
                            [
                                'key' => 'condition_name',
                                'operator' => '==',
                                'value' => 'checkout_shipping_country'
                            ]
                        ],
                        'default' => [],
                    ],
                ],
                'default' => [],
            ],
        ],
        'default' => [],
    ]  
);