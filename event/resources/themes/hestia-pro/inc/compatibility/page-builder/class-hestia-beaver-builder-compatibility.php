<?php
/**
 * Beaver Builder Compatibility class.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Beaver_Builder_Compatibility
 */
class Hestia_Beaver_Builder_Compatibility extends Hestia_Page_Builder_Helper {


	/**
	 * Initialize features.
	 */
	public function init() {
		parent::init();

		if ( defined( 'FL_THEME_BUILDER_VERSION' ) ) {
			/**
			 * Compatibility with Beaver Themer header and footer templates.
			 */
			add_action( 'after_setup_theme', array( $this, 'header_footer_support' ) );
			add_action( 'wp', array( $this, 'header_footer_render' ), 100 );
		}

		/**
		 * Show hide frontpage sections in Beaver Builder.
		 */
		add_action( 'wp_enqueue_scripts', array( $this, 'page_builder_enqueue' ) );
		add_action( 'wp_ajax_hestia_pagebuilder_hide_frontpage_section', array( $this, 'hestia_pagebuilder_hide_frontpage_section' ) );

		/**
		 * When a new page is edited with Beaver Builder, set the full width template
		 */
		add_action( 'wp', array( $this, 'maybe_set_page_template' ), 20 );
	}

	/**
	 * This function checks if Beaver Themer and Beaver Builder Pro plugins are activated.
	 *
	 * @return bool
	 */
	protected function should_load_feature() {
		if ( ! defined( 'FL_BUILDER_VERSION' ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Check if page is edited with Beaver Builder.
	 *
	 * @param string $pid Post id.
	 *
	 * @return bool
	 */
	protected function is_edited_with_builder( $pid ) {
		if ( class_exists( 'FLBuilderModel', false ) ) {
			if ( FLBuilderModel::is_builder_enabled() === true ) {
				return true;
			}
		}
		return false;
	}


	/**
	 * Add theme support for header and footer.
	 *
	 * @since  1.1.24
	 * @access public
	 */
	public function header_footer_support() {
		add_theme_support( 'fl-theme-builder-headers' );
		add_theme_support( 'fl-theme-builder-footers' );
	}


	/**
	 * Add header and footer support for beaver.
	 *
	 * @since  1.1.24
	 * @access public
	 */
	public function header_footer_render() {

		if ( ! class_exists( 'FLThemeBuilderLayoutData', false ) ) {
			return;
		}

		// Get the header ID.
		$header_ids = FLThemeBuilderLayoutData::get_current_page_header_ids();

		// If we have a header, remove the theme header and hook in Theme Builder's.
		if ( ! empty( $header_ids ) ) {
			remove_all_actions( 'hestia_do_header' );
			remove_all_actions( 'hestia_do_top_bar' );
			add_action( 'hestia_do_header', 'FLThemeBuilderLayoutRenderer::render_header' );
		}

		// Get the footer ID.
		$footer_ids = FLThemeBuilderLayoutData::get_current_page_footer_ids();

		// If we have a footer, remove the theme footer and hook in Theme Builder's.
		if ( ! empty( $footer_ids ) ) {
			remove_all_actions( 'hestia_do_footer' );
			add_action( 'hestia_do_footer', 'FLThemeBuilderLayoutRenderer::render_footer' );
		}
	}

	/**
	 * Enqueue scripts
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
	}

	/**
	 * Check if we're in Beaver Builder Preview.
	 *
	 * @return bool
	 */
	private function is_plugin_preview() {
		if ( class_exists( 'FLBuilderModel', false ) ) {
			if ( FLBuilderModel::is_builder_active() === true ) {
				return true;
			}
		}
		return false;
	}
}
