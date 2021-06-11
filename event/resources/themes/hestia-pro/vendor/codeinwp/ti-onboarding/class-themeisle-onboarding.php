<?php
/**
 * Theme Onboarding - Demo Import
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      12/07/2018
 *
 * @package         themeisle-onboarding
 * @soundtrack      Summer On Lock (feat. Pusha T, Jadakiss, Fabolous, Agent Sasco - Royce da 5'9"
 */

/**
 * Class Themeisle_Onboarding
 *
 * @package themeisle-onboarding
 */
class Themeisle_Onboarding {
	/**
	 * The version of this library
	 *
	 * @var string Version string.
	 */
	const VERSION = '1.8.3';
	/**
	 * Sites Library API URL.
	 *
	 * @var string API root string.
	 */
	const API_ROOT = 'ti-sites-lib/v1';
	/**
	 * Storage for the remote fetched info.
	 *
	 * @var string Transient slug.
	 */
	const STORAGE_TRANSIENT = 'themeisle_sites_library_data';
	/**
	 * Onboarding Path Relative to theme dir.
	 *
	 * @var string Onboarding root path.
	 */
	const OBOARDING_PATH = '/vendor/codeinwp/ti-onboarding';
	/**
	 * Instance of Themeisle_Onboarding
	 *
	 * @var Themeisle_Onboarding
	 */
	protected static $instance = null;
	/**
	 * Instance of Themeisle_OB_Admin
	 *
	 * @var Themeisle_OB_Admin
	 */
	protected $admin = null;

	/**
	 * Method to return path to child class in a Reflective Way.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @return string
	 */
	static public function get_dir() {
		return apply_filters( 'themeisle_site_import_uri', trailingslashit( get_template_directory_uri() ) . self::OBOARDING_PATH );
	}

	/**
	 * Instantiate the class.
	 *
	 * @static
	 * @since  1.0.0
	 * @access public
	 * @return Themeisle_Onboarding
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Holds the sites data.
	 *
	 * @var null
	 */
	private function init() {

		if ( ! $this->should_load() ) {
			return;
		}
		$this->setup_admin();
		$this->setup_api();
	}

	/**
	 * Utility to check if sites library should be loaded.
	 *
	 * @return bool
	 */
	private function should_load() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$theme_support = get_theme_support( 'themeisle-demo-import' );

		if ( empty( $theme_support ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Setup admin functionality.
	 *
	 * @return void
	 */
	private function setup_admin() {
		require_once 'includes/class-themeisle-ob-admin.php';
		if ( ! class_exists( 'Themeisle_OB_Admin' ) ) {
			return;
		}
		$this->admin = new Themeisle_OB_Admin();
		$this->admin->init();
	}

	/**
	 * Setup the restful functionality.
	 *
	 * @return void
	 */
	private function setup_api() {
		require_once 'includes/class-themeisle-ob-rest-server.php';
		if ( ! class_exists( 'Themeisle_OB_Rest_Server' ) ) {
			return;
		}

		$api = new Themeisle_OB_Rest_Server();
		$api->init();
		require_once 'includes/importers/helpers/trait-themeisle-ob.php';
	}

	/**
	 * Render the onboarding.
	 */
	public function render_onboarding() {
		$this->admin->render_site_library();
	}

	/**
	 * Disallow object clone
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function __clone() {
	}

	/**
	 * Disable un-serializing
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function __wakeup() {
	}
}
