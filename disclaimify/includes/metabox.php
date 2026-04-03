<?php

/**
 * Exit if accessed directly
 * */
if (!defined('ABSPATH')) {
    exit;
}

/**
* Custom Post Type Metabox
*/
class DisclaimifyMetaBox {

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

	private $configs;
	private $sections;
    
	public function __construct() {
        /**
         * Configs for the metabox
         */        
        $this->configs = [
            [
                "id" => "show_disclaimify_at",
                "title" => __("Where to Show?", "disclaimify"),
                "callback" => [ $this, 'disclaimify_metabox_callback' ],
                "context" => "normal",
                "priority" => "default",
                "options" => apply_filters('disclaimify_filter_for_meta_support', [
                    [
                        "id" => "before_post_content",
                        "type" => "checkbox",
                        "label" => __("Before post content", "disclaimify"),
                        "name" => 'show_disclaimify_at',
                        "checked" => true
                    ],
                    [
                        "id" => "after_post_content",
                        "type" => "checkbox",
                        "label" => __("After post content", "disclaimify"),
                        "name" => 'show_disclaimify_at',
                    ],
                ])
            ],
            [
                "id" => "select_post_type",
                "title" => __("Select Post Type", "disclaimify"),
                "callback" => [ $this, 'disclaimify_metabox_callback' ],
                "context" => "normal",
                "priority" => "default",
                "options" => disclaimify_get_post_type_for_metabox()
            ],
            [
                "id" => "include_condition",
                "title" => __("Include Condition", "disclaimify"),
                "callback" => [ $this, 'disclaimify_metabox_callback' ],
                "context" => "normal",
                "priority" => "default",
                "options" => [
                    [
                        "id" => 'show_on_all',
                        "type" => "radio",
                        "label" => __('Show on all', 'disclaimify'),
                        "name" => "include_condition",
                        "checked" => true,
                    ],
                    [
                        "id" => 'show_on_taxonomies',
                        "type" => "radio",
                        "label" => __('Only show on selected taxonomies (Category / Tags )', 'disclaimify'),
                        "name" => "include_condition",
                        "checked" => null,
                    ],
                    [
                        "id" => 'show_on_posts',
                        "type" => "radio",
                        "label" => __('Only show on selected post(s)', 'disclaimify'),
                        "name" => "include_condition",
                        "checked" => null,
                    ]
                ],
                "dep_options" => [
                    [
                        "id" => "include_taxonomies",
                        "type" => "text",
                        "label" => __('Please insert taxonomy slug(s) below', 'disclaimify'),
                        "placeholder" => __('Each taxonomy slug must be separated by comma and space - Ex: slug-1, slug-2, slug-3', 'disclaimify'),
                        "name" => "include_taxonomies",
                        "dep" => [
                            "id" => "show_on_taxonomies",
                            "name" => "include_condition"
                        ],
                    ],
                    [
                        "id" => "include_posts",
                        "type" => "text",
                        "label" => __('Please insert post ID(s) below', 'disclaimify'),
                        "placeholder" => __('Each Post ID must be separated by comma and space - Ex: 1, 2, 3', 'disclaimify'),
                        "name" => "include_posts",
                        "dep" => [
                            "id" => "show_on_posts",
                            "name" => "include_condition"
                        ],
                    ],
                ]
            ],
            [
                "id" => "exclude_condition",
                "title" => __("Exclude Condition", "disclaimify"),
                "callback" => [ $this, 'disclaimify_metabox_callback' ],
                "context" => "normal",
                "priority" => "default",
                "options" => [
                    [
                        "id" => 'disable',
                        "type" => "radio",
                        "label" => __('Disable', 'disclaimify'),
                        "name" => "exclude_condition",
                        "checked" => true,
                    ],
                    [
                        "id" => 'hide_on_taxonomies',
                        "type" => "radio",
                        "label" => __('Only hide on selected taxonomies (Category / Tags )', 'disclaimify'),
                        "name" => "exclude_condition",
                        "checked" => null,
                    ],
                    [
                        "id" => 'hide_on_posts',
                        "type" => "radio",
                        "label" => __('Only hide on selected post(s)', 'disclaimify'),
                        "name" => "exclude_condition",
                        "checked" => null,
                    ]
                ],
                "dep_options" => [
                    [
                        "id" => "exclude_taxonomies",
                        "type" => "text",
                        "label" => __('Please insert taxonomy slug(s) below', 'disclaimify'),
                        "placeholder" => __('Each taxonomy slug must be separated by comma and space - Ex: slug-1, slug-2, slug-3', 'disclaimify'),
                        "name" => "exclude_taxonomies",
                        "dep" => [
                            "id" => "hide_on_taxonomies",
                            "name" => "exclude_condition"
                        ],
                    ],
                    [
                        "id" => "exclude_posts",
                        "type" => "text",
                        "label" => __('Please insert post ID(s) below', 'disclaimify'),
                        "placeholder" => __('Each Post ID must be separated by comma and space - Ex: 1, 2, 3', 'disclaimify'),
                        "name" => "exclude_posts",
                        "dep" => [
                            "id" => "hide_on_posts",
                            "name" => "exclude_condition"
                        ],
                    ],
                ]
                ],
            [
                "id" => "priority",
                "title" => __("Priority", "disclaimify"),
                "callback" => [ $this, 'disclaimify_metabox_callback' ],
                "context" => "normal",
                "priority" => "default",
                "options" => [
                    [
                        "id" => "priority",
                        "type" => "number",
                        "label" => __('The lower the number, the higher the priority', 'disclaimify'),
                        "name" => "priority",
                        "default" => 1,
                        "min" => 1
                    ]
                ]
                    ],
            [
                "id" => "custom_labels",
                "title" => __("Custom Labels", "disclaimify"),
                "callback" => [ $this, 'disclaimify_metabox_callback' ],
                "context" => "normal",
                "priority" => "default",
                "options" => apply_filters('disclaimify_filter_for_labels', [])
            ]
        ];

		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		add_action( 'save_post', [ $this, 'save_post' ] );

	}

