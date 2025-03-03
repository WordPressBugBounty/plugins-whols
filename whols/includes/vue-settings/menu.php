<?php
return [
    '/' => [
        'title' => __('General Settings', 'whols'),
        'icon' => 'Setting'
    ],
    'registration-login' => [
        'title' => __('Registration & Login', 'whols'),
        'items' => [
            'registration' => [
                'title' => __('Registration Options', 'whols')
            ],
            'fields-manager' => [
                'title' => __('Fields Manager', 'whols')
            ]
        ]
    ],
    'product-settings' => [
        'title' => __('Product Settings', 'whols'),
        'items' => [
            'product-visibility' => [
                'title' => __('Product Visibility', 'whols')
            ],
            'wholesaler-only-categories' => [
                'title' => __('Wholesaler Only Categories', 'whols')
            ]
        ]
    ],
    'guest-access' => [
        'title' => __('Guest Access Restriction', 'whols'),
    ],
    'dynamic-rules' => [
        'title' => __('Dynamic Rules', 'whols'),
    ],
    'request-a-quote' => [
        'title' => __('Request a Quote', 'whols'),
    ],
    'conversation' => [
        'title' => __('Conversation', 'whols'),
    ],
    'wallet' => [
        'title' => __('Wallet', 'whols'),
    ],
    'notifications' => [
        'title' => __('Message & Email Notifications', 'whols'),
        'items' => [
            'email-notifications' => [
                'title' => __('Email Notifications', 'whols')
            ],
            'thank-you-message' => [
                'title' => __('Custom Thank You Message', 'whols')
            ]
        ]
    ],
    'others' => [
        'title' => __('Other Settings', 'whols'),
    ],
    'design' => [
        'title' => __('Design', 'whols'),
    ]
];