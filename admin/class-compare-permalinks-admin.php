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
    $args = [
      'post_type'       => ['post', 'page'],
      'post_status'     => 'publish',
      'posts_per_page'  => -1,
      'orderby'         => 'title',
      'order'           => 'ASC',
    ];

    $posts = get_posts($args);
  ?>

    <div class="wrap | compare-permalinks-settings-page">
      <h1>
        <?php _e('Compare Permalinks', 'compare-permalinks') ?>
      </h1>

      <div class="compare-permalinks-table">
        <table>
          <thead>
            <tr>
              <th><?php _e('Current website URLs', 'compare-permalinks') ?></th>
              <th><?php _e('Imported URLs', 'compare-permalinks') ?></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach($posts as $post): ?>
            <tr>
              <td>
                <a href="<?php echo get_permalink($post) ?>">
                  <?php echo get_permalink($post) ?>
                </a>
              </td>
              <td>
                <a href="<?php echo get_permalink($post) ?>">
                  <?php echo get_permalink($post) ?>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php
  }

}
