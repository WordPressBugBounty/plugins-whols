<?php
return [ 'conversation_route' => [
    'title' => __('Conversation', 'whols'),
    'sections' => [],
    'fields' => [
        'enable_conversation' => [
            'id' => 'enable_conversation',
            'type' => 'switch',
            'title' => __('Enable', 'whols'),
            'label' => __('Yes', 'whols'),
            'help' => __('Enable to allow customers to start a conversation with the wholesaler from the my account page.', 'whols'),
            'default' => '0',
            'is_pro' => true
        ],
        'start_conversation_label' => [
            'id' => 'start_conversation_label',
            'type' => 'text',
            'title' => __('Start Conversation Label', 'whols'),
            'help' => __('Label of the start conversation text shown on the popup form.', 'whols'),
            'default' => '',
            'class' => 'whols-pro-field-opacity'
        ]
    ]
]];
