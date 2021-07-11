<?php
/**
 * Front Page ShortCodes
 *
 * @package Hestia
 */

/**
 * Class Hestia_Front_Page_Shortcodes
 */
class Hestia_Front_Page_Shortcodes extends Hestia_Abstract_Main {
	/**
	 * Initialize front page shortcodes functionality.
	 */
	public function init() {
		add_action( 'init', array( $this, 'create_shortcodes' ) );
	}

	/**
	 * Create shortcodes.
	 */
	public function create_shortcodes() {
		$tags = array(
			'hestia_slider',
			'hestia_features',
			'hestia_testimonials',
			'hestia_team',
			'hestia_subscribe',
			'hestia_shop',
			'hestia_pricing',
			'hestia_portfolio',
			'hestia_contact',
			'hestia_blog',
			'hestia_ribbon',
			'hestia_clients_bar',
		);
		foreach ( $tags as $tag ) {
			add_shortcode( $tag, array( $this, 'shortcode_callback' ) );
		}
	}

	/**
	 * Callback for shortcode.
	 * Get the markup for each section.
	 *
	 * @param array  $atts attributes.
	 * @param null   $content content.
	 * @param string $tag shortcode tag.
	 *
	 * @return string
	 */
	public function shortcode_callback( $atts, $content = null, $tag = '' ) {
		$full_width_sections = array(
			'hestia_slider',
		);

		$tag_words = explode( '_', $tag );
		$tag_words = array_map( 'ucfirst', $tag_words );
		$tag       = implode( '_', $tag_words );
		$class     = $tag . '_Section';
		if ( $tag === 'Hestia_Slider' ) {
			$class = $tag . '_Section_Addon';
		}

		$section = new $class;
		if ( ! class_exists( $class ) ) {
			return '';
		}

		ob_start();
		ob_clean();

		if ( in_array( $tag, $full_width_sections, true ) ) {
			if ( $tag === 'hestia_slider' ) {
				call_user_func( array( $section, 'slider_render_callback' ) );
			} else {
				call_user_func( array( $section, 'render_section' ) );
			}
		} else {
			call_user_func( array( $section, 'render_section' ), true );
		}
		return ob_get_clean();
	}
}

