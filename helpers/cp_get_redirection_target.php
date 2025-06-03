<?php

function cp_get_redirection_target($source_url) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'redirection_items';

    // Check if table exists
    $table_exists = $wpdb->get_var($wpdb->prepare(
        "SHOW TABLES LIKE %s",
        $table_name
    ));

    if ($table_exists !== $table_name) {
        return null; // Table doesn't exist — Redirection plugin not installed
    }

    $source_path = '/' . ltrim(wp_parse_url($source_url, PHP_URL_PATH), '/');

    $query = $wpdb->prepare("
        SELECT action_data 
        FROM {$wpdb->prefix}redirection_items 
        WHERE url = %s 
          AND action_type = 'url' 
          AND action_code = 301
        LIMIT 1
    ", $source_path);

    $target = $wpdb->get_var($query);

    return $target ? trim($target) : null;
}
