<?php
/**
 * Replace header, footer or hooks with the default editor.
 *
 * @package Inc/Addons/Modules/Custom_Layouts/Admin/Builders
 */

/**
 * Class Default_Editor
 */
class Hestia_Default_Editor extends Hestia_Abstract_Builders {

	/**
	 * Check if class should load or not.
	 *
	 * @return bool
	 */
	public function should_load() {
		return true;
	}

	/**
	 * Function that enqueues styles if needed.
	 */
	public function add_styles() {
		return false;
	}

	/**
	 * Builder id.
	 *
	 * @return string
	 */
	function get_builder_id() {
		return 'default';
	}

	/**
	 * Load markup for current hook.
	 *
	 * @param int $post_id Layout id.
	 *
	 * @return mixed|void
	 */
	function render( $post_id ) {
		global $post;
		$post_id = Hestia_Abstract_Builders::maybe_get_translated_layout( $post_id );
		setup_postdata( $post_id );
		$post    = get_post( $post_id );
		$content = get_the_content( null, false, $post );
		$content = apply_filters( 'the_content', $content );
		echo apply_filters( 'neve_custom_layout_magic_tags', $content, $post_id );
		wp_reset_postdata();

		return true;
	}
}
