<?php

class Compare_Permalinks_Admin {
	private $plugin_name;
	private $version;
	private $engine;

	public function __construct($plugin_name, $version, $engine) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->engine = $engine;
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
    $engine = $this->engine;

	  require_once plugin_dir_path(__FILE__) . 'partials/compare-settings.php';
  }

  public function register_settings() {
    register_setting('compare_permalinks_settings_group', 'compare_permalinks_post_types', [
      'default' => [],
      'type' => 'array',
      'sanitize_callback' => 'post_types_option_sanitization',
    ]);

    function post_types_option_sanitization($input) {
      return is_array($input) ? array_map('sanitize_text_field', $input) : [];
    }

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
    ?>
      <label>
        <input 
          type="checkbox" 
          name="compare_permalinks_post_types[]" 
          value="<?php echo esc_attr($post_type->name) ?>" 
          <?php echo esc_attr($checked) ?>
        >
        <?php echo esc_html($post_type->label) ?>
      </label><br>
    <?php
    }
  }
}
