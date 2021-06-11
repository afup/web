<?php
/**
 * Typography add-ons.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Public_Typography_Addon
 */
class Hestia_Public_Typography_Addon extends Hestia_Public_Typography {

	/**
	 * Initialize base features.
	 */
	public function init() {
		parent::init();
	}

	/**
	 * Add top bar style.
	 */
	public function add_inline_font_styles() {
		parent::add_inline_font_styles();
		wp_add_inline_style( 'hestia_style', $this->typography_inline_style_addon() );
	}

	/**
	 * Typography inline style addon.
	 *
	 * @return string
	 */
	private function typography_inline_style_addon() {
		$custom_css = '';
		/**
		 * Menu font size
		 */
		$custom_css .= $this->get_inline_style( 'hestia_menu_fs', array( $this, 'get_menu_style' ) );

		return $custom_css;
	}

	/**
	 * [Generic] Menu font size.
	 *
	 * This function is called by hestia_get_inline_style to change the font size for:
	 * Primary menu
	 * Footer menu
	 *
	 * @param string $value Font value.
	 * @param string $dimension Query dimension.
	 *
	 * @return string
	 */
	public function get_menu_style( $value, $dimension = 'desktop' ) {
		$custom_css = '';
		if ( empty( $value ) ) {
			return $custom_css;
		}
		switch ( $dimension ) {
			case 'desktop':
			case 'tablet':
			case 'mobile':
				$v1 = ( 12 + (int) $value ) > 10 ? ( 12 + (int) $value ) : 10;
				break;
		}

		$custom_css .= ! empty( $v1 ) ? '
		.navbar #main-navigation a, .footer .footer-menu li a {
		  font-size: ' . $v1 . 'px;
		}
		.footer-big .footer-menu li a[href*="mailto:"]:before, .footer-big .footer-menu li a[href*="tel:"]:before{
		  width: ' . $v1 . 'px;
		  height: ' . $v1 . 'px;
		}
		' : '';

		$custom_css = $this->add_media_query( $dimension, $custom_css );

		return $custom_css;
	}
}
