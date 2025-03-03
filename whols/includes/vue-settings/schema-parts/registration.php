<?php
return ['registration_route' => [
    'title' => __('Registration Settings', 'whols'),
    'sections' => [
        // General Settings
        'general' => [
            'title' => __('General Settings', 'whols'),
        ],
        // Form Settings
        'form' => [
            'title' => __('Form Settings', 'whols'),
        ],
        // Redirect Settings
        'redirect' => [
            'title' => __('Redirect Settings', 'whols'),
        ],
        // reCAPTCHA Settings
        'recaptcha' => [
            'title' => __('reCAPTCHA Settings', 'whols'),
        ],
    ],
    'fields' => [
        'registration_page' => [
            'id' => 'registration_page',
            'section' => 'general',
            'type' => 'select',
            'title' => __('Set up The Registration Page', 'whols'),
            'options' => 'page',
            'placeholder' => __('---Search a page---', 'whols'),
            'filterable' => true,
            'help' => __('Set up registration page:<br>• Select a page to display the registration form<br>• Or use shortcode [whols_registration_form] on any page<br>• Form includes all necessary wholesaler fields', 'whols'),
            'default' => ''
        ],
        'default_wholesale_role' => [
            'id' => 'default_wholesale_role',
            'section' => 'general',
            'type' => 'select',
            'title' => __('Default Wholesaler Role', 'whols'),
            'options' => 'whols_roles',
            'filterable' => false,
            'help' => __('Choose default wholesaler role:<br>• Automatically assigned to new registrations<br>• Can be changed later in user management<br>• Determines initial pricing tier access', 'whols'),
            'default' => 'whols_default_role',
            'condition' => [
                [
                    'key' => 'pricing_model',
                    'operator' => '==',
                    'value' => 'multiple_role'
                ]
            ],
            'is_pro' => true
        ],
        'enable_auto_approve_customer_registration' => [
            'id' => 'enable_auto_approve_customer_registration',
            'section' => 'general',
            'type' => 'switch',
            'title' => __('Enable Auto Approve', 'whols'),
            'help' => __('Control registration approval:<br>• ON: Instant account activation<br>• OFF: Manual admin approval required<br>• Affects all new wholesaler registrations', 'whols'),
            'default' => '0',
            'class' => 'whols-pro-field-opacity'
        ],
        'registration_form_submit_button_label' => [
            'id' => 'registration_form_submit_button_label',
            'section' => 'form',
            'type' => 'text',
            'title' => __('Submit Button Label', 'whols'),
            'help' => __('Customize registration button:<br>• Change the text on submit button<br>• Make it clear and action-oriented<br>• Default: "Register As Wholesaler"', 'whols'),
            'default' => __('Register As Wholesaler', 'whols')
        ],
        // @todo: add default message
        'registration_successful_message_for_auto_approve' => [
            'section' => 'form',
            'id' => 'registration_successful_message_for_auto_approve',
            'type' => 'textarea',
            'title' => __('Successful Registration Message', 'whols'),
            'help' => __('Auto-approval success message:<br>• Shown after successful registration<br>• Only displays when auto-approve is ON<br>• Supports HTML for formatting', 'whols'),
            'desc' => __('For Auto Approval', 'whols'),
            'condition' => [
                [
                    'key' => 'enable_auto_approve_customer_registration',
                    'operator' => '==',
                    'value' => '1'
                ]
            ],
            'default' => __('Thank you for registering! Your account has been automatically approved. You can now log in and start using your wholesale account.', 'whols'),
        ],
        'registration_successful_message_for_manual_approve' => [
            'section' => 'form',
            'id' => 'registration_successful_message_for_manual_approve',
            'type' => 'textarea',
            'title' => __('Successful Registration Message', 'whols'),
            'help' => __('Manual approval message:<br>• Shown after registration when auto-approve is OFF<br>• Inform users about approval process<br>• Supports HTML for formatting', 'whols'),
            'default' => __('Thank you very much for completing the registration. Your account will be reviewed by us & approved manually. Please wait for a while.', 'whols'),
            'desc' => __('For Manual Approval', 'whols'),
            'condition' => [
                [
                    'key' => 'enable_auto_approve_customer_registration',
                    'operator' => '==',
                    'value' => '0'
                ]
            ],
        ],
        'redirect_page_customer_registration' => [
            'section' => 'redirect',
            'id' => 'redirect_page_customer_registration',
            'type' => 'text',
            'title' => __('Redirect Page URL (After Registration)', 'whols'),
            'help' => __('Post-registration redirect:<br>• Enter full URL for redirect page<br>• Redirects after successful registration<br>• Leave empty to stay on registration page', 'whols'),
            'default' => ''
        ],
        'redirect_page_customer_login' => [
            'section' => 'redirect',
            'id' => 'redirect_page_customer_login',
            'type' => 'text',
            'title' => __('Redirect Page URL (After Login)', 'whols'),
            'help' => __('Post-login redirect:<br>• Enter full URL for redirect page<br>• Must be same-domain URL<br>• Leave empty for default dashboard', 'whols'),
            'default' => ''
        ],
        'enable_recaptcha' => [
            'section' => 'recaptcha',
            'id' => 'enable_recaptcha',
            'type' => 'switch',
            'title' => __('Enable Google reCAPTCHA (V3)', 'whols'),
            'default' => '0',
            'is_pro' => true
        ],
        'recaptcha_site_key' => [
            'section' => 'recaptcha',
            'id' => 'recaptcha_site_key',
            'type' => 'text',
            'title' => __('Recaptcha Site Key', 'whols'),
            'default' => '',
            'condition' => [
                [
                    'key' => 'enable_recaptcha',
                    'operator' => '==',
                    'value' => '1'
                ]
            ]
        ],
        'recaptcha_secret_key' => [
            'section' => 'recaptcha',
            'id' => 'recaptcha_secret_key',
            'type' => 'text',
            'title' => __('Recaptcha Secret Key', 'whols'),
            'default' => '',
            'condition' => [
                [
                    'key' => 'enable_recaptcha',
                    'operator' => '==',
                    'value' => '1'
                ]
            ]
        ],
        'recaptcha_min_score' => [
            'section' => 'recaptcha',
            'id' => 'recaptcha_min_score',
            'type' => 'text',
            'title' => __('Recaptcha Minimum Score', 'whols'),
            'help' => __('Configure reCAPTCHA security level:<br>• Enter value between 0.0 and 1.0<br>• Higher score = stricter verification<br>• Recommended: 0.5<br>• Leave empty to accept all scores', 'whols'),
            'default' => '0.5',
            'condition' => [
                [
                    'key' => 'enable_recaptcha',
                    'operator' => '==',
                    'value' => '1'
                ]
            ]
        ],
        'recaptcha_badge_disable' => [
            'section' => 'recaptcha',
            'id' => 'recaptcha_badge_disable',
            'type' => 'switch',
            'title' => __('Disable Recaptcha Badge', 'whols'),
            'help' => __('Control reCAPTCHA badge visibility:<br>• ON: Hide the badge<br>• OFF: Show badge at bottom-right', 'whols'),
            'default' => '0',
            'condition' => [
                [
                    'key' => 'enable_recaptcha',
                    'operator' => '==',
                    'value' => '1'
                ]
            ]
        ]
    ]
]];
