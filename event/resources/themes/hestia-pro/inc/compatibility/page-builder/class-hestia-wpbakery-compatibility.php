<?php
/**
 * WPBakery Compatibility class.
 *
 * @package Hestia
 */

/**
 * Class Hestia_WPBakery_Compatibility
 */
class Hestia_WPBakery_Compatibility extends Hestia_Page_Builder_Helper {

	/**
	 * Initialize features.
	 */
	public function init() {
		parent::init();

		add_action( 'vc_frontend_editor_render', array( $this, 'maybe_set_page_template' ), 100 );
	}

	/**
	 * This function checks if WPBakery plugin is activated.
	 *
	 * @return bool
	 */
	protected function should_load_feature() {
		if ( ! defined( 'WPB_VC_VERSION' ) ) {
			return false;
		}
		return true;
	}


	/**
	 * Check if page is edited with WPBakery.
	 *
	 * @param string $pid Post id.
	 *
	 * @return bool
	 */
	protected function is_edited_with_builder( $pid ) {
		if ( function_exists( 'vc_enabled_frontend' ) ) {
			return vc_enabled_frontend();
		}
		return false;
	}
}
