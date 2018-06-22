<?php
/**
 * Builds the post type class instances.
 * Instances are stored locally and returned from cache on subsequent build calls.
 *
 * All post types supported by the ElasticPress Rules Builder are declared here.
 *
 * Usage:
 *
 * ```php
 *
 * $factory = new PostTypeFactory();
 * $factory->build_all();
 *
 * $factory->build_if( FOO_POST_TYPE );
 *
 * ```
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder\PostType;

/**
 * Class to build and retrieve post type instances.
 */
class PostTypeFactory implements \EP_Rules_Builder\FactoryInterface {
	/**
	 * Holds previously created post type instances.
	 *
	 * @var array
	 */
	public $post_types = [];

	/**
	 * Mapping for post types to classes.
	 *
	 * @var array
	 */
	protected $post_type_mapping = [];

	/**
	 * Builds all supported post types. This is bound to the 'init' hook
	 * to allow both frontend and backend to get these post types.
	 *
	 * @return void
	 */
	public function build_all() {
		array_map( [ $this, 'build_if' ], $this->get_supported_post_types() );
	}

	/**
	 * Conditionally builds a post type or returns the stored instance.
	 *
	 * @param string $post_type The post type name.
	 * @return \EP_Rules_Builder\AbstractPostType The post type instance.
	 */
	public function build_if( $post_type ) {
		// Build the instance if not previously built.
		if ( ! $this->exists( $post_type ) ) {
			$this->post_types[ $post_type ] = $this->build( $post_type );

			// Register the instance if needed.
			if ( $this->post_types[ $post_type ]->can_register() ) {
				$this->post_types[ $post_type ]->register();
			}
		}

		return $this->post_types[ $post_type ];
	}

	/**
	 * Instantiates and returns an instance for the specified post type.
	 *
	 * @param string $post_type          The post type name.
	 * @return \EP_Rules_Builder\AbstractPostType The post type object.
	 * @throws \Exception                An exception is thrown if an invalid post type name was specified.
	 */
	public function build( $post_type ) {
		// Bail early if the post type isn't valid.
		if ( empty( $this->post_type_mapping[ $post_type ] ) ) {
			throw new \Exception(
				sprintf(
					'Mapping not found for Post Type: %1$s',
					$post_type
				)
			);
			return;
		}

		$class = $this->post_type_mapping[ $post_type ];

		// If mapping is not fully qualified, qualify it.
		if ( 0 !== strpos( $class, 'EP_Rules_Builder' ) ) {
			$class = 'EP_Rules_Builder\PostType\\' . $class;
		}

		// Instantiate the class and return the instance.
		$instance = new $class();
		return $instance;
	}

	/**
	 * Returns a list of supported post types.
	 *
	 * @return array List of supported post type names.
	 */
	protected function get_supported_post_types() {
		return array_keys( $this->post_type_mapping );
	}

	/**
	 * Checks if the post type specified was previously built.
	 *
	 * @param string $post_type The post type name.
	 * @return bool True if the post type was previously built, false otherwise.
	 */
	public function exists( $post_type ) {
		return ! empty( $this->post_types[ $post_type ] );
	}
}
