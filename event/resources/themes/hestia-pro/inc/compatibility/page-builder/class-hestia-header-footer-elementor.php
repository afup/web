<?php
/**
 * Compatibility with Elementor Header Footer plugin.
 *
 * @package Hestia
 */
/**
 * Class Hestia_Header_Footer_Elementor
 */
class Hestia_Header_Footer_Elementor extends Hestia_Abstract_Main {
	/**
	 * Check if plugin is installed.
	 */
	private function should_load() {
		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			return false;
		}
		if ( ! class_exists( 'Header_Footer_Elementor', false ) ) {
			return false;
		}
		return true;
	}
	/**
	 * Init function.
	 */
	public function init() {
		if ( ! $this->should_load() ) {
			return;
		}
		$this->add_theme_builder_hooks();
	}
	/**
	 * Replace theme hooks with the one from the plugin.
	 */
	private function add_theme_builder_hooks() {
		add_filter( 'body_class', array( $this, 'add_body_class' ) );
		add_action( 'hestia_do_header', array( $this, 'do_header' ), 0 );
		add_action( 'hestia_do_footer', array( $this, 'do_footer' ), 0 );
	}

	/**
	 * Add body class to know to disable parallax on header.
	 *
	 * @param array $classes Classes on body.
	 * @return array
	 */
	public function add_body_class( $classes ) {
		$classes[] = 'header-footer-elementor';

		return $classes;
	}

	/**
	 * Replace Header hooks.
	 */
	public function do_header() {
		if ( ! hfe_header_enabled() ) {
			return;
		}
		hfe_render_header();
		remove_all_actions( 'hestia_do_header' );
		remove_all_actions( 'hestia_do_top_bar' );
	}
	/**
	 * Replace Footer hooks.
	 */
	public function do_footer() {
		if ( ! hfe_footer_enabled() ) {
			return;
		}
		hfe_render_footer();
		remove_all_actions( 'hestia_do_footer' );
	}
}
