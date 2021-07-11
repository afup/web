<?php
/**
 * File responsible for sdk files loading.
 *
 * @package     ThemeIsleSDK
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1.0
 */

namespace ThemeisleSDK;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$products      = apply_filters( 'themeisle_sdk_products', array() );
$path          = dirname( __FILE__ );
$files_to_load = [
	$path . '/src/' . 'Loader.php',
	$path . '/src/' . 'Product.php',

	$path . '/src/' . 'Common/Abstract_module.php',
	$path . '/src/' . 'Common/Module_factory.php',

	$path . '/src/' . 'Modules/Dashboard_widget.php',
	$path . '/src/' . 'Modules/Rollback.php',
	$path . '/src/' . 'Modules/Uninstall_feedback.php',
	$path . '/src/' . 'Modules/Licenser.php',
	$path . '/src/' . 'Modules/Endpoint.php',
	$path . '/src/' . 'Modules/Notification.php',
	$path . '/src/' . 'Modules/Logger.php',
	$path . '/src/' . 'Modules/Translate.php',
	$path . '/src/' . 'Modules/Review.php',
	$path . '/src/' . 'Modules/Recommendation.php',
];

$files_to_load = array_merge( $files_to_load, apply_filters( 'themeisle_sdk_required_files', [] ) );

foreach ( $files_to_load as $file ) {
	if ( is_file( $file ) ) {
		require_once $file;
	}
}

Loader::init();

foreach ( $products as $product ) {
	Loader::add_product( $product );
}
