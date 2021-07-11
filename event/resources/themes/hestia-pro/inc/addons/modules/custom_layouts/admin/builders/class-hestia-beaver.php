<?php
/**
 * Replace header, footer or hooks for Beaver Builder page builder.
 *
 * @package Inc/Addons/Modules/Custom_Layouts/Admin/Builders
 */
/**
 * Class Hestia_Beaver
 */
class Hestia_Beaver extends Hestia_Abstract_Builders {


	/**
	 * Check if class should load or not.
	 *
	 * @return bool
	 */
	function should_load() {
		return class_exists( 'FLBuilderModel', false );
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
		return 'beaver';
	}

	/**
	 * Load markup for current hook.
	 *
	 * @param int $post_id Layout id.
	 *
	 * @return mixed|void
	 */
	function render( $post_id ) {
		$post_id = Hestia_Abstract_Builders::maybe_get_translated_layout( $post_id );
		$content = \FLBuilderShortcodes::insert_layout(
			array(
				'id' => $post_id,
			)
		);
		echo apply_filters( 'hestia_custom_layout_magic_tags', $content, $post_id );
		return true;
	}
}
