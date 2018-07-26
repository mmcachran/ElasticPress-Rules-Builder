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
	 * The prefix for this metabox.
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	const METABOX_PREFIX = 'ep_';

	/**
	 * Determines if the metabox should be registered.
	 *
	 * @since 0.1.0
	 *
	 * @return bool True if the metabox should be registered, false otherwise.
	 */
	public function can_register() {
		return true;
	}

	/**
	 * Register hooks for the metabox.
	 *
	 * @since 0.1.0
	 *
	 * @return bool
	 */
	abstract public function register();

	/**
	 * Initializes the metabox.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	abstract public function get_metabox();
}
