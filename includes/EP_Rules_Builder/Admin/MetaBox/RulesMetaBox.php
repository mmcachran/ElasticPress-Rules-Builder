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
		wp_die( __LINE__ );
	}
}
