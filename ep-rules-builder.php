<?php
/**
 * Plugin Name: ElasticPress Rules Builder
 * Description: Control (boost, bury, hide) search results based on seach keywords.
 * Version:     0.1
 * Author:      mmcachran
 * Author URI:  https://github.com/mmcachran/ElasticPress-Rules-Builder
 * License:     GPLv2 or later
 * Text Domain: ep-rules-builder
 * Domain Path: /languages/
 *
 * @package ElasticPress Rules Builder
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Wrapper around PHP's define function. The defined constant is
 * ignored if it has already been defined. This allows the
 * config.local.php to override any constant in config.php.
 *
 * @param string $name  The constant name.
 * @param mixed  $value The constant value.
 * @return void
 */
function ep_rules_builder_define( $name, $value ) {
	// Bail early if already defined.
	if ( defined( $name ) ) {
		return;
	}

	// Define the constant.
	define( $name, $value );
}

if ( file_exists( __DIR__ . '/config.test.php' ) && defined( 'PHPUNIT_RUNNER' ) ) {
	require_once __DIR__ . '/config.test.php';
}

if ( file_exists( __DIR__ . '/config.local.php' ) ) {
	require_once __DIR__ . '/config.local.php';
}

require_once __DIR__ . '/config.php';

/**
 * Loads the EP Rules Builder PHP autoloader if possible.
 *
 * @return bool True or false if autoloading was successful.
 */
function ep_rules_builder_autoload() {
	// Bail early if it cannot be autoloaded.
	if ( ! ep_rules_builder_can_autoload() ) {
		return false;
	}

	require_once ep_rules_builder_autoloader();
	return true;
}

/**
 * In server mode we can autoload if autoloader file exists. For
 * test environments we prevent autoloading of the plugin to prevent
 * global pollution and for better performance.
 *
 * @return bool True if the plugin can be autoloaded, false otherwise.
 */
function ep_rules_builder_can_autoload() {
	// Bail early if the autoloader doesn't exist.
	if ( ! file_exists( ep_rules_builder_autoloader() ) ) {
		error_log( 'Fatal Error: Composer not setup in ' . EP_RULES_BUILDER_PLUGIN_DIR ); // @codingStandardsIgnoreLine
		return false;
	}

	return true;
}

/**
 * Default is Composer's autoloader
 *
 * @return string The path to the composer autoloader.
 */
function ep_rules_builder_autoloader() {
	return EP_RULES_BUILDER_PLUGIN_DIR . '/vendor/autoload.php';
}

/**
 * Plugin code entry point. Singleton instance is used to maintain a common single
 * instance of the plugin throughout the current request's lifecycle.
 *
 * If autoloading failed an admin notice is shown and logged to
 * error_log.
 *
 * @return void
 */
function ep_rules_builder_autorun() {
	// Bail early if plugin cannot be autoloded.
	if ( ! ep_rules_builder_autoload() ) {
		add_action( 'admin_notices', 'ep_rules_builder_autoload_notice' );
		return;
	}

	// Bail early if plugin requirements are not met.
	if ( ! ep_rules_builder_requirements_met() ) {
		add_action( 'admin_notices', 'ep_rules_builder_requirements_notice' );
		return;
	}

	$plugin = \EP_Rules_Builder\Plugin::get_instance();
	$plugin->enable();
}

/**
 * Displays notice if the plugin cannot be autoloaded.
 *
 * @return void
 */
function ep_rules_builder_autoload_notice() {
	$class   = 'notice notice-error';
	$message = 'Error: Please run $ composer install in the EP Rules Builder plugin directory.';

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_html( $class ), wp_kses_post( $message ) );
	error_log( $message ); // @codingStandardsIgnoreLine
}

/**
 * Determines if requirements are met for this plugin.
 *
 * @return bool True if the plugin requirements are met, false otherwise.
 */
function ep_rules_builder_requirements_met() {
	return true;
}

/**
 * Displays notice if the plugin cannot be autoloaded.
 *
 * @return void
 */
function ep_rules_builder_requirements_notice() {
}

// Kick off the plugin.
ep_rules_builder_autorun();
