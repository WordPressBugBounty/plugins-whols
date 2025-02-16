<?php
return array(
    'enable_custom_thank_you_message' => [
        'id' => 'enable_custom_thank_you_message',
        'type' => 'switch',
        'title' => __('Enable', 'whols'),
        'label' => __('Yes', 'whols'),
        'help' => __('Enable to customize thank you message for the wholesalers.', 'whols'),
        'default' => '0'
    ],
    'thank_you_message_placement' => [
        'id' => 'thank_you_message_placement',
        'type' => 'select',
        'title' => __('Placement', 'whols'),
        'options' => [
            'before_default_message' => __('Before default "Thank You" message', 'whols'),
            'after_default_message' => __('After default "Thank You" message', 'whols'),
            'replace_default_message' => __('Replace default "Thank You" message', 'whols')
        ],
        'default' => 'replace_default_message',
        'help' => __('Define how the message should be displayed.', 'whols')
    ],
    'custom_thank_you_message' => [
        'id' => 'custom_thank_you_message',
        'type' => 'wp_editor',
        'title' => __('Message', 'whols'),
        'desc' => __('Use the placeholder tags below to get dynamic content. <br><span class="whols_pre">{billing_first_name}, {billing_last_name}, {billing_email}</pre>', 'whols'),
        'default' => __('Thank you <strong>{billing_first_name}</strong>. Your order has been received.', 'whols')
    ]
);