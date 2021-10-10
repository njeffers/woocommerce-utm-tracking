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

	function create_post_return_id(){
		$Order = $this->factory()->post->create_and_get();

		return (int) $Order->ID;
	}


	/**
	 * If we don't pass an order_id to the add_utms_to_order action, we need to die()
	 *
	 * @author Nick Jeffers
	 * @url    github.com/njeffers
	 */
	public function test_fail_if_missing_order_id(){


		$_REQUEST[ 'order_id' ] = $this->create_post_return_id();

		try {
			$this->_handleAjax( 'add_utms_to_order' );
		} catch ( WPAjaxDieStopException $e ) {
			// We expected this, do nothing.
		}

		$this->assertEquals( 'Missing required values.', $e->getMessage() );

	}


	// @todo make sure it's an actual order post_type
	public function test_fail_if_order_id_isnt_order_post_type(){
		
	}

	// @todo make sure order_id is an int
	public function test_confirm_order_id_is_int(){}
	public function test_fail_if_nonce_bad(){

		$_REQUEST[ 'order_id' ] = 123;
		$_REQUEST[ 'variables' ] = 'test';
		$_REQUEST[ 'nonce' ] = 'nonce';

		try {
			$this->_handleAjax( 'add_utms_to_order' );
		} catch ( WPAjaxDieStopException $e ) {
			// We expected this, do nothing.
		}

		$this->assertEquals( 'Nonce Failed.', $e->getMessage() );


//		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'woocommerce_utm_' . $order_id ) ) {


		}


	/*
	 * @todo
	 * - fail if nonce is bad
	 * -
	 *
	 */






}
