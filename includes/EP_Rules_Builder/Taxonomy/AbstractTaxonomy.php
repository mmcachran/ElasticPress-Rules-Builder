<?php
/**
 * Base class for taxonomies
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder\Taxonomy;

/**
 * Abstract class for taxonomies.
 */
abstract class AbstractTaxonomy implements \EP_Rules_Builder\RegistrationInterface {
	/**
	 * Determines if the taxonomy should be registered.
	 *
	 * @return bool True if the object should be registered, false otherwise.
	 */
	public function can_register() {
		return true;
	}

	/**
	 * Registration method for the object.
	 *
	 * @return void
	 */
	public function register() {
		\register_taxonomy(
			$this->get_name(),
			$this->get_post_types(),
			$this->get_options()
		);
	}

	/**
	 * Get the options for the taxonomy.
	 *
	 * @return array Options for the taxonomy.
	 */
	public function get_options() {
		return [
			'labels'            => $this->get_labels(),
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
		];
	}

	/**
	 * Get the labels for the taxonomy.
	 *
	 * @return array Labels for the taxonomy.
	 */
	public function get_labels() {
		$plural_label   = $this->get_plural_label();
		$singular_label = $this->get_singular_label();

		$labels = [
			'name'                       => $plural_label, // Already translated via get_plural_label().
			'singular_name'              => $singular_label, // Already translated via get_singular_label().

			// Translators: %1$s The plural label.
			'search_items'               => sprintf( esc_html__( 'Search %1$s', 'ep-rules-builder' ), $plural_label ),

			// Translators: %1$s The plural label.
			'popular_items'              => sprintf( esc_html__( 'Popular %1$s', 'ep-rules-builder' ), $plural_label ),

			// Translators: %1$s The plural label.
			'all_items'                  => sprintf( esc_html__( 'All %1$s', 'ep-rules-builder' ), $plural_label ),

			// Translators: %1$s The singular label.
			'edit_item'                  => sprintf( esc_html__( 'Edit %1$s', 'ep-rules-builder' ), $singular_label ),

			// Translators: %1$s The singular label.
			'update_item'                => sprintf( esc_html__( 'Update %1$s', 'ep-rules-builder' ), $singular_label ),

			// Translators: %1$s The singular label.
			'add_new_item'               => sprintf( esc_html__( 'Add New %1$s', 'ep-rules-builder' ), $singular_label ),

			// Translators: %1$s The singular label.
			'new_item_name'              => sprintf( esc_html__( 'New %1$s Name', 'ep-rules-builder' ), $singular_label ),

			// Translators: %1$s The plural label.
			'separate_items_with_commas' => sprintf( esc_html__( 'Separate %1$s with commas', 'ep-rules-builder' ), strtolower( $plural_label ) ),

			// Translators: %1$s The plural label.
			'add_or_remove_items'        => sprintf( esc_html__( 'Add or remove %1$s', 'ep-rules-builder' ), strtolower( $plural_label ) ),

			// Translators: %1$s The plural label.
			'choose_from_most_used'      => sprintf( esc_html__( 'Choose from the most used %1$s', 'ep-rules-builder' ), strtolower( $plural_label ) ),

			// Translators: %1$s The plural label.
			'not_found'                  => sprintf( esc_html__( 'No %1$s found.', 'ep-rules-builder' ), strtolower( $plural_label ) ),

			// Translators: %1$s The plural label.
			'not_found_in_trash'         => sprintf( esc_html__( 'No %1$s found in Trash.', 'ep-rules-builder' ), strtolower( $plural_label ) ),
		];

		return $labels;
	}

	/**
	 * Setting the post types to an empty array will ensure no post type is
	 * registered with this taxonomy.
	 *
	 * @return array Post types to register this taxonomy to.
	 */
	public function get_post_types() {
		return [];
	}
}
