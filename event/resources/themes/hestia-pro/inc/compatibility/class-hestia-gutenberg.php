<?php
/**
 * Hestia Gutenberg integration handler class.
 *
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      2018-12-06
 *
 * @package hestia
 */

/**
 * Class Hestia_Gutenberg
 *
 * @since 2.0.18
 */
class Hestia_Gutenberg extends Hestia_Abstract_Main {
	/**
	 * The post ID.
	 *
	 * @since  2.0.18
	 * @access private
	 * @var null
	 */
	private $post_id = null;

	/**
	 * Css style added inline for mobile.
	 *
	 * @since  2.0.18
	 * @access private
	 * @var string
	 */
	private $mobile_style = '';

	/**
	 * Css style added inline for tablet.
	 *
	 * @since  2.0.18
	 * @access private
	 * @var string
	 */
	private $tablet_style = '';

	/**
	 * Css style added inline for desktop.
	 *
	 * @since  2.0.18
	 * @access private
	 * @var string
	 */
	private $desktop_style = '';

	/**
	 * Initialize the compatibility module.
	 *
	 * @since  2.0.18
	 * @access public
	 * @return void
	 */
	public function init() {
		if ( apply_filters( 'hestia_filter_gutenberg_integration', true ) !== true ) {
			return;
		}
		$this->set_post_id();
		$this->run_styles();
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue' ) );
	}

