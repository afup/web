/* global tiAboutPageObject */
import Sticky from 'sticky-js';

let stickyMenu = new Sticky( '.ti-about-tablist',
		{ marginTop: 32, stickyClass: 'stickied', stickyFor: 782 } );

function hashTabs() {
	let hash = window.location.hash;
	if ( hash !== null ) {
		let tab = document.querySelector( 'a.tab[href="' + hash + '"]' );
		if ( tab === null ) {
			return false;
		}
		tab.click();
	}
}

function handleTabs() {
	let links = document.querySelectorAll( '.ti-about-tablist a.tab' );
	for ( let i = 0; i < links.length; i++ ) {
		links[i].addEventListener( 'click', function(event) {
			event.preventDefault();
			let active = document.querySelector( '.tab-content.active' ),
					newTabId = event.target.getAttribute( 'href' );

			window.location.hash = newTabId;
			if ( active !== null ) {
				active.classList.remove( 'active' );
				for ( let i = 0; i < links.length; i++ ) {
					links[i].classList.remove( 'active' );
				}
			}

			links[i].classList.add( 'active' );
			document.querySelector( newTabId ).classList.add( 'active' );
		} );
	}
}

function handleLinkingInTabs() {
	let linksToSelf = document.querySelectorAll(
			'#about-tabs > div a[href^=\'#\']' );
	for ( let i = 0; i < linksToSelf.length; i++ ) {
		linksToSelf[i].addEventListener( 'click', function(event) {
			event.preventDefault();
			let index = event.target.getAttribute( 'href' ).substr( 1 );
			let clickEvent = new Event( 'click' );
			document.querySelector( 'li[data-tab-id="' + index + '"] a.tab' ).
					dispatchEvent( clickEvent );
			return false;
		} );
	}
}

/* Show required actions next to page title and tab title */
function addRequiredActionsBadge() {
	if ( tiAboutPageObject.nr_actions_required > 0 ) {
		let badge = document.createElement( 'span' ),
				recommendedTab = document.querySelector( '.tab.recommended_actions' );

		badge.classList.add( 'badge-action-count' );
		badge.innerText = tiAboutPageObject.nr_actions_required;
		recommendedTab.appendChild( badge );
	}
}

function addContentLoadedClass(){
	let body = document.body;
	body.classList.add('about-loaded');
}

/**
 * Run JS on load.
 */
window.addEventListener( 'DOMContentLoaded', function() {
	addContentLoadedClass();
	handleTabs();
	hashTabs();
	addRequiredActionsBadge();
	handleLinkingInTabs();
} );


// Legacy code in jQuery.
/* global console */
jQuery( document ).ready(
		function () {
			jQuery( '.ti-about-page-required-action-button' ).click( function () {
				var plugin_slug = jQuery( this ).attr( 'data-slug' );
				var card = jQuery( '.' + plugin_slug );
				jQuery.ajax(
						{
							type: 'POST',
							data: { action: 'update_recommended_plugins_visibility', slug: plugin_slug, nonce: tiAboutPageObject.nonce },
							url: tiAboutPageObject.ajaxurl,
							beforeSend: function() {
								jQuery(card).fadeOut();
							},
							success: function ( response ) {
								console.log(response.required_actions);
								if( response.required_actions === 0 ) {
									jQuery('#about-tabs #recommended_actions, [data-tab-id="recommended_actions"], #adminmenu .wp-submenu li a span.badge-action-count').fadeOut().remove();
									jQuery( '#about-tabs ul > li:first-child a' ).click();
								}
								jQuery(card).remove();
								jQuery( '#about-tabs ul li > .recommended_actions span, #adminmenu .wp-submenu li a span.badge-action-count' ).text( response.required_actions );
							},
							error: function ( jqXHR, textStatus, errorThrown ) {
								jQuery(card).fadeIn();
								console.log( jqXHR + ' :: ' + textStatus + ' :: ' + errorThrown );
							}
						}
				);
			} );

			// Remove activate button and replace with activation in progress button.
			jQuery( document ).on(
					'DOMNodeInserted', '.activate-now', function () {
						var activateButton = jQuery( this );
						if ( activateButton.length ) {
							var url = jQuery( activateButton ).attr( 'href' );
							if ( typeof url !== 'undefined' ) {
								// Request plugin activation.
								jQuery.ajax(
										{
											beforeSend: function () {
												jQuery( activateButton ).replaceWith( '<a class="button updating-message">' + tiAboutPageObject.activating_string + '...</a>' );
											},
											async: true,
											type: 'GET',
											url: url,
											success: function () {
												// Reload the page.
												location.reload();
											}
										}
								);
							}
						}
					}
			);
		}
);

