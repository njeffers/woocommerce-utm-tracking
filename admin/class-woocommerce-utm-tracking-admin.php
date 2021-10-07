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
//		add_action( "wp_ajax_add_utms_to_order", "woocomerce_utm_add_utms_to_order" );
//		add_action( "wp_ajax_nopriv_add_utms_to_order", "woocomerce_utm_add_utms_to_order" );

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Utm_Tracking_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Utm_Tracking_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-utm-tracking-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Utm_Tracking_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Utm_Tracking_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-utm-tracking-admin.js', array( 'jquery' ), $this->version, false );

	}


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




	public function woocommerce_utm_add_order_tracking_metabox_callback( )
	{

        global $post;


		echo '<table class="styled-table">';
		echo '<thead>';
		echo '<tr>';
		echo '<th>Key</td>';
		echo '<th>Value</td>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		foreach( Woocommerce_Utm_Tracking::get_tracking_meta_keys()  as $meta_key ) {

			$value = get_post_meta( $post->ID, 'woocommerce_utm_' . $meta_key, true );

			if( $meta_key == 'publisher_id' ){

//				$publisher_array = revoffers_get_publisher_array();

//				if( array_key_exists( (int) $value, $publisher_array ) ){
//					$value .= ' (' . $publisher_array[$value] . ')';
//				}
			}
			if( $value ){
				echo '<tr>';
				echo '<td class="styled-table-key">' . $meta_key . '</td>';
				echo '<td class="styled-table-value">' . $value . '</td>';
				echo '</tr>';
			}

		}

		echo '</tbody>';
		echo '</table>';

	}

	public function woocomerce_utm_add_utms_to_order()
	{

		$order_id   = (int) $_REQUEST[ 'order_id' ];
		$variables = wc_clean( $_REQUEST[ 'variables' ] );
		$nonce      = $_REQUEST[ 'nonce' ];

		if ( ! $order_id || ! $variables || ! $nonce ) {
			wp_die( __( 'Missing required values.', 'woocommerce' ) );
		}

		if ( ! wp_verify_nonce( wc_clean( $nonce ), 'woocommerce_utm_' . $order_id ) ) {
			wp_die( __( 'Nonce Failed.', 'woocommerce' ) );
		}


		// echo '<pre>' . print_r( $variables, true ) . '</pre>';

		$utm_tracking_keys = Woocommerce_Utm_Tracking::get_tracking_meta_keys();

		foreach( $variables as $name => $value ){

			// new key, who dis? - make sure it's an approved key...
			if( ! in_array( $name, $utm_tracking_keys ) ){
				continue;
			}

			// dont allow it to be overwritten if it's already set
			if ( get_post_meta( $order_id, $name ) ) {
				// continue;
			}

			update_post_meta( $order_id, 'woocommerce_utm_' . $name, $value );

		}

		do_action( 'woocommerce_utm_variables_added_to_order', $order_id, $variables );

		return;

	}


}
