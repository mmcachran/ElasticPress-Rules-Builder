<?php
/**
 * Class to create the rules metabox.
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder\Admin\MetaBox;

/**
 * Creates the rules metabox.
 */
class RulesMetaBox extends AbstractMetaBox {
	/**
	 * Determines if the metabox should be registered.
	 *
	 * @return bool True if the metabox should be registered, false otherwise.
	 */
	public function can_register() {
		return class_exists( '\Fieldmanager_Group' );
	}

	/**
	 * Register hooks for the metabox.
	 *
	 * @return void
	 */
	public function register() {
		// Register the agenda metabox for all allowed post types.
		foreach ( $this->get_post_types() as $post_type ) {
			add_action( "fm_post_{$post_type}", [ $this, 'get_metabox' ] );
		}
	}

	/**
	 * Returns the post types this metabox should be registered to.
	 *
	 * @return array The post types to register the metabox to.
	 */
	protected function get_post_types() {
		return [
			EP_RULE_POST_TYPE,
		];
	}

	/**
	 * Get the name for the metabox.
	 *
	 * @return string The name for the base metabox.
	 */
	protected function get_metabox_name() {
		return self::METABOX_PREFIX . 'rules';
	}

	/**
	 * Initializes the metabox.
	 *
	 * @return void
	 */
	public function get_metabox() {
		// Initialize metabox here...
	}
}
