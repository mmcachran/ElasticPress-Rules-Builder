<?php // @codingStandardsIgnoreLine
/**
 * Base class for post types.
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder\PostType;

/**
 * Abstract class for post types.
 */
abstract class AbstractPostType implements \EP_Rules_Builder\RegistrationInterface {
	/**
	 * Get the post type name.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	abstract public function get_name();

	/**
	 * Get the singular post type label.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	abstract public function get_singular_label();

	/**
	 * Get the plural post type label.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	abstract public function get_plural_label();

	/**
	 * Determines if the post type should be registered.
	 *
	 * @since 0.1.0
	 *
	 * @return bool True if the post type should be registered, false otherwise.
	 */
	public function can_register() {
		return true;
	}

	/**
	 * Registers a post type and associates it's taxonomies.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register() {
		$this->register_post_type();
		$this->register_taxonomies();
	}

	/**
	 * Get the options for the post type.
	 *
	 * @since 0.1.0
	 *
	 * @return array Options for the custom post type.
	 */
	public function get_options() {
		return array(
			'labels'            => $this->get_labels(),
			'public'            => true,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'show_in_nav_menus' => false,
			'supports'          => $this->get_editor_supports(),
		);
	}

	/**
	 * Get the labels for the post type.
	 *
	 * @since 0.1.0
	 *
	 * @return array Labels for the Custom Post Type.
	 */
	public function get_labels() {
		$plural_label   = $this->get_plural_label();
		$singular_label = $this->get_singular_label();

		$labels = [
			'name'               => $plural_label, // Already translated via get_plural_label().
			'singular_name'      => $singular_label, // Already translated via get_singular_label().

			// Translators: %1$s The plural label.
			'all_items'          => sprintf( esc_html__( 'All %1$s', 'ep-rules-builder' ), $plural_label ),

			// Translators: %1$s The singular label.
			'add_new_item'       => sprintf( esc_html__( 'Add New %1$s', 'ep-rules-builder' ), $singular_label ),

			// Translators: %1$s The singular label.
			'edit_item'          => sprintf( esc_html__( 'Edit %1$s', 'ep-rules-builder' ), $singular_label ),

			// Translators: %1$s The singular label.
			'new_item'           => sprintf( esc_html__( 'New %1$s', 'ep-rules-builder' ), $singular_label ),

			// Translators: %1$s The singular label.
			'view_item'          => sprintf( esc_html__( 'View %1$s', 'ep-rules-builder' ), $singular_label ),

			// Translators: %1$s The plural label.
			'search_items'       => sprintf( esc_html__( 'Search %1$s', 'ep-rules-builder' ), $plural_label ),

			// Translators: %1$s The plural label.
			'not_found'          => sprintf( esc_html__( 'No %1$s found.', 'ep-rules-builder' ), strtolower( $plural_label ) ),

			// Translators: %1$s The plural label.
			'not_found_in_trash' => sprintf( esc_html__( 'No %1$s found in Trash.', 'ep-rules-builder' ), strtolower( $plural_label ) ),

			// Translators: %1$s The plural label.
			'parent_item_colon'  => sprintf( esc_html__( 'Parent %1$s:', 'ep-rules-builder' ), $plural_label ),
		];

		return $labels;
	}

	/**
	 * Registers the current post type with WordPress.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_post_type() {
		\register_post_type(
			$this->get_name(),
			$this->get_options()
		);
	}

	/**
	 * Registers the taxonomies declared with the current post type.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_taxonomies() {
		$taxonomies  = $this->get_supported_taxonomies();
		$object_type = $this->get_name();

		// Bail early if no taxonomies.
		if ( empty( $taxonomies ) ) {
			return;
		}

		// Loop through taxonomies and register each to CPT.
		foreach ( $taxonomies as $taxonomy ) {
			\register_taxonomy_for_object_type(
				$taxonomy,
				$object_type
			);
		}
	}

	/**
	 * The Editor Supports defaults. Wired to 'supports' option of
	 * register_post_type.
	 *
	 * @since 0.1.0
	 *
	 * @return array Editor supports for the CPT.
	 */
	public function get_editor_supports() {
		return array(
			'title',
			'editor',
			'author',
			'thumbnail',
			'excerpt',
		);
	}

	/**
	 * Determine if a post is in this post type.
	 *
	 * @since 0.1.0
	 *
	 * @param  int|\WP_Post $post The post to check.
	 * @return bool            True if the post is in the post type, false otherwise.
	 */
	public function is_post_in_post_type( $post ) {
		$this_post_type = $this->get_name();
		$post_type      = get_post_type( $post );

		return $this_post_type === $post_type;
	}
}
