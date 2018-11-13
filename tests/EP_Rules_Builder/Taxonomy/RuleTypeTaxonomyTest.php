<?php
/**
 * Unit tests the Rule Type Taxonomy class.
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder\Taxonomy;

/**
 * Tests the Rule Type taxonomy.
 */
class RuleTypeTaxonomyTest extends \WP_UnitTestCase {
	/**
	 * Holds an instance of the Rule Type Taxonomy class.
	 *
	 * @var \EP_Rules_Builder\Taxonomy\RuleTypeTaxonomy
	 */
	public $taxonomy;

	/**
	 * Sets up unit tests.
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
		$this->taxonomy = new RuleTypeTaxonomy();
	}

	/**
	 * Tests the taxonomy has a name.
	 *
	 * @return void
	 */
	public function test_it_has_a_name() {
		$actual = $this->taxonomy->get_name();
		$this->assertEquals( EP_RULE_TYPE_TAXONOMY, $actual );
	}

	/**
	 * Tests the taxonomy has a singular label.
	 *
	 * @return void
	 */
	public function test_it_has_a_singular_label() {
		$actual = $this->taxonomy->get_singular_label();
		$this->assertEquals( 'EP Rule Type', $actual );
	}

	/**
	 * Tests the taxonomy has a plural label.
	 *
	 * @return void
	 */
	public function test_it_has_a_plural_label() {
		$actual = $this->taxonomy->get_plural_label();
		$this->assertEquals( 'EP Rule Types', $actual );
	}

	/**
	 * Tests the taxonomy is public.
	 *
	 * @return void
	 */
	public function test_it_is_a_public_taxonomy() {
		$actual = $this->taxonomy->get_options();
		$this->assertFalse( $actual['public'] );
	}
}

