<?php

namespace Disclaimify;

/**
 * Exit if accessed directly
 * */
if (!defined('ABSPATH')) {
    exit;
}

/**
* Shortcode
*/
final class DisclaimifyShortcode {

    private static $_instance = null;

    /**
     * Instance
     * 
     * Initializes a singleton instance
     * 
     * @return self class
     */
    static function instance() {
        if (is_null( self::$_instance )) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Base class constructor
     * @return void
     */
    private function __construct() {
        add_shortcode('disclaimify', [$this, 'disclaimify_shortcode_callback']);
    }

    function disclaimify_shortcode_callback($atts) {
        wp_enqueue_style('disclaimify-style');

        $atts = shortcode_atts( 
            [
                "id" => null
            ],
            $atts,
        );
        extract($atts);
        $disclaimify = get_post($id);
        if($disclaimify) {
            return wp_kses_post($disclaimify->post_content);
        }
        return "";
    }

}

/**
 * Initialize Shortcode Class
 */
DisclaimifyShortcode::instance();
