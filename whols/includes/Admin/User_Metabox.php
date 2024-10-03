<?php
/**
 * User Metabox
 *
 * @since 1.0.5
 */

namespace Whols\Admin;

/**
 * User Metabox
 */
class User_Metabox {

    /**
     * Metabox constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        // Generate additional custom fields for user
        add_action( 'show_user_profile', array( $this, 'additional_fields_for_user' ) );
        add_action( 'edit_user_profile', array( $this, 'additional_fields_for_user' ) );

        // Save fields
        add_action( 'personal_options_update', array( $this, 'save_additional_fields' ) );
        add_action( 'edit_user_profile_update', array( $this, 'save_additional_fields' ) );
    }

    /**
     * Generate custom fields for user
     */
    public function additional_fields_for_user() {
        if( empty($_GET['user_id']) ){
            return;
        }

        $user_id = absint($_GET['user_id']);
        $user_meta = get_user_meta( $user_id, '', false );
        ?>
        <h3><?php echo esc_html__("Whols - Additional Fields", "whols"); ?></h3>

        <table class="form-table whols-additional-fields">

            <?php
            $has_additional_fields = false;
            foreach($user_meta as $key => $value){
                if( str_starts_with($key, '_whols_') ){
                    $key        = str_replace('_whols_', '', $key);
                    $fields     = whols_get_registration_fields();
                    $field_info = !empty($fields[$key]) ? $fields[$key] : array();

                    if($field_info){
                        $has_additional_fields = true;
                        // add input class
                        $field_info['input_class'] = array('regular-text');
                        ?>
                        <tr>
                            <th><label for="<?php echo esc_attr($key) ?>"><?php echo esc_html($field_info['label']); ?></label></th>
                            <td>
                                <?php whols_form_field( $key, $field_info, $value[0] ); ?>
                            </td>
                        </tr>
                        <?php
                    }
                }
            } ?>

            <?php
                if( !$has_additional_fields ){
                    echo esc_html__('No additional fields found!', 'whols');
                }
            ?>
        </table>

    <?php }

    /**
     * Save custom fields
     */
    public function save_additional_fields( $user_id ) {
        $posted_data = wp_unslash($_POST);

        if ( empty( $posted_data['_wpnonce'] ) || ! wp_verify_nonce( $posted_data['_wpnonce'], 'update-user_' . $user_id ) ) {
            return;
        }
        
        if ( !current_user_can( 'edit_user', $user_id ) ) { 
            return false; 
        }

        $user_meta = get_user_meta( $user_id, '', false );
        foreach($user_meta as $key => $value){
            if( str_starts_with($key, '_whols_') ){
                update_user_meta( $user_id, $key, $posted_data[$key] );
            }
        }
    }
}