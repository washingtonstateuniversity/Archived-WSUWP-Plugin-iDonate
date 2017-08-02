<?php

/**
 * The core plugin class file
 *
 * Defines the functions necessary to register our custom post statuses with
 * WordPress.
 *
 * @link       http://jamescollings.co.uk/blog/wordpress-create-custom-post-status/
 * @since      1.1.2
 *
 * @package    WSUWP_Plugin_iDonate_Post_Status
 * @author     Jared Crain
 */
class WSUWP_Plugin_iDonate_Post_Status {

	/**
	 * Initializes the plugin by registering the hooks necessary
	 * for creating our custom post status within WordPress.
	 *
	 * @since    1.1.2
	 */
	public function init() {
		add_action( 'init', array( $this, 'create_archive_custom_post_status' ) );
		add_action( 'init', array( $this, 'create_searchable_custom_post_status' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'wsuf_custom_post_status_enqueue_scripts' ), 99 );
	}

	function create_archive_custom_post_status() {
		register_post_status( 'archive', array(
			'label'                     => _x( 'Archive', 'post' ),
			'public'                    => false,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Archive <span class="count">(%s)</span>', 'Archive <span class="count">(%s)</span>' ),
		));
	}

	function create_searchable_custom_post_status() {
		register_post_status( 'searchable', array(
			'label'                     => _x( 'Searchable', 'post' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Searchable <span class="count">(%s)</span>', 'Searchable <span class="count">(%s)</span>' ),
		));
	}

	function wsuf_custom_post_status_enqueue_scripts() {
		global $post;
		$post_type = $post && $post->post_type ? $post->post_type : '';
		$post_status = $post && $post->post_status ? $post->post_status : '';
		wp_enqueue_script( 'wsuf_custom_post_status', plugins_url( '/wsuwp-custom-post-status.js', __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-autocomplete', 'jquery-ui-button', 'underscore' ), '1.1.4', true );
		wp_localize_script( 'wsuf_custom_post_status', 'wpData', array(
			'post_type' => $post_type,
			'post_status' => $post_status,
		));
	}
}
