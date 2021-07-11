/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * @package hestia
 * @since 1.1.38
 */

/* global wp*/

var padding_radius_hover_selectors               = [
    '.btn.btn-primary:not(.colored-button):not(.btn-left):not(.btn-right):not(.btn-just-icon):not(.menu-item)',
    'input[type="submit"]:not(.search-submit)',
    'body:not(.woocommerce-account) .woocommerce .button.woocommerce-Button',
    '.woocommerce .product button.button',
    '.woocommerce .product button.button.alt',
    '.woocommerce .product #respond input#submit',
    '.woocommerce-cart .blog-post .woocommerce .cart-collaterals .cart_totals .checkout-button',
    '.woocommerce-checkout #payment #place_order',
    '.woocommerce-account.woocommerce-page button.button',
    '.nav-cart .nav-cart-content .widget .buttons .button',
    '.woocommerce a.button.wc-backward',
    'body.woocommerce .wccm-catalog-item a.button',
    'body.woocommerce a.wccm-button.button',
    'form.woocommerce-form-coupon button.button',
    'div.wpforms-container .wpforms-form button[type=submit].wpforms-submit',
    'div.woocommerce a.button.alt',
    'div.woocommerce table.my_account_orders .button',
];
var padding_radius_selectors = [
    '.btn.colored-button',
    '.btn.btn-left',
    '.btn.btn-right',
    '.btn:not(.colored-button):not(.btn-left):not(.btn-right):not(.btn-just-icon):not(.menu-item):not(.hestia-sidebar-open):not(.hestia-sidebar-close)',
];
var radius_hover_selectors                = [
    'input[type="submit"].search-submit',
    '.hestia-view-cart-wrapper .added_to_cart.wc-forward',
    '.woocommerce-product-search button',
    '.woocommerce-cart .actions .button',
    '#secondary div[id^=woocommerce_price_filter] .button',
    '.woocommerce div[id^=woocommerce_widget_cart].widget .buttons .button',
    '.searchform input[type=submit]',
    '.searchform button',
    '.search-form:not(.media-toolbar-primary) input[type=submit]',
    '.search-form:not(.media-toolbar-primary) button',
    '.woocommerce-product-search input[type=submit]',
];

/**
 * Live refresh for buttons padding
 */
wp.customize(
    'hestia_button_padding_dimensions', function( value ) {
        'use strict';
        value.bind(
            function( to ) {
                if ( to ) {
                    var values = JSON.parse( to );
                    var buttonSelectors = padding_radius_hover_selectors.join() + ', ' +
                        padding_radius_selectors.join();
                    var desktop_dimensions = JSON.parse( values.desktop );
                    if( desktop_dimensions !== ''){
                        var dimensions = {
                            horizontal:    desktop_dimensions.desktop_horizontal,
                            vertical:      desktop_dimensions.desktop_vertical
                        };

                        jQuery(buttonSelectors).css('padding' , dimensions.vertical + 'px ' + dimensions.horizontal+'px');

                    }
                }
            }

        );
    });

/**
 * Live refresh for buttons border radius
 */
wp.customize(
    'hestia_buttons_border_radius', function( value ) {
        'use strict';
        value.bind(
            function( to ) {
                var buttonSelectors = padding_radius_hover_selectors.join() + ', ' +
                    radius_hover_selectors.join()+ ', ' +
                    padding_radius_selectors.join();

                jQuery(buttonSelectors).css('border-radius' , to+'px');
            }
        );
    }
);
