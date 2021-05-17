<?php
/**
 * Admin notices manager
 *
 * @package Hestia
 */

/**
 * Class Hestia_Admin_Notices_Manager
 */
class Hestia_Admin_Notices_Manager extends Hestia_Abstract_Main {
	/**
	 * Initialize notice manager.
	 */
	public function init() {
		add_action( 'admin_notices', array( $this, 'translate_notice' ) );
		add_action( 'admin_init', array( $this, 'ignore_multi_language' ) );
	}

	/**
	 * Add notice for front page translations.
	 */
	public function translate_notice() {
		global $current_user;
		$user_id = $current_user->ID;

		/* Check that the user hasn't already clicked to ignore the message */
		if ( get_user_meta( $user_id, 'hestia_ignore_multi_language_upsell_notice' ) ) {
			return;
		}
		if ( ! $this->should_display_translate_notice() ) {
			return;
		}

		echo '<div class="notice notice-warning" style="position:relative;">';
		printf( '<a href="%s" class="notice-dismiss" style="text-decoration:none;"></a>', '?hestia_nag_ignore=0' );
		echo '<p>';
		/* translators: Upsell to get the pro version */
		printf( esc_html__( 'Hestia front-page is not multi-language compatible, for this feature %s.', 'hestia-pro' ), sprintf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( apply_filters( 'hestia_upgrade_link_from_child_theme_filter', 'https://themeisle.com/themes/hestia-pro/upgrade/' ) ), esc_html__( 'Get the PRO version!', 'hestia-pro' ) ) );
		echo '</p>';
		echo '</div>';
	}

	/**
	 * Check if Polylang, TranslatePress or WPML are installed
	 * and the custom frontpage is selected
	 *
	 * @return bool
	 */
	private function should_display_translate_notice() {
		if ( defined( 'HESTIA_PRO_FLAG' ) ) {
			return false;
		}

		if ( get_option( 'show_on_front' ) === 'page' ) {
			if ( defined( 'POLYLANG_VERSION' ) ) {
				return true;
			}
			if ( defined( 'TRP_PLUGIN_VERSION' ) ) {
				return true;
			}
			if ( get_option( 'icl_sitepress_settings' ) !== false ) {
				return true;
			}

			return false;
		}
	}

	/**
	 * Ignore notice.
	 */
	public function ignore_multi_language() {
		global $current_user;
		$user_id = $current_user->ID;
		/* If user clicks to ignore the notice, add that to their user meta */
		if ( isset( $_GET['hestia_nag_ignore'] ) && 0 === (int) $_GET['hestia_nag_ignore'] ) {
			add_user_meta( $user_id, 'hestia_ignore_multi_language_upsell_notice', 'true', true );
		}
	}
}
