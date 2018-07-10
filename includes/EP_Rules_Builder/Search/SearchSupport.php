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
	 * @var string
	 */
	protected $search_term = false;

	/**
	 * Holds function scores for boost/bury.
	 *
	 * @var array
	 */
	protected $function_scores = [];

	/**
	 * Determines if the metabox should be registered.
	 *
	 * @return bool True if the metabox should be registered, false otherwise.
	 */
	public function can_register() {
		// Determine if this is the search page.
		$can_integrate = is_search();

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

		// Do things with the query...

		return $formatted_args;
	}
}
