<?php

class Compare_Permalinks {
	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct() {
    $this->version = COMPARE_PERMALINKS_VERSION;
		$this->plugin_name = COMPARE_PERMALINKS_NAME;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	private function load_dependencies() {
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-compare-permalinks-loader.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-compare-permalinks-i18n.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-compare-permalinks-admin.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-compare-permalinks-public.php';

		$this->loader = new Compare_Permalinks_Loader();
	}

	private function set_locale() {
		$plugin_i18n = new Compare_Permalinks_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	private function define_admin_hooks() {
		$plugin_admin = new Compare_Permalinks_Admin($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('admin_init', $plugin_admin, 'register_settings');
		$this->loader->add_action('admin_menu', $plugin_admin, 'add_settings_page');
		$this->loader->add_action('admin_init', $plugin_admin, 'handle_csv_export_action');
	}

	private function define_public_hooks() {
		$plugin_public = new Compare_Permalinks_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
	}

	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}
}
