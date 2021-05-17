/**
 * Main customize js file
 *
 * @package Hestia
 */

(function ($) {

    wp.customize(
        'hestia_shop_hide_categories', function (value) {
            value.bind(
                function (newval) {
                    var card_selector = $('.card-product .category');

                    if ( card_selector.length < 1 ) {
                        wp.customize.preview.send('refresh');
                        return;
                    }

                    if (newval) {
                        card_selector.css('display', 'none');
                    } else {
                        card_selector.css('display', 'block');
                    }
                }
            );
        }
    );

    wp.customize(
        'hestia_disable_order_note', function (value) {
            value.bind(
                function (newval) {
                    var selector = $('.shop_table.woocommerce-checkout-review-order-table');

                    if ( selector.length < 1 ) {
                        wp.customize.preview.send('refresh');
                        return;
                    }

                    if (newval) {
                        selector.css('display', 'none');
                    } else {
                        selector.css('display', 'block');
                    }
                }
            );
        }
    );

    wp.customize(
        'hestia_disable_coupon', function (value) {
            value.bind(
                function (newval) {
                    var coupon_selector = $('#hestia-checkout-coupon');

                    if ( coupon_selector.children().length < 1 ) {
                        wp.customize.preview.send('refresh');
                        return;
                    }

                    if (newval) {
                        coupon_selector.css('display', 'none');
                    } else {
                        coupon_selector.css('display', 'block');
                    }
                }
            );
        }
    );

})(jQuery);



