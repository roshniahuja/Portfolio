<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://about.me/roshniahuja
 * @since             1.0.0
 * @package           portfolio
 *
 * @wordpress-plugin
 * Plugin Name:       Portfolio Management
 * Plugin URI:        portfolio
 * Description:       Creates a custom post type and taxonomy with features to create Portfolio by adding Image and text custom fields.
 * Version:           1.0.0
 * Author:            Roshni Ahuja
 * Author URI:        https://about.me/roshniahuja
 * Text Domain:       portfolio
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'portfolio_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-portfolio-activator.php
 */
function activate_portfolio() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-portfolio-activator.php';
	portfolio_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-portfolio-deactivator.php
 */
function deactivate_portfolio() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-portfolio-deactivator.php';
	portfolio_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_portfolio' );
register_deactivation_hook( __FILE__, 'deactivate_portfolio' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-portfolio.php';
/****** Register portfolio post type and portfolio taxonomy******/
require plugin_dir_path( __FILE__ ) . 'includes/portfolio-cpt.php';
/****** Shortcodes ******/
require plugin_dir_path( __FILE__ ) . 'public/class-portfolio-shortcodes.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_portfolio() {

	$plugin = new portfolio();
	$plugin->run();

}
run_portfolio();
