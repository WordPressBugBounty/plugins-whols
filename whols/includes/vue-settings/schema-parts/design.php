<?php
return [
    'retailer_price_label' => [
        'id' => 'retailer_price_label',
        'type' => 'fieldset',
        'title' => __('Retailer Price Label', 'whols'),
        'fields' => [
            'retailer_price_label_color' => [
                'type' => 'color',
                'title' => __('Color', 'whols'),
                'output' => '.whols_retailer_price .whols_label_left',
                'default' => ''
            ],
            'retailer_price_label_font_size' => [
                'type' => 'number',
                'title' => __('Font size', 'whols'),
                'suffix' => 'px',
                'output' => '.whols_retailer_price .whols_label_left',
                'output_mode' => 'font-size',
                'default' => ''
            ],
            'retailer_price_label_line_height' => [
                'type' => 'number',
                'title' => __('Line Height', 'whols'),
                'suffix' => 'px',
                'output' => '.whols_retailer_price .whols_label_left',
                'output_mode' => 'line-height',
                'default' => ''
            ],
            'retailer_price_label_font_weight' => [
                'type' => 'number',
                'title' => __('Font Weight', 'whols'),
                'suffix' => ' ',
                'output' => '.whols_loop_custom_price .whols_label .whols_label_left',
                'output_mode' => 'font-weight',
                'help' => __('Default: 700', 'whols'),
                'default' => ''
            ]
        ]
    ],

    // Styling
    'retailer_price' => [
        'id' => 'retailer_price',
        'type' => 'fieldset',
        'title' => __('Price', 'whols'),
        'fields' => [
            'retailer_price_color' => [
                'type' => 'color',
                'title' => __('Color', 'whols'),
                'output' => '.whols_retailer_price del',
                'default' => ''
            ],
            'retailer_price_font_size' => [
                'type' => 'number',
                'title' => __('Font size', 'whols'),
                'suffix' => 'px',
                'output' => '.whols_retailer_price del',
                'output_mode' => 'font-size',
                'default' => ''
            ],
            'retailer_price_line_height' => [
                'type' => 'number',
                'title' => __('Line Height', 'whols'),
                'suffix' => 'px',
                'output' => '.whols_retailer_price del',
                'output_mode' => 'line-height',
                'default' => ''
            ],
            'retailer_price_font_weight' => [
                'type' => 'number',
                'title' => __('Font Weight', 'whols'),
                'suffix' => ' ',
                'output' => '.whols_retailer_price del',
                'output_mode' => 'font-weight',
                'help' => __('Default: 400', 'whols'),
                'default' => ''
            ]
        ]
    ],

    // Retailer Price Margin
    'retailer_price_margin' => [
        'id' => 'retailer_price_margin',
        'type' => 'spacing',
        'title' => __('Margin', 'whols'),
        'output' => '.whols_retailer_price',
        'output_mode' => 'margin',
        'default' => [
            'top' => '',
            'right' => '',
            'bottom' => '',
            'left' => '',
            'unit' => 'px'
        ]
    ],

    // Wholesaler Price Label Styling
    'wholesaler_price_label' => [
        'id' => 'wholesaler_price_label',
        'type' => 'fieldset',
        'title' => __('Wholesaler Price Label', 'whols'),
        'fields' => [
            'wholesaler_price_label_color' => [
                'type' => 'color',
                'title' => __('Color', 'whols'),
                'output' => '.products .product .price .whols_wholesaler_price .whols_label_left',
                'default' => ''
            ],
            'wholesaler_price_label_font_size' => [
                'type' => 'number',
                'title' => __('Font size', 'whols'),
                'suffix' => 'px',
                'output' => '.products .product .price .whols_wholesaler_price .whols_label_left',
                'output_mode' => 'font-size',
                'default' => ''
            ],
            'wholesaler_price_label_line_height' => [
                'type' => 'number',
                'title' => __('Line Height', 'whols'),
                'suffix' => 'px',
                'output' => '.products .product .price .whols_wholesaler_price .whols_label_left',
                'output_mode' => 'line-height',
                'default' => ''
            ],
            'wholesaler_price_label_font_weight' => [
                'type' => 'number',
                'title' => __('Font Weight', 'whols'),
                'suffix' => ' ',
                'output' => '.products .product .price .whols_wholesaler_price .whols_label_left',
                'output_mode' => 'font-weight',
                'help' => __('Default: 700', 'whols'),
                'default' => ''
            ]
        ]
    ],

    // Wholesaler Price Styling
    'wholesaler_price' => [
        'id' => 'wholesaler_price',
        'type' => 'fieldset',
        'title' => __('Price', 'whols'),
        'fields' => [
            'wholesaler_price_color' => [
                'type' => 'color',
                'title' => __('Color', 'whols'),
                'output' => '.whols_wholesaler_price .whols_label_right',
                'default' => ''
            ],
            'wholesaler_price_font_size' => [
                'type' => 'number',
                'title' => __('Font size', 'whols'),
                'suffix' => 'px',
                'output' => '.whols_wholesaler_price .whols_label_right',
                'output_mode' => 'font-size',
                'default' => ''
            ],
            'wholesaler_price_line_height' => [
                'type' => 'number',
                'title' => __('Line Height', 'whols'),
                'suffix' => 'px',
                'output' => '.whols_wholesaler_price .whols_label_right',
                'output_mode' => 'line-height',
                'default' => ''
            ],
            'wholesaler_price_font_weight' => [
                'type' => 'number',
                'title' => __('Font Weight', 'whols'),
                'suffix' => ' ',
                'output' => '.whols_wholesaler_price .whols_label_right',
                'output_mode' => 'font-weight',
                'help' => __('Default: 400', 'whols'),
                'default' => ''
            ]
        ]
    ],

    // Wholesaler Price Margin
    'wholesaler_price_margin' => [
        'id' => 'wholesaler_price_margin',
        'type' => 'spacing',
        'title' => __('Margin', 'whols'),
        'output' => '.whols_wholesaler_price',
        'output_mode' => 'margin',
        'default' => [
            'top' => '',
            'right' => '',
            'bottom' => '',
            'left' => '',
            'unit' => 'px'
        ]
    ],

    // Save Amount Label Styling
    'save_amount_label' => [
        'id' => 'save_amount_label',
        'type' => 'fieldset',
        'title' => __('Save Amount Label', 'whols'),
        'fields' => [
            'save_amount_label_color' => [
                'type' => 'color',
                'title' => __('Color', 'whols'),
                'output' => '.whols_save_amount .whols_label_left',
                'default' => ''
            ],
            'save_amount_label_font_size' => [
                'type' => 'number',
                'title' => __('Font size', 'whols'),
                'suffix' => 'px',
                'output' => '.whols_save_amount .whols_label_left',
                'output_mode' => 'font-size',
                'default' => ''
            ],
            'save_amount_label_line_height' => [
                'type' => 'number',
                'title' => __('Line Height', 'whols'),
                'suffix' => 'px',
                'output' => '.whols_save_amount .whols_label_left',
                'output_mode' => 'line-height',
                'default' => ''
            ],
            'save_amount_label_font_weight' => [
                'type' => 'number',
                'title' => __('Font Weight', 'whols'),
                'suffix' => ' ',
                'output' => '.whols_save_amount .whols_label .whols_label_left',
                'output_mode' => 'font-weight',
                'help' => __('Default: 700', 'whols'),
                'default' => ''
            ]
        ]
    ],

    // Save Amount Price Styling
    'save_amount_price' => [
        'id' => 'save_amount_price',
        'type' => 'fieldset',
        'title' => __('Price', 'whols'),
        'fields' => [
            'save_amount_price_color' => [
                'type' => 'color',
                'title' => __('Color', 'whols'),
                'output' => '.whols_save_amount .whols_label_right',
                'default' => ''
            ],
            'save_amount_price_font_size' => [
                'type' => 'number',
                'title' => __('Font size', 'whols'),
                'suffix' => 'px',
                'output' => '.whols_save_amount .whols_label_right',
                'output_mode' => 'font-size',
                'default' => ''
            ],
            'save_amount_price_line_height' => [
                'type' => 'number',
                'title' => __('Line Height', 'whols'),
                'suffix' => 'px',
                'output' => '.whols_save_amount .whols_label_right',
                'output_mode' => 'line-height',
                'default' => ''
            ],
            'save_amount_price_font_weight' => [
                'type' => 'number',
                'title' => __('Font Weight', 'whols'),
                'suffix' => ' ',
                'output' => '.whols_save_amount .whols_label_right',
                'output_mode' => 'font-weight',
                'help' => __('Default: 400', 'whols'),
                'default' => ''
            ]
        ]
    ],

    // Save Amount Margin
    'save_amount_margin' => [
        'id' => 'save_amount_margin',
        'type' => 'spacing',
        'title' => __('Margin', 'whols'),
        'output' => '.whols_save_amount',
        'output_mode' => 'margin',
        'default' => [
            'top' => '',
            'right' => '',
            'bottom' => '',
            'left' => '',
            'unit' => 'px'
        ]
    ],

    // Minimum Quantity Notice Styling
    'minimum_quantity_notice' => [
        'id' => 'minimum_quantity_notice',
        'type' => 'fieldset',
        'title' => __('Minimum Quantity Notice', 'whols'),
        'fields' => [
            'retailer_price_color' => [
                'type' => 'color',
                'title' => __('Color', 'whols'),
                'output' => '.whols_minimum_quantity_notice',
                'default' => ''
            ],
            'retailer_price_font_size' => [
                'type' => 'number',
                'title' => __('Font size', 'whols'),
                'suffix' => 'px',
                'output' => '.whols_minimum_quantity_notice',
                'output_mode' => 'font-size',
                'default' => ''
            ],
            'retailer_price_line_height' => [
                'type' => 'number',
                'title' => __('Line Height', 'whols'),
                'suffix' => 'px',
                'output' => '.whols_minimum_quantity_notice',
                'output_mode' => 'line-height',
                'default' => ''
            ],
            'retailer_price_font_weight' => [
                'type' => 'number',
                'title' => __('Font Weight', 'whols'),
                'suffix' => ' ',
                'output' => '.whols_minimum_quantity_notice',
                'output_mode' => 'font-weight',
                'help' => __('Default: 400', 'whols'),
                'default' => ''
            ]
        ]
    ]  
];