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
		 * @since 0.1.0
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
		 * @since 0.1.0
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

		// Bail early if the rule's triggers do not apply to this search.
		if ( ! $this->rule_triggers_are_valid( $rule_id ) ) {
			return false;
		}

		return true;
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
		$rule_data = get_post_meta( $rule_id, EP_RULES_BUILDER_METABOX_PREFIX . 'general', true );

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

	/**
	 * Parses a rule's triggers.
	 *
	 * @since 0.1.0
	 *
	 * @param int $rule_id The rule to test triggers.
	 * @return bool True if the rule's triggers are valid, false otherwise.
	 */
	protected function rule_triggers_are_valid( int $rule_id ) {
		$rule_triggers = get_post_meta( $rule_id, EP_RULES_BUILDER_METABOX_PREFIX . 'triggers', true );

		// Bail early if no rule triggers.
		if ( empty( $rule_triggers['triggers'] ) ) {
			return false;
		}

		$condition = isset( $rule_triggers['condition'] ) ? $rule_triggers['condition'] : 'all';

		// Loop through rule triggers and test each.
		foreach ( (array) $rule_triggers['triggers'] as $trigger ) {
			// Test if the trigger applies.
			$trigger_applies = $this->test_trigger( $trigger );

			// Bail early if the condition is "any" and a condition has been met.
			if ( $applies && ( 'any' === $condition ) ) {
				return true;
			} elseif ( ! $applies && ( 'all' === $condition ) ) {
				// Bail early if the condition is 'all' and a condition is not met.
				return false;
			}
		}

		// If we made it here the trigger is valid.
		return true;
	}

	/**
	 * Tests a single rule trigger.
	 *
	 * @since 0.1.0
	 *
	 * @param array $trigger The trigger to test against.
	 * @return bool True if the trigger is valid, false otherwise.
	 */
	protected function test_trigger( array $trigger ) {
		/**
		 * Override for the trigger.
		 *
		 * Modify the results of testing a rule's trigger.
		 *
		 * @since 0.1.0
		 *
		 * @param null  $null          If a result other than null is returned, the trigger test will be returned early
		 * @param array $trigger       The trigger being tested.
		 * @param string $search_term  The term being searched for.
		 */
		$override = apply_filters( 'ep_rules_builder_test_trigger_override', null, $trigger, $this->search_term );

		// Bail early if the override was used.
		if ( null !== $override ) {
			return $override;
		}

		// Bail early if no operator.
		if ( empty( $trigger['operator'] ) || empty( $trigger['keyword'] ) ) {
			return false;
		}

		// Test criteria against keyword.
		switch ( $trigger['operator'] ) {
			case 'equals':
			case 'is':
				return (string) $trigger['keyword'] === (string) $this->search_term;

			case 'does_not_equal':
			case 'is_not':
				return ! ( (string) $trigger['keyword'] === (string) $this->search_term );

			case 'equals_or_greater_than':
				return (int) $trigger['keyword'] >= (int) $this->search_term;

			case 'equals_or_less_than':
				return (int) $trigger['keyword'] <= (int) $this->search_term;

			case 'greater_than':
				return (int) $trigger['keyword'] > (int) $this->search_term;

			case 'less_than':
				return (int) $trigger['keyword'] < (int) $this->search_term;

			case 'contains':
			case 'is_in':
				return stristr( $this->search_term, $trigger['keyword'] );

			case 'is_not_in':
			case 'does_not_contain':
				return ! stristr( $this->search_term, $trigger['keyword'] );
		}

		// The trigger is not valid.
		return false;
	}
}
