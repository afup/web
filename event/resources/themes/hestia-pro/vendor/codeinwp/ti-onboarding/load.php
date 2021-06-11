<?php
/**
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      06/11/2018
 *
 * @package themeisle-onboarding
 */

if ( ! defined( 'TI_ONBOARDING_DISABLED' ) ) {
	define( 'TI_ONBOARDING_DISABLED', false );
}

if ( TI_ONBOARDING_DISABLED === true ) {
	add_filter(
		'ti_about_config_filter',
		function ( $config ) {
			unset( $config['welcome_notice'] );

			return $config;
		}
	);

	return false;
}


if ( ! class_exists( 'Themeisle_Onboarding' ) ) {
	require_once dirname( __FILE__ ) . '/class-themeisle-onboarding.php';
}

if ( ! class_exists( 'Themeisle_OB_WP_Import_Logger' ) ) {
	require_once dirname( __FILE__ ) . '/includes/importers/helpers/class-themeisle-ob-import-logger.php';
}

if ( class_exists( 'WP_CLI' ) && ! class_exists( 'Themeisle_OB_WP_Cli' ) ) {
	require_once 'includes/class-themeisle-ob-wp-cli.php';
}

