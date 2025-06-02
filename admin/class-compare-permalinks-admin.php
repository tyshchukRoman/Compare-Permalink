<?php

class Compare_Permalinks_Admin {
	private $plugin_name;
	private $version;

	public function __construct($plugin_name, $version) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	public function enqueue_styles() {
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/compare-permalinks-admin.css', [], $this->version, 'all');
	}

	public function enqueue_scripts() {
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/compare-permalinks-admin.js', ['jquery'], $this->version, false);
	}

	public function add_settings_page() {
    add_management_page(
      __('Compare Permalinks', 'compare-permalinks'),
      __('Compare Permalinks', 'compare-permalinks'),
      'manage_options',                               
      'compare-permalinks-settings',                  
      [$this, 'compare_permalinks_settings_display'] 
    );
	}

	public function compare_permalinks_settings_display() {
	  require_once plugin_dir_path(__FILE__) . 'partials/compare-settings.php';
  }

	public function handle_csv_export_action() {
    if (
      isset($_POST['compare_permalinks_export_csv']) &&
      isset($_POST['compare_permalinks_export_csv_nonce']) &&
      wp_verify_nonce($_POST['compare_permalinks_export_csv_nonce'], 'compare_permalinks_export_csv')
    ) {
      $current_urls = cp_get_current_urls();
      $site_title = get_bloginfo('name');

      if (!empty($current_urls)) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="'.$site_title.'-permalinks.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        foreach ($current_urls as $url) {
          fputcsv($output, [$url]);
        }

        fclose($output);
        exit;
      }
    }
  }

  public function register_settings() {
    register_setting('compare_permalinks_settings_group', 'compare_permalinks_post_types');

    add_settings_section(
      'compare_permalinks_main_section',
      __('Settings', 'compare-permalinks'),
      null,
      'compare_permalinks_settings_page'
    );

    add_settings_field(
      'compare_permalinks_post_types_field',
      __('Post Types', 'compare-permalinks'),
      [$this, 'compare_permalinks_post_types_field_callback'],
      'compare_permalinks_settings_page',
      'compare_permalinks_main_section'
    );
  }

  function compare_permalinks_post_types_field_callback() {
    $selected = get_option('compare_permalinks_post_types', []);
    $post_types = get_post_types(['public' => true], 'objects');

    foreach ($post_types as $post_type) {
      $checked = in_array($post_type->name, $selected) ? 'checked' : '';
      echo '<label>';
      echo '<input type="checkbox" name="compare_permalinks_post_types[]" value="' . esc_attr($post_type->name) . '" ' . $checked . '>';
      echo ' ' . esc_html($post_type->label);
      echo '</label><br>';
    }
  }

}
