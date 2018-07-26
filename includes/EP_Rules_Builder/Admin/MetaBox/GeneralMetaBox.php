<?php
/**
 * Class to create the general metabox.
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder\Admin\MetaBox;

/**
 * Creates the general metabox.
 */
class GeneralMetaBox extends AbstractMetaBox {
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
		return self::METABOX_PREFIX . 'general';
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

					'description' => new \Fieldmanager_Textfield(
						[
							'label'            => esc_html__( 'Description', 'ep-rules-builder' ),
							'description'      => esc_html__( 'Description for the rule (only used for reference).', 'ep-rules-builder' ),
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

					'start_date'  => new \Fieldmanager_Datepicker(
						[
							'label'       => esc_html__( 'Start Date', 'ep-rules-builder' ),
							'description' => esc_html__( 'Date for the rule to start.', 'ep-rules-builder' ),
						]
					),

					'end_date'    => new \Fieldmanager_Datepicker(
						[
							'label'       => esc_html__( 'End Date', 'ep-rules-builder' ),
							'description' => esc_html__( 'Date for the rule to end.', 'ep-rules-builder' ),
						]
					),
				],
			]
		);

		// Add the metabox.
		$fm->add_meta_box( esc_html__( 'General Settings', 'ep-rules-builder' ), $this->get_post_types() );
	}
}
