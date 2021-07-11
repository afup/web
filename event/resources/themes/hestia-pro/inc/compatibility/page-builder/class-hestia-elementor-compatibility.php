<?php
/**
 * Elementor Compatibility class.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Elementor_Compatibility
 */
class Hestia_Elementor_Compatibility extends Hestia_Page_Builder_Helper {



	/**
	 * Initialize features.
	 */
	public function init() {

		parent::init();
		add_action( 'after_switch_theme', array( $this, 'set_elementor_flag' ) );

		add_action( 'elementor/frontend/after_enqueue_styles', array( $this, 'enqueue_elementor_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'page_builder_enqueue' ) );

		add_action( 'wp_ajax_hestia_pagebuilder_hide_frontpage_section', array( $this, 'hestia_pagebuilder_hide_frontpage_section' ) );
		add_action( 'wp_ajax_hestia_elementor_deactivate_default_styles', array( $this, 'hestia_elementor_deactivate_default_styles' ) );

		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'maybe_set_page_template' ), 1 );
	}

	/**
	 * This function checks if Elementor plugin is activated.
	 *
	 * @return bool
	 */
	protected function should_load_feature() {
		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			return false;
		}
		return true;
	}


	/**
	 * Set flag for elementor.
	 */
	public function set_elementor_flag() {
		update_option( 'hestia_had_elementor', 'no' );
	}

	/**
	 * Enqueue styles for elementor.
	 */
	public function enqueue_elementor_styles() {
		$disabled_color_schemes      = get_option( 'elementor_disable_color_schemes' );
		$disabled_typography_schemes = get_option( 'elementor_disable_typography_schemes' );

		if ( $disabled_color_schemes === 'yes' && $disabled_typography_schemes === 'yes' ) {
			wp_enqueue_style( 'hestia-elementor-style', get_template_directory_uri() . '/assets/css/page-builder-style.css', array(), HESTIA_VERSION );
		}
	}

	/**
	 * Elementor default styles disabling.
	 */
	public function hestia_elementor_deactivate_default_styles() {
		$nonce = $_POST['nonce'];
		if ( ! wp_verify_nonce( $nonce, 'hestia-elementor-notice-nonce' ) ) {
			return;
		}
		$reply = $_POST['reply'];
		if ( ! empty( $reply ) ) {
			if ( $reply === 'yes' ) {
				update_option( 'elementor_disable_color_schemes', 'yes' );
				update_option( 'elementor_disable_typography_schemes', 'yes' );
			}
			update_option( 'hestia_had_elementor', 'yes' );
		}
		die();
	}

	/**
	 * Check if we're in Elementor Preview.
	 *
	 * @return bool
	 */
	private function is_plugin_preview() {
		if ( ( isset( $_REQUEST['action'] ) && 'elementor' === $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Check if page is edited with elementor.
	 *
	 * @param string $pid Post id.
	 *
	 * @return bool
	 */
	protected function is_edited_with_builder( $pid ) {
		$post_meta = get_post_meta( $pid, '_elementor_edit_mode', true );
		if ( $post_meta === 'builder' ) {
			return true;
		}
		return false;
	}

	/**
	 * Enqueue page builder scripts.
	 */
	public function page_builder_enqueue() {
		if ( $this->is_plugin_preview() && is_front_page() ) {
			wp_enqueue_script( 'hestia-builder-integration', get_template_directory_uri() . '/assets/js/admin/hestia-pagebuilder.js', array(), HESTIA_VERSION );
			wp_localize_script(
				'hestia-builder-integration',
				'hestiaBuilderIntegration',
				array(
					'ajaxurl'    => admin_url( 'admin-ajax.php' ),
					'nonce'      => wp_create_nonce( 'hestia-pagebuilder-nonce' ),
					'hideString' => esc_html__( 'Disable section', 'hestia-pro' ),
				)
			);
		}

		$had_elementor = get_option( 'hestia_had_elementor' );
		// Ask user if he wants to disable default styling for plugin.
		if ( $had_elementor === 'no' && $this->is_plugin_preview() ) {
			wp_enqueue_script( 'hestia-elementor-notice', get_template_directory_uri() . '/assets/js/admin/hestia-elementor-notice.js', array(), HESTIA_VERSION );
			wp_localize_script(
				'hestia-elementor-notice',
				'hestiaElementorNotice',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'hestia-elementor-notice-nonce' ),
				)
			);
		}
	}
}
