<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Compare_Permalinks
 *
 * @wordpress-plugin
 * Plugin Name:       Compare Permalinks
 * Plugin URI:        #
 * Description:       Plugin for comparing permalinks after redesign task
 * Version:           1.0.0
 * Author:            Tyshchuk Roman
 * Author URI:        #
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       compare-permalinks
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'COMPARE_PERMALINKS_VERSION', '1.0.0' );

/**
 * Plugin Path
 */
if(!defined('COMPARE_PERMALINKS_PATH')) {
  define('COMPARE_PERMALINKS_PATH', plugin_dir_path(__FILE__));
}

/**
 * Plugin URI
 */
if(!defined('COMPARE_PERMALINKS_URI')) {
  define('COMPARE_PERMALINKS_URI', plugins_url('/', __FILE__));
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-compare-permalinks-activator.php
 */
function activate_compare_permalinks() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-compare-permalinks-activator.php';
	Compare_Permalinks_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-compare-permalinks-deactivator.php
 */
function deactivate_compare_permalinks() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-compare-permalinks-deactivator.php';
	Compare_Permalinks_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_compare_permalinks' );
register_deactivation_hook( __FILE__, 'deactivate_compare_permalinks' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-compare-permalinks.php';

/**
 * Plugin Helpers
 */
require_once plugin_dir_path( __FILE__ ) . 'helpers/helpers.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_compare_permalinks() {

	$plugin = new Compare_Permalinks();
	$plugin->run();

}
run_compare_permalinks();
