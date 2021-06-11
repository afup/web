/**
 * Scripts file for Hestia Pro
 *
 * @package Hestia
 */
(function ( $ ) {
	$.hestiaProScripts = {
		'showCartAfterAdd': function () {
			// Open WooCommerce nav cart after add product
			var addToCartBtn = $( '.btn.product_type_simple.ajax_add_to_cart' );

			if ( addToCartBtn.length > 0 ) {

				var navCart = $( 'li.nav-cart' );

				$( addToCartBtn ).click(
					function () {
						setTimeout(
							function () {
								navCart.addClass( 'hestia-anim-cart' );
							}, 1000
						); // delay before, avoid adding product flick

						setTimeout(
							function () {
								navCart.removeClass( 'hestia-anim-cart' );
							}, 5000
						); // close the cart content after 5 sec
					}
				);
			}
		},
		'fixElementorHeader': function () {
			if ( $( '.header-footer-elementor' ).length > 0 ) {
				return;
			}
			if ( $( '.elementor-location-header' ).length === 0 ) {
				return false;
			}
			if ( ! $( 'body' ).hasClass( 'header-layout-classic-blog' ) ) {
				return false;
			}

			if ( $( 'body' ).hasClass( 'home page-template-default' ) ) {
				return false;
			}
			$( '.page .main-raised' ).css( 'margin-top', 0 );
		}
	};
}( jQuery ));

jQuery( document ).ready( function () {
	jQuery.hestiaProScripts.showCartAfterAdd();
	jQuery.hestiaProScripts.fixElementorHeader();
} );