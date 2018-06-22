<?php
/**
 * Unit tests the abstract taxonomy class.
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder\Taxonomy;

/**
 * Tests for the Abstract taxonomy.
 */
class AbstractTaxonomyTest extends \WP_UnitTestCase {
	/**
	 * Holds an instance of a taxonomy class.
	 *
	 * @var \EP_Rules_Builder\Taxonomy\ThingTaxonomy
	 */
	public $taxonomy;

	/**
	 * Sets up the unit test.
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		$this->taxonomy = new ThingTaxonomy();
	}

	/**
	 * Tests a taxonomy has a name.
	 *
	 * @return void
	 */
	public function test_it_has_a_name() {
		$actual = $this->taxonomy->get_name();
		$this->assertEquals( 'thing', $actual );
	}

	/**
	 * Tests the taxonomy has a singular label.
	 *
	 * @return void
	 */
	public function test_it_has_singular_label() {
		$actual = $this->taxonomy->get_singular_label();
		$this->assertEquals( 'thing', $actual );
	}

	/**
	 * Tests the taxonomy has a plural label.
	 *
	 * @return void
	 */
	public function test_it_has_a_plural_label() {
		$actual = $this->taxonomy->get_plural_label();
		$this->assertEquals( 'things', $actual );
	}

	/**
	 * Tests the taxonomy has labels.
	 *
	 * @return void
	 */
	public function test_it_has_labels() {
		$labels = $this->taxonomy->get_labels();
		$this->assertEquals( 'things', $labels['name'] );
		$this->assertEquals( 'thing', $labels['singular_name'] );
	}

	/**
	 * Tests the taxonomy has options.
	 *
	 * @return void
	 */
	public function test_it_has_options() {
		$options = $this->taxonomy->get_options();
		$this->assertNotEmpty( $options );
	}

	/**
	 * Tests the taxonomy does not have post types.
	 *
	 * @return void
	 */
	public function test_it_does_not_have_post_types() {
		$actual = $this->taxonomy->get_post_types();
		$this->assertEmpty( $actual );
	}

	/**
	 * Tests the taxonomy can be registered.
	 *
	 * @return void
	 */
	public function test_it_can_be_registered() {
		$this->taxonomy->register();
		$this->assertTrue( taxonomy_exists( 'thing' ) );
	}

}

/**
 * Class to test the abstract taxonomy class.
 */
class ThingTaxonomy extends AbstractTaxonomy {

	/**
	 * The name of the taxonomy.
	 *
	 * @var string
	 */
	public $name = 'thing';

	/**
	 * Returns the name of the taxonomy.
	 *
	 * @return string The taxonomy name.
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Returns the singular label for the taxonomy.
	 *
	 * @return string The taxonomy singular label.
	 */
	public function get_singular_label() {
		return $this->name;
	}

	/**
	 * Returns the plural label for the taxonomy.
	 *
	 * @return string The taxonomy plural label.
	 */
	public function get_plural_label() {
		return $this->name . 's';
	}

}
