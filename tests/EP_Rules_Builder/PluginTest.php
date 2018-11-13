<?php
/**
 * Unit tests for the main plugin file.
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder;

/**
 * Class to test the main plugin file.
 */
class PluginTest extends \WP_UnitTestCase {
	/**
	 * Holds an instance of the main plugin class.
	 *
	 * @var \EP_Rules_Builder\Plugin
	 */
	public $plugin;

	/**
	 * Sets up the unit tests.
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		$this->plugin = new Plugin();
	}

	/**
	 * Tests the plugin returns a singleton instance.
	 *
	 * @return void
	 */
	public function test_it_has_a_singleton_instance() {
		$a = Plugin::get_instance();
		$b = Plugin::get_instance();

		$this->assertInstanceOf( 'EP_Rules_Builder\Plugin', $a );
		$this->assertSame( $a, $b );
	}
}
