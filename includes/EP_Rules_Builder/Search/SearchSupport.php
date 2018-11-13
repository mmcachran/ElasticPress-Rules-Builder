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
	 * Operator scripts for string fields
	 *
	 * @since  0.1.0
	 * @var array
	 */
	protected $string_scripts = [
		'contains'         => "return _score + ( doc[[FIELD]].value.toLowerCase().indexOf( '[TEXT]' ) != -1 ? [VALUE] : 0 )",
		'is_in'            => "return _score + ( doc[[FIELD]].value.toLowerCase().indexOf( '[TEXT]' ) != -1 ? [VALUE] : 0 )",
		'does_not_contain' => "return _score + ( doc[[FIELD]].value.toLowerCase().indexOf( '[TEXT]' ) == -1 ? [VALUE] : 0 )",
		'is_not_in'        => "return _score + ( doc[[FIELD]].value.toLowerCase().indexOf( '[TEXT]' ) == -1 ? [VALUE] : 0 )",
		'is'               => "return _score + ( doc[[FIELD]].value.toLowerCase() == '[TEXT]' ? [VALUE] : 0 )",
		'is_not'           => "return _score + ( doc[[FIELD]].value.toLowerCase() != '[TEXT]' ? [VALUE] : 0 )",
	];

	/**
	 * Operator scripts for taxonomy object fields
	 *
	 * @since  0.1.0
	 * @var array
	 */
	protected $taxonomy_object_scripts = [
		'contains'         => "return _score + ( doc[[FIELD]].value.find { it.name && it.name.toLowerCase().indexOf( '[TEXT]' ) != -1 } != null ? [VALUE] : 0 )",
		'is_in'            => "return _score + ( doc[[FIELD]].value.find { it.name && it.name.toLowerCase().indexOf( '[TEXT]' ) != -1 } != null ? [VALUE] : 0 )",
		'does_not_contain' => "return _score + ( doc[[FIELD]].value.find { it.name && it.name.toLowerCase().indexOf( '[TEXT]' ) == -1  } != null ? [VALUE] : 0 )",
		'is_not_in'        => "return _score + ( doc[[FIELD]].value.find { it.name && it.name.toLowerCase().indexOf( '[TEXT]' ) == -1  } != null ? [VALUE] : 0 )",
		'is'               => "return _score + ( doc[[FIELD]].value.find { it.name && it.name.toLowerCase() == '[TEXT]' } != null ? [VALUE] : 0 )",
		'is_not'           => "return _score + ( doc[[FIELD]].value.find { it.name && it.name == '[TEXT]' } == null ? [VALUE] : 0 )",
	];

	/**
	 * Operator scripts for meta object fields
	 *
	 * @since  0.1.0
	 * @var array
	 */
	protected $meta_object_scripts = [
		'contains'         => "return _score + ( doc[[FIELD]].value.find { it.raw && it.raw.toLowerCase().indexOf( '[TEXT]' ) != -1 } != null ? [VALUE] : 0 )",
		'is_in'            => "return _score + ( doc[[FIELD]].value.find { it.raw && it.raw.toLowerCase().indexOf( '[TEXT]' ) != -1 } != null ? [VALUE] : 0 )",
		'does_not_contain' => "return _score + ( doc[[FIELD]].value.find { it.raw && it.raw.toLowerCase().indexOf( '[TEXT]' ) == -1  } != null ? [VALUE] : 0 )",
		'is_not_in'        => "return _score + ( doc[[FIELD]].value.find { it.raw && it.raw.toLowerCase().indexOf( '[TEXT]' ) == -1  } != null ? [VALUE] : 0 )",
		'is'               => "return _score + ( doc[[FIELD]].value.find { it.raw && it.raw.toLowerCase() == '[TEXT]' } != null ? [VALUE] : 0 )",
		'is_not'           => "return _score + ( doc[[FIELD]].value.find { it.raw && it.raw == '[TEXT]' } == null ? [VALUE] : 0 )",
	];

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

		// Bail early if no valid rules.
		if ( empty( $rules ) ) {
			return $formatted_args;
		}

		// Loop through valid rules to add actions.
		foreach ( $rules as $rule_id ) {
			$formatted_args = $this->apply_actions( $rule_id, $formatted_args );
		}

		// Add function scores if necessary.
		if ( ! empty( $this->function_scores ) ) {
			// Move the existing query if necessary.
			if ( isset( $formatted_args['query'] ) ) {
				$existing_query = $formatted_args['query'];
				unset( $formatted_args['query'] );
				$formatted_args['query']['function_score']['query'] = $existing_query;
			}

			// Move existing filter if necessary.
			if ( isset( $formatted_args['filter'] ) ) {
				$formatted_args['query']['function_score']['filter'] = $formatted_args['filter'];
				unset( $formatted_args['filter'] );
			}

			// Add functions.
			$formatted_args['query']['function_score']['functions'] = $this->function_scores;

			// Specify how the computed scores are combined.
			$formatted_args['query']['function_score']['score_mode'] = 'sum';
			$formatted_args['query']['function_score']['boost_mode'] = 'multiply';

			// Remove empty match_all query if it exists.
			if ( empty( $formatted_args['query']['function_score']['query']['match_all'] ) ) {
				unset( $formatted_args['query']['function_score']['query']['match_all'] );
			}

			// Remove empty query if it exists.
			if ( empty( $formatted_args['query']['function_score']['query'] ) ) {
				unset( $formatted_args['query']['function_score']['query'] );
			}
		}

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

	/**
	 * Apply rules to formatted search args
	 *
	 * @since 0.1.0
	 *
	 * @param int   $rule_id        ID of the rule.
	 * @param array $formatted_args Formatted search args.
	 * @return array                New formatted search args.
	 */
	protected function apply_actions( int $rule_id, array $formatted_args ) {
		// Get actions for the rule.
		$rule_actions = get_post_meta( $rule_id, EP_RULES_BUILDER_METABOX_PREFIX . 'actions', true );

		// Bail early if no actions to apply.
		if ( empty( $rule_actions['actions'] ) ) {
			return $formatted_args;
		}

		// Loop through and apply each action.
		foreach ( (array) $rule_actions['actions'] as $action ) {
			$formatted_args = $this->apply_action( $action, $formatted_args );
		}

		return $formatted_args;
	}

	/**
	 * Apply action to formatted args
	 *
	 * @since 0.1.0
	 *
	 * @param  array $action         Action configuration.
	 * @param  array $formatted_args Formatted search args.
	 * @return array                 Modified formatted search args.
	 */
	protected function apply_action( array $action, array $formatted_args ) {
		// Bail early if no action.
		if ( empty( $action['type'] ) ) {
			return $formatted_args;
		}

		// Format args based on the action type.
		switch ( $action['type'] ) {
			case 'boost':
				$formatted_args = $this->add_boost_or_bury( $action, $formatted_args, 'boost' );
				break;

			case 'bury':
				$formatted_args = $this->add_boost_or_bury( $action, $formatted_args, 'bury' );
				break;

			case 'hide':
				$formatted_args = $this->add_hide( $action, $formatted_args );
				break;

			default:
				break;
		}

		return $formatted_args;
	}

	/**
	 * Add hide post IDs to the query.
	 *
	 * @param array $action         The action array.
	 * @param array $formatted_args The current formatted arguments.
	 * @return array                The updated formatted arguments.
	 */
	protected function add_hide( array $action, array $formatted_args ) {
		// Get hide IDs from the action.
		$hide_ids = ! empty( $action['hide'] ) ? explode( ',', $action['hide'] ) : [];

		// Bail early if no hide ids.
		if ( empty( $hide_ids ) ) {
			return $formatted_args;
		}

		// Add to the list of formatted args.
		foreach ( $hide_ids as $hide_id ) {
			$formatted_args['post_filter']['bool']['must_not'][]['terms'] = array(
				'post_id' => [ trim( $hide_id ) ],
			);
		}

		return $formatted_args;
	}

	/**
	 * Apply dynamic and non-dynamic boost/bury
	 *
	 * @since 0.1.0
	 *
	 * @param  array  $action         Action configuration.
	 * @param  array  $formatted_args Formatted search args.
	 * @param  string $type           Type to apply (boost or bury).
	 * @return array                  New formatted search args.
	 */
	protected function add_boost_or_bury( $action, $formatted_args, $type = 'boost' ) {
		// Check for dynamics scripting.
		// if ( $this->plugin->dynamic_scripting ) {
			$formatted_args = $this->add_dynamic_boost_or_bury( $action, $formatted_args, $type );
		// } else {
			// Add non-dynamic to make sure results exist.
			// $formatted_args = $this->add_non_dynamic_boost_or_bury( $action, $formatted_args, $type );
		// }
		return $formatted_args;
	}

	/**
	 * Apply boost/bury to formatted args
	 *
	 * @since 0.1.0
	 *
	 * @param  array  $action         Action configuration.
	 * @param  array  $formatted_args Formatted search args.
	 * @param  string $type           Type to apply (boost or bury).
	 * @return array                  New formatted search args.
	 */
	protected function add_dynamic_boost_or_bury( $action, $formatted_args, $type = 'boost' ) {
		// Get boost/bury value.
		$value = isset( $action[ $type ] ) ? $action[ $type ] : false;

		// Bail early if no value.
		if ( false === $value ) {
			return $formatted_args;
		}

		// Bail early if no text.
		if ( ! isset( $action['text'] ) ) {
			return $formatted_args;
		}

		// Bail early if no field.
		if ( empty( $action['field'] ) ) {
			return $formatted_args;
		}

		// Lowercase text.
		$text = strtolower( $action['text'] );

		// Get operator.
		$operator = isset( $action['operator'] ) ? $action['operator'] : false;

		/**
		 * Figure out what script type we need to use.
		 *
		 * @var string
		 * @todo  update rules builder to include types
		 */
		$script_type = 'string_scripts';

		// Use meta object type if needed.
		if ( stristr( $action['field'], 'meta' ) ) {
			$script_type = 'meta_object_scripts';
		}

		/**
		 * Filter dynamic scripts
		 *
		 * Add/edit dynamic scripts
		 *
		 * @since 0.1.0
		 *
		 * @param object Existing scripts
		 * @param string Script type being filtered, object_scripts or string_scripts
		 * @param string Type of action, boost or bury
		 * @param array  Action being applied
		 */
		$this->{$script_type} = apply_filters(
			'wds_ep_ui_boost_or_bury_scripts',
			$this->{$script_type},
			$script_type,
			$type,
			$action
		);

		// Get the correct script to use.
		$script = isset( $this->{$script_type}[ $operator ] ) ? $this->{$script_type}[ $operator ] : false;

		// Bail early if no script.
		if ( empty( $script ) ) {
			return $formatted_args;
		}

		// If menu order only use menu order and not meta.
		if ( stristr( $action['field'], 'menu_order' ) ) {
			$action['field'] = 'menu_order';
		}

		// Replace query vars.
		$script = str_replace( '[FIELD]', "'" . $action['field'] . "'", $script );
		$script = str_replace( '[TEXT]', $text, $script );
		$script = str_replace( '[VALUE]', $value, $script );

		// Build script score.
		$score_script = new \stdclass;

		$score_script->script_score = [
			'script' => [
				'lang'   => 'painless',
				'inline' => $script,
			],
		];

		// Add script_score.
		$this->function_scores[] = $score_script;

		// Return original formatted args.
		return $formatted_args;
	}

	/**
	 * Apply non-dynamic boost/bury to formatted args
	 *
	 * @since 0.1.0
	 *
	 * @param  array  $action         Action configuration.
	 * @param  array  $formatted_args Formatted search args.
	 * @param  string $type           Type to apply (boost or bury).
	 * @return array                  New formatted search args.
	 */
	protected function add_non_dynamic_boost_or_bury( array $action, array $formatted_args, $type = 'boost' ) {
		// Get boost/bury value.
		$value = isset( $action[ $type ] ) ? $action[ $type ] : false;

		// Bail early if no value.
		if ( false === $value ) {
			return $formatted_args;
		}

		// Bail early if no text.
		if ( ! isset( $action['text'] ) ) {
			return $formatted_args;
		}

		// Use name if searching terms.
		if ( stristr( $action['field'], 'terms.' ) ) {
			$action['field'] .= '.name';
		}

		// If menu order only use menu order and not meta.
		if ( stristr( $action['field'], 'menu_order' ) ) {
			$action['field'] = 'menu_order';
		}

		// Lowercase text.
		$text = strtolower( $action['text'] );

		// Start query array for should match.
		$query = array(
			'query' => $text,
		);

		// Only add boost if dynamic function_score isn't added.
		if ( ! $this->plugin->dynamic_scripting ) {
			$query['boost'] = $value;
		}

		// Update formatted args.
		$formatted_args['query']['bool']['should'][] = array(
			'match' => array(
				$action['field'] => $query,
			),
		);

		return $formatted_args;
	}
}
