/**
 * Script for the customizer auto scrolling.
 *
 * Sends the section name to the preview.
 *
 * @since    1.1.50
 * @package Hestia
 *
 * @author    ThemeIsle
 */

/* global wp */

var hestia_customize_scroller = function ( $ ) {
	'use strict';

	$(
		function () {
				var customize = wp.customize;

				$( 'ul[id*="hestia_frontpage_sections"] .accordion-section' ).not( '.panel-meta' ).each(
					function () {
						$( this ).on(
							'click', function() {
								var section = $( this ).attr( 'aria-owns' ).split( '_' ).pop();
								customize.previewer.send( 'clicked-customizer-section', section );
							}
						);
					}
				);
		}
	);
};

hestia_customize_scroller( jQuery );
