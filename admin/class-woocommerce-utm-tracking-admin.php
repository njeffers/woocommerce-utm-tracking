<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://nickjeffers.com
 * @since      1.0.0
 *
 * @package    Woocommerce_Utm_Tracking
 * @subpackage Woocommerce_Utm_Tracking/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_Utm_Tracking
 * @subpackage Woocommerce_Utm_Tracking/admin
 * @author     Nick Jeffers <nick@nickjeffers.com>
 */
class Woocommerce_Utm_Tracking_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

//        update_post_meta(17, 'woocommerce_utm_source', 'test_nick');

		add_action( 'add_meta_boxes', array($this, 'woocomerce_utm_add_order_tracking_metabox' ) );
		add_action( 'wp_ajax_add_utms_to_order', array($this, 'woocomerce_utm_add_utms_to_order' ) );
		add_action( 'wp_ajax_nopriv_add_utms_to_order', array($this, 'woocomerce_utm_add_utms_to_order' ) );
		add_action( 'manage_edit-shop_order_columns', array($this, 'woocommerce_utm_add_utm_source_column_header' ), 20 );
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'woocommerce_utm_add_utm_source_column_content' ) );


	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-utm-tracking-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		// NOTE: left as a placeholder for possible future use.

		// wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-utm-tracking-admin.js', array( 'jquery' ), $this->version, false );

	}


	/**
	 * Add our metabax to the Edit Order screen
	 *
	 * @author Nick Jeffers
	 * @url    github.com/njeffers
	 */
	public function woocomerce_utm_add_order_tracking_metabox() {
		add_meta_box(
			'woocomerce-utm-order-tracking',
			'UTM Tracking &#x2764;',
			array( $this, 'woocommerce_utm_add_order_tracking_metabox_callback'),
			'shop_order',
			'side',
			'low'
		);
	}


	/**
	 * Callback function from woocomerce_utm_add_order_tracking_metabox()
	 *
	 * @author Nick Jeffers
	 * @url    github.com/njeffers
	 */
	public function woocommerce_utm_add_order_tracking_metabox_callback()
	{

		include_once( 'partials/woocommerce-utm-tracking-admin-display.php' );

	}

	public function woocomerce_utm_add_utms_to_order()
	{

		if ( ! isset( $_REQUEST[ 'order_id' ] ) || ! isset( $_REQUEST[ 'variables' ] ) || ! isset( $_REQUEST[ 'nonce' ] ) ) {
			wp_die( __( 'Missing required values.', 'woocommerce-utm' ) );
		}


		$order_id   = (int) $_REQUEST[ 'order_id' ];

		// @todo - need to sanitize the variables field, or pass them in a different way;
		$variables = $_REQUEST[ 'variables' ];
		$nonce   = sanitize_text_field( $_REQUEST[ 'nonce' ] ) ;


		if( ! is_int( $order_id ) || $order_id < 1 ){
			wp_die( __( 'Bad order_id value.', 'woocommerce-utm' ) );
		}

		if(  'shop_order' != get_post_type( $order_id ) ){
			wp_die( __( 'order_id isn\'t an Order post type.', 'woocommerce-utm' ) );
		}

		if ( ! wp_verify_nonce( $nonce, 'woocommerce_utm_' . $order_id ) ) {
			wp_die( __( 'Nonce Failed.', 'woocommerce-utm' ) );
		}


		$utm_tracking_keys = Woocommerce_Utm_Tracking::get_tracking_meta_keys();

		foreach( $variables as $name => $value ){
			$this->add_meta_key_to_order( $order_id, $name, $value, $utm_tracking_keys );
		}

		do_action( 'woocommerce_utm_variables_added_to_order', $order_id, $variables );

	}

	/**
	 * Adds a meta key and value to the order if it doesn't already exist
	 *
	 * @param $order_id
	 * @param $name
	 * @param $value
	 * @param $utm_tracking_keys
	 *
	 * @return bool|int
	 * @author Nick Jeffers
	 * @url    github.com/njeffers
	 */
	public function add_meta_key_to_order( $order_id, $name, $value, $utm_tracking_keys ){

		// new key, who dis? - make sure it's an approved key...
		if( ! in_array( $name, $utm_tracking_keys ) ){
			return false;
		}

		// don't allow it to be overwritten if it's already set
		if ( get_post_meta( $order_id, $name ) ) {
			return false;
		}

		return update_post_meta( $order_id, '_woocommerce_utm_' . $name, $value );

	}


	/**
	 * Add a "UTM Source" column on the Orders page, but put it after the Shipping Address Column
	 *
	 * @param $columns
	 *
	 * @return array
	 * @author Nick Jeffers
	 * @url    github.com/njeffers
	 */
	public function woocommerce_utm_add_utm_source_column_header( $columns )
	{

		$new_columns = array();

		foreach ( $columns as $column_name => $column_info ) {

			$new_columns[ $column_name ] = $column_info;

			if ( 'shipping_address' === $column_name ) {
				$new_columns[ 'utm_source' ] = __( 'UTM Source', 'my-textdomain' );
			}
		}

		return $new_columns;
	}

	/**
	 * Content for the UTM Source column
	 *
	 * @param $column
	 * @author Nick Jeffers
	 * @url    github.com/njeffers
	 */
	public function woocommerce_utm_add_utm_source_column_content( $column )
	{
		global $post;

		if ( 'utm_source' === $column ) {
			$order = wc_get_order( $post->ID );
			$value = $order->get_meta( '_woocommerce_utm_utm_source' );

			echo $value;
		}
	}


}