	public function add_meta_boxes($post_type) {
		if ( in_array($post_type, ['disclaimify']) ) {
            add_meta_box(
                'disclaimify_settings',
                __('Disclaimify Settings', 'disclaimify'),
                [ $this, 'disclaimify_metabox_callback' ],
                ['disclaimify'],
                'normal',
                'default',
            );
		}
	}
	public function save_post( $post_id ) {
        $data = [];
		foreach ( $this->configs as $config ) {
            foreach($config['options'] as $field) {
                switch ( $field['type'] ) {
                    case 'checkbox':
                    case 'radio':
                        if(isset( $_POST[ $field['name'] ] )) {
                            if(is_array($_POST[ $field['name'] ])) {
                                foreach ($_POST[ $field['name'] ] as $key => $value) {
                                    $data[$field['name']][$key] = sanitize_text_field($value);
                                }
                            } else {
                                $data[$field['name']] = sanitize_text_field($_POST[ $field['name'] ]);
                            }
                        }
                        break;
                    default:
                        if ( isset( $_POST[ $field['name'] ] ) ) {
                            $data[$field['name']] = sanitize_text_field( $_POST[ $field['name'] ] );
                        }
                }
            }
            if(isset($config['dep_options'])) {
                foreach($config['dep_options'] as $field) {
                    switch ( $field['type'] ) {
                        case 'checkbox':
                        case 'radio':
                            $data[$field['name']] = isset( $_POST[ $field['name'] ] ) ? sanitize_text_field( $_POST[ $field['name'] ] ) : '';
                            break;
                        default:
                            if ( isset( $_POST[ $field['name'] ] ) ) {
                                $data[$field['name']] = sanitize_text_field( $_POST[ $field['name'] ] );
                            }
                    }
                }
            }
		}
        update_post_meta( $post_id, 'disclaimify_settings', $data );
	}

	public function disclaimify_metabox_callback($post, $data) {
        foreach ($this->configs as $config) {
            if ((isset($config['options']) && !empty($config['options'])) || (isset($config['dep_options']) && !empty($config['options']))) {
                echo "<div class='disclaimify_meta-fields-section'>";
                    echo "<h3 class='disclaimify_meta-fields-section-title'>".esc_html($config['title'])."</h3>";
                    if (isset($config['options'])) {
                        echo "<div class='disclaimify_meta-fields'>";
                            foreach ($config['options'] as $option) {
                                if(!empty($option)) {
                                    $this->field($option);
                                }
                            }
                        echo "</div>";
                    }
                    if(isset($config['dep_options'])) {
                        echo "<div class='disclaimify_meta-fields disclaimify_meta-fields-dep-options'>";
                            foreach ($config['dep_options'] as $option) { 
                                if(!empty($option)) {
                                    $this->field($option);
                                }
                            }
                        echo "</div>";
                    }
                echo "</div>";
            }
        }
	}

