<?php

/**
 * The core plugin class file
 *
 * Defines the functions necessary to register our AJAX functions
 * in WordPress that will
 *
 * @since      0.0.2
 *
 * @package    WSUWP_Plugin_iDonate_Ajax_Data
 * @author     Blair Lierman <blair.lierman@wsu.edu>
 */
class WSUWP_Plugin_iDonate_Ajax_Data {

	/**
	 * Initializes the actions necessary for hooking in our AJAX methods
	 *
	 * @since    0.0.2
	 */
	public function init() {

		add_action( 'wp_ajax_nopriv_wsuwp_plugin_idonate_ajax_fund_search', array( $this, 'wsuwp_plugin_idonate_ajax_fund_search' ) );
		add_action( 'wp_ajax_wsuwp_plugin_idonate_ajax_fund_search', array( $this, 'wsuwp_plugin_idonate_ajax_fund_search' ) );

	}

	public function wsuwp_plugin_idonate_ajax_fund_search() {
		$search_term = strtolower( $_REQUEST['term'] );

		$args = array(
			'post_type' => 'fund',
			's' => $search_term,
			'post_status' => 'private',
			'posts_per_page' => -1, // Get all posts
			'orderby'     => 'title',
			'order'       => 'ASC',
		);

		$wp_query = new WP_Query( $args );

		$return = array();

		// The Loop
		if ( $wp_query->have_posts() ) {

			while ( $wp_query->have_posts() ) {
				$wp_query->the_post();

				$title = get_the_title();
				$des_id = get_post_meta( get_the_ID(), 'designationId', true );

				$return[] = array( 'label' => $title , 'value' => $des_id );
			}
		}

		echo( wp_json_encode( $return ) );

		wp_die();

	}
}
