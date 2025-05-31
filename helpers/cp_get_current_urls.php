<?php

function cp_get_current_urls() {
  $post_type = get_option('compare_permalinks_post_types', 'any');

  $args = [
    'post_type'       => $post_type,
    'post_status'     => 'publish',
    'posts_per_page'  => -1,
    'orderby'         => 'title',
    'order'           => 'ASC',
  ];

  $posts = get_posts($args);

  return array_map(fn($post) => get_permalink($post), $posts);
}
