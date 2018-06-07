<?php
/**
 * Base plugin class for the EP Rules Builder.
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder;

/**
 * This Plugin's main class.
 * All subclasses/modules are managed from within this class.
 * This class is used as a singleton and should not be instantiated directly.
 *
 * Usage:
 *
 * ```php
 *
 * $plugin = \EP_Rules_Builder\Plugin::get_instance();
 *
 * ```
 */
class Plugin {
    /**
     * Holds a single instance of this class.
     *
     * @var \EP_Rules_Builder\Plugin
     */
    private static $singleton_instance = null;

    /**
     * Returns a single instance of this class.
     *
     * @return \EP_Rules_Builder\Plugin A singleton instance of this class.
     */
    public static get_instance() {
        if ( null === self::$singleton_instance ) {
            self::$singleton_instance = new self();
        }

        return self::$singleton_instance;
    }

    /**
     * Class constructor.
     *
     * @return void
     */
    private function __construct() {

    }

    /**
     * Starts the plugin by subscribing to the WordPress lifecycle hooks.
     *
     * @return void
     */
    public function enable() {

    }

}