    private function field( $field ) {
		switch ( $field['type'] ) {
			case 'checkbox':
				$this->checkbox( $field );
				break;
			case 'radio':
				$this->radio( $field );
				break;
			case 'number':
				$this->number( $field );
				break;
			default:
				$this->input( $field );
		}
	}
    private function checkbox( $field ) {
		printf(
			'<div class="disclaimify_meta-field disclaimify_meta-field_checkbox"><input id="%1$s" name="%2$s[%1$s]" class="disclaimify_meta-field_input" type="checkbox" %3$s><label class="disclaimify_meta-field_label" for="%1$s">%4$s</label></div>',
            esc_attr($field['id']),
            esc_attr($field['name']),
			esc_attr($this->checked( $field )),
			esc_html($field['label'])
		);
	}
    private function radio( $field ) {
		printf(
			'<div class="disclaimify_meta-field disclaimify_meta-field_radio"><input id="%1$s" value="%1$s" name="%2$s" class="disclaimify_meta-field_input" type="radio" %3$s ><label class="disclaimify_meta-field_label" for="%1$s">%4$s</label></div>',
            esc_attr($field['id']),
            esc_attr($field['name']),
			esc_attr($this->checkedRadio( $field )),
			esc_html($field['label'])
		);
	}
	private function number( $field ) {
		printf(
			'<div class="disclaimify_meta-field disclaimify_meta-field_number"><label for="%1$s" class="disclaimify_meta-field_label">%3$s</label><input id="%1$s" name="%2$s" class="disclaimify_meta-field_input" type="number" value="%4$s" min="%5$s"></div>',
			esc_attr($field['id']),
			esc_attr($field['name']),
			esc_html($field['label']),
			esc_attr($this->value( $field )),
			esc_attr($field['min']),
		);
	}
	private function input( $field ) {
        $dep = isset($field['dep']) ? $field['dep'] : [];
        $dep_id = isset($dep['id']) ? "data-dep-id={$dep['id']}" : '';
        $dep_display = '';
        if(!empty($dep)) {
            $dep_display = disclaimify_get_post_meta_option_data('disclaimify_settings', $dep['name']) === $dep['id'] ? '' : 'display: none;';
        }
		printf(
			'<div class="disclaimify_meta-field disclaimify_meta-field_text" %7$s style="%8$s"><label for="%1$s" class="disclaimify_meta-field_label">%3$s</label><input id="%1$s" name="%2$s" class="disclaimify_meta-field_input" type="%4$s" value="%5$s" placeholder="%6$s"></div>',
			esc_attr($field['id']),
			esc_attr($field['name']),
			esc_html($field['label']),
			esc_attr($field['type']),
			esc_attr($this->value( $field )),
			esc_attr($field['placeholder']),
			esc_attr($dep_id),
			esc_attr($dep_display)
		);
	}

	private function checked( $field ) {
        $post_meta_data = disclaimify_get_post_meta_option_data('disclaimify_settings', $field['name']);
		if (!empty($post_meta_data) ) {
            if (isset($post_meta_data[$field['id']]) ) {
                return 'checked';
            }
        } else if ( isset( $field['checked'] ) ) {
            return 'checked';
        }
		return '';
	}
	private function checkedRadio( $field ) {
        $post_meta_data = disclaimify_get_post_meta_option_data('disclaimify_settings', $field['name']);
		if ( $post_meta_data === $field['id'] ) {
			return 'checked';
		} else if ( isset( $field['checked'] ) ) {
			return 'checked';
		}
		return '';
	}

	private function value( $field ) {
        $post_meta_data = disclaimify_get_post_meta_option_data('disclaimify_settings', $field['name']);
		if ( isset($post_meta_data) ) {
			$value = $post_meta_data;
		} else if ( isset( $field['default'] ) ) {
			$value = $field['default'];
		} else {
			return '';
		}
		return str_replace( '\u0027', "'", $value );
	}
}
DisclaimifyMetaBox::instance();