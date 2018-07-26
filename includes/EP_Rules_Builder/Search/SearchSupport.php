<?php
/**
 * Provides search support for the plugin.
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder\Search;

/**
 * Class for hooking into ElasticPress and providing search support.
 */
class SearchSupport implements \EP_Rules_Builder\RegistrationInterface {
	/**
	 * Holds current search term
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	protected $search_term = false;

	/**
	 * Holds function scores for boost/bury.
	 *
	 * @since 0.1.0
	 *
	 * @var array
	 */
	protected $function_scores = [];

	/**
	 * Determines if the metabox should be registered.
	 *
	 * @since 0.1.0
	 *
	 * @return bool True if the metabox should be registered, false otherwise.
	 */
	public function can_register() {
		// Determine if this is the search page.
		$can_integrate = ! is_admin();

		/**
		 * Filter for modifying whethor or not the search integration should be allowed.
		 *
		 * Hook for allowing the Rules Builder to integrate with EP queries.
		 *
		 * @param bool $integrate True if allowed to integrate, false otherwise.
		 */
		return apply_filters( 'ep_rules_builder_integrate_search', $can_integrate );
	}

	/**
	 * Register hooks for the metabox.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register() {
		add_filter( 'ep_formatted_args', [ $this, 'format_search_args' ], 999, 2 );
	}

	/**
	 * Format ES search arguments
	 *
	 * @since 0.1.0
	 *
	 * @param  array $formatted_args Formatted search args.
	 * @param  array $args           Unformatted search args.
	 * @return array                 Modified formatted search args.
	 */
	public function format_search_args( array $formatted_args = [], array $args = [] ) {
		// Bail early if we do not have a keyword.
		if ( empty( $args['s'] ) ) {
			return $formatted_args;
		}

		// Save search term in a class property for later use.
		$this->search_term = $args['s'];

		// Fetch valid search rules for the query.
		$rules = $this->get_search_rules();

		// Do things with the query...
		return $formatted_args;
	}

	/**
	 * Returns a list of valid search rules.
	 *
	 * @since 0.1.0
	 *
	 * @return array A list of valid search rules.
	 */
	protected function get_search_rules() {
		// Holds the response for this method.
		$response = [];

		/**
		 * Get the number of posts per page.
		 *
		 * Modify the number of posts per page.
		 *
		 * @param int $posts_per_page The default posts per page.
		 */
		$posts_per_page = apply_filters( 'ep_rules_builder_rules_limit', 500 );

		// Arguments for the search rules query.
		$args = [
			'post_type'              => EP_RULE_POST_TYPE,
			'post_status'            => 'publish',
			'posts_per_page'         => $posts_per_page,
			'ep_integrate'           => false,
			'no_found_rows'          => true,
			'fields'                 => 'ids',
			'update_post_meta_cache' => true,
			'update_post_term_cache' => true,
		];

		$rules = new \WP_Query( $args );

		// Bail early if no posts.
		if ( ! $rules->have_posts() ) {
			return $response;
		}

		// Loop through rules to determine if each is valid.
		foreach ( $rules->posts as $rule_id ) {
			// Skip if the rule is not valid.
			if ( ! $this->is_valid_rule( $rule_id ) ) {
				continue;
			}

			// Add the rule to our response.
			$results[] = $rule_id;
		}

		var_dump( $results ); die;

		return $results;
	}

	/**
	 * Determine if a rule is valid.
	 *
	 * @since 0.1.0
	 *
	 * @param int $rule_id The rule to test.
	 * @return bool        True if the rule is valid, false otherwise.
	 */
	protected function is_valid_rule( int $rule_id ) {
		// Bail early if the rule isn't in a valid date range.
		if ( ! $this->is_rule_dates_valid( $rule_id ) ) {
			return false;
		}
	}

	/**
	 * Determine if a rule is in the valid date range..
	 *
	 * @since 0.1.0
	 *
	 * @param int $rule_id The rule to test.
	 * @return bool        True if the rule is valid, false otherwise.
	 */
	protected function is_rule_dates_valid( int $rule_id ) {
		// Get rule general data from meta.
		$rule_data = get_post_meta( $rule_id, METABOX_PREFIX . 'general', true );

		// Bail early if no meta.
		if ( empty( $rule_data ) ) {
			return false;
		}

		// Get the start and end dates for this rule.
		$start_date = ! empty( $rule_data['start_date'] ) ? intval( $rule_data['start_date'] ) : false;
		$end_date   = ! empty( $rule_data['end_date'] ) ? intval( $rule_data['end_date'] ) : false;
		$now        = strtotime( 'now' );

		// Check start date.
		if ( ! empty( $start_date ) ) {
			// Bail early if we haven't reached the start date yet.
			if ( $now < $start_date ) {
				return false;
			}
		}

		// Check end date.
		if ( ! empty( $end_date ) ) {
			// Bail early if we've already reached the end date.
			if ( $now > $end_date ) {
				return false;
			}
		}

		// This is a valid rule.
		return true;
	}
}
