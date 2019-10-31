<?php

/**
 * The core plugin class file
 *
 * Defines the functions necessary to register our custom reciept checkbox with
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
		add_action( 'post_submitbox_misc_actions', array( $this, 'createCustomField' ) );
		add_action( 'save_post', array( $this, 'saveCustomField' ) );
	}

	function createCustomField() {
		$post_id = get_the_ID();

		if ( get_post_type( $post_id ) !== 'idonate_fund' ) {
			return;
		}

		$value = get_post_meta( $post_id, 'hideReceipt', true );
		wp_nonce_field( 'hide_receipt_'.$post_id, 'hide_receipt' );

		?>
		<div class="misc-pub-section misc-pub-section-last">
			<label><input type="checkbox" value="1" <?php checked( $value, true, true ); ?> name="hideReceipt" /><?php esc_attr_e( 'Hide Email Receipts', 'pmg' ); ?></label>
		</div>
		<?php
	}

	function saveCustomField( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if (
			! isset( $_POST['hide_receipt'] ) ||
			! wp_verify_nonce( $_POST['hide_receipt'], 'hide_receipt_' . $post_id )
		) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( isset( $_POST['hideReceipt'] ) ) {
			update_post_meta( $post_id, 'hideReceipt', $_POST['hideReceipt'] );
		} else {
			delete_post_meta( $post_id, 'hideReceipt' );
		}
	}
}
