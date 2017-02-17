<?php

/**
 * plugin class file for the fundselector shortcode
 *
 * Defines the functions necessary to register our custom post types with
 * WordPress.
 *
 * @link       https://github.com/washingtonstateuniversity/WSUWP-Plugin-iDonate
 * @since      0.0.1
 *
 * @package    WSUWP_Plugin_iDonate_ShortCode_Fund_Selector
 * @author     Blair Lierman
 */
class WSUWP_Plugin_iDonate_ShortCode_Fund_Selector {

	/**
	 * Initializes the plugin by registering the hooks necessary
	 * for creating our custom post type within WordPress.
	 *
	 * @since    0.0.1
	 */
	public function init() {

		add_shortcode( 'idonate_fundselector', array( $this, 'fundselector_create_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wsuf_fundselector_enqueue_scripts' ), 99 );
		add_action( 'rest_api_init', array( $this, 'wsuf_fundselector_register_designation_id' ) );
		add_action( 'rest_api_init', array( $this, 'wsuf_fundselector_register_endpoint_get_funds' ) );
	}

	// [idonate_fundselector embed="iDonate-embed-guid" server="production/staging"]
	public function fundselector_create_shortcode( $atts ) {
		$args = shortcode_atts( array(
			'embed' => '',
			'server' => 'production',
			'rest_url' => rest_url( '' ),
			'unit_taxonomy' => '',
			'unit_category' => '',
			'unit_description' => '',
			'unit_title' => '',
			'unit_scholarship_category' => 'idonate_general-scholarship',
		), $atts );

		$args['embed'] = sanitize_key( $args['embed'] );

		if ( empty( $args['embed'] ) ) {
			return '';
		}

		$args['unit_taxonomy'] = sanitize_key( $args['unit_taxonomy'] );
		$args['unit_category'] = sanitize_key( $args['unit_category'] );
		$args['unit_scholarship_category'] = sanitize_key( $args['unit_scholarship_category'] );
		$args['unit_title'] = sanitize_text_field( $args['unit_title'] );
		$args['unit_description'] = esc_html( $args['unit_description'] );

		wp_localize_script( 'wsuf_fundselector', 'wpData', array(
			'request_url_base' => esc_url( $args['rest_url'] . 'wp/v2/' ),
			'plugin_url_base' => esc_url( $args['rest_url'] . 'idonate_fundselector/v1/' ),
			'unit_taxonomy' => $args['unit_taxonomy'],
			'unit_category' => $args['unit_category'],
		));

		

		$return_string = '<div id="fundSelectionForm"><div id="firstform">';

		$return_string .= '	<div class="help-text wrapper hidden">
						 		<span class="left">Thank you! You can add more funds from one of these categories:</span>
								<span class="close remove right"><a href="#"></a></span>
							</div>';
		
		$unit_included = ! empty( $args['unit_taxonomy'] ) && ! empty( $args['unit_category'] );

		$unit_title = ! empty( $args['unit_title'] ) ? $args['unit_title'] : 'Unit Priorities';

		$unit_priorities = $unit_included ? '<a class="active" role="button" data-tab="categoryTab" href="#" >' . $unit_title . '</a>' : '';

		// Major Categories button group
		$return_string .= '
		<div id="majorcategory" class="wrapper" role="group" aria-label="Category Selection Group">'
			. $unit_priorities .
			'<a class="' . ($unit_included ? '' : 'active') . '" role="button" data-tab="prioritiesTab" href="#" >WSU Priorities</a>
			<a class="" role="button" data-tab="subcategoryTab" data-category="idonate_programs" href="#">Programs</a>
			<a class="" role="button" data-tab="subcategoryTab" data-category="idonate_colleges" href="#">Colleges</a>
			<a class="" role="button" data-tab="subcategoryTab" data-category="idonate_campuses" href="#">Campuses</a>
            <span><a class="search" role="button" href="#"></a></span>
		</div>';

		// Unit Priorities Tab
		$unit_priorities = $this->wsuf_fundselector_funds_get_funds( $args['unit_taxonomy'], $args['unit_category'] );
		$unit_priorities_list = '<option disabled selected value> SELECT A FUND </option>';

		foreach ( $unit_priorities as $unit_priority ) {
			$fund_name = esc_html( $unit_priority['fund_name'] );
			$fund_designation_id = esc_attr( $unit_priority['designation_id'] );
			$unit_priorities_list .= "<option value=\"{$fund_designation_id}\">{$fund_name}</option>";
		}

		$unit_description = ! empty( $args['unit_description'] ) ? $args['unit_description'] : 'Please choose a fund to support';

		$return_string .= '
		<div id="categoryTab" class="categoryTab wrapper ' . ($unit_included ? '' : 'hidden') . '">    
			<label for="unit-priorities">' . $unit_description . '</label>
			<select name="unit-priorities" id="unit-priorities" class="form-control fund-selection fund">'
				. $unit_priorities_list .
			'</select>
		</div>';

		// University Priorities Tab
		$priorities = $this->wsuf_fundselector_funds_get_funds( 'idonate_priorities', 'idonate_priorities' );
		$priorities_list = '<option disabled selected value> SELECT A FUND </option>';

		foreach ( $priorities as $priority ) {
			$fund_name = esc_html( $priority['fund_name'] );
			$fund_designation_id = esc_attr( $priority['designation_id'] );
			$priorities_list .= "<option value=\"{$fund_designation_id}\">{$fund_name}</option>";
		}

		$return_string .= '
		<div id="prioritiesTab" class="categoryTab wrapper ' . ($unit_included ? 'hidden' : '') . '">    
			<label for="priorities">Choose one of the university\'s greatest needs</label>
			<select name="priorities" id="priorities" class="form-control fund-selection fund">'
				. $priorities_list .
			'</select>
		</div>';

		// Subcategory tab (Colleges, Campuses, Programs)
		$return_string .= '
		<div id="subcategoryTab" class="categoryTab hidden">';

		// Categories Select Menu
		$return_string .= '    
		<label for="subcategories">Choose a category</label>
		<select name="subcategories" id="subcategories" class="form-control fund">
			<option disabled selected value> SELECT A CATEGORY </option>
		</select>';

		// Funds Select Menu
		$return_string .= '    
		<div class="wrapper"><label for="funds">Choose a fund</label>
		<select name="funds" id="funds" class="form-control fund-selection fund" disabled>
			<option disabled selected value> SELECT A FUND </option>
		</select></div>';

		$return_string .= '</div>';

		// Search AutoComplete
		$return_string .= '
		<div class="form-group has-feedback wrapper search hidden">
			<input id="fundSearch" type="text" class="form-control" placeholder="Search for a fund..." >
			<span class="glyphicon glyphicon-search form-control-feedback" aria-hidden="true"></span>
		</div>
		';

		// Dollar Amount Selectors
		$return_string .= '
		<div class="amountwrapper wrapper" style="opacity:0;display:none;" role="group">
			<button type="button" class="amount-selection btn btn-default other" data-amount="25" >OTHER</button>
			<button type="button" class="amount-selection btn btn-default" data-amount="25" >$25</button>
			<button type="button" class="amount-selection btn btn-default" data-amount="50">$50</button>
			<button type="button" class="amount-selection btn btn-default" data-amount="100">$100</button>
			<button type="button" class="amount-selection btn btn-default" data-amount="2000">$2000</button>
			<div class="input-group otherprice" style="opacity:0; display:none;">
				<div class="input-group-addon">$</div>
				<!-- Maximum length of 8 includes cents (.xx) -->
				<input class="form-control" id="otherAmount" placeholder="Other Amount" maxlength="8" data-max="99999" value="100" type="text">
				<a class="btnlhtgry plus" id="addFundButton">Add Fund</a>
				<span id="errorOtherAmount" class="error"></span>
			</div>
			<input name="inpAmount" id="inpAmount" class="value" data-token="amount" value="100" type="hidden">
		</div>
		';

		// Add Fund Button
		$return_string .= '
			<input name="inpDesignationId" id="inpDesignationId" type="hidden">
			<input name="inpFundName" id="inpFundName" type="hidden">
		';

		// Selected Funds List
		$return_string .= '
		<ul id="selectedFunds" class="list-group wrapper">
		</ul>
		';

		// Total Amount information
		$return_string .= '
		<div class="disclaimer total" style="display:none;">Thank you for your donation of $<span id="totalAmount"></span>. When you proceed to checkout, you will be sent to our payment processing service.</div>
		';

		// Credit Card Disclaimer
		$return_string .= '
		<div class="disclaimer creditcard" style="display:none;">Please note: The WSU Foundation does not retain your credit card information after the processing of your donation is complete.</div>
		';

		// Scholarship Support Checkbox
		$scholarship_fund = $this->wsuf_fundselector_funds_get_single_scholarship_fund( $args['unit_scholarship_category'] );

		if ( ! empty( $scholarship_fund ) ) {
			$scholarship_des_id = esc_attr( $scholarship_fund['designation_id'] );
			$scholarship_description = esc_html( $scholarship_fund['description'] );
			$scholarship_name = sanitize_text_field( $scholarship_fund['fund_name'] );
			$scholarship_title = sanitize_text_field( $scholarship_fund['title'] );

			$return_string .= '
			<input type="checkbox" id="genScholarship" value="scholarship_check" data-designation_id="' . $scholarship_des_id . '" data-fund_name="' . $scholarship_name . '" data-amount=10 > 
			<label for="genScholarship">' . $scholarship_description . ' (' . $scholarship_title  . ').</label>
			';
		}

		$return_string .= '
		<div class="gift-planning">
			<div class="gift-planning-header">Is WSU in your Will?</div>
			<p>Charitable gifts from estates and other planned gifts play an integral role in the future of Washington State University. The WSU Foundation offers several tax-wise giving options to support WSU’s mission while fulfilling your personal philanthropic goals.</p>
			<input type="checkbox" id="gpInWill"> 
			<label for="gpInWill">I have included the WSU Foundation in my Will or other estate plans.</label>
			<input type="checkbox" id="gpMoreInfo"> 
			<label for="gpMoreInfo">I am considering including the WSU Foundation in my Will or other estate plans. Please send me information.</label>
		</div>
		';

		// Continue button
		$return_string .= '<p class="txtright continuebutton" style="display:none;"><a class="btnlhtgry" id="continueButton">Proceed to Checkout</a></p></div>';

		if ( 'staging' === $args['server'] ) {
			$url = 'https://staging-embed.idonate.com/idonate.js';
		} else {
			$url = 'https://embed.idonate.com/idonate.js';
		}

		// Loading Message List
		$return_string .= '<div id="secondform" style="display: none;"><a class="left btnlhtgry" id="backButton">Back</a><h2 id="embedLoadingMessage" style="display: none;">Loading Payment Process</h2>';

		wp_enqueue_script( 'wsuf_fundselector_idonate_embed', $url, array(), false, true );

		$return_string .= '<div id="iDonateEmbed" data-idonate-embed="' . $args['embed'] . '" data-defer></div>';

		return $return_string . '</div></div>';
	}

