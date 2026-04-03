<?php

/**
 * Get Current Post Metadata
 * @param string $meta_key
 * @return mixed Array
 */
function disclaimify_get_post_meta_data($meta_key) {
    global $post;
    if ( metadata_exists( 'post', $post->ID, $meta_key ) && $post->post_status !== 'auto-draft' ) {
        return get_post_meta( $post->ID, $meta_key, true );
    }
}

/**
 * Get Current Post Metadata
 * @param string $meta_key
 * @param string $id
 * @return mixed
 */
function disclaimify_get_post_meta_option_data($meta_key, $array_id) {
    $data = disclaimify_get_post_meta_data($meta_key);
    return isset($data[$array_id]) ? $data[$array_id] : null;
}

/**
 * Get all public post types as array for metabox radio type option
 * @return array
 */
function disclaimify_get_post_type_for_metabox() {
    $post_types = [];
    $post_types_list = get_post_types( array(
        'public'             => true,
        'publicly_queryable' => true,
    ), 'objects', 'or' );

    foreach ($post_types_list as $key => $post_type) {
        $post_types[] = [
            "type" => "radio",
            "label" => $post_type->label,
            "id" => $key,
            "name" => "select_post_type",
            "checked" => $key === 'post' ? true : null,
        ];
    }
    return $post_types;
}

/**
 * Get post taxonomies slug by $post_id
 * @param mixed $post_id
 * @return array taxonomies slug
 */
function disclaimify_get_post_taxonomies_slug($post_id) {
    $taxonomies_slug = [];
    $taxonomies = get_post_taxonomies($post_id);
    foreach ($taxonomies as $taxonomy) {
        $terms = get_the_terms($post_id, $taxonomy);
        if($terms) {
            foreach($terms as $term) {
                array_push($taxonomies_slug, $term->slug);
            }
        }
    }
    return $taxonomies_slug;
}

/**
 * Get the latest Disclaimify from multiple disclaimify.
 * @param array $ids
 * @return mixed disclaimify id
 */
function disclaimify_filter_by_priority($ids) {
    $priority = [];
    $disclaimify_by_min_priority = [];
    foreach ($ids as $id) {
        $disclaimify_meta = get_post_meta($id, 'disclaimify_settings', true);

        $priority[$id] = $disclaimify_meta['priority'];
    }
    if(!empty($priority)) {
        $disclaimify_by_min_priority = array_keys($priority, min($priority));
    }
    if(count($disclaimify_by_min_priority) > 1) {
        $disclaimify_args = [
            'post_type' => 'disclaimify',
            'post_status' => 'publish',
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'posts_per_page' => -1,
            'fields' => 'ids',
            "include" => $disclaimify_by_min_priority
        ];
        $disclaimifys = get_posts($disclaimify_args);
        return $disclaimifys[0];
    }
    if(!empty($disclaimify_by_min_priority)) {
        return $disclaimify_by_min_priority[0];
    }
    return null;
}

/**
 * Check Disclaimify Rules for current post.
 * @param mixed $post_id
 * @return mixed Disclaimify ID
 */
function disclaimify_check_rules($post_id) {
    $disclaimify_args = [
        'post_type' => 'disclaimify',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'fields' => 'ids'
    ];
    $disclaimifys = get_posts($disclaimify_args);
    $disclaimify_ids = [];
    foreach ($disclaimifys as $disclaimify_id) {
        $disclaimify_meta = get_post_meta($disclaimify_id, 'disclaimify_settings', true);
        if( get_post_type($post_id) === $disclaimify_meta['select_post_type'] ) {
            /**
             * Include Condition `show_on_all` check with all Exclude Condition
             */
            if($disclaimify_meta['include_condition'] === 'show_on_all') {
                if($disclaimify_meta['exclude_condition'] === 'disable') {
                    $disclaimify_ids[] = $disclaimify_id;
                    continue;
                }
                if($disclaimify_meta['exclude_condition'] === 'hide_on_taxonomies') {
                    $exclude_taxonomies = $disclaimify_meta['exclude_taxonomies'];
                    $taxonomies_slug = disclaimify_get_post_taxonomies_slug($post_id);
                    $common_taxonomies = array_intersect(explode(', ', $exclude_taxonomies), $taxonomies_slug);
                    if(empty($common_taxonomies)) {
                        $disclaimify_ids[] = $disclaimify_id;
                        continue;
                    }
                }
                if($disclaimify_meta['exclude_condition'] === 'hide_on_posts') {
                    if( !in_array( $post_id, explode(', ', $disclaimify_meta['exclude_posts']) ) ) {
                        $disclaimify_ids[] = $disclaimify_id;
                        continue;
                    }
                }
            }
            /**
             * Include Condition `show_on_taxonomies` check with all Exclude Condition
             */
            if($disclaimify_meta['include_condition'] === 'show_on_taxonomies') {
                $include_taxonomies = $disclaimify_meta['include_taxonomies'];
                $taxonomies_slug = disclaimify_get_post_taxonomies_slug($post_id);
                $common_include_taxonomies = array_intersect(explode(', ', $include_taxonomies), $taxonomies_slug);
                if(!empty($common_include_taxonomies)) {
                    if($disclaimify_meta['exclude_condition'] === 'disable') {
                        $disclaimify_ids[] = $disclaimify_id;
                        continue;
                    }
                    if($disclaimify_meta['exclude_condition'] === 'hide_on_taxonomies') {
                        $exclude_taxonomies = $disclaimify_meta['exclude_taxonomies'];
                        $common_exclude_taxonomies = array_intersect(explode(', ', $exclude_taxonomies), $taxonomies_slug);
                        if(empty($common_exclude_taxonomies)) {
                            $disclaimify_ids[] = $disclaimify_id;
                            continue;
                        }
                    }
                    if($disclaimify_meta['exclude_condition'] === 'hide_on_posts') {
                        if(!in_array( $post_id, explode(', ', $disclaimify_meta['exclude_posts']) )) {
                            $disclaimify_ids[] = $disclaimify_id;
                            continue;
                        }
                    }
                }
            }
            /**
             * Include Condition `show_on_posts` check with all Exclude Condition
             */
            if($disclaimify_meta['include_condition'] === 'show_on_posts') {
                $taxonomies_slug = disclaimify_get_post_taxonomies_slug($post_id);
                if(in_array( $post_id, explode(', ', $disclaimify_meta['include_posts']))) {
                    if($disclaimify_meta['exclude_condition'] === 'disable') {
                        $disclaimify_ids[] = $disclaimify_id;
                        continue;
                    }
                    if($disclaimify_meta['exclude_condition'] === 'hide_on_taxonomies') {
                        $exclude_taxonomies = $disclaimify_meta['exclude_taxonomies'];
                        $common_exclude_taxonomies = array_intersect(explode(', ', $exclude_taxonomies), $taxonomies_slug);
                        if(empty($common_exclude_taxonomies)) {
                            $disclaimify_ids[] = $disclaimify_id;
                            continue;
                        }
                    }
                    if($disclaimify_meta['exclude_condition'] === 'hide_on_posts') {
                        if(!in_array( $post_id, explode(', ', $disclaimify_meta['exclude_posts']))) {
                            $disclaimify_ids[] = $disclaimify_id;
                            continue;
                        }
                    }
                }
            }
        }
    }
    return disclaimify_filter_by_priority($disclaimify_ids);
}