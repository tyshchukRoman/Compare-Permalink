<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Compare_Permalinks
 * @subpackage Compare_Permalinks/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Compare_Permalinks
 * @subpackage Compare_Permalinks/admin
 * @author     Tyshchuk Roman <tisukroman@gmail.com>
 */
class Compare_Permalinks_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/compare-permalinks-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/compare-permalinks-admin.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Add Settings Page for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function add_settings_page() {
    add_management_page(
      __('Compare Permalinks', 'compare-permalinks'), // Page title
      __('Compare Permalinks', 'compare-permalinks'), // Menu title
      'manage_options',                               // Capability
      'compare-permalinks-settings',                  // Menu slug
      [$this, 'compare_permalinks_settings_display']  // Callback function
    );
	}

	/**
	 * HTML/PHP for Settings Page
	 *
	 * @since    1.0.0
	 */
	public function compare_permalinks_settings_display() {
	  require_once plugin_dir_path( __FILE__ ) . 'partials/compare-settings.php';
  }

	/**
	 * Handle csv export action
	 *
	 * @since    1.0.0
	 */
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
      __('Post Types', 'compare-permalinks'),
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
