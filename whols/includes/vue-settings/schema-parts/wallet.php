<?php
return ['wallet_route' => [
    'title' => __('Wallet', 'whols'),
    'sections' => [],
    'fields' => [
        'enable_wallet_payment' => [
            'id' => 'enable_wallet_payment',
            'type' => 'switch',
            'title' => __('Enable', 'whols'),
            'label' => __('Yes', 'whols'),
            'help' => __('Enable to allow customers to pay using their wallet.', 'whols'),
            'default' => '0',
            'is_pro' => true
        ],
        'min_amount_can_recharge' => [
            'id' => 'min_amount_can_recharge',
            'type' => 'number',
            'title' => __('Minimum Amount Can Recharge', 'whols'),
            'placeholder' => __('No Limit', 'whols'),
            'help' => __('Minimum amount that can be recharged to the wallet. Leave it blank for no limit.', 'whols'),
            'min' => 1,
            'default' => '10',
            'class' => 'whols-pro-field-opacity'
        ],
        'max_amount_can_recharge' => [
            'id' => 'max_amount_can_recharge',
            'type' => 'number',
            'title' => __('Maximum Amount Can Recharge', 'whols'),
            'placeholder' => __('No Limit', 'whols'),
            'help' => __('Maximum amount that can be recharged to the wallet. Leave it blank for no limit.', 'whols'),
            'min' => 1,
            'default' => '200',
            'class' => 'whols-pro-field-opacity'
        ],
        'otp_verification_method' => [
            'id' => 'otp_verification_method',
            'type' => 'select',
            'title' => __('OTP Verification Method', 'whols'),
            'options' => [
                '' => __('None', 'whols'),
                'email' => __('Email', 'whols')
            ],
            'help' => __('Select the OTP verification method for wallet transactions.', 'whols'),
            'default' => '',
            'class' => 'whols-pro-field-opacity'
        ],  
    ]
]];