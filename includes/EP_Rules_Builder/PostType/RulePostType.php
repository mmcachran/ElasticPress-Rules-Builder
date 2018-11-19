<?php
/**
 * Class for the Rule post type.
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder\PostType;

/**
 * Class to create the Event post type.
 */
class RulePostType extends AbstractPostType {
	/**
	 * Returns the name of the post type.
	 *
	 * @since 0.1.0
	 *
	 * @return string The name of the post type.
	 */
	public function get_name() {
		return EP_RULE_POST_TYPE;
	}

	/**
	 * Returns the singular name for the post type.
	 *
	 * @since 0.1.0
	 *
	 * @return string The singular name for the post type.
	 */
	public function get_singular_label() {
		return esc_html__( 'EP Rule', 'ep-rules-builder' );
	}

	/**
	 * Returns the plural name for the post type.
	 *
	 * @since 0.1.0
	 *
	 * @return string The plural name for the post type.
	 */
	public function get_plural_label() {
		return esc_html__( 'EP Rules', 'ep-rules-builder' );
	}

	/**
	 * Returns the supported taxonomies for the post type.
	 *
	 * @since 0.1.0
	 *
	 * @return array The supported taxonomies for the post type.
	 */
	public function get_supported_taxonomies() {
		return [
			EP_RULE_TYPE_TAXONOMY,
		];
	}

	/**
	 * Options for the post type.
	 *
	 * @since 0.1.0
	 *
	 * @return array Options for the post type.
	 */
	public function get_options() {
		return [
			'labels'              => $this->get_labels(),
			'supports'            => $this->get_editor_supports(),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'menu_icon'           => 'dashicons-search',
			'menu_position'       => 12,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'post',
			'rewrite'             => [
				'slug' => 'event',
			],
		];
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
		);
	}
}
