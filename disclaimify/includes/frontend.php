<?php

namespace Disclaimify;

/**
 * Exit if accessed directly
 * */
if (!defined('ABSPATH')) {
    exit;
}

/**
* Frontend
*/
final class DisclaimifyFrontend {

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
        add_filter( 'the_content', [$this, 'content_filter'], 10 );

        add_filter("blocksy:post-meta:items", [$this, "blocksy_post_meta"]);
    }

    function blocksy_post_meta($to_return) {
        if ( is_singular() ) {
            $post_id = get_queried_object_id();
            $disclaimify_id = disclaimify_check_rules($post_id);
            $disclaimify_meta = get_post_meta($disclaimify_id, 'disclaimify_settings', true);
            $show_disclaimify_at = isset($disclaimify_meta['show_disclaimify_at']) ? $disclaimify_meta['show_disclaimify_at'] : null;
            $before_post_meta = isset($show_disclaimify_at['before_post_meta']) ? $show_disclaimify_at['before_post_meta'] : null;
            $after_post_meta = isset($show_disclaimify_at['after_post_meta']) ? $show_disclaimify_at['after_post_meta'] : null;
            $post_meta_label = isset($disclaimify_meta['meta_label']) && !empty($disclaimify_meta['meta_label']) ? $disclaimify_meta['meta_label'] : __('Disclaimify', 'disclaimify');
            if(get_the_ID() === $post_id) {
                if(isset($before_post_meta) && $before_post_meta === 'on') {
                    $to_return = "<li class='meta-disclaimify'><span>". esc_html($post_meta_label) ."</span><div class='disclaimify-popover' style='opacity: 0; visibility: hidden;'>". do_shortcode("[disclaimify id='{$disclaimify_id}']") ."</div></li>" . $to_return;
                }
                if(isset($after_post_meta) && $after_post_meta === 'on') {
                    $to_return = $to_return . "<li class='meta-disclaimify'><span>". esc_html($post_meta_label) ."</span><div class='disclaimify-popover' style='opacity: 0; visibility: hidden;'>". do_shortcode("[disclaimify id='{$disclaimify_id}']") ."</div></li>";
                }
            }
        }
        return $to_return;
    }

    function content_filter($content) {
        if ( is_singular() ) {
            $post_id = get_queried_object_id();
            $disclaimify_id = disclaimify_check_rules($post_id);
            $disclaimify_meta = get_post_meta($disclaimify_id, 'disclaimify_settings', true);
            $show_disclaimify_at = isset($disclaimify_meta['show_disclaimify_at']) ? $disclaimify_meta['show_disclaimify_at'] : null;
            $before_post_content = isset($show_disclaimify_at['before_post_content']) ? $show_disclaimify_at['before_post_content'] : null;
            $after_post_content = isset($show_disclaimify_at['after_post_content']) ? $show_disclaimify_at['after_post_content'] : null;
            if(get_the_ID() === $post_id) {
                if(isset($before_post_content) && $before_post_content === 'on') {
                    $content = do_shortcode("[disclaimify id='{$disclaimify_id}']") . $content;
                }
                if(isset($after_post_content) && $after_post_content === 'on') {
                    $content =  $content . do_shortcode("[disclaimify id='{$disclaimify_id}']");
                }
            }
        }
        return $content;
    }

}

/**
 * Initialize Frontend Class
 */
DisclaimifyFrontend::instance();
