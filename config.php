<?php
/**
 * Holds configuration constants.
 *
 * @package ElasticPress Rules Builder
 */

$plugin_version = '0.1';

if ( file_exists( __DIR__ . '/.commit' ) ) {
	$plugin_version .= '-' . file_get_contents( __DIR__ . '/.commit' ); // @codingStandardsIgnoreLine
}

// Plugin Constants.
ep_rules_builder_define( 'EP_RULES_BUILDER_PLUGIN', __DIR__ . '/ep-rules-builder.php' );
ep_rules_builder_define( 'EP_RULES_BUILDER_PLUGIN_VERSION', $plugin_version );
ep_rules_builder_define( 'EP_RULES_BUILDER_PLUGIN_DIR', __DIR__ );
ep_rules_builder_define( 'EP_RULES_BUILDER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Metabox prefix.
ep_rules_builder_define( 'METABOX_PREFIX', 'ep_' );

// Post Types.
ep_rules_builder_define( 'EP_RULE_POST_TYPE', 'ep-rule' );

// Taxonomies.
ep_rules_builder_define( 'EP_RULE_TYPE_TAXONOMY', 'ep-rule-type' );
