<?php
/**
 * Whols Frontend
 *
 * @since 1.0.0
 */

namespace Whols;

/**
 * Frontend class.
 */
class Frontend {

    /**
     * Frontend constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        // Admin assets hook into action.
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );

        // Filter the content to add the [whols_registration_form] shortcode to the assigned page
        add_filter( 'the_content', array( $this, 'filter_the_content' ) );

    }

    /**
     * Enqueue frontend assets
     *
     * @since 1.0.0
     */
    public function enqueue_frontend_assets() {
        // Scripts
        $suffix = \Automattic\Jetpack\Constants::is_true( 'SCRIPT_DEBUG' ) ? '' : '.min';
        wp_enqueue_script( 'serializejson', WC()->plugin_url() . '/assets/js/jquery-serializejson/jquery.serializejson' . $suffix . '.js', array( 'jquery' ), '2.8.1' );

        wp_enqueue_style( 'whols-style', WHOLS_ASSETS . '/css/style.css', null, WHOLS_VERSION );
    }


    /**
     * If the content doesn't have the shortcode, add it to the end of the content
     * 
     * @param content The content of the post.
     * 
     * @return The content of the page, with the shortcode appended to the end.
     */
    public function filter_the_content( $content ){
        global $post;

        if( $post && $post->ID == whols_get_option('registration_page') && !has_shortcode($content, 'whols_registration_form') ){
            $shortcode = do_shortcode('[whols_registration_form]');
            return $content . $shortcode;
        }

        return $content;
    }
}