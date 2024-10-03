<?php
if( ! class_exists( 'CSF_Field_registration_details' ) ) {
    class CSF_Field_registration_details extends CSF_Fields {
        public $post_id;
        public $wholesale_user_id;

        public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
            parent::__construct( $field, $value, $unique, $where, $parent );

            $this->post_id = isset( $_GET['post'] ) ? absint($_GET['post']) : '';
            $this->wholesale_user_id = $this->get_wholesale_user_id();
        }

        public function render() {
            echo $this->field_before();
            $fields = whols_get_option('registration_fields');

            $name       = '';
            $username   = '';
            $email      = '';

            if( !empty($this->wholesale_user_id) ){
                $user_info = get_userdata( $this->wholesale_user_id );
                $name      = !empty($user_info->display_name) ? $user_info->display_name : '';
                $username  = !empty($user_info->user_login) ? $user_info->user_login : '';
                $email     = !empty($user_info->user_email) ? $user_info->user_email : '';
            }

            if( empty( $fields ) ){
                return;
            }
            ?>
            <table>
                <tr>
                    <td><strong><?php echo __('Name', 'whols') ?></strong></td>
                    <td><?php echo esc_html($name) ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo __('Username', 'whols') ?></strong></td>
                    <td><?php echo esc_html($username) ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo __('Email', 'whols') ?></strong></td>
                    <td>
                        <a href="mailto:<?php echo esc_attr($email) ?>"><?php echo esc_html($email) ?></a>
                    </td>
                </tr>

                <?php foreach($fields as $field):
                    $field_type = '';
                    $field_label = '';
                    $field_value = '';

                    $field_label = !empty($field['label']) ? $field['label'] : '';

                    // 4 types of fields are available, default(wp), custom, whols, billing
                    if( !empty($field['field']) && $field['field'] == 'custom' ){
                        $field_type = 'custom';

                        // Field label and value
                        $field_label = empty($field['label']) && !empty($field['custom_field_name']) ? $field['custom_field_name'] : $field_label;
                        $field_value = get_user_meta( $this->wholesale_user_id, '_whols_'. $field['custom_field_name'], true );
                    } elseif( !empty($field['field']) && str_contains($field['field'], 'billing_') ){
                        $field_type = 'billing';

                        // Field label and value
                        $field_label = empty($field['label']) && !empty($field['field']) ? $field['field'] : $field_label;
                        $field_value = get_user_meta( $this->wholesale_user_id, $field['field'], true );

                        // If country field, get country name
                        if( $field['field'] == 'billing_country' ){
                            $countries = WC()->countries->get_countries();
                            $field_value = !empty($countries[$field_value]) ? $countries[$field_value] : $field_value;
                        }
                    } elseif( !empty($field['field']) && str_contains($field['field'], 'whols_') ){
                        $field_type = 'whols';

                        // Field label and value
                        $field_label = empty($field['label']) && !empty($field['field']) ? $field['field'] : $field_label;

                        $meta_value = get_post_meta( $this->post_id, 'whols_user_request_meta', true );
                        $field_value = !empty($meta_value['assign_role']) ? $meta_value['assign_role'] : '';
                    }

                    if( $field_type == '' ){ // Default
                        continue;
                    }
                ?>
                <tr>
                    <td><strong><?php echo esc_html($field_label) ?></strong></td>
                    <td><?php echo esc_html($field_value) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php
            echo $this->field_after();

        }

        public function get_wholesale_user_id(){
            $meta = get_post_meta( $this->post_id, 'whols_user_request_meta', true );
            return !empty($meta['user_id']) ? $meta['user_id'] : '';
        }

    }
}