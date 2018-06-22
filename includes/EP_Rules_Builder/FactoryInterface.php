<?php
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
	 * @param string $name The name of the object to build.
	 * @return object The base object.
	 */
	public function build_if( $name );

	/**
	 * Instantiates and returns an instance of the object being built.
	 *
	 * @param string $name The name of the object to build.
	 * @return object An instance of the object being built.
	 */
	public function build( $name );

	/**
	 * Builds all supported objects.
	 *
	 * @return void
	 */
	public function build_all();
}
