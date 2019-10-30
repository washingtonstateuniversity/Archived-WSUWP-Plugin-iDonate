<?php

/**
 * The core plugin class file
 *
 * Defines the functions necessary to register our custom post statuses with
 * WordPress.
 *
 * @link       https://www.pmg.com/blog/wordpress-how-to-adding-a-custom-checkbox-to-the-post-publish-box/
 * @since      1.2.3
 *
 * @package    WSUWP_Plugin_iDonate_Post_Receipting_Checkbox
 * @author     Jared Crain
 */
class WSUWP_Plugin_iDonate_Post_Receipting_Checkbox {

	/**
	 * Initializes the plugin by registering the hooks necessary
	 * for adding a custom checkbox in WordPress.
	 *
	 * @since    1.2.3
	 */
    public function init() {
		add_action('post_submitbox_misc_actions', array( $this, 'createCustomField' ) );
		add_action('save_post', array( $this, 'saveCustomField' ) );
	}

	function createCustomField()
	{
		$post_id = get_the_ID();
	
		if (get_post_type($post_id) != 'post') {
			return;
		}
	
		$value = get_post_meta($post_id, '_my_custom_field', true);
		wp_nonce_field('my_custom_nonce_'.$post_id, 'my_custom_nonce');
		?>
		<div class="misc-pub-section misc-pub-section-last">
			<label><input type="checkbox" value="1" <?php checked($value, true, true); ?> name="_my_custom_field" /><?php _e('My Custom Checkbox Field', 'pmg'); ?></label>
		</div>
		<?php
	}

	function saveCustomField($post_id)
	{
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		
		if (
			!isset($_POST['my_custom_nonce']) ||
			!wp_verify_nonce($_POST['my_custom_nonce'], 'my_custom_nonce_'.$post_id)
		) {
			return;
		}
		
		if (!current_user_can('edit_post', $post_id)) {
			return;
		}
		if (isset($_POST['_my_custom_field'])) {
			update_post_meta($post_id, '_my_custom_field', $_POST['_my_custom_field']);
		} else {
			delete_post_meta($post_id, '_my_custom_field');
		}
	}
}
