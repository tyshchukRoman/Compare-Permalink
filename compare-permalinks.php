<?php
/**
 * Plugin Name:       Compare Permalinks
 * Description:       Plugin for comparing permalinks after redesign task
 * Version:           1.0.0
 * Author:            Tyshchuk Roman
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       compare-permalinks
 * Domain Path:       /languages
 */

if (!defined('WPINC')) {
	die;
}

/*
 * Defining constants
 */
if(!defined('COMPARE_PERMALINKS_VERSION')) {
  define('COMPARE_PERMALINKS_VERSION', '1.0.0');
}

if(!defined('COMPARE_PERMALINKS_NAME')) {
  define('COMPARE_PERMALINKS_NAME', 'compare-permalinks');
}

if(!defined('COMPARE_PERMALINKS_PATH')) {
  define('COMPARE_PERMALINKS_PATH', plugin_dir_path(__FILE__));
}

if(!defined('COMPARE_PERMALINKS_URI')) {
  define('COMPARE_PERMALINKS_URI', plugins_url('/', __FILE__));
}

/*
 * Activation
 */
function activate_compare_permalinks() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-compare-permalinks-activator.php';
	Compare_Permalinks_Activator::activate();
}
register_activation_hook(__FILE__, 'activate_compare_permalinks');

/*
 * Deactivation
 */
function deactivate_compare_permalinks() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-compare-permalinks-deactivator.php';
	Compare_Permalinks_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'deactivate_compare_permalinks');

/*
 * Plugin Run
 */
function run_compare_permalinks() {
  require_once plugin_dir_path(__FILE__) . 'includes/class-compare-permalinks.php';
  require_once plugin_dir_path(__FILE__) . 'helpers/helpers.php';

	$plugin = new Compare_Permalinks();
	$plugin->run();
}
run_compare_permalinks();
