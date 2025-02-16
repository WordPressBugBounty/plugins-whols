<?php
return array(
    'enable_request_a_quote' => [
        'id' => 'enable_request_a_quote',
        'type' => 'switch',
        'title' => __('Enable', 'whols'),
        'label' => __('Yes', 'whols'),
        'help' => __('Enable to allow customers to request a quote for the products.', 'whols'),
        'default' => '0'
    ],
    'request_a_quote_label' => [
        'id' => 'request_a_quote_label',
        'type' => 'text',
        'title' => __('Request a Quote Label', 'whols'),
        'help' => __('Label of the request a quote button.', 'whols'),
        'default' => '',
        'condition' => [
            [
                'key' => 'enable_request_a_quote',
                'operator' => '==',
                'value' => '1'
            ]
        ]
    ],
    'create_conversation_when_request_a_quote' => [
        'id' => 'create_conversation_when_request_a_quote',
        'type' => 'switch',
        'title' => __('Create Conversation When Request a Quote', 'whols'),
        'label' => __('Yes', 'whols'),
        'help' => __('Enable to create a new conversation when a request a quote is submitted. <br> Conversation feature should be enabled to use this option.', 'whols'),
        'default' => '0',
        'is_pro' => true
    ]
);