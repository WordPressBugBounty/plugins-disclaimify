<?php

namespace Disclaimify;

/**
 * Exit if accessed directly
 * */
if (!defined('ABSPATH')) {
    exit;
}

/**
* Assets
*/
final class DisclaimifyAssets {

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
        add_action('admin_enqueue_scripts', [$this, 'admin_scripts']);
        add_action('wp_enqueue_scripts', [$this, 'scripts']);
    }

    /**
     * Frontend Scripts
     * @return void
     */
    function scripts($hook) {
        wp_register_style('disclaimify-style', DISCLAIMIFY_PL_URL . 'assets/css/style-frontend.css', [], DISCLAIMIFY_VERSION);
    }

    /**
     * Admin Scripts
     * @return void
     */
    function admin_scripts($hook) {
        if(('post.php' === $hook || 'post-new.php' === $hook) && 'disclaimify' === get_post_type()) {
            wp_enqueue_style('disclaimify-style', DISCLAIMIFY_PL_URL . 'assets/css/style.css', [], DISCLAIMIFY_VERSION);
            wp_enqueue_script('disclaimify-main', DISCLAIMIFY_PL_URL . 'assets/js/main.js', ['jquery'], DISCLAIMIFY_VERSION, true);
        }
    }

}

/**
 * Initialize Assets Class
 */
DisclaimifyAssets::instance();
