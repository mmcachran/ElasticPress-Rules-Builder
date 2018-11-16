<?php
/**
 * Helper functions for rendering template files.
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder;

/**
 * Determine if dynamic scripting is disabled
 *
 * @since 0.1.0
 * @return bool true if enabled, false otherwise
 */
function dynamic_scripting_enabled() {
	// Bail early if EP function doens't exist.
	if ( ! function_exists( 'ep_get_host' ) ) {
		return false;
	}

	$cache_key = 'ep_rules_builder_dynamic_scripting';

	// Check cache.
	$dynamic_scripting = get_transient( $cache_key );

	// Bail early if set in cache.
	if ( ! empty( $dynamic_scripting ) ) {
		return ( '1' === $dynamic_scripting );
	}

	// Bail early if EP function doens't exist.
	if ( ! function_exists( 'ep_get_host' ) ) {
		return false;
	}

	// Bail early if no host.
	if ( is_wp_error( ep_get_host() ) ) {
		return array(
			'status' => false,
			'msg'    => esc_html__( 'Elasticsearch Host is not available.', 'ep-rules-builder' ),
		);
	}

	// Get node settings.
	$request = ep_remote_request( '_nodes/_master', array( 'method' => 'GET' ) );

	// Bail early if there was an error.
	if ( is_wp_error( $request ) ) {
		return [
			'status' => false,
			'msg'    => $request->get_error_message(),
		];
	}

	// Decode response.
	$response = json_decode( wp_remote_retrieve_body( $request ), true );

	// Bail early if response isn't set.
	if ( ! isset( $response['nodes'] ) ) {
		return false;
	}

	// Parse nodes.
	foreach ( $response['nodes'] as $node ) {
		// Skip if no settings.
		if ( ! isset( $node['settings']['script'] ) ) {
			continue;
		}

		// Bail early if allowed_types isn't available.
		if ( ! isset( $node['settings']['script']['allowed_types'] ) ) {
			return false;
		}

		// Is dynamic scripting not disabled?
		$dynamic_scripting = ( 'inline' === $node['settings']['script']['allowed_types'] );

		// We only want the first node's settings.
		break;
	}

	// Cache results for a day.
	set_transient(
		$cache_key,
		$dynamic_scripting,
		apply_filters( 'ep_rules_builder_dynamic_scripting_exp', DAY_IN_SECONDS )
	);

	/**
	 * Filter dynamic scripting
	 *
	 * Turn dynamic scripting on/off via filter
	 *
	 * @since 0.1.0
	 *
	 * @param         bool is dynamic scripting enabled in _nodes settings
	 */
	return apply_filters(
		'ep_rules_builder_dynamic_scripting_enabled',
		$dynamic_scripting
	);
}
