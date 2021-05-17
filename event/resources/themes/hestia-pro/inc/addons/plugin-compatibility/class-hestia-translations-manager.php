<?php
/**
 * WPML and Polylang compatibility functions.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Translations_Manager
 */
class Hestia_Translations_Manager extends Hestia_Abstract_Main {

	/**
	 * Initialize the control. Add all the hooks necessary.
	 */
	public function init() {
		add_filter( 'hestia_translate_single_string', array( $this, 'translate_single_string' ), 10, 2 );
		add_action( 'after_setup_theme', array( $this, 'register_strings' ), 11 );
	}

	/**
	 * Filter to translate strings
	 *
	 * @param string $original_value original string value.
	 * @param string $domain textdomain.
	 *
	 * @return string
	 */
	public function translate_single_string( $original_value, $domain ) {
		if ( is_customize_preview() ) {
			$wpml_translation = $original_value;
		} else {
			$wpml_translation = apply_filters( 'wpml_translate_single_string', $original_value, $domain, $original_value );
			if ( $wpml_translation === $original_value && function_exists( 'pll__' ) ) {
				return pll__( $original_value );
			}
		}

		return $wpml_translation;
	}

	/**
	 * Register all strings.
	 */
	public function register_strings() {
		$this->features_register_strings();
		$this->testimonials_register_strings();
		$this->team_register_strings();
		$this->slider_register_strings();
		$this->clients_bar_register_strings();
	}

	/**
	 * Features section. Register strings for translations.
	 *
	 * @modified 1.1.30
	 * @access private
	 */
	private function features_register_strings() {
		$default = Hestia_Defaults_Models::instance()->get_features_default();
		$this->pll_string_register_helper( 'hestia_features_content', $default, 'Features section' );
	}

	/**
	 * Testimonials section. Register strings for translations.
	 *
	 * @modified 1.1.34
	 * @access private.
	 */
	private function testimonials_register_strings() {
		$default = Hestia_Defaults_Models::instance()->get_testimonials_default();
		$this->pll_string_register_helper( 'hestia_testimonials_content', $default, 'Testimonials section' );
	}

	/**
	 * Team section. Register strings for translations.
	 *
	 * @modified 1.1.34
	 * @access private.
	 */
	private function team_register_strings() {
		$default = Hestia_Defaults_Models::instance()->get_team_default();
		$this->pll_string_register_helper( 'hestia_team_content', $default, 'Team section' );
	}

	/**
	 * Register polylang strings
	 *
	 * @since 1.1.31
	 * @modified 1.1.34
	 * @access private
	 */
	private function slider_register_strings() {
		$default = Hestia_Defaults_Models::instance()->get_slider_default();
		$this->pll_string_register_helper( 'hestia_slider_content', json_encode( $default ), 'Slider section' );
	}

	/**
	 * Register polylang strings for clients bar
	 *
	 * @since 1.1.47
	 */
	private function clients_bar_register_strings() {
		$this->pll_string_register_helper( 'hestia_clients_bar_content', false, 'Clients bar' );
	}

	/**
	 * Helper to register pll string.
	 *
	 * @param String    $theme_mod Theme mod name.
	 * @param bool/json $default Default value.
	 * @param String    $name Name for polylang backend.
	 */
	private function pll_string_register_helper( $theme_mod, $default = false, $name ) {
		if ( ! function_exists( 'pll_register_string' ) ) {
			return;
		}

		$repeater_content = get_theme_mod( $theme_mod, $default );
		$repeater_content = json_decode( $repeater_content );
		if ( ! empty( $repeater_content ) ) {
			foreach ( $repeater_content as $repeater_item ) {
				foreach ( $repeater_item as $field_name => $field_value ) {
					if ( $field_value !== 'undefined' ) {
						if ( $field_name === 'social_repeater' ) {
							$social_repeater_value = json_decode( $field_value );
							if ( ! empty( $social_repeater_value ) ) {
								foreach ( $social_repeater_value as $social ) {
									foreach ( $social as $key => $value ) {
										if ( $key === 'link' ) {
											pll_register_string( 'Social link', $value, $name );
										}
										if ( $key === 'icon' ) {
											pll_register_string( 'Social icon', $value, $name );
										}
									}
								}
							}
						} else {
							if ( $field_name !== 'id' ) {
								$f_n = ucfirst( $field_name );
								pll_register_string( $f_n, $field_value, $name );
							}
						}
					}
				}
			}
		}
	}

}
