<?php // @codingStandardsIgnoreLine
/**
 * Helper functions for rendering template files.
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder;

/**
 * Helper function for including a template.
 *
 * @since 0.1.0
 *
 * @param string $template The template name to include.
 * @param array  $params    An array of parameters to include with the template.
 * @param array  $opts      Options for including the template.
 * @return void
 */
function include_template( $template, array $params = [], array $opts = [] ) {
	$template_dir = EP_RULES_BUILDER_PLUGIN_DIR . '/templates/';

	// Bail early if the template does not exist.
	if ( ! file_exists( $template_dir . $template ) ) {
		return;
	}

	// Extract params array to make keys available as direct variables.
	extract( $params ); // @codingStandardsIgnoreLine
	include $template_dir . $template;
}
