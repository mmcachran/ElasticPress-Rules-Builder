<?php
/**
 * Unit tests the Taxonomy factory.
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder\Taxonomy;

/**
 * Class to unit tests the taxonomy factory.
 */
class TaxonomyFactoryTest extends \WP_UnitTestCase {
	/**
	 * Holds an instance of the taxonomy factory class.
	 *
	 * @var \EP_Rules_Builder\Taxonomy\TaxonomyFactory
	 */
	public $factory;

	/**
	 * Sets up the unit tests.
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		$this->factory = new TaxonomyFactory();
	}

	/**
	 * Tests an instance of the class can be created.
	 *
	 * @return void
	 */
	public function test_it_can_be_created() {
		$this->assertInstanceOf(
			'\EP_Rules_Builder\Taxonomy\TaxonomyFactory',
			$this->factory
		);
	}

	/**
	 * Tests the factory knows if a taxonomy that doesn't exist.
	 *
	 * @return void
	 */
	public function test_it_knows_if_taxonomy_does_not_exist() {
		$actual = $this->factory->exists( 'bar' );
		$this->assertFalse( $actual );
	}

	/**
	 * Tests the factory knows if a taxonomy does exist.
	 *
	 * @return void
	 */
	public function test_it_knows_if_taxonomy_exists() {
		$this->factory->taxonomies['foo'] = new \stdClass();
		$actual                           = $this->factory->exists( 'foo' );
		$this->assertTrue( $actual );
	}

	/**
	 * Tests the factory can register a taxonomy in WP.
	 *
	 * @return void
	 */
	public function test_it_registers_the_taxonomy_with_wordpress_on_build() {
		$this->factory->build( EP_RULE_TYPE_TAXONOMY );
		$actual = taxonomy_exists( EP_RULE_TYPE_TAXONOMY );
		$this->assertTrue( $actual );
	}

	/**
	 * Tests the factory does not rebuild existing taxonomies.
	 *
	 * @return void
	 */
	public function test_it_will_not_rebuild_existing_taxonomy() {
		$this->factory->taxonomies['foo'] = 'cached';
		$actual                           = $this->factory->build_if( 'foo' );
		$this->assertEquals( 'cached', $actual );
	}

	/**
	 * Tests the factory can build all taxonomies.
	 *
	 * @return void
	 */
	public function test_it_can_build_all_supported_taxonomies() {
		$this->factory->build_all();

		$actual = $this->factory->exists( EP_RULE_TYPE_TAXONOMY );
		$this->assertTrue( $actual );
	}

	/**
	 * Tests the factory can build the Rule Type taxonomy.
	 *
	 * @return void
	 */
	public function test_it_can_build_the_rule_type_taxonomy() {
		$actual = $this->factory->build( EP_RULE_TYPE_TAXONOMY );
		$this->assertInstanceOf(
			'\EP_Rules_Builder\Taxonomy\RuleTypeTaxonomy',
			$actual
		);
	}
}
