/* global woocommerce_params */

(function ($) {
	$(document).on('click', 'body.seamless-add-to-cart .single_add_to_cart_button', function (e) {
		e.preventDefault();

		var $thisbutton = $(this),
			$form = $thisbutton.closest('form.cart');

		var data = {};
		$.each( $form.serializeArray(), function( _, kv ) {
			data[kv.name] = kv.value;
		});

		if ( ! data.hasOwnProperty( 'product_id' ) && $thisbutton.val() ){
			data.product_id = $thisbutton.val();
		}

		if( ! data.product_id ) {
			return false;
		}
		if ( data.hasOwnProperty( 'variation_id' ) ) {
			data.product_id = data.variation_id;
			delete data.variation_id;
			delete data[ 'add-to-cart' ];
		}

		var requestUrl = woocommerce_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'add_to_cart' );

		var navCart = $( 'li.nav-cart' );
		$.ajax({
			type: 'post',
			url: requestUrl,
			data: data,
			beforeSend: function () {
				$thisbutton.removeClass('added').addClass('loading');
			},
			complete: function () {
				$thisbutton.removeClass('loading').removeClass('added');
			},
			success: function (response) {
				if (response.error & response.product_url) {
					window.location = response.product_url;
					return;
				} else {
					if( navCart.length > 0 ){

						navCart.addClass( 'hestia-anim-cart' );
						setTimeout(
							function () {
								navCart.removeClass( 'hestia-anim-cart' );
							}, 2000
						);
					}
					$(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $thisbutton]);
				}
			},
		});

		return false;
	});
})(jQuery);
