<?php // @codingStandardsIgnoreLine
/**
 * An interface for factory objects to implement.
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder;

/**
 * Interface for factory objects to implement.
 */
interface FactoryInterface {
	/**
	 * Determines if the object should be built.
	 *
	 * @since 0.1.0
	 *
	 * @param string $name The name of the object to build.
	 * @return object The base object.
	 */
	public function build_if( $name );

	/**
	 * Instantiates and returns an instance of the object being built.
	 *
	 * @since 0.1.0
	 *
	 * @param string $name The name of the object to build.
	 * @return object An instance of the object being built.
	 */
	public function build( $name );

	/**
	 * Builds all supported objects.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function build_all();
}
