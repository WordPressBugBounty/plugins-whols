<?php
return ['save-order-list_route' => [
    'title' => __('Save Order List', 'whols'),
    'sections' => [],
    'fields' => [
        'enable_save_order_list' => array(
            'id'         => 'enable_save_order_list',
            'type'       => 'switch',
            'title'      => __( 'Enable', 'whols' ),
            'text_on'    => esc_html__( 'Yes', 'whols' ),
            'text_off'   => esc_html__( 'No', 'whols' ),
            'help'       => __( 'Enable this feature to allow wholesale customers to save their cart items as reusable lists. <br> They can manage multiple lists and quickly reorder items.', 'whols' ),
            'default' => '0'
        ),

        'save_list_button_text' => array(
            'id'         => 'save_list_button_text',
            'type'       => 'text',
            'title'      => esc_html__( 'Save List Button Text', 'whols' ),
            'desc'       => esc_html__( 'Customize the text displayed on the save list button.', 'whols' ),
            'default' => __('Save as List', 'whols'),
        ),

        'max_lists_per_user' => array(
            'id'          => 'max_lists_per_user',
            'type'        => 'number',
            'title'       => esc_html__( 'Maximum Lists Per User', 'whols' ),
            'desc'        => esc_html__( 'Set the maximum number of lists a wholesale customer can save. By default, 5 lists are allowed.', 'whols' ),
            'help'        => esc_html__( 'This helps manage storage and ensures optimal performance.', 'whols' ),
            'default' => '5',
            'is_pro' => true,
        ),
    ]
]];