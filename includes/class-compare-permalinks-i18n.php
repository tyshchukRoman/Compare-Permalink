<?php

class Compare_Permalinks_i18n {
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'compare-permalinks',
			false,
			dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
		);
	}
}
