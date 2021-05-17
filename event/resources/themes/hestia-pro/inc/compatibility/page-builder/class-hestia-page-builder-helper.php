<?php
/**
 * Common functions used for compatibility with page builders.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Elementor_Compatibility
 */
abstract class Hestia_Page_Builder_Helper extends Hestia_Abstract_Main {

	/**
	 * Init function.
	 */
	public function init() {
		if ( $this->should_load_feature() === false ) {
			return;
		}
	}


	/**
	 * Decide if we should load features for a builder.
	 *
	 * @return bool
	 */
	protected abstract function should_load_feature();

	/**
	 * Decide if a page is edited with a page builder or not.
	 *
	 * @param string $pid Post id.
	 *
	 * @return bool
	 */
	protected abstract function is_edited_with_builder( $pid );


	/**
	 * Section deactivation
	 */
	public function hestia_pagebuilder_hide_frontpage_section() {
		$nonce = $_POST['nonce'];
		if ( ! wp_verify_nonce( $nonce, 'hestia-pagebuilder-nonce' ) ) {
			return;
		}
		$section = $_POST['section'];
		if ( ! empty( $section ) ) {
			if ( $section === 'products' ) {
				$theme_mod = esc_html( 'hestia_shop_hide' );
			} else {
				$theme_mod = esc_html( 'hestia_' . $section . '_hide' );
			}
			if ( ! empty( $theme_mod ) ) {
				set_theme_mod( $theme_mod, 1 );
			}
		}
		die();
	}


	/**
	 * Decide if we should set page template in builder or not.
	 *
	 * @return bool
	 */
	public function maybe_set_page_template() {
		/**
		 * Bail if post type is not page.
		 */
		if ( get_post_type() !== 'page' ) {
			return false;
		}

		global $post;

		if ( ! isset( $post ) ) {
			return false;
		}

		$post_id = hestia_get_current_page_id();
		if ( ! isset( $post_id ) || $post_id === false ) {
			return false;
		}

		/**
		 * Don't change if user already set a page template.
		 */
		$post_meta_template = get_post_meta( $post_id, '_wp_page_template', true );
		if ( $post_meta_template !== 'default' && ! empty( $post_meta_template ) ) {
			return false;
		}

		/**
		 * Bail if is frontpage
		 */
		if ( 'page' === get_option( 'show_on_front' ) ) {
			if ( get_option( 'page_on_front' ) === $post_id ) {
				return false;
			}
		}

		/**
		 * Bail if page is not edited with builder.
		 */
		if ( $this->is_edited_with_builder( $post_id ) === false ) {
			return false;
		}

		return $this->set_page_template( $post_id );
	}


	/**
	 * Set page layout.
	 *
	 * @return bool
	 */
	private function set_page_template( $post_id ) {
		global $post;

		if ( isset( $post ) && ( is_admin() || is_singular() ) ) {
			if ( empty( $post->post_content ) ) {
				update_post_meta( $post_id, '_wp_page_template', 'page-templates/template-pagebuilder-full-width.php' );
			}
		}

		return true;
	}
}
