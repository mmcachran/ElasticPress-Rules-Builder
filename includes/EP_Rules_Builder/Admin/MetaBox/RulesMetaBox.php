<?php
/**
 * Class to create the rules metabox.
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder\Admin\MetaBox;

/**
 * Creates the rules metabox.
 */
class RulesMetaBox extends AbstractMetaBox {
	/**
	 * Determines if the metabox should be registered.
	 *
	 * @return bool True if the metabox should be registered, false otherwise.
	 */
	public function can_register() {
		return class_exists( '\Fieldmanager_Group' );
	}

	/**
	 * Register hooks for the metabox.
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
	 * @return string The name for the base metabox.
	 */
	protected function get_metabox_name() {
		return self::METABOX_PREFIX . 'rules';
	}

	/**
	 * Initializes the metabox.
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
					'actions'   => $this->get_actions_metabox(),
				],
			]
		);

		// Add the metabox.
		$fm->add_meta_box( esc_html__( 'Rules', 'ep-rules-builder' ), $this->get_post_types() );
	}

	/**
	 * Returns the triggers metabox group.
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
							'description'      => esc_html__( 'Title for the trigger.', 'ep-rules-builder' ),
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
	 * Returns the actions metabox group.
	 *
	 * @return \Fieldmanager_Group The actions metabox group.
	 */
	protected function get_actions_metabox() {
		return new \Fieldmanager_Group(
			[
				'label'          => esc_html__( 'Actions', 'ep-rules-builder' ),
				'label_macro'    => array( 'Action: %s', 'title' ),
				'add_more_label' => esc_html__( 'Add Another Action', 'ep-rules-builder' ),
				'limit'          => 0,
				'sortable'       => true,
				'collapsible'    => true,
				'extra_elements' => 0,
				'children'       => [

					'title'  => new \Fieldmanager_Textfield(
						[
							'label'            => esc_html__( 'Title', 'ep-rules-builder' ),
							'description'      => esc_html__( 'Title for the action.', 'ep-rules-builder' ),
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

					'action' => new \Fieldmanager_Select(
						[
							'label'   => esc_html__( 'Action', 'ep-rules-builder' ),
							'options' => [
								'boost' => esc_html__( 'Boost', 'ep-rules-builder' ),
								'bury'  => esc_html__( 'Bury', 'ep-rules-builder' ),
								'hide'  => esc_html__( 'Hide', 'ep-rules-builder' ),
							],
						]
					),

					'boost'  => new \Fieldmanager_Textfield(
						[
							'label'            => esc_html__( 'Boost', 'ep-rules-builder' ),
							'field_class'      => 'text',
							'input_type'       => 'number',
							'validation_rules' => [
								'required' => false,
							],
							'attributes'       => [
								'maxlength' => 80,
								'size'      => 60,
							],
							'display_if'       => [
								'src'   => 'action',
								'value' => 'boost',
							],
						]
					),

					'bury'   => new \Fieldmanager_Textfield(
						[
							'label'            => esc_html__( 'Bury', 'ep-rules-builder' ),
							'field_class'      => 'text',
							'input_type'       => 'number',
							'validation_rules' => [
								'required' => false,
							],
							'attributes'       => [
								'maxlength' => 80,
								'size'      => 60,
							],
							'display_if'       => [
								'src'   => 'action',
								'value' => 'bury',
							],
						]
					),

					'hide'   => new \Fieldmanager_Textfield(
						[
							'label'            => esc_html__( 'Hide', 'ep-rules-builder' ),
							'field_class'      => 'text',
							'validation_rules' => [
								'required' => false,
							],
							'attributes'       => [
								'maxlength' => 80,
								'size'      => 60,
							],
							'display_if'       => [
								'src'   => 'action',
								'value' => 'hide',
							],
						]
					),
				],
			]
		);
	}

	/**
	 * Get all operators
	 *
	 * @return array  operator options
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

	/**
	 * Get numeric operator
	 *
	 * @return array  operator options
	 */
	public function get_numeric_operator_options() {
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
	 * Get string operators
	 *
	 * @return array  operator options
	 */
	public function get_string_operator_options() {
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
