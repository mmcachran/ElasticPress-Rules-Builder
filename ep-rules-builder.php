<?php
/**
 * Plugin Name: ElasticPress Rules Builder
 * Description: Control (boost, bury, hide) search results based on seach keywords.
 * Version:     0.1
 * Author:      mmcachran
 * Author URI:  https://github.com/mmcachran/ElasticPress-Rules-Builder
 * License:     GPLv2 or later
 * Text Domain: ep-rules-builder
 * Domain Path: /lang/
 *
 * @package ElasticPress Rules Builder
 */

namespace EP_Rules_Builder;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
