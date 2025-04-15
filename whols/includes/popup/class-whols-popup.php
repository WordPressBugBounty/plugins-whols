<?php
/**
 * Whols_Popup Class
 * 
 * A reusable popup component for WordPress and WooCommerce
 * 
 * Example of using the reusable popup for product variations:
 * 
 * // Register an example popup type
 * whols_popup()->register_popup('example', array(
 *     'title'           => __('Example Popup', 'whols'),
 *     'class'           => 'whols-example-popup',
 *     'width'           => 'medium',
 *     'ajax_action'     => 'load_example_popup',
 *     'ajax_callback'   => array('Whols_Popup', 'load_example_popup'),
 *     'close_on_overlay' => true,
 * ));
 * 
 * // Trigger the popup
echo whols_popup()->get_popup_trigger('example');
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Whols_Popup {
    
    /**
     * Instance of this class.
     *
     * @var object
     */
    protected static $instance = null;
    
    /**
     * Popup ID prefix
     */
    private $popup_prefix = 'whols-popup';
    
    /**
     * Registered popups
     */
    private $registered_popups = array();
    
    /**
     * Return an instance of this class.
     *
     * @return object A single instance of this class.
     */
    public static function get_instance() {
        if (null == self::$instance) {
            self::$instance = new self;
        }
        
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Add popup container to footer
        add_action('wp_footer', array($this, 'add_popup_container'));
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        // Enqueue the core popup scripts and styles
        wp_enqueue_script(
            'whols-popup',
            plugin_dir_url(__FILE__) . 'js/whols-popup.js',
            array('jquery'),
            '1.0.0',
            true
        );
        
        // Localize script with AJAX URL
        wp_localize_script('whols-popup', 'whols_popup', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('whols-popup-nonce'),
        ));
        
        // Custom styles
        wp_enqueue_style(
            'whols-popup-style',
            plugin_dir_url(__FILE__) . 'css/whols-popup.css',
            array(),
            '1.0.0'
        );
    }
    
    /**
     * Register a new popup
     *
     * @param string $type Popup name identifier
     * @param array $args Popup arguments
     * @return bool
     */
    public function register_popup($type, $args = array()) {
        if (empty($type)) {
            return false;
        }
        
        $defaults = array(
            'title'          => '',
            'class'          => '',
            'width'          => 'medium',  // small, medium, large, or specific px value
            'height'         => 'auto',    // auto or specific px value
            'ajax_action'    => '',        // AJAX action to load content
            'ajax_callback'  => '',        // PHP callback function to handle AJAX request
            'close_on_overlay' => true,
            'template'       => '',        // Template file to use
        );
        
        $args = wp_parse_args($args, $defaults);
        
        // Register the popup type
        $this->registered_popups[$type] = $args;
        
        // Register AJAX handler if provided
        if (!empty($args['ajax_action']) && !empty($args['ajax_callback'])) {
            add_action('wp_ajax_' . $args['ajax_action'], $args['ajax_callback']);
            add_action('wp_ajax_nopriv_' . $args['ajax_action'], $args['ajax_callback']);
        }
        
        return true;
    }
    
    /**
     * Get popup ID
     *
     * @param string $type Popup name
     * @return string
     */
    public function get_popup_id($type) {
        return $this->popup_prefix . '-' . sanitize_title($type);
    }
    
    /**
     * Get popup settings
     *
     * @param string $type Popup name
     * @return array|false
     */
    public function get_popup_settings($type) {
        if (isset($this->registered_popups[$type])) {
            return $this->registered_popups[$type];
        }
        
        return false;
    }
    
    /**
     * Add popup container to footer
     */
    public function add_popup_container() {
        ?>
        <div id="whols-popup-overlay" class="whols-popup-overlay">
            <?php foreach ($this->registered_popups as $type => $args) : 
                $popup_id = $this->get_popup_id($type);
                $popup_class = !empty($args['class']) ? $args['class'] : '';
                $popup_width = $args['width'];
                $width_style = '';
                
                if ($popup_width !== 'small' && $popup_width !== 'medium' && $popup_width !== 'large') {
                    $width_style = 'max-width: ' . esc_attr($popup_width) . ';';
                }
            ?>
            <div id="<?php echo esc_attr($popup_id); ?>" 
                 class="whols-popup <?php echo esc_attr($popup_class); ?> whols-popup--size-<?php echo esc_attr($args['width']); ?>"
                 style="<?php echo esc_attr($width_style); ?>"
                 data-popup-name="<?php echo esc_attr($type); ?>"
                 data-close-overlay="<?php echo esc_attr($args['close_on_overlay'] ? 'true' : 'false'); ?>">
                
                <div class="whols-popup__header">
                    <h2 id="<?php echo esc_attr($popup_id); ?>-title" class="whols-popup__title"><?php echo esc_html($args['title']); ?></h2>
                    <span class="whols-popup__close">&times;</span>
                </div>
                
                <div id="<?php echo esc_attr($popup_id); ?>-content" class="whols-popup__content"></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
    
    /**
     * Generate popup trigger HTML
     *
     * @param string $type Popup name
     * @param array $args Button/trigger arguments
     * @param array $data Custom data attributes
     * @return string
     */
    public function get_popup_trigger($type, $args = array(), $data = array()) {
        if (!isset($this->registered_popups[$type])) {
            return '';
        }
        
        $defaults = array(
            'text'      => __('Open', 'whols'),
            'class'     => 'button',
            'tag'       => 'a',
            'href'      => '#',
            'id'        => '',
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $data_attributes = '';
        $data['popup-name'] = $type;
        
        foreach ($data as $key => $value) {
            $data_attributes .= ' data-' . sanitize_html_class($key) . '="' . esc_attr($value) . '"';
        }
        
        $tag = tag_escape($args['tag']);
        $href = $tag === 'a' ? ' href="' . esc_url($args['href']) . '"' : '';
        $id = !empty($args['id']) ? ' id="' . esc_attr($args['id']) . '"' : '';
        
        return sprintf(
            '<%1$s%2$s class="%3$s" data-role="popup-trigger"%4$s>%5$s</%1$s>',
            $tag,
            $href,
            esc_attr($args['class']),
            $data_attributes,
            esc_html($args['text'])
        );
    }
}

// Initialize
function whols_popup() {
    return Whols_Popup::get_instance();
}
