<?php
/**
 * Loader for the ThemeIsleSDK
 *
 * Logic for loading always the latest SDK from the installed themes/plugins.
 *
 * @package     ThemeIsleSDK
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}
// Current SDK version and path.
$themeisle_sdk_version = '3.2.16';
$themeisle_sdk_path    = dirname( __FILE__ );

global $themeisle_sdk_max_version;
global $themeisle_sdk_max_path;

// If this is the latest SDK and it comes from a theme, we should load licenser separately.
$themeisle_sdk_relative_licenser_path = '/src/Modules/Licenser.php';

global $themeisle_sdk_abs_licenser_path;
if ( ! is_file( $themeisle_sdk_path . $themeisle_sdk_relative_licenser_path ) && is_file( $themeisle_sdk_max_path . $themeisle_sdk_relative_licenser_path ) ) {
	$themeisle_sdk_abs_licenser_path = $themeisle_sdk_max_path . $themeisle_sdk_relative_licenser_path;
	add_filter( 'themeisle_sdk_required_files', 'themeisle_sdk_load_licenser_if_present' );
}
if ( version_compare( $themeisle_sdk_version, $themeisle_sdk_max_path ) == 0 &&
	apply_filters( 'themeisle_sdk_should_overwrite_path', false, $themeisle_sdk_path, $themeisle_sdk_max_path ) ) {
	$themeisle_sdk_max_path = $themeisle_sdk_path;
}

if ( version_compare( $themeisle_sdk_version, $themeisle_sdk_max_version ) > 0 ) {
	$themeisle_sdk_max_version = $themeisle_sdk_version;
	$themeisle_sdk_max_path    = $themeisle_sdk_path;
}

// load the latest sdk version from the active Themeisle products.
if ( ! function_exists( 'themeisle_sdk_load_licenser_if_present' ) ) :
	/**
	 * Always load the licenser, if present.
	 *
	 * @param array $to_load Previously files to load.
	 */
	function themeisle_sdk_load_licenser_if_present( $to_load ) {
		global $themeisle_sdk_abs_licenser_path;
		$to_load[] = $themeisle_sdk_abs_licenser_path;

		return $to_load;
	}
endif;

// load the latest sdk version from the active Themeisle products.
if ( ! function_exists( 'themeisle_sdk_load_latest' ) ) :
	/**
	 * Always load the latest sdk version.
	 */
	function themeisle_sdk_load_latest() {
		/**
		 * Don't load the library if we are on < 5.4.
		 */
		if ( version_compare( PHP_VERSION, '5.4.32', '<' ) ) {
			return;
		}
		global $themeisle_sdk_max_path;
		require_once $themeisle_sdk_max_path . '/start.php';
	}
endif;
add_action( 'init', 'themeisle_sdk_load_latest' );
