<?php
/**
 * The template for 500 page in PWA.
 *
 * @package Hestia
 */
pwa_get_header( 'pwa' );

do_action( 'hestia_do_server_error' );

pwa_get_footer( 'pwa' );
