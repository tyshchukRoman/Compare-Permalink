<?php

function cp_get_current_urls() {
    $post_type = get_option('compare_permalinks_post_types', 'any');
    $all_urls = [];

    /*
     * WPML active
     */
    if (function_exists('icl_get_languages')) {
        $languages = icl_get_languages('skip_missing=0');
        $original_lang = apply_filters('wpml_current_language', null);

        foreach ($languages as $lang_code => $lang) {
            do_action('wpml_switch_language', $lang_code);

            $args = [
                'post_type'      => $post_type,
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'orderby'        => 'title',
                'order'          => 'ASC',
                'suppress_filters' => false,
            ];

            $posts = get_posts($args);

            foreach ($posts as $post) {
                $all_urls[] = cp_get_url_path(apply_filters('wpml_permalink', get_permalink($post), $lang_code));
            }
        }

        // Switch back to original language
        do_action('wpml_switch_language', $original_lang);
    } 
    
    /*
     * No Multillingual plugins enabled
     */
    else {
        $args = [
            'post_type'         => $post_type,
            'post_status'       => 'publish',
            'posts_per_page'    => -1,
            'orderby'           => 'title',
            'order'             => 'ASC',
            'suppress_filters'  => false,
        ];

        $posts = get_posts($args);

        foreach ($posts as $post) {
            $all_urls[] = cp_get_url_path(get_permalink($post));
        }
    }

    return array_unique($all_urls);
}
