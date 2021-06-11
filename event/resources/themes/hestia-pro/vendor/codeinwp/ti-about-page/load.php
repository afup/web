<?php
/**
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      10/09/2018
 *
 * @package ti-about-page
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}
if ( ! class_exists( 'Ti_About_Page' ) ) {
	require_once 'class-ti-about-page.php';
}

if ( ! class_exists( 'Ti_About_Plugin_Helper' ) ) {
	require_once 'includes' . DIRECTORY_SEPARATOR . 'class-ti-about-plugin-helper.php';
}

if ( ! defined( 'TI_ABOUT_PAGE_DIR' ) ) {
	define( 'TI_ABOUT_PAGE_DIR', get_template_directory() . '/vendor/codeinwp/ti-about-page/' );
}

if ( ! defined( 'TI_ABOUT_PAGE_URL' ) ) {
	define( 'TI_ABOUT_PAGE_URL', get_template_directory_uri() . '/vendor/codeinwp/ti-about-page/' );
}
if ( ! defined( 'TI_ABOUT_PAGE_VERSION' ) ) {
	define( 'TI_ABOUT_PAGE_VERSION', '1.0.8' );
}
