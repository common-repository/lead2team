<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.lead2team.com
 * @since      1.0.0
 *
 * @package    Lead2team
 * @subpackage Lead2team/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Lead2team
 * @subpackage Lead2team/includes
 * @author     Lead2Team <info@lead2team.com>
 */
class Lead2team_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'lead2team',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
