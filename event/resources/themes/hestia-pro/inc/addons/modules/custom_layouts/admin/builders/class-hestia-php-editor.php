<?php
/**
 * Php Editor to add custom code;
 *
 * @package Inc/Addons/Modules/Custom_Layouts/Admin/Builders
 */

/**
 * Class Php_Editor
 */
class Hestia_Php_Editor extends Hestia_Abstract_Builders {

	/**
	 * Check if class should load or not.
	 *
	 * @return bool
	 */
	function should_load() {
		return true;
	}

	/**
	 * Function that enqueues styles if needed.
	 */
	function add_styles() {
		return false;
	}

	/**
	 * Builder id.
	 *
	 * @return string
	 */
	function get_builder_id() {
		return 'custom';
	}

	/**
	 * Load markup for current hook.
	 *
	 * @param int $post_id Layout id.
	 *
	 * @return mixed|void
	 */
	function render( $post_id ) {
		$post_id       = Hestia_Abstract_Builders::maybe_get_translated_layout( $post_id );
		$file_name     = get_post_meta( $post_id, 'hestia_editor_content', true );
		$wp_upload_dir = wp_upload_dir( null, false );
		$upload_dir    = $wp_upload_dir['basedir'] . '/hestia-theme/';
		$file_path     = $upload_dir . $file_name . '.php';
		include_once( $file_path );

		return true;
	}

}
