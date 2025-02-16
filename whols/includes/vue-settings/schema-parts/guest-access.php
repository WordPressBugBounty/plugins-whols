<?php
return array(
    'hide_price_for_guest_users' => [
        'id' => 'hide_price_for_guest_users',
        'type' => 'switch',
        'title' => __('Hide Price For Guest Users', 'whols'),
        'help' => __('If enabled, the price will be hidden for non-logged in users.', 'whols'),
        'default' => '0'
    ],
    'lgoin_to_see_price_label' => [
        'id' => 'lgoin_to_see_price_label',
        'type' => 'text',
        'title' => __('Login To See Price" Custom Label', 'whols'),
        'help' => __('This label will be shown in the product listing/loop of your shop page & product details page. Leave blank to show the default label.', 'whols'),
        'condition' => [
            [
                'key' => 'hide_price_for_guest_users',
                'operator' => '==',
                'value' => '1'
            ]
        ],
        'default' => __('Login to view price', 'whols')
    ],
    'hide_price_for_general_customers' => [
        'id' => 'hide_price_for_general_customers',
        'type' => 'switch',
        'title' => __('Hide Price For Non-wholesale Customers', 'whols'),
        'help' => __('Prices are visible exclusively to wholesale customers, while general customers will not see the product price even if they are logged in.', 'whols'),
        'default' => '0',
        'is_pro' => true
    ],
    'enable_website_restriction' => [
        'id' => 'enable_website_restriction',
        'type' => 'radio',
        'title' => __('Enable Website Restriction Type', 'whols'),
        'options' => [
            '' => __('No Restriction', 'whols'),
            'for_only_shop' => __('For Only Shop', 'whols'),
            'for_entire_wbesite' => __('For Entire Website', 'whols')
        ],
        'default' => '',
        'class' => 'whols-pro-field-opacity'
    ],
    'who_can_access_shop' => [
        'id' => 'who_can_access_shop',
        'type' => 'radio',
        'title' => __('Who Can Access Shop', 'whols'),
        'options' => [
            'everyone' => __('Everyone', 'whols'),
            'logedin_users_with_wholesale_role' => __('Only Wholesalers', 'whols'),
            'logedin_users' => __('All Logged in Users', 'whols')
        ],
        'help' => __('Define who can access the shop.', 'whols'),
        'default' => 'everyone',
        'class' => 'whols-pro-field-opacity'
    ],
    'who_can_access_entire_website' => [
        'id' => 'who_can_access_entire_website',
        'type' => 'select',
        'title' => __('Who Can Access Entire Website', 'whols'),
        'options' => [
            'everyone' => __('Everyone', 'whols'),
            'logedin_users_with_wholesale_role' => __('Only Wholesalers', 'whols'),
            'logedin_users' => __('All Logged in Users', 'whols')
        ],
        'help' => __('Define who can access the entire website.', 'whols'),
        'condition' => [
            [
                'key' => 'enable_website_restriction',
                'operator' => '==',
                'value' => 'for_entire_wbesite'
            ]
        ],
        'default' => 'everyone',
    ]
);