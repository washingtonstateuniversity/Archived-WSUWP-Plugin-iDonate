<?php

class WSUWP_Plugin_iDonate_Tests extends WP_UnitTestCase {
	/**
	 * An initial sample test to verify working tests.
	 */
	public function test_WSUWP_Plugin_iDonate_function() {
		$this->assertTrue( true );
	}

	/**
	 * Verify that the 'fund' Post Type created by the plugin exists
	 */
	public function test_WSUWP_Plugin_iDonate_Post_Type_Fund() {

		$this->assertTrue(post_type_exists( 'fund' ));
	}

	/**
	 * Verify that only one new post type was added
	 */
	public function test_WSUWP_Plugin_iDonate_Post_Type_Fund_count() {
		$args = array(
			'_builtin' => false,
		);

		$post_types = get_post_types( $args ); 

		$this->assertTrue(count($post_types) == 1);
	}
}
