<?php
/**
 * PHPUnit bootstrap file
 *
 * @package My_Plugin
 */

//$_tests_dir = getenv( 'WP_TESTS_DIR' )'
$_tests_dir = __DIR__ . '../../tmp/wordpress-tests-lib';
//$_tests_dir = __DIR__;
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	require dirname( dirname( __FILE__ ) ) . '/woocommerce-utm-tracking.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
