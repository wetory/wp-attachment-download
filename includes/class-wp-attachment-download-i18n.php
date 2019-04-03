<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.wetory.eu/
 * @since      1.0.0
 *
 * @package    Wp_Attachment_Download
 * @subpackage Wp_Attachment_Download/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wp_Attachment_Download
 * @subpackage Wp_Attachment_Download/includes
 * @author     Wetory <tomas.rybnicky@wetory.eu>
 */
class Wp_Attachment_Download_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp-attachment-download',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
