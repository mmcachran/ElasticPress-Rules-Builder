<?php
/**
 * Class to create the triggers metabox.
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder\Admin\MetaBox;

/**
 * Creates the triggers metabox.
 */
class TriggersMetaBox extends AbstractMetaBox {
	/**
	 * Determines if the metabox should be registered.
	 *
	 * @since 0.1.0
	 *
	 * @return bool True if the metabox should be registered, false otherwise.
	 */
	public function can_register() {
		return class_exists( '\Fieldmanager_Group' );
	}

	/**
	 * Register hooks for the metabox.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register() {
		// Register the agenda metabox for all allowed post types.
		foreach ( $this->get_post_types() as $post_type ) {
			add_action( "fm_post_{$post_type}", [ $this, 'get_metabox' ] );
		}
	}

	/**
	 * Returns the post types this metabox should be registered to.
	 *
	 * @since 0.1.0
	 *
	 * @return array The post types to register the metabox to.
	 */
	protected function get_post_types() {
		return [
			EP_RULE_POST_TYPE,
		];
	}

	/**
	 * Get the name for the metabox.
	 *
	 * @since 0.1.0
	 *
	 * @return string The name for the base metabox.
	 */
	protected function get_metabox_name() {
		return EP_RULES_BUILDER_METABOX_PREFIX . 'triggers';
	}

	/**
	 * Initializes the metabox.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function get_metabox() {
		$fm = new \Fieldmanager_Group(
			[
				'name'     => $this->get_metabox_name(),
				'children' => [
					'condition' => new \Fieldmanager_Select(
						[
							'label'   => esc_html__( 'Condition', 'ep-rules-builder' ),
							'options' => [
								'any' => __( 'Any (or)', 'ep-rules-builder' ),
								'all' => __( 'All (and)', 'ep-rules-builder' ),
							],
						]
					),
					'triggers'  => $this->get_triggers_metabox(),
				],
			]
		);

		// Add the metabox.
		$fm->add_meta_box( esc_html__( 'Triggers', 'ep-rules-builder' ), $this->get_post_types() );
	}

	/**
	 * Returns the triggers metabox group.
	 *
	 * @since 0.1.0
	 *
	 * @return \Fieldmanager_Group The triggers metabox group.
	 */
	protected function get_triggers_metabox() {
		return new \Fieldmanager_Group(
			[
				'label'          => esc_html__( 'Triggers', 'ep-rules-builder' ),
				'label_macro'    => array( 'Trigger: %s', 'title' ),
				'add_more_label' => esc_html__( 'Add Another Trigger', 'ep-rules-builder' ),
				'limit'          => 0,
				'sortable'       => true,
				'collapsible'    => true,
				'extra_elements' => 0,
				'children'       => [

					'title'    => new \Fieldmanager_Textfield(
						[
							'label'            => esc_html__( 'Title', 'ep-rules-builder' ),
							'description'      => esc_html__( 'Title for the trigger (only used for reference).', 'ep-rules-builder' ),
							'field_class'      => 'text',
							'validation_rules' => [
								'required' => false,
							],
							'attributes'       => [
								'maxlength' => 80,
								'size'      => 60,
							],
						]
					),

					'operator' => new \Fieldmanager_Select(
						[
							'label'   => esc_html__( 'Operator', 'ep-rules-builder' ),
							'options' => $this->get_operator_options(),
						]
					),

					'keyword'  => new \Fieldmanager_Textfield(
						[
							'label'            => esc_html__( 'Keyword', 'ep-rules-builder' ),
							'description'      => esc_html__( 'The keyword for the search to activate the trigger.', 'ep-rules-builder' ),
							'field_class'      => 'text',
							'validation_rules' => [
								'required' => false,
							],
							'attributes'       => [
								'maxlength' => 80,
								'size'      => 60,
							],
						]
					),
				],
			]
		);
	}

	/**
	 * Get all operators.
	 *
	 * @since 0.1.0
	 *
	 * @return array  Operator options.
	 */
	public function get_operator_options() {
		// get numeric operators.
		$numeric_operators = $this->get_numeric_operator_options();

		// get string operators.
		$string_operators = $this->get_string_operator_options();

		// combine both sets.
		$operators = array_merge( $numeric_operators, $string_operators );

		/**
		 * Filter allowed operators
		 *
		 * Modify allowed operators.
		 *
		 * @param         array built-in operators
		 */
		return apply_filters( 'ep_rules_builder_operator_options', $operators );
	}
}
