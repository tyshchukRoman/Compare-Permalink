<?php

class Compare_Permalinks_Engine {

  private string|array $post_type;
  private string $site_title;

  private array $site_urls = [];
  private array $file_urls = [];
  private array $site_redirects = [];
  private array $results = [];

  public function __construct() {
    $this->post_type = get_option('compare_permalinks_post_types', []);
    $this->site_title = get_bloginfo('name');
  }
  
  public function handle_csv_export_action() {
    if (
      !isset($_POST['compare_permalinks_export_csv']) ||
      !isset($_POST['compare_permalinks_export_csv_nonce']) ||
      !wp_verify_nonce(
        sanitize_text_field(wp_unslash($_POST['compare_permalinks_export_csv_nonce'])),
        'compare_permalinks_export_csv'
      )
    ) {
      return;
    }

    if (!current_user_can('manage_options')) {
      wp_die(esc_html__('You are not allowed to export permalinks.', 'compare-permalinks'));
    }

    $current_urls = $this->get_site_urls();

    if (empty($current_urls) || !is_array($current_urls)) {
      wp_die(esc_html__('No URLs found to export.', 'compare-permalinks'));
    }

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . sanitize_file_name($this->site_title . '-permalinks.csv') . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    if ($output === false) {
      wp_die(esc_html__('Could not open output stream for CSV export.', 'compare-permalinks'));
    }

    foreach ($current_urls as $url) {
      $url = $this->normalize_url(sanitize_text_field($url));

      fputcsv($output, [$url]);
    }

    fclose($output);
    exit;
  }

  public function get_site_urls(): array {
    if(!empty($this->site_urls)) {
      return $this->site_urls;
    }

    // wpml is active
    if (function_exists('icl_get_languages')) {
      $languages = icl_get_languages('skip_missing=0');
      $original_lang = apply_filters('wpml_current_language', null);

      foreach ($languages as $lang_code => $lang) {
        do_action('wpml_switch_language', $lang_code);

        $posts = $this->get_posts();

        foreach ($posts as $post) {
          $this->site_urls[] = $this->normalize_url(apply_filters('wpml_permalink', get_permalink($post), $lang_code));
        }
      }

      do_action('wpml_switch_language', $original_lang);
    } 
    // no multilingual plugin is active
    else {
      $posts = $this->get_posts();

      foreach ($posts as $post) {
        $this->site_urls[] = $this->normalize_url(get_permalink($post));
      }
    }

    return $this->site_urls;
  }

  public function get_file_urls(): array {
    if(!empty($this->file_urls)) {
      return $this->file_urls;
    }

    if(!isset($_POST['submit']) || empty($_POST['submit'])) {
      return $this->file_urls;
    }

    if (!isset($_FILES["imported-links"]) || empty($_FILES["imported-links"])){
      return $this->file_urls;
    } 

    if(
      !isset($_POST['compare_permalinks_file_upload_nonce']) ||
      !wp_verify_nonce(sanitize_file_name(wp_unslash($_POST['compare_permalinks_file_upload_nonce'])), 'compare_permalinks_file_upload')
    ){
      return $this->file_urls;
    }

    if (isset($_FILES["imported-links"]['error']) && $_FILES["imported-links"]['error'] !== UPLOAD_ERR_OK) {
      return $this->file_urls;
    }

    if (!isset($_FILES["imported-links"]['tmp_name']) || empty($_FILES["imported-links"]['tmp_name'])) {
      return $this->file_urls;
    }

    $tmp_name = sanitize_text_field($_FILES["imported-links"]['tmp_name']);
    $handle = fopen($tmp_name, 'r');

    if($handle === false) {
      return $this->file_urls;
    }

    while (($url = fgets($handle)) !== false) {
      $this->file_urls[] = $this->normalize_url($url);
    }

    fclose($handle);

    return $this->file_urls;
  }

  function get_results(): array {
    if(!empty($this->results)) {
      return $this->results;
    }

    $site_urls = $this->get_site_urls();
    $file_urls = $this->get_file_urls();
    $site_redirects = $this->get_site_redirects();

    if(empty($file_urls) || empty($site_urls)) {
      return $this->results;
    }

    foreach ($file_urls as $file_url) {
      $redirection_url = $this->get_redirection_target($file_url);

      if (in_array($file_url, $site_urls)) {
        $this->results[] = [
          'status' => 'match',
          'imported' => $file_url,
          'current' => $file_url,
        ];
      } 
      elseif($redirection_url) {
        $this->results[] = [
          'status' => 'redirect',
          'imported' => $file_url,
          'current' => $redirection_url,
        ];
      }
      else {
        $best_match = null;
        $highest_similarity = 0;

        foreach ($site_urls as $site_url) {
          similar_text($file_url, $site_url, $percent);
          if ($percent > $highest_similarity) {
            $highest_similarity = $percent;
            $best_match = $site_url;
          }
        }

        $this->results[] = [
          'status' => 'mismatch',
          'imported' => $file_url,
          'current' => $best_match,
          'similarity' => $highest_similarity,
        ];
      }
    }

    return $this->results;
  }

  function get_site_redirects(): array {
    if(!empty($this->site_redirects)) {
      return $this->site_redirects;
    }

    global $wpdb;

    $table = $wpdb->prefix . 'redirection_items';

    $table_exists = $wpdb->get_var($wpdb->prepare(
      "SHOW TABLES LIKE %s", $table
    ));

    if ($table_exists !== $table) {
      return $this->site_redirects;
    }

    $results = $wpdb->get_results("
        SELECT url, action_data, action_code
        FROM {$table}
        WHERE action_type = 'url'
    ", ARRAY_A);

    foreach ($results as $row) {
      $source = $this->normalize_url($row['url']);
      $target = $this->normalize_url($row['action_data']);

      $this->site_redirects[$source] = $target;
    }

    return $this->site_redirects;
  }

  public function get_redirection_target(string $source_url): string|null {
    $site_redirects = $this->get_site_redirects();

    if(!isset($site_redirects[$source_url])) {
      return null;
    }

    return $this->normalize_url($site_redirects[$source_url]);
  }

  public function get_posts(): array {
    return get_posts([
      'post_type' => $this->post_type,
      'post_status' => 'publish',
      'posts_per_page' => -1,
      'orderby' => 'title',
      'order' => 'ASC',
      'suppress_filters' => false,
      'fields' => 'ids'
    ]);
  }

  public function normalize_url(string $url): string {
    $path = wp_parse_url(trim($url), PHP_URL_PATH);
    return trim($path, ' /');
  }

}