	/**
	 * Set the post ID.
	 *
	 * @since  2.0.18
	 * @access private
	 * @return int|null
	 */
	private function set_post_id() {
		if ( ! isset( $_GET['post'] ) ) {
			return null;
		}
		$this->post_id = $_GET['post'];
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since  2.0.18
	 * @access public
	 * @return void
	 */
	public function enqueue() {
		wp_enqueue_style( 'hestia-gutenberg-css', get_template_directory_uri() . '/assets/css/gutenberg-editor-style' . ( ( HESTIA_DEBUG ) ? '' : '.min' ) . '.css', array(), HESTIA_VERSION );
		wp_add_inline_style( 'hestia-gutenberg-css', $this->get_inline_style() );
	}

	/**
	 * Get the inline style string.
	 *
	 * @since  2.0.18
	 * @access private
	 * @return string
	 */
	private function get_inline_style() {
		$css = '';

		$css .= $this->mobile_style;

		$css .= '@media( min-width: 480px ) {' . $this->tablet_style . '}';
		$css .= '@media( min-width: 768px ) {' . $this->desktop_style . '}';

		return $css;
	}

	/**
	 * Actually go through all the inline styles.
	 */
	private function run_styles() {

		// Font families
		$this->set_style(
			'hestia_headings_font',
			false,
			'
		.editor-styles-wrapper .editor-writing-flow h1, 
		.editor-styles-wrapper .editor-writing-flow h2, 
		.editor-styles-wrapper .editor-writing-flow h3, 
		.editor-styles-wrapper .editor-writing-flow h4, 
		.editor-styles-wrapper .editor-writing-flow h5,
		.editor-styles-wrapper .editor-writing-flow h6,
		.editor-styles-wrapper .editor-post-title__block .editor-post-title__input,
		.editor-styles-wrapper.header-default .editor-post-title__block .editor-post-title__input',
			'font-family'
		);
		$this->set_style( 'hestia_body_font', false, '.editor-styles-wrapper .editor-writing-flow, .editor-default-block-appender textarea.editor-default-block-appender__content', 'font-family' );

		// Accent color
		$this->set_style( 'accent_color', apply_filters( 'hestia_accent_color_default', '#e91e63' ), '.editor-styles-wrapper .editor-writing-flow a', 'color' );

		// Header text color
		$this->set_style( 'header_text_color', '#fff', '.editor-styles-wrapper.header-default .editor-post-title__block .editor-post-title__input', 'color' );

		// Font sizes
		$headings_map = $this->get_headings_font_size_defaults_map();

		// Header font size.
		$this->set_font_size_style( 'hestia_header_titles_fs', $headings_map['h1'], '.editor-styles-wrapper .editor-post-title__block .editor-post-title__input' );

		// Headings font size.
		foreach ( $headings_map as $tag => $args ) {
			$this->set_font_size_style( 'hestia_post_page_headings_fs', $args, '.editor-styles-wrapper .editor-writing-flow ' . esc_attr( $tag ) );
		}

		// Content font size.
		$default_values = array(
			'desktop' => 18,
			'tablet'  => 16,
			'mobile'  => 16,
		);

		$this->set_font_size_style( 'hestia_post_page_content_fs', $default_values, '.editor-styles-wrapper .editor-writing-flow, .editor-styles-wrapper .editor-writing-flow p, .editor-default-block-appender textarea.editor-default-block-appender__content' );
	}

	/**
	 * Set the font size style.
	 *
	 * This is made to work with our system of font sizes.
	 *
	 * @since  2.0.18
	 * @access private
	 *
	 * @param string $theme_mod the theme mod key.
	 * @param array  $base_values the font base values ['mobile','tablet','desktop'].
	 * @param string $selector css selector.
	 */
	private function set_font_size_style( $theme_mod, $base_values, $selector ) {
		if ( empty( $theme_mod ) || ! is_array( $base_values ) || empty( $selector ) ) {
			return;
		}

		$value = get_theme_mod( $theme_mod );

		if ( empty( $value ) ) {
			return;
		}

		$value = json_decode( $value, true );

		$value = wp_parse_args(
			$value,
			array(
				'desktop' => 0,
				'tablet'  => 0,
				'mobile'  => 0,
			)
		);

		$values_to_set = array(
			'desktop' => intval( $base_values['desktop'] ) + intval( $value['desktop'] ),
			'tablet'  => intval( $base_values['tablet'] ) + intval( $value['tablet'] ),
			'mobile'  => intval( $base_values['mobile'] ) + intval( $value['mobile'] ),
		);

		foreach ( $values_to_set as $query => $value ) {
			$this->add_css( $selector, $values_to_set[ $query ], 'font-size', 'px', $query );
		}
	}

	/**
	 * Set style per media query
	 *
	 * @since  2.0.18
	 * @access private
	 *
	 * @param string     $theme_mod theme mod key.
	 * @param string|int $default_value default value.
	 * @param string     $selector css selector.
	 * @param string     $property css property.
	 * @param string     $suffix suffix for the css value.
	 * @param string     $media_query media query.
	 */
	private function set_style( $theme_mod, $default_value, $selector, $property, $suffix = '', $media_query = 'mobile' ) {
		if ( empty( $selector ) || empty( $theme_mod ) || empty( $property ) ) {
			return;
		}

		$value = get_theme_mod( $theme_mod, $default_value );
		if ( empty( $value ) ) {
			return;
		}

		$this->add_css( $selector, $value, $property, $suffix, $media_query );
	}

	/**
	 * Add CSS.
	 *
	 * @since  2.0.18
	 * @access private
	 *
	 * @param string     $selector css selector.
	 * @param string|int $value value to set.
	 * @param string     $property css property.
	 * @param string     $suffix suffix for the css value.
	 * @param string     $media_query media query.
	 */
	private function add_css( $selector, $value, $property, $suffix, $media_query = 'mobile' ) {
		if ( empty( $value ) ) {
			return;
		}
		$css = esc_attr( $selector ) . '{' . esc_attr( $property ) . ': ' . esc_attr( $value ) . esc_attr( $suffix ) . ';}';
		switch ( $media_query ) {
			default:
			case 'mobile':
				$this->mobile_style .= $css;
				break;
			case 'tablet':
				$this->tablet_style .= $css;
				break;
			case 'desktop':
				$this->desktop_style .= $css;
				break;
		}
	}

	/**
	 * Get the default values for header font sizes.
	 *
	 * @since  2.0.18
	 * @access private
	 * @return array
	 */
	private function get_headings_font_size_defaults_map() {
		return array(
			'h1' => array(
				'desktop' => 42,
				'tablet'  => 36,
				'mobile'  => 36,
			),
			'h2' => array(
				'desktop' => 37,
				'tablet'  => 32,
				'mobile'  => 32,
			),
			'h3' => array(
				'desktop' => 32,
				'tablet'  => 28,
				'mobile'  => 28,
			),
			'h4' => array(
				'desktop' => 27,
				'tablet'  => 24,
				'mobile'  => 24,
			),
			'h5' => array(
				'desktop' => 23,
				'tablet'  => 21,
				'mobile'  => 21,
			),
			'h6' => array(
				'desktop' => 18,
				'tablet'  => 18,
				'mobile'  => 18,
			),
		);
	}
}
