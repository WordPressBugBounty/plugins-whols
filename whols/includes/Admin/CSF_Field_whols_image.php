<?php
/**
 * Field: whols_image
 */
if( ! class_exists( 'CSF_Field_whols_image' ) ) {
    class CSF_Field_whols_image extends CSF_Fields {

        public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
            parent::__construct( $field, $value, $unique, $where, $parent );
        }

        public function render() {
            echo $this->field_before();
            ?>

            <div class="whols_image_wrapper">
                <img src="<?php echo esc_url($this->field['url']) ?>" alt="">
                <a href="#"><?php echo esc_html__('Get PRO to Unlock', 'whols') ?></a>
            </div>

            <?php
            echo $this->field_after();
        }

        // Usage
        // array(
        //     'id'         => 'enable_wholesale_only_categories',
        //     'type'       => 'whols_image',
        //     'url'        => WHOLS_ASSETS. '/images/wholesale-only-categories.jpg'
        // ),
    }
  }