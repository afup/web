<?php
/**
 * Compatibility functions for Dokan Multivendor functions
 *
 * @package hestia
 * @since 1.1.44
 */

/**
 * Class Hestia_Dokan_Compatibility
 */
class Hestia_Dokan_Compatibility extends Hestia_Abstract_Main {

	/**
	 * Hestia_Dokan_Compatibility constructor.
	 */
	public function init() {
		if ( ! class_exists( 'WeDevs_Dokan', false ) ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'dokan_dashboard_wrap_before', array( $this, 'before_wrap' ) );
		add_action( 'dokan_dashboard_wrap_after', array( $this, 'after_wrap' ) );
	}

	/**
	 * Enqueue style for dokan plugin.
	 *
	 * @since 1.1.44
	 */
	public function enqueue() {
		wp_enqueue_style( 'hestia-dokan-style', get_template_directory_uri() . '/inc/addons/plugin-compatibility/dokan/style.css', array(), HESTIA_VERSION );
	}

	/**
	 * Add wraper for new-product-single for Dokan
	 *
	 * @since 1.1.44
	 */
	protected function before_wrap() {
		?>
		<div class="section section-text pagebuilder-section">
		<?php
	}

	/**
	 * Close wrapper for new-product-single for Dokan
	 *
	 * @since 1.1.44
	 */
	protected function after_wrap() {

		?>
		</div>
		<?php
	}
}
