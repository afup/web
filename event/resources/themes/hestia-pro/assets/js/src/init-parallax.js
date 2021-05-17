/**
 * Call parallax script for the layers added in customizer
 *
 * @since 1.1.72
 * @package Hestia
 */

/* global Parallax*/
jQuery( document ).ready(
    function ( $ ) {

        var isMobile = {
            Android: function() {
                return navigator.userAgent.match(/Android/i);
            },
            BlackBerry: function() {
                return navigator.userAgent.match(/BlackBerry/i);
            },
            iOS: function() {
                return navigator.userAgent.match(/iPhone|iPad|iPod/i);
            },
            Opera: function() {
                return navigator.userAgent.match(/Opera Mini/i);
            },
            Windows: function() {
                return navigator.userAgent.match(/IEMobile/i);
            },
            any: function() {
                return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
            }
        };

        $.hestiaParallax = {
            'parallaxMove':function(){
                if( isMobile.any() ) {
                    return;
                }
                var parallaxElement = jQuery('#parallax_move');
                if( parallaxElement.length>0 ) {
                    var scene = document.getElementById('parallax_move');
                    var window_width = jQuery(window).outerWidth();
                    parallaxElement.css({
                        'width':            window_width + 120,
                        'margin-left':      -60,
                        'margin-top':       -60,
                        'position':         'absolute'
                    });
                    var h = jQuery('.page-header').outerHeight();
                    parallaxElement.children().each(function(){
                        jQuery(this).css({
                            'height': h+100
                        });
                    });
                    new Parallax(scene);
                }
            }
        };

        jQuery(window).on( 'load', function(){ $.hestiaParallax.parallaxMove(); } );
        jQuery(window).resize(function(){$.hestiaParallax.parallaxMove();});

    }
);
