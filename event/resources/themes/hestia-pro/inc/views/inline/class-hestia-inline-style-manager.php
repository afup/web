<?php
/**
 * Enqueue fonts and run functions that are needed for inline style.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Inline_Style_Manager
 */
class Hestia_Inline_Style_Manager extends Hestia_Abstract_Main {
	/**
	 * Add all the hooks necessary.
	 */
	public function init() {
		add_action( 'enqueue_block_editor_assets', array( $this, 'register_google_font' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_google_font' ) );
		add_action( 'after_setup_theme', array( $this, 'sync_new_fs' ) );
	}

	/**
	 * Register the fonts that are selected in customizer.
	 */
	public function register_google_font() {

		/**
		 * Headings font family.
		 */
		$hestia_headings_font = get_theme_mod( 'hestia_headings_font', apply_filters( 'hestia_headings_default', false ) );
		if ( ! empty( $hestia_headings_font ) ) {
			$this->enqueue_google_font( $hestia_headings_font );
		}

		/**
		 * Body font family.
		 */
		$hestia_body_font = get_theme_mod( 'hestia_body_font', apply_filters( 'hestia_body_font_default', false ) );
		if ( ! empty( $hestia_body_font ) ) {
			$this->enqueue_google_font( $hestia_body_font );
		}
	}

	/**
	 * Enqueues a Google Font
	 *
	 * @since 1.1.38
	 *
	 * @param string $font font string.
	 */
	private function enqueue_google_font( $font ) {

		// Get list of all Google Fonts
		$google_fonts = hestia_get_google_fonts();

		// Make sure font is in our list of fonts
		if ( ! $google_fonts || ! in_array( $font, $google_fonts, true ) ) {
			return;
		}

		// Sanitize handle
		$handle = trim( $font );
		$handle = strtolower( $handle );
		$handle = str_replace( ' ', '-', $handle );

		// Sanitize font name
		$font = trim( $font );

		$base_url = '//fonts.googleapis.com/css';

		// Apply the chosen subset from customizer
		$subsets     = '';
		$get_subsets = get_theme_mod( 'hestia_font_subsets', array( 'latin' ) );
		if ( ! empty( $get_subsets ) ) {
			$font_subsets = array();
			foreach ( $get_subsets as $get_subset ) {
				$font_subsets[] = $get_subset;
			}
			$subsets .= implode( ',', $font_subsets );
		}

		// Weights
		$weights = apply_filters( 'hestia_font_weights', array( '300', '400', '500', '700' ) );

		// Add weights to URL
		if ( ! empty( $weights ) ) {
			$font .= ':' . implode( ',', $weights );
		}

		$query_args = array(
			'family' => urlencode( $font ),
		);
		if ( ! empty( $subsets ) ) {
			$query_args['subset'] = urlencode( $subsets );
		}
		$url = add_query_arg( $query_args, $base_url );

		// Enqueue style
		wp_enqueue_style( 'hestia-google-font-' . $handle, $url, array(), false );
	}

	/**
	 * Function to import font sizes from old controls to new ones.
	 *
	 * @since 1.1.58
	 */
	public function sync_new_fs() {
		$execute = get_option( 'hestia_sync_font_sizes' );
		if ( $execute !== false ) {
			return;
		}
		$headings_fs_old = get_theme_mod( 'hestia_headings_font_size' );
		$body_fs_old     = get_theme_mod( 'hestia_body_font_size' );
		if ( empty( $body_fs_old ) && empty( $headings_fs_old ) ) {
			return;
		}

		if ( ! empty( $headings_fs_old ) ) {
			$decoded = $this->calculate_fs_value( $headings_fs_old, 37 );
			set_theme_mod( 'hestia_section_primary_headings_fs', $decoded );
			set_theme_mod( 'hestia_section_secondary_headings_fs', $decoded );
			set_theme_mod( 'hestia_header_titles_fs', $decoded );
			set_theme_mod( 'hestia_post_page_headings_fs', $decoded );
		}

		if ( ! empty( $body_fs_old ) ) {
			$decoded = $this->calculate_fs_value( $body_fs_old, 12 );
			set_theme_mod( 'hestia_section_content_fs', $decoded );
			set_theme_mod( 'hestia_post_page_content_fs', $decoded );
		}
		update_option( 'hestia_sync_font_sizes', true );
	}

	/**
	 * Calculate new value for the new font size control based on the old control.
	 *
	 * @param string $old_value     Value from the old control.
	 * @param int    $decrease_rate Value to substract from the old value.
	 *
	 * @return string
	 */
	private function calculate_fs_value( $old_value, $decrease_rate ) {
		$decoded = json_decode( $old_value );
		if ( ! hestia_is_json( $old_value ) ) {
			$tmp_array = array(
				'desktop' => floor( $decoded - $decrease_rate ) > 25 ? 25 : ( floor( $decoded - $decrease_rate ) < - 25 ? - 25 : floor( $decoded - $decrease_rate ) ),
				'mobile'  => 0,
				'tablet'  => 0,
			);
			$decoded   = json_encode( $tmp_array );
		} else {
			$decoded->desktop = floor( $decoded->desktop - $decrease_rate ) > 25 ? 25 : ( floor( $decoded->desktop - $decrease_rate ) < - 25 ? - 25 : floor( $decoded->desktop - $decrease_rate ) );
			$decoded->tablet  = floor( $decoded->tablet - $decrease_rate ) > 25 ? 25 : ( floor( $decoded->tablet - $decrease_rate ) < - 25 ? - 25 : floor( $decoded->tablet - $decrease_rate ) );
			$decoded->mobile  = floor( $decoded->mobile - $decrease_rate ) > 25 ? 25 : ( floor( $decoded->mobile - $decrease_rate ) < - 25 ? - 25 : floor( $decoded->mobile - $decrease_rate ) );
			$decoded          = json_encode( $decoded );
		}

		return $decoded;
	}

	/**
	 * This function is called by each function that adds css if the control have media queries enabled.
	 *
	 * @param string $dimension  Query dimension.
	 * @param string $custom_css Css.
	 *
	 * @return string
	 */
	public function add_media_query( $dimension, $custom_css ) {
		switch ( $dimension ) {
			case 'desktop':
				$custom_css = '@media (min-width: 769px){' . $custom_css . '}';
				break;
				break;
			case 'tablet':
				$custom_css = '@media (max-width: 768px){' . $custom_css . '}';
				break;
			case 'mobile':
				$custom_css = '@media (max-width: 480px){' . $custom_css . '}';
				break;
		}

		return $custom_css;
	}

	/**
	 * This function checks if the value stored in the customizer control named '$control_name' is a json object.
	 * If the value is json it means that the customizer range control stores a value for every device ( mobile, tablet,
	 * desktop). In this case, for each of those devices it calls '$function_name' that with the following parameters:
	 * the device and the value for the control on that device.
	 * '$function_name' returns css code that will be added to inline style.
	 * If the value is not json then it's int and the '$function_name' function will be called just once for all three
	 * devices.
	 *
	 * @param string $control_name  Control name.
	 * @param array  $function_name Function to be called.
	 *
	 * @since 1.1.38
	 * @return string
	 */
	protected function get_inline_style( $control_name, $function_name ) {
		$control_value = get_theme_mod( $control_name );
		if ( $control_name === 'hestia_header_titles_fs' ) {
			$control_value = get_theme_mod( $control_name, apply_filters( 'hestia_header_titles_fs_default', 0 ) );
		}
		if ( empty( $control_value ) && ! is_numeric( $control_value ) ) {
			return '';
		}

		$custom_css = '';
		if ( hestia_is_json( $control_value ) ) {
			$control_value = json_decode( $control_value, true );
			if ( ! empty( $control_value ) ) {

				foreach ( $control_value as $key => $value ) {
					$custom_css .= call_user_func( $function_name, intval( $value ), $key );
				}
			}
		} else {
			$custom_css .= call_user_func( $function_name, intval( $control_value ) );
		}

		return $custom_css;
	}


}
