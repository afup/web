/**
 * File customizer-controls.js
 *
 * The file for generic customizer controls.
 *
 * @package Hestia
 */

/* global imageObject */

jQuery( document ).ready(
	function () {
		'use strict';

		jQuery.aboutBackground = {

			init: function () {
				this.updateBackgroundControl();
				this.updateBackgroundControlBuffer();
				this.focusMenu();
				this.updateEditorLink();
            },

            updateBackgroundControl: function () {
                wp.customize(
                    'page_on_front', function( value ) {
                        value.bind(
                            function( newval ) {

                                jQuery.ajax({
                                    type: 'POST',
                                    data: {
                                        action: 'update_image_buffer',
                                        pid: newval,
                                        nonce: imageObject.imagenonce
                                    },
                                    url: imageObject.ajaxurl,
                                    success: function (result) {
                                        var image = result.data;
                                        var html;
                                        if (image !== '' && image !== 'undefined') {
                                            html = '<label for="hestia_feature_thumbnail-button">' +
                                                '<span class="customize-control-title">About background</span>' +
                                                '</label>' +
                                                '<div class="attachment-media-view attachment-media-view-image landscape">' +
                                                '<div class="thumbnail thumbnail-image">' +
                                                '<img class="attachment-thumb" src="' + image + '" draggable="false" alt=""> ' +
                                                '</div>' +
                                                '<div class="actions">' +
                                                '<button type="button" class="button remove-button">Remove</button>' +
                                                '<button type="button" class="button upload-button control-focus" id="hestia_feature_thumbnail-button">Change Image</button> ' +
                                                '<div style="clear:both"></div>' +
                                                '</div>' +
                                                '</div>';
                                        } else {
                                            html = '<label class="customize-control-title" for="customize-media-control-button-105">About background</label>' +
                                                '<div class="customize-control-notifications-container" style="display: none;"><ul></ul></div>' +
                                                '<div class="attachment-media-view">\n' +
                                                '<div class="placeholder">' +
                                                'No image selected' +
                                                '</div>' +
                                                '<div class="actions">' +
                                                '<button type="button" class="button default-button">Default</button>' +
                                                '<button type="button" class="button upload-button" id="customize-media-control-button-105">Select image</button>' +
                                                '</div>' +
                                                '</div>';
                                        }
                                        wp.customize.control( 'hestia_feature_thumbnail' ).container['0'].innerHTML = html;
                                        wp.customize.instance( 'hestia_feature_thumbnail' ).previewer.refresh();
                                    }
                                });
                            }
                        );
                    }
                );
            },

            updateBackgroundControlBuffer: function () {
                /**
                 * Update the buffer for about background.
                 */
                wp.customize( 'hestia_feature_thumbnail', function ( value ) {
                    value.bind( function ( newval ) {
                    	jQuery.ajax({
		                   type: 'POST',
		                   url: imageObject.ajaxurl,
		                   data: {
			                   action: 'update_image_buffer',
			                   value: newval,
			                   nonce: imageObject.imagenonce,
		                   }
                    	});
                    });
                });
            },

			/**
			* Focus menu when the user clicks on customizer shortcut of the menu.
			*/
			focusMenu: function () {
				wp.customize.previewer.bind(
					'trigger-focus-menu', function() {
						wp.customize.section( 'menu_locations' ).focus();
					}
				);
			},

            updateEditorLink: function () {
                wp.customize(
                    'page_on_front', function( value ) {
                        value.bind(
                            function( newval ) {
                            	if( typeof wp.customize.control( 'hestia_shortcut_editor' ) !== 'undefined' ){
                                    var newLink = wp.customize.control( 'hestia_shortcut_editor' ).container['0'].innerHTML .replace(/(post=).*?(&)/,'$1' + newval + '$2');
                                    wp.customize.control( 'hestia_shortcut_editor' ).container['0'].innerHTML = newLink;
	                            }
							}
						);
                    }
				);
            }
		};

        jQuery.aboutBackground.init();



		wp.customize(
			'hestia_team_content', function ( value ) {
				value.bind(
					function () {
						var authors_values;
						var result = '';

						if ( jQuery.isFunction( wp.customize._value.hestia_authors_on_blog ) ) {
							authors_values = wp.customize._value.hestia_authors_on_blog();
						}
						jQuery( '#customize-control-hestia_team_content .customizer-repeater-general-control-repeater-container' ).each(
							function () {
								var title = jQuery( this ).find( '.customizer-repeater-title-control' ).val();
								var id = jQuery( this ).find( '.social-repeater-box-id' ).val();
								if ( typeof (title) !== 'undefined' && title !== '' && typeof (id) !== 'undefined' && id !== '' ) {
									result += '<option value="' + id + '" ';
									if ( authors_values && authors_values !== 'undefined' ) {
										if ( authors_values.indexOf( id ) !== -1 ) {
											result += 'selected';
										}
									}
									result += '>' + title + '</option>';
								}
							}
						);

						jQuery( '#customize-control-hestia_authors_on_blog .repeater-multiselect-team' ).html( result );
					}
				);
			}
		);

		/* Move controls to Widgets sections. Used for sidebar placeholders */
		if ( typeof wp.customize.control( 'hestia_placeholder_sidebar_1' ) !== 'undefined' ) {
			wp.customize.control( 'hestia_placeholder_sidebar_1' ).section( 'sidebar-widgets-sidebar-1' );
		}
		if ( typeof wp.customize.control( 'hestia_placeholder_sidebar_woocommerce' ) !== 'undefined' ) {
			wp.customize.control( 'hestia_placeholder_sidebar_woocommerce' ).section( 'sidebar-widgets-sidebar-woocommerce' );
		}

		jQuery(document).on( 'click', '.quick-links a', function(e) {
			e.preventDefault();
			e.stopPropagation();
			var control = jQuery( this ).data( 'control-focus' );
			if ( control ){
				wp.customize.control( control ).focus();
				jQuery( 'label.' + control ).click();
			}
			var section = jQuery( this ).data( 'section-focus' );
			if( section ){
				wp.customize.section( section ).focus();
			}
		} );

		jQuery( '.focus-customizer-header-image' ).on( 'click', function ( e ) {
			e.preventDefault();
			wp.customize.section( 'header_image' ).focus();
		} );


		/**
		 * Toggle section user clicks on customizer shortcut.
		 */
		var customize = wp.customize;
        if( typeof customize !== 'undefined' && customize.hasOwnProperty('previewer') ) {
			customize.previewer.bind(
				'hestia-customize-disable-section', function ( data ) {
					jQuery( '[data-customize-setting-link=' + data + ']' ).trigger( 'click' );
				}
			);

			customize.previewer.bind(
				'hestia-customize-focus-control', function ( data ) {
					wp.customize.control( data ).focus();
				}
			);
		}
		// Toggle visibility of Header Video notice when active state change.
        customize.control( 'header_video', function( headerVideoControl ) {
            headerVideoControl.deferred.embedded.done( function() {
                var toggleNotice = function() {
                    var section = customize.section( headerVideoControl.section() ), noticeCode = 'video_header_not_available';
					section.notifications.remove( noticeCode );
                };
                toggleNotice();
                headerVideoControl.active.bind( toggleNotice );
            } );
        } );
	}
);
