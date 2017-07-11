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
		$this->add_idonate_fund_caps_to_admin();
		$this->add_idonate_fund_caps_to_non_admin_roles();
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
			'taxonomies' => array( 'idonate_priorities', 'idonate_colleges', 'idonate_campuses', 'idonate_programs' ),
			'supports' => array( 'title', 'custom-fields' ),
			'capability_type' => array( 'idonate_fund' , 'idonate_funds' ),
			'map_meta_cap' => true,
		);

		register_post_type( 'idonate_fund', $args );
	}

	# Give Administrators All Fund Editing Capabilities
	function add_idonate_fund_caps_to_admin() {
		$caps = array(
			'read',
			'read_idonate_fund',
			'read_private_idonate_funds',
			'edit_idonate_funds',
			'edit_private_idonate_funds',
			'edit_published_idonate_funds',
			'edit_others_idonate_funds',
			'publish_idonate_funds',
			'delete_idonate_funds',
			'delete_private_idonate_funds',
			'delete_published_idonate_funds',
			'delete_others_idonate_funds',
		);

		$roles = array(
			get_role( 'administrator' ),
		);

		foreach ( $roles as $role ) {
			foreach ( $caps as $cap ) {
				$role->add_cap( $cap );
			}
		}
	}

	# Give other roles Fund editing Capabilities
	function add_idonate_fund_caps_to_non_admin_roles() {
		add_role( 'fund_editor', 'Fund Editor' );

		# Everyone gets these capabilities:
		$caps = array(
			'read_idonate_fund',
			'read_private_idonate_funds',
			'edit_idonate_funds',
			'edit_private_idonate_funds',
			'edit_published_idonate_funds',
			'edit_others_idonate_funds',
			'publish_idonate_funds',
			'delete_idonate_funds',
		);

		$roles = array(
			get_role( 'fund_editor' ),
		);

		foreach ( $roles as $role ) {
			foreach ( $caps as $cap ) {
				$role->add_cap( $cap );
			}
		}
	}
}
