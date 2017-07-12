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
 * @author     Blair Lierman
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
		add_action( 'admin_footer-post.php', array( $this, 'append_post_status_list' ) );
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

	function append_post_status_list() {
		global $post;
		$complete = '';
		$label = '';
		$text = '';
		;
		if ( 'idonate_fund' === $post->post_type ) {
			if ( 'archive' === $post->post_status || 'searchable' === $post->post_status ) {
				$complete = ' selected="selected"';
				$label = '<span id="post-status-display">Archived</span>';
				$text = ('archive' === $post->post_status ? '$("#post-status-display").text("Archived");' : '$("#post-status-display").text("Searchable");');
			} else {
				$text = '$(".misc-pub-section label").append("' . $label . '");';
			}
			$script = '
				<script>
				jQuery(document).ready(function($){
				$(\'select#post_status\').append(\'<option value="archive" ' . $complete . '="">Archive</option>\');
				$(\'select#post_status\').append(\'<option value="searchable" ' . $complete . '="">Searchable</option>\');
				' . $text . '
				});
				</script>
				';
			echo $script; // WPCS: XSS ok, sanitization ok.
		}
	}
}
