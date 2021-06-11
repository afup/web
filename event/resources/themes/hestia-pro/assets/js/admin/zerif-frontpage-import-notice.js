/**
 * Notice for importing Zerif frontpage
 *
 * @package Hestia
 */

/* global hestiaZerifImport */

(function ($) {
	$(document).ready(function () {

		$(document).on('click', '.notice.hestia-import-zerif .notice-dismiss', function () {
			jQuery.ajax({
				async: true,
				type: 'POST',
				data: {
					action: 'dismiss_zerif_import',
					nonce: hestiaZerifImport.dismissNonce
				},
				url: hestiaZerifImport.ajaxurl
			});
		});

		$(document).on('click', '#import-zerif-frontpage-button', function () {

			if ( $(this).attr( 'disabled' ) === 'disabled' ) {
				return;
			}

			$(this).parent().append('<span class="import-zerif-frontpage-loader"><span class="spinner" style="visibility: visible; float: none;  margin-top: 0;"></span></span>');
			$(this).attr( 'disabled', 'disabled' );

			jQuery.ajax({
				async: true,
				type: 'POST',
				data: {
					action: 'import_zerif_frontpage',
					nonce: hestiaZerifImport.importNonce
				},
				url: hestiaZerifImport.ajaxurl,
				success: function (result) {
					if ( result.success === true && typeof result.data !== 'undefined' ) {
						window.location.href = result.data;
					}
					$('.import-zerif-frontpage-loader').remove();
				},
				error: function () {
					$('.import-zerif-frontpage-loader').remove();
				},
			});
		});

	});
})(jQuery);