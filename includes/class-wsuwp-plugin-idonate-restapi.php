<?php

/**
 * plugin class file for the custom fundselector REST API additions
 *
 * Defines the functions necessary to register our custom REST API methods with
 * WordPress.
 *
 * @link       https://github.com/washingtonstateuniversity/WSUWP-Plugin-iDonate
 * @since      0.0.17
 *
 * @package    WSUWP_Plugin_iDonate_Custom_REST_API
 * @author     Blair Lierman
 */
class WSUWP_Plugin_iDonate_Custom_REST_API {

	/**
	 * Initializes the plugin by registering the hooks necessary
	 * for creating our custom post type within WordPress.
	 *
	 * @since    0.0.17
	 */
	public function init() {

		add_action( 'rest_api_init', array( $this, 'wsuf_fundselector_register_designation_id' ) );
		add_action( 'rest_api_init', array( $this, 'wsuf_fundselector_register_endpoint_get_funds' ) );
		add_action( 'rest_api_init', array( $this, 'wsuf_fundselector_register_endpoint_get_fund_by_des_id' ) );
		add_action( 'rest_api_init', array( $this, 'wsuf_fundselector_register_endpoint_fund_search' ) );

		/** Loads the fundselector shortcode class file. */
		require_once( dirname( __FILE__ ) . '/class-wsuwp-shortcode-fundselector.php' );

		$this->fundselector_shortcode = new WSUWP_Plugin_iDonate_ShortCode_Fund_Selector();
	}

	/**
	* Add the designation ID to the Fund response
	**/
	function wsuf_fundselector_register_designation_id() {
		register_rest_field( 'idonate_fund',
			'designationId',
			array(
				'get_callback'    => array( $this, 'wsuf_fundselector_get_post_meta' ),
				'update_callback' => null,
				'schema'          => null,
			)
		);
	}

	/**
	* Get the value for the specified field_name argument
	*
	* @param array $object Details of current post.
	* @param string $field_name Name of field.
	* @param WP_REST_Request $request Current request
	*
	* @return mixed
	*/
	function wsuf_fundselector_get_post_meta( $object, $field_name, $request ) {
		return get_post_meta( $object['id'], $field_name, true );
	}

	/**
	* Add a new custom REST endpoint to get funds for a specific taxonomy and category by slug
	*
	* @since 0.0.5
	**/
	function wsuf_fundselector_register_endpoint_get_funds() {
		register_rest_route( 'idonate_fundselector/v1', '/funds/(?P<category>.*?)/(?P<subcategory>.*)',
			array(
				'methods' => 'GET',
				'callback' => array( $this, 'wsuf_fundselector_funds_get_funds_rest' ),
			)
		);
	}

	/**
	* Get a list of funds for a specific taxonomy and category via the REST API
	*
	* @param WP_Rest_Request $data data from the REST request
	*
	* @return array $return_array (from wsuf_fundselector_funds_get_funds)
	*
	* @since 0.0.5
	*/
	function wsuf_fundselector_funds_get_funds_rest( $data ) {

		$category = $data['category'];
		$subcategory = $data['subcategory'];

		return $this->fundselector_shortcode->wsuf_fundselector_funds_get_funds( $category, $subcategory );
	}

	/**
	* Add a new custom REST endpoint to get a fund name for a specific designation ID by slug
	*
	* @since 0.0.17
	**/
	function wsuf_fundselector_register_endpoint_get_fund_by_des_id() {
		register_rest_route( 'idonate_fundselector/v1', '/fund/(?P<designationId>.*?)',
			array(
				'methods' => 'GET',
				'callback' => array( $this, 'wsuf_fundselector_funds_get_fund_by_des_id_rest' ),
			)
		);
	}

	/**
	* Gets a fund for a specific designation ID passed via the REST API
	*
	* @param WP_Rest_Request $data data from the REST request
	*
	* @return array $return_array (from wsuf_fundselector_funds_get_funds)
	*
	* @since 0.0.17
	*/
	function wsuf_fundselector_funds_get_fund_by_des_id_rest( $data ) {
		$designation_id = $data['designationId'];

		return $this->fundselector_shortcode->wsuf_fundselector_funds_get_fund_name( $designation_id );
	}

	/**
	* Add a new custom REST endpoint to get a list of funds based off of a search term
	*
	* @since 1.1.2
	**/
	function wsuf_fundselector_register_endpoint_fund_search() {
		//var_dump("Reached endpoint register");
		register_rest_route( 'idonate_fundselector/v1', '/search/(?P<searchTerm>.*?)',
			array(
				'methods' => 'GET',
				'callback' => array( $this, 'wsuf_fundselector_funds_search_rest' ),
			)
		);
	}

	/**
	* Gets a list of funds matching a search term passed via the REST API
	*
	* @param WP_Rest_Request $data data from the REST request
	*
	* @return array $return_array (from wsuf_fundselector_funds_search_funds)
	*
	* @since 1.1.2
	*/
	function wsuf_fundselector_funds_search_rest( $data ) {
		$search_term = $data['searchTerm'];
		return $this->fundselector_shortcode->wsuf_fundselector_funds_search_funds( $search_term );
	}
}
