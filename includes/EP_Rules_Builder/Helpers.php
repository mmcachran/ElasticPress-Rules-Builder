<?php // @codingStandardsIgnoreLine
/**
 * Core file to include helpers.
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder;

/**
 * Returns the singleton instance of the plugin.
 *
 * @since 0.1.0
 *
 * @return Plugin
 */
function get_plugin() {
	return Plugin::get_instance();
}

/**
 * Retrieves the plugin's version.
 *
 * @since 0.1.0
 *
 * @return float The plugin's version.
 */
function get_plugin_version() {
	// Default to the version constant.
	$version  = EP_RULES_BUILDER_PLUGIN_VERSION;
	$revision = EP_RULES_BUILDER_PRIZE_PLUGIN_DIR . '/.revision';

	if ( file_exists( $revision ) ) {
		$version = file_get_contents( $revision ); // @codingStandardsIgnoreLine
	}

	return apply_filters( 'ep_rules_builder_plugin_version', $version );
}

/**
 * Get a property from either an object or an array.
 *
 * @since 0.1.0
 *
 * @param  string       $key        The name of the property to retrieve.
 * @param  array|object $data The object to retrieve the property for.
 * @param  mixed        $default     The default if the property is empty or not found.
 * @return mixed
 */
function get_property( $key, $data, $default = null ) {
	$value = null;

	if ( is_array( $data ) ) {
		$value = get_array_property( $key, $data, $default );
	} elseif ( is_object( $data ) ) {
		$value = get_object_property( $key, $data, $default );
	}

	return $value;
}

/**
 * Get a property from an array.
 *
 * @since 0.1.0
 *
 * @param string $key     The name of the property to retrieve.
 * @param array  $data    The array to retrieve the property for.
 * @param mixed  $default The default if the property is empty or not found.
 * @return mixed
 */
function get_array_property( $key, $data, $default = null ) {
	return isset( $data[ $key ] ) ? $data[ $key ] : $default;
}

/**
 * Get a property from an object.
 *
 * @since 0.1.0
 *
 * @param string $key     The name of the property to retrieve.
 * @param object $data    The object to retrieve the property for.
 * @param mixed  $default The default if the property is empty or not found.
 * @return mixed
 */
function get_object_property( $key, $data, $default = null ) {
	return isset( $data->$key ) ? $data->$key : $default;
}

/**
 * Get a post object, but only if the ID is not zero. Use this instead of WordPress' get_post( $post ) when you don't want the global post returned when a valid post or post ID isn't given.
 *
 * @since 0.1.0
 *
 * @param  int|\WP_Post $post The post ID or object.
 * @return \WP_Post|null
 */
function get_post( $post ) {
	if ( is_valid_post_id( $post ) ) {
		$post = \get_post( $post );
	}

	return is_post( $post ) ? $post : null;
}

/**
 * Determine if the object is a post object.
 *
 * @since 0.1.0
 *
 * @param  \WP_Post $post The post object.
 * @return boolean
 */
function is_post( $post ) {
	return $post instanceof \WP_Post;
}

/**
 * Determine if the object is a term object.
 *
 * @since 0.1.0
 *
 * @param  \WP_Term $term The object.
 * @return boolean
 */
function is_term( $term ) {
	return $term instanceof \WP_Term;
}

/**
 * Determine if a post ID is valid.
 *
 * @since 0.1.0
 *
 * @param  int|string $post_id The post ID.
 * @return boolean
 */
function is_valid_post_id( $post_id ) {
	return ( ! empty( $post_id ) && is_numeric( $post_id ) );
}

/**
 * Logs a message to the debug log.
 *
 * @since 0.1.0
 *
 * @param mixed $message The message to log to the debug.log.
 * @return bool True if the message was successfully added, false otherwise.
 */
function debug( $message ) {
	// Check for non-strings.
	if ( ! is_string( $message ) ) {
		$message = print_r( $message, true ); // @codingStandardsIgnoreLine
	}

	// Append to the debug log.
	return @file_put_contents( WP_CONTENT_DIR . '/debug.log', "\n[" . date( 'd-M-Y h:i:s A', current_time( 'timestamp' ) ) . '] ' . $message, FILE_APPEND ); // @codingStandardsIgnoreLine
}

/**
 * Check if a URL is a 404.
 *
 * @since 0.1.0
 *
 * @param string $url The URL to check.
 * @return bool       True if the URL is not a 404, false otherwise.
 */
function is_url_404( $url ) {
	$response      = wp_remote_get( $url ); // @codingStandardsIgnoreLine
	$response_code = wp_remote_retrieve_response_code( $response );
	return ( 404 === (int) $response_code );
}

// Require additional helpers from the plugin.
require_once __DIR__ . '/Helper/Template.php';
require_once __DIR__ . '/Helper/Elasticsearch.php';
