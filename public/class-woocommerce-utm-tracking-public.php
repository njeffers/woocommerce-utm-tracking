<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://nickjeffers.com
 * @since      1.0.0
 *
 * @package    Woocommerce_Utm_Tracking
 * @subpackage Woocommerce_Utm_Tracking/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Woocommerce_Utm_Tracking
 * @subpackage Woocommerce_Utm_Tracking/public
 * @author     Nick Jeffers <nick@nickjeffers.com>
 */
class Woocommerce_Utm_Tracking_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;


	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-utm-tracking-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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


		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-utm-tracking-public.js', array('jquery'), $this->version, true );


        if( ! Woocommerce_Utm_Tracking::woocommerce_enabled() || ! is_wc_endpoint_url( 'order-received' ) ){
            return;
        }

        global $wp;

        // Get the order ID
        $order_id  = absint( $wp->query_vars['order-received'] );

        $utm_data = array(
            'order_id' => $order_id,
            'nonce' => wp_create_nonce('woocommerce_utm_' . $order_id),
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'variableKeys' => wp_json_encode( Woocommerce_Utm_Tracking::get_tracking_meta_keys() )
        );

		$script_before = "var utm_data = " . wp_json_encode( $utm_data ) . ';';

		wp_add_inline_script($this->plugin_name, $script_before, 'before');


        $script_after = '            
            variableToReturn = getVariablesArray();

            if( Object.keys(variableToReturn).length > 0 ){
                passVariablesToOrder( variableToReturn );
            }';

		wp_add_inline_script($this->plugin_name, $script_after, 'after');

	}


}
