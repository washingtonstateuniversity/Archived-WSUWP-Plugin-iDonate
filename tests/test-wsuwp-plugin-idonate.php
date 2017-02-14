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

		$this->assertTrue( post_type_exists( 'idonate_fund' ) );
	}

	/**
	 * Verify that only one new post type was added
	 */
	public function test_WSUWP_Plugin_iDonate_Post_Type_Fund_count() {
		$args = array(
			'_builtin' => false,
		);

		$post_types = get_post_types( $args );

		$this->assertTrue( count( $post_types ) === 1 );

	}

	public function test_WSUWP_Plugin_iDonate_Custom_Taxonomies_exists() {

		$this->assertTrue( taxonomy_exists( 'idonate_priorities' ) );
		$this->assertTrue( taxonomy_exists( 'idonate_colleges' ) );
		$this->assertTrue( taxonomy_exists( 'idonate_campuses' ) );
		$this->assertTrue( taxonomy_exists( 'idonate_programs' ) );

	}

	public function test_WSUWP_Plugin_iDonate_Custom_Taxonomies_count() {

		$args = array(
			'_builtin' => false,
		);

		$taxonomies = get_taxonomies( $args );

		$this->assertNotEmpty( $taxonomies );
		$this->assertCount( 5, $taxonomies );
	}
}
