<?php

/**
 * The core plugin class file
 *
 * Defines the functions necessary to register our custom post types with
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
	 * for creating our custom post type within WordPress.
	 *
	 * @since    1.1.2
	 */
	public function init() {
		add_action( 'init', array( $this, 'custom_post_status' ) );
        add_action( 'admin_footer-post.php', array( $this, 'append_post_status_list' ) );
	}

	function custom_post_status() {
     register_post_status( 'archive', array(
          'label'                     => _x( 'Archive', 'post' ),
          'public'                    => true,
          'show_in_admin_all_list'    => true,
          'show_in_admin_status_list' => true,
          'label_count'               => _n_noop( 'Archive <span class="count">(%s)</span>', 'Archive <span class="count">(%s)</span>' )
     ) );
    }

    function append_post_status_list() {
        global $post;
        $complete = '';
        $label = '';
        $text = '';
        var_dump('post type: ' . $post->post_type );
        if($post->post_type == 'idonate_fund') {
            if($post->post_status == 'archive') {
                $complete = ' selected="selected"';
                $label = '<span id="post-status-display">Archived</span>';
                $text = '$("#post-status-display").text("Archived");';
            }
            else {
                $text = '$(".misc-pub-section label").append("' . $label . '");';
            }
            echo '
                <script>
                jQuery(document).ready(function($){
                $(\'select#post_status\').append(\'<option value="archive" '. $complete .'="">Archive</option>\');
                ' . $text . '
                });
                </script>
                ';
        }
    }
}
