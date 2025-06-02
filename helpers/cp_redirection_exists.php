<?php

function cp_redirection_exists($source, $target) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'redirection_items';

    // Check if table exists
    $table_exists = $wpdb->get_var($wpdb->prepare(
        "SHOW TABLES LIKE %s",
        $table_name
    ));

    if ($table_exists !== $table_name) {
        return false; // Table doesn't exist — Redirection plugin not installed
    }

    // Normalize source and target paths
    $source = '/' . ltrim(wp_parse_url($source, PHP_URL_PATH), '/');
    $target_path = '/' . ltrim(wp_parse_url($target, PHP_URL_PATH), '/');

    // Check if such redirection exists
    $result = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) 
         FROM {$table_name}
         WHERE url = %s 
           AND action_type = 'url' 
           AND action_code = 301 
           AND TRIM(TRAILING '/' FROM CAST(action_data AS CHAR)) = TRIM(TRAILING '/' FROM %s)",
        $source,
        $target_path
    ));

    return $result > 0;
}
