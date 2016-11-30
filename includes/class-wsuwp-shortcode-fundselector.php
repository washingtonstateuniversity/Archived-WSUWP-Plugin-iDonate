<?php

/**
 * plugin class file for the fundselector shortcode
 *
 * Defines the functions necessary to register our custom post types with
 * WordPress.
 *
 * @link       https://github.com/washingtonstateuniversity/WSUWP-Plugin-iDonate
 * @since      1.0.0
 *
 * @package    WSUWP_Plugin_iDonate_ShortCode_Fund_Selector
 * @author     Blair Lierman
 */
class WSUWP_Plugin_iDonate_ShortCode_Fund_Selector {

	/**
	 * Initializes the plugin by registering the hooks necessary
	 * for creating our custom post type within WordPress.
	 *
	 * @since    1.0.0
	 */
	public function init() {

		add_shortcode( 'idonate_fundselector', array( $this, 'fundselector_create_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wsuf_fundselector_enqueue_scripts' ) );
		add_action( 'rest_api_init', array( $this, 'wsuf_fundselector_register_designation_id' ) );
	}

	// [idonate_fundselector embed="iDonate-embed-guid"]
	public function fundselector_create_shortcode( $atts ) {
		$args = shortcode_atts( array(
			'embed' => '',
		), $atts );

		$return_string = '<div id="fundSelectionForm"  class="container-fluid">';

		// Major Categories button group
		$return_string .= '
		<div id="majorcategory" class="btn-group btn-group-justified" role="group" aria-label="Category Selection Group">
			<a class="btn btn-primary active" role="button" data-tab="prioritiesTab" href="#" >Priorities</a>
			<a class="btn btn-default" role="button" data-tab="subcategoryTab" data-category="idonate_programs" href="#">Programs</a>
			<a class="btn btn-default" role="button" data-tab="subcategoryTab" data-category="idonate_colleges" href="#">Colleges</a>
			<a class="btn btn-default" role="button" data-tab="subcategoryTab" data-category="idonate_campuses" href="#">Campuses</a>
		</div>';

		// Priorities Tab
		$priorities = $this->wsuf_fundselector_funds_get_funds( 'idonate_priorities', 'idonate_priorities' );
		$priorities_list = '<option disabled selected value> -- Select a Fund -- </option>';

		foreach ( $priorities as $priority ) {
			$fund_name = esc_html( $priority['fund_name'] );
			$fund_designation_id = esc_attr( $priority['designation_id'] );
			$priorities_list .= "<option value=\"{$fund_designation_id}\">{$fund_name}</option>";
		}

		$return_string .= '
		<div id="prioritiesTab" class="categoryTab">    
			<label for="priorities">Choose one of the university\'s greatest needs</label>
			<select name="priorities" id="priorities" class="form-control fund-selection">'
				. $priorities_list .
			'</select>
		</div>';

		// Subcategory tab (Colleges, Campuses, Programs)
		$return_string .= '
		<div id="subcategoryTab" class="categoryTab hidden">';

		// Categories Select Menu
		$return_string .= '    
		<label for="subcategories">Choose a category</label>
		<select name="subcategories" id="subcategories" class="form-control">
			<option disabled selected value> -- Select a Category -- </option>
		</select>';

		// Funds Select Menu
		$return_string .= '    
		<label for="funds">Choose a fund</label>
		<select name="funds" id="funds" class="form-control fund-selection" disabled>
			<option disabled selected value> -- Select a Fund -- </option>
		</select>';

		$return_string .= '</div>';

		// Search Separator
		$return_string .= '
		<div class="search-separator">
		OR
		</div>
		';

		// Search AutoComplete
		$return_string .= '
		<div class="form-group has-feedback">
			<label for="fundSearch">Search for any Fund: </label>
			<input id="fundSearch" type="search" class="form-control" placeholder="Search for a fund..." >
			<span class="glyphicon glyphicon-search form-control-feedback" aria-hidden="true"></span>
		</div>
		';

		// Selected Funds List
		$return_string .= '
		<div class="panel panel-default">
			<!-- Default panel contents -->
			<div class="panel-heading" id="selectedFundsHeader">Selected Funds</div>
			<div class="panel-body">
				<p>These are the funds that you have chosen to support today. Thank you for your generousity!</p>
			</div>
			
			<!-- List group -->
			<div class="panel-collapse collapse in container-fluid" id="selectedFundsContainer" role="tabpanel" aria-expanded="true" aria-labelledby="selectedFundsHeader">
				<div class="clear-fix"></div>
				<div id="selectedFunds" class="list-group row">
					<div class="list-group-item col-sm-9" data-designation_id="1234"> Test Fund Name for WSU Foundation</div> 
					<div class="list-group-item col-sm-2">$25</div>
					<a href="#" role="button" class="list-group-item col-sm-2"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span><span class="sr-only">Remove Fund button</span></a>
				</div>
			</div>
		</div>
		';

		// Continue button
		$return_string .= '<button type="button" id="continueButton" class="btn btn-primary btn-block" disabled>Continue</button>';

		$embed_id = esc_attr( $args['embed'] );
		$return_string .= '<div id="iDonateEmbed" data-idonate-embed="' . $embed_id . '" data-defer></div>';

		return $return_string . '</div>';
	}

	/*
	* Enqueues JavaScript files
	*/
	function wsuf_fundselector_enqueue_scripts() {

		wp_enqueue_script( 'wsuf_fundselector_utils', plugins_url( '/wsuwp-shortcode-fundselector-utils.js', __FILE__ ), array( 'jquery' ), '1.0', true );

		wp_enqueue_script( 'wsuf_fundselector', plugins_url( '/wsuwp-shortcode-fundselector.js', __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-autocomplete', 'jquery-ui-button' ), '1.0', true );

		wp_localize_script( 'wsuf_fundselector', 'wpData', array(
			'request_url_base' => esc_url( rest_url( '/wp/v2/' ) ),
		));

		wp_enqueue_script( 'idonate_embed', 'https://staging-embed.idonate.com/idonate.js', '2', true );

		wp_enqueue_style( 'wsuf_fundselector_bootstrap', plugins_url( '/../css/bootstrap.min.css', __FILE__ ) );
		wp_enqueue_style( 'wsuf_fundselector_bootstrap_theme', plugins_url( '/../css/bootstrap-theme.css', __FILE__ ) );
		wp_enqueue_style( 'wsuf_fundselector_bootstrap_theme_custom', plugins_url( '/../css/bootstrap-theme-custom.css', __FILE__ ) );
		wp_enqueue_style( 'wsuf_fundselector', plugins_url( '/../wsuwp-plugin-idonate.css', __FILE__ ), array( 'spine-theme' ), null );
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
}
