<?php
/**
 * Base plugin class for the EP Rules Builder.
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder;

/**
 * This Plugin's main class.
 * All subclasses/modules are managed from within this class.
 * This class is used as a singleton and should not be instantiated directly.
 *
 * Usage:
 *
 * ```php
 *
 * $plugin = \EP_Rules_Builder\Plugin::get_instance();
 *
 * ```
 */
class Plugin {
	/**
	 * Holds a single instance of this class.
	 *
	 * @var \EP_Rules_Builder\Plugin
	 */
	private static $singleton_instance = null;

	/**
	 * Holds admin support objects.
	 *
	 * @var array
	 */
	protected $admin_support = [];

	/**
	 * Holds admin menu support objects.
	 *
	 * @var array
	 */
	protected $admin_menu_support = [];

	/**
	 * Holds support objects.
	 *
	 * @var array
	 */
	protected $support = [];

	/**
	 * Holds an instance of post type factory object.
	 *
	 * @var PostType\PostTypeFactory
	 */
	protected $post_type_factory = null;

	/**
	 * Holds an instance of taxonomy factory object.
	 *
	 * @var Taxonomy\TaxonomyFactory
	 */
	protected $taxonomy_factory = null;

	/**
	 * Returns a single instance of this class.
	 *
	 * @return \EP_Rules_Builder\Plugin A singleton instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$singleton_instance ) {
			self::$singleton_instance = new self();
		}

		return self::$singleton_instance;
	}

	/**
	 * Starts the plugin by subscribing to the WordPress lifecycle hooks.
	 *
	 * @return void
	 */
	public function enable() {
		add_action( 'init', [ $this, 'init' ] );
		add_action( 'admin_init', [ $this, 'init_admin' ], 20 );
		add_action( 'admin_menu', [ $this, 'init_admin_menu' ], 20 );
	}

	/**
	 * Runs on init WP lifecycle hook.
	 *
	 * @return void
	 */
	public function init() {
		// Register the taxonomy factory.
		$this->taxonomy_factory = new Taxonomy\TaxonomyFactory();
		$this->taxonomy_factory->build_all();

		// Register the post type factory.
		$this->post_type_factory = new PostType\PostTypeFactory();
		$this->post_type_factory->build_all();

		// Initialize admin support classes.
		if ( is_admin() ) {
			$this->init_admin_support();
		}

		// Supporting classes for the plugin that should be registered on the init hook.
		$this->support = [
			new \EP_Rules_Builder\Search\SearchSupport(),
		];

		// Register objects.
		$this->register_objects( $this->support );
	}

	/**
	 * Initializes admin support.
	 *
	 * @return void
	 */
	protected function init_admin_support() {
		$this->admin_support = [
			new Admin\MetaBox\RulesMetaBox(),
		];

		// Register objects.
		$this->register_objects( $this->admin_support );
	}

	/**
	 * Runs on the admin_init WP lifecycle hook.
	 *
	 * @return void
	 */
	public function init_admin() {

	}

	/**
	 * Runs on the init_admin_menu WP lifecycle hook.
	 *
	 * @return void
	 */
	public function init_admin_menu() {
		$this->admin_menu_support = [
			new \EP_Rules_Builder\Admin\MenuSupport(),
		];

		// Register objects.
		$this->register_objects( $this->admin_menu_support );
	}

	/**
	 * Registers an array of objects.
	 *
	 * @param array $objects The array of objects to register.
	 * @return void
	 */
	protected function register_objects( array $objects ) {
		array_map( [ $this, 'register_object' ], $objects );
	}

	/**
	 * Registers a single object.
	 *
	 * @param object $object The object to register.
	 * @return void
	 */
	public function register_object( $object ) {
		// Bail early if there are no registration methods.
		if ( ! method_exists( $object, 'can_register' ) || ! method_exists( $object, 'register' ) ) {
			return;
		}

		// Bail early if the object cannot be registered.
		if ( ! $object->can_register() ) {
			return;
		}

		// Register the object.
		$object->register();
	}

}
