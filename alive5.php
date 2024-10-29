<?php

/**
 * @link              http://alive5.com
 * @since             1.0.0
 * @package           Alive5
 *
 * @wordpress-plugin
 * Plugin Name:       Alive5
 * Plugin URI:        http://alive5.com
 * Description:       Alive5 - SMS Made Simple.
 * Version:           1.0.0
 * Author:            Alive5
 * Author URI:        http://alive5.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       alive5
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'ALIVE5_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-alive5-activator.php
 */
function activate_alive5() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-alive5-activator.php';
	Alive5_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-alive5-deactivator.php
 */
function deactivate_alive5() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-alive5-deactivator.php';
	Alive5_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_alive5' );
register_deactivation_hook( __FILE__, 'deactivate_alive5' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-alive5.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_alive5() {

	$plugin = new Alive5();
	$plugin->run();

}
run_alive5();
