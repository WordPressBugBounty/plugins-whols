<?php
return [
    'auto_apply_minimum_quantity' => [
        'id' => 'auto_apply_minimum_quantity',
        'type' => 'switch',
        'title' => __('Auto Input Minimum Quantity', 'whols'),
        'label' => __('Yes', 'whols'),
        'help' => __('Manage minimum quantity behavior:<br>• ON: Automatically set quantity to minimum required<br>• OFF: Let customers manually enter quantity<br>• Applies to product pages only', 'whols'),
        'default' => '0'
    ],
    'force_auto_apply_minimum_quantity' => [
        'id' => 'force_auto_apply_minimum_quantity',
        'type' => 'switch',
        'title' => __('Force Applying Minimum Quantity', 'whols'),
        'label' => __('Force Applying Minimum Quantity', 'whols'),
        'help' => __('Control quantity from product lists:<br>• ON: Force minimum quantity when adding from product lists<br>• OFF: Allow any quantity from quick-add buttons<br>• Only works when Auto Input Minimum Quantity is enabled', 'whols'),
        'condition' => [
            [
                'key' => 'auto_apply_minimum_quantity',
                'operator' => '==',
                'value' => '1'
            ]
        ],
        'default' => '0'
    ],
    'exclude_tax_for_wholesale_customers' => [
        'id' => 'exclude_tax_for_wholesale_customers',
        'type' => 'switch',
        'title' => __('Exclude Tax', 'whols'),
        'label' => __('Yes', 'whols'),
        'help' => __('Manage tax settings for wholesalers:<br>• ON: Remove tax charges for wholesale orders<br>• OFF: Apply normal tax rates<br>• Affects all wholesale user roles', 'whols'),
        'default' => '0'
    ],
    'disable_coupon_for_wholesale_customers' => [
        'id' => 'disable_coupon_for_wholesale_customers',
        'type' => 'switch',
        'title' => __('Disable Coupons', 'whols'),
        'label' => __('Yes', 'whols'),
        'help' => __('Control coupon usage for wholesalers:<br>• ON: Prevent wholesalers from using any coupons<br>• OFF: Allow normal coupon usage<br>• Can be configured per wholesale role', 'whols'),
        'desc' => __('(This option can be overridden for each role individually)', 'whols'),
        'default' => '0'
    ],
    'disable_specific_payment_gateway_for_wholesale_customers' => [
        'id' => 'disable_specific_payment_gateway_for_wholesale_customers',
        'type' => 'select',
        'title' => __('Disable Payment Gateway', 'whols'),
        'help' => __('Restrict payment methods:<br>• Select gateways to disable for wholesalers<br>• Affects checkout payment options<br>• Can be configured per wholesale role', 'whols'),
        'desc' => __('(This option can be overridden for each role individually)', 'whols'),
        'options' => 'dynamic',
        'placeholder' => __('Select Gateways', 'whols'),
        'multiple' => true,
        'default' => [],
        'is_pro' => true
    ],
    'allow_free_shipping_for_wholesale_customers' => [
        'id' => 'allow_free_shipping_for_wholesale_customers',
        'type' => 'switch',
        'title' => __('Allow Free Shipping', 'whols'),
        'label' => __('Yes', 'whols'),
        'help' => __('Configure wholesale shipping options:<br>• ON: Enable free shipping for wholesale orders<br>• OFF: Apply normal shipping rates<br>• Note: Requires free shipping method setup in WooCommerce Shipping Zones<br>• Can be configured per wholesale role', 'whols'),
        'desc' => __('(This option can be overridden for role individually)', 'whols'),
        'default' => '0'
    ]
];