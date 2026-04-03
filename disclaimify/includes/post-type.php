<?php

/**
 * Exit if accessed directly
 * */
if (!defined('ABSPATH')) {
    exit;
}

/**
* Custom Post Type
*/
class DisclaimifyPostType {

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
        add_action('init', [$this, 'disclaimify_post_type'], 11);
        add_filter('manage_disclaimify_posts_columns', [$this, 'disclaimify_posts_columns']);
        add_filter('manage_disclaimify_posts_custom_column', [$this, 'populate_disclaimify_posts_column'], 10, 2);
        add_filter('manage_edit-disclaimify_sortable_columns', [$this, 'disclaimify_columns_sortable']);
    }

    /**
     * Register Disclaimify Post Type
     */
    function disclaimify_post_type() {

        $labels = [
            'name'                  => _x( 'Disclaimifys', 'Post Type General Name', 'disclaimify' ),
            'singular_name'         => _x( 'Disclaimify', 'Post Type Singular Name', 'disclaimify' ),
            'menu_name'             => __( 'Disclaimify', 'disclaimify' ),
            'name_admin_bar'        => __( 'Disclaimify', 'disclaimify' ),
            'archives'              => __( 'Item Archives', 'disclaimify' ),
            'attributes'            => __( 'Item Attributes', 'disclaimify' ),
            'parent_item_colon'     => __( 'Parent Item:', 'disclaimify' ),
            'all_items'             => __( 'All Items', 'disclaimify' ),
            'add_new_item'          => __( 'Add New Item', 'disclaimify' ),
            'add_new'               => __( 'Add New', 'disclaimify' ),
            'new_item'              => __( 'New Item', 'disclaimify' ),
            'edit_item'             => __( 'Edit Item', 'disclaimify' ),
            'update_item'           => __( 'Update Item', 'disclaimify' ),
            'view_item'             => __( 'View Item', 'disclaimify' ),
            'view_items'            => __( 'View Items', 'disclaimify' ),
            'search_items'          => __( 'Search Item', 'disclaimify' ),
            'not_found'             => __( 'Not found', 'disclaimify' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'disclaimify' ),
            'featured_image'        => __( 'Featured Image', 'disclaimify' ),
            'set_featured_image'    => __( 'Set featured image', 'disclaimify' ),
            'remove_featured_image' => __( 'Remove featured image', 'disclaimify' ),
            'use_featured_image'    => __( 'Use as featured image', 'disclaimify' ),
            'insert_into_item'      => __( 'Insert into item', 'disclaimify' ),
            'uploaded_to_this_item' => __( 'Uploaded to this item', 'disclaimify' ),
            'items_list'            => __( 'Items list', 'disclaimify' ),
            'items_list_navigation' => __( 'Items list navigation', 'disclaimify' ),
            'filter_items_list'     => __( 'Filter items list', 'disclaimify' ),
        ];
        $args = [
            'label'                 => __( 'Disclaimify', 'disclaimify' ),
            'description'           => __( 'Disclaimify Description', 'disclaimify' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-text-page',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
        ];
        register_post_type( 'disclaimify', $args );

    }

    /**
     * Add Shortcode column on disclaimify table
     */
    function disclaimify_posts_columns($columns) {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __('Title', 'disclaimify'),
            'shortcode' => __('Shortcode', 'disclaimify'),
            'date' => __('Date', 'disclaimify')
        );
        return $columns;
    }

    /**
     * Add Shortcode content on disclaimify table column
     */
    function populate_disclaimify_posts_column($column, $post_id) {
        if ($column === 'shortcode') {
            echo "[disclaimify id='{$post_id}']";
        }
    }
    
    /**
     * Make Shortcode column sortable
     */
    function disclaimify_columns_sortable($columns) {
        $columns['shortcode'] = 'shortcode';
        return $columns;
    }

}
DisclaimifyPostType::instance();
