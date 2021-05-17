/**
 * Notice for Elementor
 *
 * @package Hestia
 */

/* global hestiaElementorNotice */

jQuery( document ).ready(
	function () {

			var style = '<style>.hestia-disable-elementor-styling{position:fixed;z-index:9999;top:0;left:0;width:100%;height:100%;background-color:rgba(0,0,0,.8)}.hestia-elementor-notice-wrapper{position:fixed;top:50%;left:50%;max-width:380px;border-radius:6px;color:#6d7882;background-color:#fff;text-align:center;-webkit-transform:translate(-50%,-50%);-ms-transform:translate(-50%,-50%);transform:translate(-50%,-50%)}.hestia-elementor-notice-body{padding:10px 20px;font-size:12px;line-height:1.5}.hestia-elementor-notice-header{padding:10px 0 20px;color:#6d7882;font-size:13px;font-weight:700}.hestia-elementor-notice-buttons{border-top:1px solid #e6e9ec}.hestia-elementor-notice-buttons>a{display:inline-block;width:50%;padding:13px 0;font-size:15px;font-weight:700;text-align:center}.hestia-elementor-notice-buttons>a.hestia-do-nothing{border-right:1px solid #e6e9ec;color:#6d7882}.hestia-elementor-notice-buttons>a.hestia-disable-default-styles{color:#9b0a46}</style>';

			var dialog = style + '<div class="hestia-disable-elementor-styling">' +
				'<div class="hestia-elementor-notice-wrapper">' +
					'<div class="hestia-elementor-notice-header">Hestia supports default styling for Elementor widgets</div>' +
					'<div class="hestia-elementor-notice-body">Do you want to disable Elementors\' default styles and use the theme defaults?</div>' +
					'<div class="hestia-elementor-notice-buttons">' +
						'<a href="#" class="hestia-do-nothing" data-reply="no">No</a>' +
						'<a href="#" class="hestia-disable-default-styles" data-reply="yes">Yes</a>' +
					'</div>' +
				'</div>' +
			'</div>';

			jQuery( 'body' ).prepend( dialog );
			jQuery( '.hestia-elementor-notice-buttons > a' ).on(
				'click', function() {

					var reply = jQuery( this ).data( 'reply' );
					jQuery.ajax(
						{
							url: hestiaElementorNotice.ajaxurl,
							data: {
								reply: reply,
								nonce: hestiaElementorNotice.nonce,
								action: 'hestia_elementor_deactivate_default_styles'
							},
							type: 'post',
							success: function () {
								if ( reply === 'yes' ) {
									parent.location.reload();
								} else {
									jQuery( '.hestia-disable-elementor-styling' ).fadeOut( 500, function() { jQuery( this ).remove(); } );
								}
							}
						}
					);
				}
			);
	}
);
