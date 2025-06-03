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

    $source_path = cp_get_url_path($source_url);

    $query = $wpdb->prepare("
        SELECT action_data 
        FROM {$table_name}
        WHERE TRIM(BOTH '/' FROM url) = TRIM(BOTH '/' FROM %s)
          AND action_type = 'url' 
          AND action_code = 301
        LIMIT 1
    ", $source_path);

    $target = $wpdb->get_var($query);

    return $target ? cp_get_url_path($target) : null;
}
