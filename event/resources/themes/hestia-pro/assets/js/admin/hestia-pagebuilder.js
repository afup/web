/**
 * Script for page builders integration
 *
 * @package Hestia
 */

/* global hestiaBuilderIntegration */

jQuery( document ).ready(
	function () {
			jQuery( '.main > section:not(#about)' ).each(
				function() {

					if ( jQuery( this ) !== 'undefined' ) {
						jQuery( this ).append( '<span class="hestia-pagebuilder-frontpage-controls"><a class="hestia-pagebuilder-section-remove"><span class="dashicons dashicons-hidden"></span>' + hestiaBuilderIntegration.hideString + '</a></span>' );
					}
				}
			);

			jQuery( '.hestia-pagebuilder-section-remove' ).on(
				'click', function() {
					var clickedSection = jQuery( this ).parent().parent();
					var sectionId      = jQuery( clickedSection ).attr( 'id' );

					jQuery.ajax(
						{
							url: hestiaBuilderIntegration.ajaxurl,
							data: {
								section: sectionId,
								nonce: hestiaBuilderIntegration.nonce,
								action: 'hestia_pagebuilder_hide_frontpage_section'
							},
							type: 'post',
							success: function() {
								jQuery( clickedSection ).fadeOut();
							}
						}
					);
				}
			);
	}
);
