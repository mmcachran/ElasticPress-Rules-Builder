<?php
/**
 * Unit tests the Market Post Type class.
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder\PostType;

/**
 * Tests the Market Post Type class.
 */
class RulePostTypeTest extends \WP_UnitTestCase {

	/**
	 * Holds an instance of the Rule Post Type.
	 *
	 * @var \EP_Rules_Builder\PostType\RulePostTypeTest
	 */
	public $post_type;

	/**
	 * Sets up unit tests.
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		$this->post_type = new RulePostType();
	}

	/**
	 * Tests the post type has a name.
	 *
	 * @return void
	 */
	public function test_it_has_a_name() {
		$actual = $this->post_type->get_name();
		$this->assertEquals( EP_RULE_POST_TYPE, $actual );
	}

	/**
	 * Tests the post type has a singular name.
	 *
	 * @return void
	 */
	public function test_it_has_singular_label() {
		$actual = $this->post_type->get_singular_label();
		$this->assertEquals( 'EP Rule', $actual );
	}

	/**
	 * Tests the post type has a plural name.
	 *
	 * @return void
	 */
	public function test_it_has_plural_label() {
		$actual = $this->post_type->get_plural_label();
		$this->assertEquals( 'EP Rules', $actual );
	}
}
