<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.weedesign.de
 * @since             1.0.0
 * @package           weebotLite
 *
 * @wordpress-plugin
 * Plugin Name:       weebotLite
 * Plugin URI:        http://www.weedesign.de/weebotLite.html
 * Description:       Bring AI to your website and generate your custom support chat bot
 * Version:           1.0.0
 * Author:            Wolfgang Ertl
 * Author URI:        http://wolfgang.ertl.weedesign.de
 * Text Domain:       weebotLite
 * Domain Path:       /languages
 */

 
/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class.weebotLite.activator.php
 */
function activate_weebotLite() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class.weebotLite.database.php';
	weebotLite_Database::install();
	require_once plugin_dir_path( __FILE__ ) . 'includes/class.weebotLite.activator.php';
	weebotLite_Activator::activate();
}
register_activation_hook( __FILE__, 'activate_weebotLite' );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class.weebotLite.deactivator.php
 */
function deactivate_weebotLite() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class.weebotLite.deactivator.php';
	weebotLite_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'deactivate_weebotLite' );

/**
 * The code that runs during plugin uninstall.
 * This action is documented in includes/class.weebotLite.database.php
 */
function uninstall_weebotLite($reinstall=false) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class.weebotLite.database.php';
	weebotLite_Database::uninstall();
	if($reinstall===true) {
		weebotLite_Database::install();
	}
}
register_uninstall_hook( __FILE__, 'uninstall_weebotLite' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class.weebotLite.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_weebotLite() {

	$plugin = new weebotLite();
	$plugin->run();

}
run_weebotLite();
