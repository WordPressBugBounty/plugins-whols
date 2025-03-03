<?php
return ['wholesaler-only-categories_route' =>[
    'title'        => esc_html__( 'Wholesaler Only Categories', 'whols'),
    'texts' => [
        'add_new' => esc_html__( 'Add New', 'whols'),
        'notice' => esc_html__( 'You can define which categories are visible only to wholesalers.', 'whols'),
        'no_categories_added_yet' => esc_html__( 'No category rules added yet.', 'whols'),
        'no_rules_added_yet' => esc_html__( 'No rules added yet.', 'whols'),
        'cancel' => esc_html__( 'Cancel', 'whols'),
        'save' => esc_html__( 'Save Changes', 'whols'),
    ],

    'sections' => [],
    'fields' => [
        'enable_wholesale_only_categories' => [
            'id'         => 'enable_wholesale_only_categories',
            'type'       => 'switch',
            'title'      => esc_html__( 'Enable', 'whols'),
            'help'       => esc_html__( 'Enable to Define Wholesaler Only Categories.', 'whols'),
            'text_on'    => esc_html__( 'Yes', 'whols' ),
            'text_off'   => esc_html__( 'No', 'whols' ),
            'default'    => '0',
            'is_pro'     => true
        ],
        'include_children' => [
            'id'         => 'include_children',
            'type'       => 'switch',
            'title'      => esc_html__( 'Include Children Categories', 'whols'),
            'help'       => __( 'If checked, all the child categories within a parent category will be selected as well. <br>If not checked, parents or children relationship will not be considered in the selection of the category.', 'whols'),
            'label'      => esc_html__( 'Yes', 'whols' ),
            'default'    => '0',
            'class'      => 'whols-pro-field-opacity',
        ],
        'wholesale_only_categories' => [
            'id'          => 'wholesale_only_categories',
            'type'        => 'repeater',
            'title'       => esc_html__( '', 'whols' ),
            'class'       => 'whols_wholesale_only_categories whols-pro-field-opacity',
            'class'       => 'whols_wholesale_only_categories',
            'fields'      => array(
                'categories' => array(
                    'id'          => 'categories',
                    'type'        => 'select',
                    'title'       => __( 'Category(s)', 'whols' ),
                    'placeholder' => __( 'Select', 'whols' ),
                    'desc'        => __('Only users with the assigned roles can access products in these selected categories.', 'whols'),
                    'options'     => 'product_cat',
                    'multiple'    => true,
                    'chosen'      => true,
                    'default'     => array()
                ),
                'roles' => array(
                    'id'          => 'roles',
                    'type'        => 'select',
                    'title'       => __( 'Assign Role(s)', 'whols' ),
                    'placeholder' => __( 'Leave it empty, to assign all roles.', 'whols' ),
                    'desc'        => __('Only users with the assigned roles can access products in these selected categories.', 'whols'),
                    'options'     => 'whols_roles',
                    'multiple'    => true,
                    'chosen'      => true,
                    'default'     => array()
                ),
            ),
            'button_title'      => __('Add New', 'whols'),
            'condition'  => [
                [
                    'key' => 'enable_wholesale_only_categories',
                    'operator' => '==',
                    'value' => '1'
                ]
            ],
            'default'    => []
        ]
    ]
]];
