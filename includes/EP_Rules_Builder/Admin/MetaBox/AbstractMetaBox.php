<?php // @codingStandardsIgnoreLine
/**
 * Base class for meta boxes.
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder\Admin\MetaBox;

/**
 * Abstract class for metabox classes to extend.
 */
abstract class AbstractMetaBox implements \EP_Rules_Builder\RegistrationInterface {
	/**
	 * Determines if the metabox should be registered.
	 *
	 * @since 0.1.0
	 *
	 * @return bool True if the metabox should be registered, false otherwise.
	 */
	public function can_register() {
		return true;
	}

	/**
	 * Register hooks for the metabox.
	 *
	 * @since 0.1.0
	 *
	 * @return bool
	 */
	abstract public function register();

	/**
	 * Initializes the metabox.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	abstract public function get_metabox();

	/**
	 * Get numeric operator.
	 *
	 * @since 0.1.0
	 *
	 * @return array  Operator options.
	 */
	protected function get_numeric_operator_options() {
		return [
			'equals'                 => esc_html__( 'Equals', 'ep-rules-builder' ),
			'does_not_equal'         => esc_html__( 'Does not equal', 'ep-rules-builder' ),
			'equals_or_greater_than' => esc_html__( 'Equals or greater than', 'ep-rules-builder' ),
			'equals_or_less_than'    => esc_html__( 'Equals or less than', 'ep-rules-builder' ),
			'greater_than'           => esc_html__( 'Greater than', 'ep-rules-builder' ),
			'less_than'              => esc_html__( 'Less than', 'ep-rules-builder' ),
		];
	}

	/**
	 * Get string operators.
	 *
	 * @since 0.1.0
	 *
	 * @return array  Operator options.
	 */
	protected function get_string_operator_options() {
		return [
			'contains'         => esc_html__( 'Contains', 'ep-rules-builder' ),
			'does_not_contain' => esc_html__( 'Does not contain', 'ep-rules-builder' ),
			'is'               => esc_html__( 'Is', 'ep-rules-builder' ),
			'is_not'           => esc_html__( 'Is not', 'ep-rules-builder' ),
			'is_in'            => esc_html__( 'Is in', 'ep-rules-builder' ),
			'is_not_in'        => esc_html__( 'Is not in', 'ep-rules-builder' ),
		];
	}
}
