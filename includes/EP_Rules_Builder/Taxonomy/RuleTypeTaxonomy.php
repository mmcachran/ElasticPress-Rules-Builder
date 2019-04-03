<?php // @codingStandardsIgnoreLine
/**
 * The EP Rules Builder Rule type taxonomy is an internal taxonomy used for categorizing rules.
 *
 * Usage:
 *
 * ```php
 *
 * $taxonomy = new RuleTypeTaxonomy();
 * $taxonomy->register();
 *
 * ```
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder\Taxonomy;

/**
 * A class for the Rule Type Taxonomy.
 */
class RuleTypeTaxonomy extends AbstractTaxonomy {
	/**
	 * Returns the name of the taxonomy.
	 *
	 * @since 0.1.0
	 *
	 * @return string The name of the taxonomy.
	 */
	public function get_name() {
		return EP_RULE_TYPE_TAXONOMY;
	}

	/**
	 * Returns the singular name for the taxonomy.
	 *
	 * @since 0.1.0
	 *
	 * @return string The singular name for the taxonomy.
	 */
	public function get_singular_label() {
		return esc_html__( 'EP Rule Type', 'ep-rules-builder' );
	}

	/**
	 * Returns the plural name for taxonomy.
	 *
	 * @since 0.1.0
	 *
	 * @return string The plural name for the taxonomy.
	 */
	public function get_plural_label() {
		return esc_html__( 'EP Rule Types', 'ep-rules-builder' );
	}

	/**
	 * Options for the taxonomy.
	 *
	 * @since 0.1.0
	 *
	 * @return array Options for the taxonomy.
	 */
	public function get_options() {
		return [
			'labels'            => $this->get_labels(),
			'hierarchical'      => true,
			'public'            => false,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
			'show_ui'           => true,
			'capability_type'   => 'post',
		];
	}
}
