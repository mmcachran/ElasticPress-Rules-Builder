<?php
/**
 * Base class for meta boxes.
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder\Admin\MetaBox;

/**
 * Abstract class for metabox classes to extend.
 */
abstract class AbstractMetaBox implements \EP_Rules_Builder\RegistrationInterface {
	/**
	 * Determines if the metabox should be registered.
	 *
	 * @return bool True if the metabox should be registered, false otherwise.
	 */
	public function can_register() {
		return true;
	}

	/**
	 * Register hooks for the metabox.
	 *
	 * @return void
	 */
	abstract public function register();
}
