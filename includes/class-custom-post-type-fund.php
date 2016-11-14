<?php

/**
 * The core plugin class file
 *
 * Defines the functions necessary to register our custom post types with
 * WordPress.
 *
 * @link       https://premium.wpmudev.org/blog/create-wordpress-custom-post-types/
 * @since      1.0.0
 *
 * @package    WSUWP_Plugin_iDonate_Post_Type_Fund
 * @author     Blair Lierman
 */
class WSUWP_Plugin_iDonate_Post_Type_Fund {

	/**
	 * Initializes the plugin by registering the hooks necessary
	 * for creating our custom post type within WordPress.
	 *
	 * @since    1.0.0
	 */
	public function init() {

		add_action( 'init', array( $this, 'custom_post_types' ) );

	}

	function custom_post_types() {
		$labels = array(
			'name' => 'Funds',
			'singular_name' => 'Fund',
			'menu_name' => 'Funds',
		);

		$args = array(
			'labels' => $labels,
			'public' => false,
			'show_ui' => true,
			'show_in_admin_bar' => true,
			'show_in_rest' => true,
			'menu_icon' => 'dashicons-welcome-learn-more',
			'hierarchical' => false,
			'taxonomies' => array( 'priorities', 'colleges', 'campuses', 'programs' ),
			'supports' => array( 'title', 'custom-fields' ),
		);

		register_post_type( 'idonate_fund', $args );
	}
}
