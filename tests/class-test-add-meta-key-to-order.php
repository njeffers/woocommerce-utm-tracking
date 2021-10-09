<?php
/**
 * Class Test_Sample
 *
 * @package My_Plugin
 */

/**
 * Sample test case.
 */
class Test_UTM_add_meta_key_to_order extends WP_UnitTestCase {

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
	 * Confirm that passing valid utm_keys returns an int from update_post_meta() (success)
	 *
	 * @author Nick Jeffers
	 * @url    github.com/njeffers
	 */
	function test_add_meta_key_to_order__add_valid_key() {

		$order_id = $this->create_post_return_id();

		$test = new Woocommerce_Utm_Tracking_Admin( 'woocommerce-utm-tracking', '1.0.0' );

		$this->assertInternalType(
			'int',
			$test->add_meta_key_to_order( $order_id, 'utm_source', 'utm_source1', $this->get_utm_tracking_keys() )
		);

	}

	/**
	 * We should get false when trying to add an invalid meta_key to the post
	 *
	 * @author Nick Jeffers
	 * @url    github.com/njeffers
	 */
	function test_add_meta_key_to_order__failure_adding_invalid_key() {

		$order_id = $this->create_post_return_id();

		$test = new Woocommerce_Utm_Tracking_Admin( 'woocommerce-utm-tracking', '1.0.0' );

		$this->assertFalse(
			$test->add_meta_key_to_order( 1231231, 'invalid_utm_field', 'invalid_utm_value', $this->get_utm_tracking_keys() )
		);

	}

	/**
	 * We don't want to allow meta keys to be overwritten
	 *
	 * @author Nick Jeffers
	 * @url    github.com/njeffers
	 */
	function test_add_meta_key_to_order__dont_allow_metas_to_be_overwritten() {

		$order_id = $this->create_post_return_id();

		$test = new Woocommerce_Utm_Tracking_Admin( 'woocommerce-utm-tracking', '1.0.0' );

		// add initial meta key
		$test->add_meta_key_to_order( $order_id, 'utm_source', 'utm_source1', $this->get_utm_tracking_keys() );

		// this one should fail, as it already exists
		$this->assertFalse(
			$test->add_meta_key_to_order( $order_id, 'utm_source', 'utm_source1', $this->get_utm_tracking_keys() )
		);

	}

}
