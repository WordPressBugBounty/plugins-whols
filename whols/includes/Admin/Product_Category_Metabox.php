<?php
/**
 * Whols Product Category Metabox
 *
 * @since 1.0.0
 */

namespace Whols\Admin;

/**
 * Product_Category_Metabox class
 */
class Product_Category_Metabox {

    /**
     * Product_Category_Metabox constructor
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->options();
    }

    /**
     * Meta options for product categories
     *
     * @since 1.0.0
     */
    public function options() {
         $pricing_model         = 'single_role';
         $roles                 = whols_get_taxonomy_terms();

        if( $pricing_model == 'single_role' ){               
            $fields = array(
                // price type 1 properties
                array(
                  'id'         => 'price_type_1_properties',
                  'type'       => 'fieldset',
                  'title'      => esc_html__( 'Price Options For Pricing Model: Single', 'whols' ),
                  'fields'     => array(
                        // enable this pricing
                        array(
                          'id'         => 'enable_this_pricing',
                          'type'       => 'switcher',
                          'title'      => esc_html__( 'Enable This Pricing', 'whols'),
                          'text_on'    => esc_html__( 'Yes', 'whols' ),
                          'text_off'   => esc_html__( 'No', 'whols' ),
                          'label'      => esc_html__( '(If not enabled global options will be used)' ),
                        ),

                        // price type
                        array(
                          'id'          => 'price_type',
                          'type'        => 'select',
                          'title'       => esc_html__( 'Price Type', 'whols'),
                          'options'     => array(
                            'flat_rate'     => esc_html__( 'Flat Rate', 'whols' ),
                            'percent'       => esc_html__( 'Percent', 'whols' ),
                          ),
                        ),

                        // price value
                        array(
                          'id'    => 'price_value',
                          'type'  => 'text',
                          'title' => esc_html__( 'Price Value', 'whols' ),
                          'attributes'  => array(
                            'type'      => 'number',
                          ),
                        ),

                        // minimum quantity
                        array(
                          'id'    => 'minimum_quantity',
                          'type'  => 'text',
                          'title' => esc_html__( 'Minimum Quantity', 'whols' ),
                          'attributes'  => array(
                            'type'      => 'number',
                          ),
                        ),
                    ),
                  'class' => 'whols_pro whols_price_type_1_properties'
                )
            );
        } // endif single role  

        $prefix = 'whols_product_category_meta';
        
         // Create taxonomy meta option wrapper
         \CSF::createTaxonomyOptions( $prefix, array(
           'taxonomy'  => 'product_cat',
           'data_type' => 'serialize', // The type of the database save options. `serialize` or `unserialize`
         ) );

         // Create a section & fields
         \CSF::createSection( $prefix, array(
          'fields' => !empty($fields) ? $fields : array()
         ) );  
    }
}
