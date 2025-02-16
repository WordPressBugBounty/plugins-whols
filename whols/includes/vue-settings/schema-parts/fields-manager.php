<?php
return array(
    'registration_fields' => [
        'id' => 'registration_fields',
        'type' => 'group',
        'title' => __('Registration Fields Manager', 'whols'),
        'class' => 'whols_field_manager',
        'button_title' => '<i class="fa fa-plus"></i>',
        'fields' => [
            'field' => [
                'id' => 'field',
                'type' => 'select',
                'title' => __('Field', 'whols'),
                'options' => [
                    'custom' => __('Custom Field', 'whols'),
                    'General' => [
                        '_whols_role' => __('Wholesaler Roles', 'whols'),
                    ],
                    'WooCommerce' => [
                        'billing_company' => __('Billing Company', 'whols'),
                        'billing_address_1' => __('Billing Address', 'whols'),
                        'billing_city' => __('Billing City', 'whols'),
                        'billing_postcode' => __('Billing Postcode', 'whols'),
                        'billing_country' => __('Billing Country', 'whols'),
                        'billing_state' => __('Billing State', 'whols'),
                        'billing_phone' => __('Billing Phone', 'whols'),
                    ],
                    'Default' => [
                        'reg_name' => __('Name', 'whols'),
                        'reg_username' => __('Username', 'whols'),
                        'reg_email' => __('Email', 'whols'),
                        'reg_password' => __('Password', 'whols'),
                    ],
                ],
                'default' => '',
            ],
            'enable' => [
                'id' => 'enable',
                'type' => 'switch',
                'title' => __('Enable', 'whols'),
                'label' => __('Yes', 'whols'),
                'default' => true,
                'condition' => [
                    [
                        'key' => 'field',
                        'operator' => '==',
                        'value' => 'custom',
                    ],
                ],
            ],
            'type' => [
                'id' => 'type',
                'type' => 'select',
                'title' => __('Field Type', 'whols'),
                'options' => [
                    'text'      => __( 'Text', 'whols' ),
                    'textarea'  => __( 'Textarea', 'whols' ),
                    'select'    => __( 'Select', 'whols' ),
                    'checkbox'  => __( 'Checkbox', 'whols' ),
                    'radio'     => __( 'Radio', 'whols' ),
                    'file'      => __( 'File / Upload', 'whols' ),
                ],
                'group' => 'registration_fields',
                'condition' => [
                    [
                        'key' => 'field',
                        'operator' => '==',
                        'value' => 'custom'
                    ]
                ],
                'default' => '',
            ],
            'custom_field_name' => [
                'id' => 'custom_field_name',
                'type' => 'text',
                'title' => __('Field Unique Name', 'whols'),
                'desc' => __('It will be used to save the field value in the database. It must be unique.', 'whols'),
                'placeholder' => __('e.g: company_name', 'whols'),
                'group' => 'registration_fields',
                'condition' => [
                    [
                        'key' => 'field',
                        'operator' => '==',
                        'value' => 'custom'
                    ]
                ],
                'default' => '',
            ],
            'label' => [
                'id' => 'label',
                'type' => 'text',
                'title' => __('Label', 'whols'),
                'default' => '',
            ],
            'options' => [
                'id' => 'options',
                'type' => 'textarea',
                'title' => __('Field Options', 'whols'),
                'desc' => __('Enter each option on a new line with key|value pair . For example: <br> <code>option-1|Option 1</code> <br> <code>option-2|Option 2</code> <br> <code>option-3|Option 3</code>', 'whols'),
                'group' => 'registration_fields',
                'condition' => [
                    [
                        'key' => 'field|type',
                        'operator' => '==|any',
                        'value' => 'custom|select,radio'
                    ]
                ],
                'default' => '',
            ],
            'allowed_files' => [
                'id' => 'allowed_files',
                'type' => 'checkbox',
                'title' => __('Allowed File Types', 'whols'),
                'options' => [
                    'image' => __('Images (JPG, PNG, GIF)', 'whols'),
                    'pdf' => __('Documents (PDF)', 'whols'),
                    'doc' => __('Word Documents (DOC, DOCX)', 'whols'),
                ],
                'default' => ['image', 'pdf'],
                'group' => 'registration_fields',
                'condition' => [
                    [
                        'key' => 'field|type',
                        'operator' => '==|==',
                        'value' => 'custom|file'
                    ]
                ],
            ],
            'maximum_allowed_size' => [
                'id' => 'maximum_allowed_size',
                'type' => 'number',
                'title' => __('Maximum File Size', 'whols'),
                'desc' => __('Leave it empty for default. Default: 2MB', 'whols'),
                'suffix' => 'MB',
                'default' => '2',
                'placeholder' => '2',
                'group' => 'registration_fields',
                'condition' => [
                    [
                        'key' => 'field|type',
                        'operator' => '==|any',
                        'value' => 'custom|file'
                    ]
                ],
                'attributes' => [
                    'min' => '1',
                    'max' => '100',
                ]
            ],
            'placeholder' => [
                'id' => 'placeholder',
                'type' => 'text',
                'title' => __('Placeholder', 'whols'),
                'default' => '',
            ],
            'description' => [
                'id' => 'description',
                'type' => 'text',
                'title' => __('Description', 'whols'),
                'default' => '',
            ],
            'value' => [
                'id' => 'value',
                'type' => 'text',
                'title' => __('Default Value', 'whols'),
                'default' => '',
            ],
            'required' => [
                'id' => 'required',
                'type' => 'switch',
                'title' => __('Required?', 'whols'),
                'label' => __('Yes', 'whols'),
                'default' => '',
                'condition' => [
                    [
                        'key' => 'field',
                        'operator' => 'not-any',
                        'value' => 'reg_name,reg_username,reg_email,reg_password'
                    ]
                ],
            ],
            'class' => [
                'id' => 'class',
                'type' => 'text',
                'title' => __('Class', 'whols'),
                'default' => '',
            ],
        ],
        'accordion_title_prefix' => 'Field: ',
        'accordion_title_number' => true,
        'default' => [
            [
                'field' => 'reg_name',
                'enable' => true,
                'label' => __('Name', 'whols'),
                'placeholder' => __('Your Name', 'whols'),
                'required' => true,
            ],
            [
                'field' => 'reg_username',
                'enable' => true,
                'label' => __('Username', 'whols'),
                'placeholder' => __('Your Username', 'whols'),
                'required' => true,
            ],
            [
                'field' => 'reg_email',
                'enable' => true,
                'label' => __('Email', 'whols'),
                'placeholder' => __('Your Email', 'whols'),
                'required' => true,
            ],
            [
                'field' => 'reg_password',
                'enable' => true,
                'label' => __('Password', 'whols'),
                'placeholder' => __('Your Password', 'whols'),
                'required' => true,
            ],
        ],
    ]  
);