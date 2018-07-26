<?php
/**
 * Creates the options page logic for the ElasticPress rules builder.
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder\Admin;

/**
 * A class to handle options page logic for the rules builder.
 */
class OptionsPage implements \EP_Rules_Builder\RegistrationInterface {
	/**
	 * Determines if the object should be registered.
	 *
	 * @since 0.1.0
	 *
	 * @return bool True if the object should be registered, false otherwise.
	 */
	public function can_register() {
        return is_admin();
    }

	/**
	 * Registration method for the object.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register() {
        register_setting( 'ep_rules_builder', 'ep_rules_builder', [ $this, '\sanitize_settings' ] );
    }
}
