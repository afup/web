<?php
/**
 * Page editor helper class.
 *
 * @package Hestia
 * @since   Hestia 1.1.3
 */

/**
 * Class Hestia_Customizer_Page_Editor_Helper
 */
class Hestia_Sync_About extends Hestia_Abstract_Main {
	/**
	 * Initialize Customizer Page Editor Helper.
	 */
	public function init() {

		/**
		 * Initialize buffers.
		 */
		add_action( 'customize_controls_init', array( $this, 'init_buffer' ), 1 );

		/**
		 * Set image buffer.
		 */
		add_action( 'wp_ajax_update_image_buffer', array( $this, 'update_image_buffer' ) );

		/**
		 * Synchronize post when you save customizer
		 */
		add_action( 'customize_save', array( $this, 'trigger_sync_from_customizer' ), 10 );

		/**
		 * Synchronize customizer when you save the frontpage
		 */
		add_action( 'save_post', array( $this, 'trigger_sync_from_page' ), 10 );

		/**
		 * Trigger update customizer when page is set from Reading.
		 */
		add_filter( 'pre_update_option_page_on_front', array( $this, 'trigger_sync_from_page_option' ), 10, 2 );

		/**
		 * The main function where the sync is happening
		 */
		add_action( 'after_setup_theme', array( $this, 'sync_controls' ) );
	}

	/**
	 * Initialize buffers when we enter customizer. The buffers should have the controls values.
	 */
	public function init_buffer() {
		$current_thumbnail = get_theme_mod( 'hestia_feature_thumbnail' );
		set_theme_mod( 'hestia_feature_thumbnail_buffer', $current_thumbnail );
	}

	/**
	 * When the editor or the image is updated in customizer, we should set the buffer. The buffer helps not saving the
	 * value until the user click "Publish".
	 */
	public function update_image_buffer() {
		$params = $_REQUEST;

		if ( ! isset( $params['nonce'] ) || ! wp_verify_nonce( $params['nonce'], 'image_nonce' ) ) {
			wp_send_json_error( 'Wrong nonce' );
		}

		if ( ! empty( $params['value'] ) ) {
			set_theme_mod( 'hestia_feature_thumbnail_buffer', $params['value'] );
		}

		wp_die();
	}

	/**
	 * When customizer is saved, we set the flag to 'sync_page' value to know that we should update the frontpage
	 * feature image but before that we should update image with the one from buffer.
	 * We need this buffer to keep the old value and update it only if the user click "Publish" in customizer.
	 *
	 * @since 1.1.60
	 */
	function trigger_sync_from_customizer() {
		$current_thumbnail = get_theme_mod( 'hestia_feature_thumbnail_buffer' );
		if ( $current_thumbnail === 'image_was_synced' ) {
			return false;
		}
		set_theme_mod( 'hestia_feature_thumbnail', $current_thumbnail );
		set_theme_mod( 'hestia_feature_thumbnail_buffer', 'image_was_synced' );

		$frontpage_id = get_option( 'page_on_front' );
		if ( ! empty( $frontpage_id ) ) {
			update_option( 'hestia_sync_needed', 'sync_page' );
		}
		return true;
	}

	/**
	 * When the frontpage is edited, we set a flag with 'sync_customizer' value to know that we should update
	 * hestia_feature_thumbnail control.
	 *
	 * @param int $post_id ID of the post that we need to update.
	 *
	 * @since 1.1.60
	 */
	public function trigger_sync_from_page( $post_id ) {
		$frontpage_id = get_option( 'page_on_front' );
		if ( empty( $frontpage_id ) ) {
			return;
		}

		if ( intval( $post_id ) === intval( $frontpage_id ) ) {
			update_option( 'hestia_sync_needed', 'sync_customizer' );
		};
	}

	/**
	 * Sync customizer when the user changes the front page from reading.
	 *
	 * @param int $new_value New page value.
	 * @param int $old_value Old page value.
	 *
	 * @return mixed
	 */
	public function trigger_sync_from_page_option( $new_value, $old_value ) {
		if ( is_customize_preview() ) {
			return $new_value;
		}
		update_option( 'hestia_sync_needed', 'sync_customizer' );
		return $new_value;
	}

	/**
	 * Based on 'hestia_sync_needed' option value, update either page or customizer controls and then we update
	 * the flag as false to know that we don't need to update anything.
	 *
	 * @since 1.1.60
	 */
	function sync_controls() {
		$should_sync = get_option( 'hestia_sync_needed' );
		if ( $should_sync === false ) {
			return;
		}
		$frontpage_id = get_option( 'page_on_front' );
		if ( empty( $frontpage_id ) ) {
			return;
		}
		switch ( $should_sync ) {
			// Synchronize customizer controls with page content
			case 'sync_customizer':
				$featured_image = get_theme_mod( 'hestia_feature_thumbnail', get_template_directory_uri() . '/assets/img/contact.jpg' );
				if ( has_post_thumbnail( $frontpage_id ) ) {
					$featured_image = get_the_post_thumbnail_url( $frontpage_id );
				}
				set_theme_mod( 'hestia_feature_thumbnail', $featured_image );
				break;
			// Synchronize frontpage content with customizer values.
			case 'sync_page':
				$thumbnail    = get_theme_mod( 'hestia_feature_thumbnail', get_template_directory_uri() . '/assets/img/contact.jpg' );
				$thumbnail_id = attachment_url_to_postid( $thumbnail );
				update_post_meta( $frontpage_id, '_thumbnail_id', $thumbnail_id );

				break;
		}
		update_option( 'hestia_sync_needed', false );
	}
}
