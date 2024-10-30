<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.lead2team.com
 * @since             1.0.0
 * @package           Lead2team
 *
 * @wordpress-plugin
 * Plugin Name:       Lead2team
 * Plugin URI: 		  https://www.lead2team.com/wordpress
 * Description:       Lead2Team's official WordPress plugin allows you to quickly install our widget on any WordPress website.
 * Version:           2.0.0
 * Requires at least: 3.0.1
 * Requires PHP:      7.4
 * Author:            Lead2team
 * Author URI:        https://www.lead2team.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       lead2team
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('LEAD2TEAM_VERSION', '2.0.0');
define('LEAD2TEAM_URL', plugin_dir_url(__FILE__));
define('LEAD_2_TEAM_API_URL', 'https://public-api.lead2team.com/v1/');
define('L2T_FILTER_KEYS', array('profiles', 'teams', 'locations'));
define('LEAD2TEAM_PLUGIN_NAME', 'lead2team');



/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-lead2team-activator.php
 */
function activate_lead2team()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-lead2team-activator.php';
	Lead2team_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_lead2team()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-lead2team-deactivator.php';
	Lead2team_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_lead2team');
register_deactivation_hook(__FILE__, 'deactivate_lead2team');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-lead2team.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_lead2team()
{

	$plugin = new Lead2team();
	$plugin->run();
}
run_lead2team();
