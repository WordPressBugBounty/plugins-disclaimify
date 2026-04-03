<?php

namespace Disclaimify;

/**
 * Exit if accessed directly
 * */
if (!defined('ABSPATH')) {
    exit;
}

/**
* Disclaimify Base
*/
final class DisclaimifyBase {

    private static $_instance = null;

    /**
     * Instance
     * Initializes a singleton instance
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
        add_action('init', [$this, 'i18n']);
        add_action('init', [$this, 'init']);
        add_action('init', [$this, 'check_theme_support'], 9);
    }

    /**
     * i18n
     * Load text domain
     * @return void
     */
    function i18n() {
        load_plugin_textdomain('disclaimify', false, dirname(DISCLAIMIFY_PLUGIN_BASE) . '/languages');
    }

    /**
     * init
     * All necessary files
     * @return void
     */
    function init() {
        require_once(DISCLAIMIFY_PL_PATH . 'includes/helper-functions.php');
        require_once(DISCLAIMIFY_PL_PATH . 'includes/assets.php');
        require_once(DISCLAIMIFY_PL_PATH . 'includes/post-type.php');
        require_once(DISCLAIMIFY_PL_PATH . 'includes/metabox.php');
        require_once(DISCLAIMIFY_PL_PATH . 'includes/shortcode.php');
        require_once(DISCLAIMIFY_PL_PATH . 'includes/frontend.php');
    }

    /**
     * Check theme support to show disclaimify on post meta
     */
    function check_theme_support() {
        $supported_themes = ["Blocksy", "Blocksy Child"];
        $current_theme = wp_get_theme();
        $current_theme_name = $current_theme->get('Name');
        if(in_array($current_theme_name, $supported_themes)) {
            add_filter('disclaimify_filter_for_meta_support', function($options) {
                $options[] = [
                    "id" => "before_post_meta",
                    "type" => "checkbox",
                    "label" => __("Prepend to post meta", "disclaimify"),
                    "name" => 'show_disclaimify_at',
                    "checked" => null
                ];
                $options[] = [
                    "id" => "after_post_meta",
                    "type" => "checkbox",
                    "label" => __("Append to post meta", "disclaimify"),
                    "name" => 'show_disclaimify_at',
                    "checked" => null
                ];
                return $options;
            });
            add_filter('disclaimify_filter_for_labels', function($options) {
                $options[] = [
                    "id" => "meta_label",
                    "type" => "text",
                    "label" => __('Disclaimer', 'disclaimify'),
                    "name" => "meta_label",
                    "placeholder" => __("Default: Disclaimer", 'disclaimify'),
                ];
                return $options;
            });
        }
    }

}

/**
 * Initialize Base Class
 */
DisclaimifyBase::instance();
