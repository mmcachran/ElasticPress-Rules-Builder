<?php
/**
 * An interface for registration objects to implement.
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder;

/**
 * Interface for registration objects to implement.
 */
interface RegistrationInterface {
	/**
	 * Determines if the object should be registered.
	 *
	 * @since 0.1.0
	 *
	 * @return bool True if the object should be registered, false otherwise.
	 */
	public function can_register();

	/**
	 * Registration method for the object.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register();
}
