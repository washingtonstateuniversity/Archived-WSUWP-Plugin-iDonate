<?php

class WSUWP_Plugin_iDonate_ShortCode_FundSelector_Tests extends WP_UnitTestCase {

	function setUp() {
		parent::setUp();

		// Set up a priorities category under the priorities taxonomy
		$this->cat_id = $this->factory->category->create( array( 'name' => 'idonate_priorities', 'taxonomy' => 'idonate_priorities' ) );

		// Set up a test post in the priorities category
		$this->post_id = $this->factory->post->create( array(
			'post_title' => 'Test Post',
			'post_status' => 'private',
			'post_type' => 'idonate_fund',
			'post_category' => array( 'idonate_priorities', $this->cat_id ),
			)
		);

		// Add the priority category
		wp_set_object_terms( $this->post_id, $this->cat_id, 'idonate_priorities' );
	}

	/**
	 * Make sure the get funds function can find a specific fund
	 */
	public function test_WSUWP_Plugin_iDonate_ShortCode_FundSelector_check_post_count() {

		$test_fundselector_shortcode = new WSUWP_Plugin_iDonate_ShortCode_Fund_Selector();

		$priorities = $test_fundselector_shortcode->wsuf_fundselector_funds_get_funds( 'idonate_priorities', 'idonate_priorities' );

		$this->assertCount( 1 , $priorities );
	}

	/**
	 * Make sure the version number was manually updated
	 */
	public function test_WSUWP_Plugin_iDonate_ShortCode_FundSelector_check_version_number() {

		$test_fundselector_shortcode = new WSUWP_Plugin_iDonate_ShortCode_Fund_Selector();

		$plugin_version = $test_fundselector_shortcode->wsuf_fundselector_get_plugin_version();

		$script_version = $test_fundselector_shortcode->wsuf_fundselector_get_script_version();

		$this->assertTrue( $plugin_version === $script_version );
	}
}
