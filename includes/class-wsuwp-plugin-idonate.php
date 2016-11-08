<?php

class WSUWP_Plugin_iDonate {
	/**
	 * @var WSUWP_Plugin_iDonate
	 */
	private static $instance;

	/**
	 * Maintain and return the one instance. Initiate hooks when
	 * called the first time.
	 *
	 * @since 0.0.1
	 *
	 * @return \WSUWP_Plugin_iDonate
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new WSUWP_Plugin_iDonate();
			self::$instance->setup_hooks();
		}
		return self::$instance;
	}

	/**
	 * Setup hooks to include.
	 *
	 * @since 0.0.1
	 */
	public function setup_hooks() {
		self::$instance->custom_posttypes_run();
		self::$instance->custom_taxonomies_run();
		self::$instance->fundselector_shortcode_run();
	}

	/**
	* Creates an instance of the WSUWP_Plugin_iDonate_Post_Type_Fund class
	* and calls its initialization method.
	*
	* @since    0.0.1
	*/
	private function custom_posttypes_run() {

		/** Loads the custom post type Fund class file. */
		require_once( dirname( __FILE__ ) . '/class-custom-post-type-fund.php' );

		$custom_post_type = new WSUWP_Plugin_iDonate_Post_Type_Fund();
		$custom_post_type->init();

	}

	/**
	* Creates an instance of the WSUWP_Plugin_iDonate_Custom_Taxonomies class
	* and calls its initialization method.
	*
	* @since    0.0.1
	*/
	private function custom_taxonomies_run() {

		/** Loads the custom taxonomy class file. */
		require_once( dirname( __FILE__ ) . '/class-custom-taxonomies.php' );

		$custom_post_type = new WSUWP_Plugin_iDonate_Custom_Taxonomies();
		$custom_post_type->init();

	}

	/**
	* Creates an instance of the WSUWP_Plugin_iDonate_Custom_Taxonomies class
	* and calls its initialization method.
	*
	* @since    0.0.1
	*/
	private function fundselector_shortcode_run() {

		/** Loads the custom taxonomy class file. */
		require_once( dirname( __FILE__ ) . '/class-wsuwp-shortcode-fundselector.php' );

		$fundselector_shortcode = new WSUWP_Plugin_iDonate_ShortCode_Fund_Selector();
		$fundselector_shortcode->init();

	}
}
