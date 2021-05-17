<?php
/**
 * The main loader class for ThemeIsle SDK
 *
 * @package     ThemeIsleSDK
 * @subpackage  Loader
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       1.0.0
 */

namespace ThemeisleSDK;

use ThemeisleSDK\Common\Module_Factory;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Singleton loader for ThemeIsle SDK.
 */
final class Loader {
	/**
	 * Singleton instance.
	 *
	 * @var Loader instance The singleton instance
	 */
	private static $instance;
	/**
	 * Current loader version.
	 *
	 * @var string $version The class version.
	 */
	private static $version = '2.0.0';
	/**
	 * Holds registered products.
	 *
	 * @var array The products which use the SDK.
	 */
	private static $products = [];
	/**
	 * Holds available modules to load.
	 *
	 * @var array The modules which SDK will be using.
	 */
	private static $available_modules = [
		'dashboard_widget',
		'rollback',
		'uninstall_feedback',
		'licenser',
		'logger',
		'translate',
		'review',
		'recommendation',
		'notification',

	];

	/**
	 * Initialize the sdk logic.
	 */
	public static function init() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Loader ) ) {
			self::$instance = new Loader();
			$modules        = array_merge( self::$available_modules, apply_filters( 'themeisle_sdk_modules', [] ) );
			foreach ( $modules as $key => $module ) {
				if ( ! class_exists( 'ThemeisleSDK\\Modules\\' . ucwords( $module, '_' ) ) ) {
					unset( $modules[ $key ] );
				}
			}
			self::$available_modules = $modules;
		}
	}

	/**
	 * Get cache token used in API requests.
	 *
	 * @return string Cache token.
	 */
	public static function get_cache_token() {
		$cache_token = get_transient( 'themeisle_sdk_cache_token' );
		if ( false === $cache_token ) {
			$cache_token = wp_generate_password( 6, false );
			set_transient( $cache_token, WEEK_IN_SECONDS );
		}

		return $cache_token;
	}

	/**
	 * Clear cache token.
	 */
	public static function clear_cache_token() {
		delete_transient( 'themeisle_sdk_cache_token' );
	}

	/**
	 * Register product into SDK.
	 *
	 * @param string $base_file The product base file.
	 *
	 * @return Loader The singleton object.
	 */
	public static function add_product( $base_file ) {

		if ( ! is_file( $base_file ) ) {
			return self::$instance;
		}
		$product = new Product( $base_file );

		Module_Factory::attach( $product, self::get_modules() );

		self::$products[ $product->get_slug() ] = $product;

		return self::$instance;
	}

	/**
	 * Get all registered modules by the SDK.
	 *
	 * @return array Modules available.
	 */
	public static function get_modules() {
		return self::$available_modules;
	}

	/**
	 * Get all products using the SDK.
	 *
	 * @return array Products available.
	 */
	public static function get_products() {
		return self::$products;
	}

	/**
	 * Get the version of the SDK.
	 *
	 * @return string The version.
	 */
	public static function get_version() {
		return self::$version;
	}

}
