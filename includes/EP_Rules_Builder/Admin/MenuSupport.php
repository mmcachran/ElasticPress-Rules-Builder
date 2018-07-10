<?php
/**
 * Adds menu support for plugin.
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder\Admin;

/**
 * Class for adding menu support.
 */
class MenuSupport implements \EP_Rules_Builder\RegistrationInterface {
	/**
	 * Determines if the object should be registered.
	 *
	 * @return bool True if the object should be registered, false otherwise.
	 */
	public function can_register() {
		return is_admin();
	}

	/**
	 * Registration method for the object.
	 *
	 * @return void
	 */
	public function register() {
		add_submenu_page(
			'elasticpress',
			esc_html__( 'Rules Builder', 'ep-rules-builder' ),
			esc_html__( 'Rules Builder', 'ep-rules-builder' ),
			'manage_options',
			'ep-rules-builder',
			[ $this, 'screen_options' ]
		);
	}

	/**
	 * Screen options for the rules builder.
	 *
	 * @return void
	 */
	public function screen_options() {
		echo \EP_Rules_Builder\include_template( 'options.php' ); // @codingStandardsIgnoreLine
	}
}
