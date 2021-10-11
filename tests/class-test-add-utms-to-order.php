<?php
/**
 * Class Test_Sample
 *
 * @package My_Plugin
 */

/**
 * Sample test case.
 */
class Test_UTM_add_utms_to_order extends WP_Ajax_UnitTestCase {

	public function setUp()
	{
		parent::setUp();
	}

	public function tearDown()
	{
		parent::tearDown();
	}

	function get_utm_tracking_keys(){
		return array(
			'utm_source',
			'utm_medium',
		);
	}

	/**
	 * Create and return a new post ID
	 *
	 * @return int
	 * @author Nick Jeffers
	 * @url    github.com/njeffers
	 */
	function create_post_return_id( $post_type = 'shop_order' ){
		$Order = $this->factory()->post->create_and_get(
			array(
				'post_type' => $post_type
			)
		);

		return (int) $Order->ID;
	}


	/**
	 * If we don't pass an order_id to the add_utms_to_order action, we need to die()
	 *
	 * @author Nick Jeffers
	 * @url    github.com/njeffers
	 * @covers Woocommerce_Utm_Tracking_Admin::woocomerce_utm_add_utms_to_order()
	 */
	public function test_fail_if_missing_values(){


		$_POST[ 'order_id' ] = $this->create_post_return_id();

		try {
			$this->_handleAjax( 'add_utms_to_order' );
		} catch ( WPAjaxDieStopException $e ) {
			// We expected this, do nothing.
		}

		$this->assertEquals( 'Missing required values.', $e->getMessage() );

	}


	// @todo make sure it's an actual order post_type
	public function test_fail_if_order_id_isnt_order_post_type(){

		// create a plain post (not a shop_order)
		$_POST[ 'order_id' ] = $this->create_post_return_id( 'post' );
		$_POST[ 'variables' ] = 'test';
		$_POST[ 'nonce' ] = wp_create_nonce('woocommerce_utm_' . $_POST[ 'order_id' ] );

		try {
			$this->_handleAjax( 'add_utms_to_order' );
		} catch ( WPAjaxDieContinueException $e ) {
			// We expected this, do nothing.
		}

		$this->assertEquals( 'order_id isn\'t an Order post type.', $e->getMessage() );
	}

	/**
	 * Make sure we fail if order id is less than 1
	 *
	 * @author Nick Jeffers
	 * @url    github.com/njeffers
	 */
	public function test_confirm_order_id_greater_than_zero(){

		$_POST[ 'order_id' ] = 0;
		$_POST[ 'variables' ] = 'test';
		$_POST[ 'nonce' ] = wp_create_nonce('woocommerce_utm_' . $_POST[ 'order_id' ] );

		try {
			$this->_handleAjax( 'add_utms_to_order' );
		} catch ( WPAjaxDieContinueException $e ) {
			// We expected this, do nothing.
		}

		$this->assertEquals( 'Bad order_id value.', $e->getMessage() );

	}

	/**
	 * Make sure we bail if we order_id isn't an int and greater than 0
	 *
	 * @author Nick Jeffers
	 * @url    github.com/njeffers
	 */
	public function test_confirm_order_id_is_int(){

		$_POST[ 'order_id' ] = 'somestring';
		$_POST[ 'variables' ] = 'test';
		$_POST[ 'nonce' ] = wp_create_nonce('woocommerce_utm_' . $_POST[ 'order_id' ] );

		try {
			$this->_handleAjax( 'add_utms_to_order' );
		} catch ( WPAjaxDieContinueException $e ) {
			// We expected this, do nothing.
		}

		$this->assertEquals( 'Bad order_id value.', $e->getMessage() );

	}

	/**
	 * @author Nick Jeffers
	 * @url    github.com/njeffers
	 * @covers Woocommerce_Utm_Tracking_Admin::woocomerce_utm_add_utms_to_order()
	 */
	public function test_fail_if_nonce_bad(){

		$_POST[ 'order_id' ] = $this->create_post_return_id();
		$_POST[ 'variables' ] = 'test';
		$_POST[ 'nonce' ] = 'bad_nonce';

		try {
			$this->_handleAjax( 'add_utms_to_order' );
		} catch ( WPAjaxDieContinueException $e ) {
			// We expected this, do nothing.
		}

		$this->assertEquals( 'Nonce Failed.', $e->getMessage() );

	}





}
