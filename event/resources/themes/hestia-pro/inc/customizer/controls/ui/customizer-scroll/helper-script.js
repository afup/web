/**
 * Script fort the customizer sections scroll function.
 *
 * @since    1.1.43
 * @package Hestia
 *
 * @author    ThemeIsle
 */

/* global wp */

var hestia_customizer_section_scroll = function ( $ ) {
	'use strict';
	$(
		function () {
				var customize = wp.customize;

				customize.preview.bind(
					'clicked-customizer-section', function( data ) {
						var sectionId = '';
						switch (data) {
							case 'sub-accordion-section-sidebar-widgets-sidebar-big-title':
								sectionId = '#carousel-hestia-generic';
								break;
							case 'shop':
								sectionId = 'section#products';
							break;
							case 'ribbon':
								sectionId = 'section.hestia-ribbon';
							break;
							case 'sub-accordion-section-sidebar-widgets-subscribe-widgets':
								sectionId = 'section#subscribe';
							break;
							case 'bar':
								sectionId = 'section.hestia-clients-bar';
							break;
							case 'slider':
								sectionId = '#carousel-hestia-generic.carousel.slide';
							break;
							default:
								sectionId = 'section#' + data;
							break;
						}
						if ( $( sectionId ).length > 0) {
							$( 'html, body' ).animate(
								{
									scrollTop: $( sectionId ).offset().top - 100
								}, 1000
							);
						}
					}
				);
		}
	);
};

hestia_customizer_section_scroll( jQuery );
