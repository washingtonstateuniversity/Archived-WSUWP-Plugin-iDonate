<?php

class WSUWP_Plugin_iDonate_ShortCode_FundSelector_Tests extends WP_UnitTestCase {

	function setUp() {
		parent::setUp();

		// Set up a priorities category under the priorities taxonomy
		$this->cat_id = $this->factory->category->create( array( 'name' => 'priorities', 'taxonomy' => 'priorities' ) );

		// Set up a test post in the priorities category
		$this->post_id = $this->factory->post->create( array(
			'post_title' => 'Test Post',
			'post_status' => 'private',
			'post_type' => 'fund',
			'post_category' => array( 'priorities', $this->cat_id ),
			)
		);

		// Add the priority category
		wp_set_object_terms( $this->post_id, $this->cat_id, 'priorities' );
	}

	/**
	 * Make sure the get funds function can find a specific fund
	 */
	public function test_WSUWP_Plugin_iDonate_ShortCode_FundSelector_check_post_count() {

		$test_fundselector_shortcode = new WSUWP_Plugin_iDonate_ShortCode_Fund_Selector();

		$priorities = $test_fundselector_shortcode->wsuf_fundselector_funds_get_funds( 'priorities', 'priorities' );

		$this->assertCount( 1 , $priorities );
	}
}
