<?php
namespace Whols\Vue_Settings;

class Settings_Defaults {
    /**
     * Get default values from the schema
     *
     * @return array Array of default values
     */
    public static function get_defaults() {
        $fields_schema = Settings_Schema::get_schema();

        return self::extract_defaults( $fields_schema );
    }

    /**
     * Recursively extract default values from fields
     *
     * @param array $fields Field configurations
     * @return array Extracted default values
     */
    public static function extract_defaults($fields = array()) {
        $defaults = [];

        foreach ($fields as $field_name => $field_config) {
            if( $field_name == 'price_type_2_properties' ) {
                $defaults[$field_name] = self::get_price_type2_defaults();
                continue;
            }

            // Handle field type fieldset
            if( $field_config['type'] == 'fieldset' ) {

                $defaults[$field_name] = self::extract_defaults( $field_config['fields'] );

            } else if( isset($field_config['default']) ) {

                $defaults[$field_name] = $field_config['default'];

            }
        }

        return $defaults;
    }

    public static function get_price_type2_defaults(){
        // Loop through the roles
        $defaults = array(
            'foo' => 'bar' // Fixed: Due to exposing empty array causing issue fresh install
        );

        // whols_roles_dropdown_options()
        $roles = whols_roles_dropdown_options();

        if( !empty( $roles ) && is_array( $roles ) ) {
            foreach ( $roles as $role_slug => $role_label ) {
                $defaults[$role_slug . '__enable_this_pricing'] = '0';
                $defaults[$role_slug . '__price_type'] = 'flat_rate';
                $defaults[$role_slug . '__price_value'] = '';
                $defaults[$role_slug . '__minimum_quantity'] = '';
            }
        }

        return $defaults;
    }
}
