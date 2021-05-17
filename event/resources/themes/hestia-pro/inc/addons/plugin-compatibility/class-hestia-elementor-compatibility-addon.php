<?php
/**
 * Elementor compatibility
 *
 * @package Hestia
 */

use ElementorPro\Modules\ThemeBuilder\Module;

/**
 * Class Hestia_Elementor_Compatibility_Addon
 */
class Hestia_Elementor_Compatibility_Addon extends Hestia_Elementor_Compatibility {

	/**
	 * Initialize module.
	 */
	public function init() {
		parent::init();

		add_action( 'wp', array( $this, 'add_theme_builder_hooks' ) );
	}

	/**
	 * Add support for elementor theme locations.
	 */
	public function add_theme_builder_hooks() {
		if ( ! class_exists( '\ElementorPro\Modules\ThemeBuilder\Module', false ) ) {
			return;
		}

		// Elementor locations compatibility.
		add_action( 'elementor/theme/register_locations', array( $this, 'register_theme_locations' ) );

		// Override theme templates.
		add_action( 'hestia_do_header', array( $this, 'do_header' ), 0 );
		add_action( 'hestia_do_footer', array( $this, 'do_footer' ), 0 );
	}

	/**
	 * Register Theme Location for Elementor
	 * see https://developers.elementor.com/theme-locations-api/
	 *
	 * @param \ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager Elementor object.
	 */
	public function register_theme_locations( $elementor_theme_manager ) {
		$elementor_theme_manager->register_location( 'header' );
		$elementor_theme_manager->register_location( 'footer' );
	}

	/**
	 * Remove actions for elementor header to act properly.
	 */
	public function do_header() {
		$did_location = Module::instance()->get_locations_manager()->do_location( 'header' );
		if ( $did_location ) {
			remove_all_actions( 'hestia_do_header' );
			remove_all_actions( 'hestia_do_top_bar' );
		}
	}

	/**
	 * Remove actions for elementor footer to act properly.
	 */
	public function do_footer() {
		$did_location = Module::instance()->get_locations_manager()->do_location( 'footer' );
		if ( $did_location ) {
			remove_all_actions( 'hestia_do_footer' );
			remove_all_actions( 'hestia_do_bottom_footer_content' );
		}
	}
}
