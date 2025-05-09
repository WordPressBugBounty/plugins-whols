<?php
namespace Whols\Vue_Settings;
use const Whols\PL_URL;
use const Whols\PL_PATH;
use const Whols\PL_VERSION;

class Settings_Page {
    public $version;

    public $plugin_screens = array(
        'toplevel_page_whols-admin'
    );

    private static $_instance = null;
    /**
     * Get Instance
     */
    public static function instance(){
        if( is_null( self::$_instance ) ){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     */
    public function __construct() {
        // Version with time for cache busting
		if( defined( 'WP_DEBUG' ) && WP_DEBUG ){
			$this->version = time();
		} else {
			$this->version = PL_VERSION;
		}

        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));

        // Add hook to remove admin notices on specific pages
        add_action('admin_head', array($this, 'remove_admin_notices'), 1);
        
        // Intentionally load the editor in the footer to override the css loaded in the header
        add_action('admin_footer', function(){
            $current_screen = get_current_screen();

            if (!in_array($current_screen->id, $this->plugin_screens)) {
                return;
            }

            wp_enqueue_editor();
        });
    }

    public function remove_admin_notices() {
        $current_screen = get_current_screen();

        // Define the screens
        $hide_notices_screens = array(
            'toplevel_page_whols-admin'
        );

        // Check if current screen should have notices removed
        if (in_array($current_screen->id, $hide_notices_screens)) {
            // Remove all notices
            remove_all_actions('admin_notices');
            remove_all_actions('all_admin_notices');
        }
    }

    /**
     * Enqueue required scripts and styles
     */
    public function enqueue_scripts($hook) {
        if (!in_array($hook, $this->plugin_screens)) {
            return;
        }

        $is_dev = isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'plugindev.test';

        if ($is_dev && $this->is_vite_running()) {
            // Development mode - load from Vite dev server
            wp_enqueue_script(
                'whols-vue-settings-vite-client',
                'http://localhost:5173/@vite/client',
                array(),
                null,
                true
            );

            add_filter('script_loader_tag', function($tag, $handle, $src) {
                // For cache busting
                $src = $src . '?v=' . $this->version;

                if ($handle === 'whols-vue-settings') {
                    return '<script type="module" src="' . esc_url($src) . '"></script>';
                }
                return $tag;
            }, 10, 3);

            wp_enqueue_script(
                'whols-vue-settings',
                'http://localhost:5173/src/vue-settings/main.js' . '?v=' . $this->version,
                array('whols-vue-settings-vite-client'),
                null,
                true
            );
        } else {
            // Production mode - load built files
            // CSS
            wp_enqueue_style(
                'whols-vue-settings-style',
                PL_URL . '/build/vue-settings/style.css',
                array(),
                $this->version,
                'all'
            );

            // JS
            wp_enqueue_script(
                'whols-vue-settings',
                PL_URL . '/build/vue-settings/main.js',
                array(),
                $this->version,
                true
            );

            // For cache busting
            // $this->enqueue_scripts_from_manifest(); // Updated the vite build process, no longer needed

            add_filter('script_loader_tag', function($tag, $handle, $src) {
                if ($handle === 'whols-vue-settings') {
                    return '<script type="module" src="' . esc_url($src) . '"></script>';
                }
                return $tag;
            }, 10, 3);
        }

        $menu = whols_include_plugin_file( 'includes/vue-settings/menu.php' );

        // Localize script with nonce and API info
        wp_localize_script('whols-vue-settings', 'wholsSettings', array(
            'nonce'       => wp_create_nonce('wp_rest'),
            'apiBaseURL'  => esc_url_raw(rest_url()),
            'pluginVersion' => PL_VERSION,
            'apiEndpoint' => 'whols/v1/settings',
            'rolesApiEndpoint' => 'whols/v1/wholesaler-roles',
            'proAdvInfo' => array(
                'purchaseURL' => 'https://wpwhols.com/pricing/?utm_source=wprepo&utm_medium=freeplugin&utm_campaign=purchasepro',
                'message' => __('Our free version is great, but it doesn\'t have all our advanced features. The best way to unlock all of the features in our plugin is by purchasing the pro version.', 'whols'),
            ),
            'adminUrl' => admin_url(),
            'supportUrl' => 'https://hasthemes.com/contact-us/',
            'docsUrl' => 'https://wpwhols.com/docs/',
            'proUrl' => 'https://wpwhols.com/pricing/',

            // There is some dynamic defaults so manage it from one place here
            'defaultSettings' => Settings_Defaults::get_defaults(),

            // Translations
            'i18n' => array(
                'save' => esc_html__('General Settings', 'whols'),
                'loading' => esc_html__('Loading...', 'whols'),
                'error' => esc_html__('Error', 'whols'),
            ),

            // Plugins Settings
            'globalSettings' => array(
                'show_wholesale_price_for' => whols_get_option('show_wholesale_price_for'),
                'currency_symbol' => get_woocommerce_currency_symbol()
            ),

            'menu' => $menu
        ));

        wp_localize_script('whols-vue-settings', 'wholsSettingsSchema', Settings_Schema::get_schema());
    }

    /**
     * Check if Vite dev server is running
     */
    private function is_vite_running() {
        $handle = curl_init('http://localhost:5173');
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_NOBODY, true);

        curl_exec($handle);
        $error = curl_errno($handle);
        curl_close($handle);

        return !$error;
    }

    /**
     * Render the Vue app container
     */
    public function render_app() {
        ?>
        <div class="wrap">
            <div id="whols-vue-settings-app"></div>
        </div>
        <?php
    }
}