	/*
	* Enqueues JavaScript files
	*/
	function wsuf_fundselector_enqueue_scripts() {

		wp_enqueue_script( 'wsuf_fundselector_utils', plugins_url( '/wsuwp-shortcode-fundselector-utils.js', __FILE__ ), array( 'jquery' ), '1.0', true );

		wp_enqueue_script( 'wsuf_fundselector', plugins_url( '/wsuwp-shortcode-fundselector.js', __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-autocomplete', 'jquery-ui-button', 'underscore' ), '1.0', true );

		wp_enqueue_script( 'wsuf_fundselector_jquery_editable', plugins_url( '/jquery.editable.min.js', __FILE__ ), array( 'jquery' ), null, true );

		wp_localize_script( 'wsuf_fundselector', 'wpData', array(
			'request_url_base' => esc_url( rest_url( '/wp/v2/' ) ),
			'plugin_url_base' => esc_url( rest_url( '/idonate_fundselector/v1/' ) ),
		));

		wp_enqueue_script( 'wsuf_fundselector_jquery_editable', plugins_url( '/jquery.editable.min.js', __FILE__ ), array( 'jquery' ), null, true );

		wp_enqueue_style( 'wsuf_fundselector', plugins_url( 'css/wsuwp-plugin-idonate.css', dirname( __FILE__ ) ), array( 'spine-theme' ), null );
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

		$category = $data['category']; // 'idonate_priorities';
		$subcategory = $data['subcategory'];

		return $this->wsuf_fundselector_funds_get_funds( $category, $subcategory );
	}

	/**
	* Get a list of all categories for a specific taxonomy
	*
	* @return array $return_array
	*/
	function wsuf_fundselector_funds_get_categories( $taxonomy ) {
		$terms = get_terms( array(
			'taxonomy' => $taxonomy,
			'hide_empty' => false,
		) );

		$return_array = array();
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			//loop over each category
			foreach ( $terms as $t ) {
				$return_array[] = array( 'category_name' => $t->name );
			}
		}

		return $return_array;
	}

	/**
	* Get a list of all funds stored in the wsuf fundselector funds table
	*
	* @return array $return_array
	*/
	function wsuf_fundselector_funds_get_funds( $category, $subcategory ) {
		$fund_list = get_posts(array(
			'post_type'   => 'idonate_fund',
			'post_status' => 'any',
			'posts_per_page' => -1, // Get all posts
			'tax_query' => array(
					array(
						'taxonomy' => $category,
						'field' => 'slug',
						'terms' => $subcategory,
					),
				),
			'orderby' => 'title',
			'order' => 'ASC',
			)
		);

		$return_array = array();

		//loop over each post
		foreach ( $fund_list as $p ) {
			//get the meta you need form each post
			$des_id = get_post_meta( $p->ID, 'designationId' , true );
			$post_title = $p->post_title;
			//do whatever you want with it
			$return_array[] = array( 'fund_name' => $post_title, 'designation_id' => $des_id );
		}

		return $return_array;
	}

	/**
	* Get a single fund stored in the a specific category
	*
	* @return array $return_fund
	*
	* @since 0.0.9
	*/
	function wsuf_fundselector_funds_get_single_scholarship_fund( $subcategory ) {
		$fund_list = get_posts(array(
			'post_type'   => 'idonate_fund',
			'post_status' => 'any',
			'posts_per_page' => 1,
			'tax_query' => array(
					array(
						'taxonomy' => 'idonate_scholarships',
						'field' => 'slug',
						'terms' => $subcategory,
					),
				),
			'orderby' => 'title',
			'order' => 'ASC',
			)
		);

		$return_fund = array();

		if ( count( $fund_list ) === 1 ) {
			$fund_object = $fund_list[0];

			$des_id = get_post_meta( $fund_object->ID, 'designationId' , true );
			$scholarship_title = get_post_meta( $fund_object->ID, 'scholarship_title' , true );
			$scholarship_desc = get_post_meta( $fund_object->ID, 'scholarship_description' , true );

			if ( empty( $scholarship_title ) ) { $scholarship_title = $fund_object->post_title; }
			if ( empty( $scholarship_desc ) ) { $scholarship_title = 'general scholarships'; }

			$return_fund = array( 'fund_name' => $fund_object->post_title, 'title' => $scholarship_title, 'description' => $scholarship_desc, 'designation_id' => $des_id );
		}

		return $return_fund;
	}
}
