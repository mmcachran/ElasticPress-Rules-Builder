<?php
/**
 * Builds the taxonomy class instances.
 * Instances are stored locally and returned from cache on subsequent build calls.
 *
 * All taxonomies supported by the ElasticPress Rules Builder are declared here.
 *
 * Usage:
 *
 * ```php
 *
 * $factory = new TaxonomyFactory();
 * $factory->build_all();
 *
 * $factory->build_if( FOO_TAXONOMY );
 *
 * ```
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder\Taxonomy;

/**
 * Class to build and retrieve taxonomy instances.
 */
class TaxonomyFactory implements \EP_Rules_Builder\FactoryInterface {
	/**
	 * Holds previously created taxonomy instances.
	 *
	 * @since 0.1.0
	 *
	 * @var array
	 */
	public $taxonomies = [];

	/**
	 * Mapping for taxonomies to classes.
	 *
	 * @since 0.1.0
	 *
	 * @var array
	 */
	protected $taxonomy_mapping = [
		EP_RULE_TYPE_TAXONOMY => 'RuleTypeTaxonomy',
	];

	/**
	 * Builds all supported taxonomies. This is bound to the 'init' hook
	 * to allow both frontend and backend to get these taxonomies.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function build_all() {
		array_map( [ $this, 'build_if' ], $this->get_supported_taxonomies() );
	}

	/**
	 * Conditionally builds a taxonomy or returns the stored instance.
	 *
	 * @since 0.1.0
	 *
	 * @param string $taxonomy The taxonomy name.
	 * @return \EP_Rules_Builder\AbstractTaxonomy The taxonomy instance.
	 */
	public function build_if( $taxonomy ) {
		// Build the instance if not previously built.
		if ( ! $this->exists( $taxonomy ) ) {
			$this->taxonomies[ $taxonomy ] = $this->build( $taxonomy );

			// Register the instance if needed.
			if ( $this->taxonomies[ $taxonomy ]->can_register() ) {
				$this->taxonomies[ $taxonomy ]->register();
			}
		}

		return $this->taxonomies[ $taxonomy ];
	}

	/**
	 * Instantiates and returns an instance for the specified taxonomy.
	 *
	 * @since 0.1.0
	 *
	 * @param string $taxonomy           The taxonomy name.
	 * @return \EP_Rules_Builder\AbstractTaxonomy The taxonomy object.
	 * @throws \Exception                An exception is thrown if an invalid taxonomy name was specified.
	 */
	public function build( $taxonomy ) {
		// Bail early if the taxonomy isn't valid.
		if ( empty( $this->taxonomy_mapping[ $taxonomy ] ) ) {
			throw new \Exception(
				sprintf(
					'Mapping not found for Taxonomy: %1$s',
					$taxonomy
				)
			);
		}

		$class = $this->taxonomy_mapping[ $taxonomy ];

		// If mapping is not fully qualified, qualify it.
		if ( 0 !== strpos( $class, 'EP_Rules_Builder' ) ) {
			$class = 'EP_Rules_Builder\Taxonomy\\' . $class;
		}

		// Instantiate the class and return the instance.
		$instance = new $class();
		return $instance;
	}

	/**
	 * Returns a list of supported taxonomies.
	 *
	 * @since 0.1.0
	 *
	 * @return array List of supported taxonomy names.
	 */
	protected function get_supported_taxonomies() {
		return array_keys( $this->taxonomy_mapping );
	}

	/**
	 * Checks if the taxonomy specified was previously built.
	 *
	 * @since 0.1.0
	 *
	 * @param string $taxonomy The taxonomy name.
	 * @return bool True if the taxonomy was previously built, false otherwise.
	 */
	public function exists( $taxonomy ) {
		return ! empty( $this->taxonomies[ $taxonomy ] );
	}
}
