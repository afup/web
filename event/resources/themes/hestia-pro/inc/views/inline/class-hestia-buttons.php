<?php
/**
 * Inline style for buttons.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Colors
 */
class Hestia_Buttons extends Hestia_Abstract_Main {

	/**
	 * Selectors that work for all features.
	 *
	 * @var array
	 */
	public $padding_radius_hover_selectors = array(
		'.btn.btn-primary:not(.colored-button):not(.btn-left):not(.btn-right):not(.btn-just-icon):not(.menu-item)',
		'input[type="submit"]:not(.search-submit)',
		'body:not(.woocommerce-account) .woocommerce .button.woocommerce-Button',
		'.woocommerce .product button.button',
		'.woocommerce .product button.button.alt',
		'.woocommerce .product #respond input#submit',
		'.woocommerce-cart .blog-post .woocommerce .cart-collaterals .cart_totals .checkout-button',
		'.woocommerce-checkout #payment #place_order',
		'.woocommerce-account.woocommerce-page button.button',
		'.woocommerce .track_order button[type="submit"]',
		'.nav-cart .nav-cart-content .widget .buttons .button',
		'.woocommerce a.button.wc-backward',
		'body.woocommerce .wccm-catalog-item a.button',
		'body.woocommerce a.wccm-button.button',
		'form.woocommerce-form-coupon button.button',
		'div.wpforms-container .wpforms-form button[type=submit].wpforms-submit',
		'div.woocommerce a.button.alt',
		'div.woocommerce table.my_account_orders .button',
	);

	/**
	 * Selectors that work for padding and border radius features.
	 *
	 * @var array
	 */
	public $padding_radius_selectors = array(
		'.btn.colored-button',
		'.btn.btn-left',
		'.btn.btn-right',
		'.btn:not(.colored-button):not(.btn-left):not(.btn-right):not(.btn-just-icon):not(.menu-item):not(.hestia-sidebar-open):not(.hestia-sidebar-close)',
	);

	/**
	 * Selectors that work for hover and border radius features.
	 *
	 * @var array
	 */
	public $radius_hover_selectors = array(
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
	);

	/**
	 * Add all the hooks necessary.
	 */
	public function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'add_inline_buttons_styles' ) );
	}

	/**
	 * Add inline style for buttons.
	 */
	public function add_inline_buttons_styles() {
		wp_add_inline_style( apply_filters( 'hestia_custom_button_padding_handle', 'hestia_style' ), $this->buttons_padding_inline_style() );
		wp_add_inline_style( apply_filters( 'hestia_custom_button_radius_handle', 'hestia_style' ), $this->buttons_radius_inline_style() );
	}

	/**
	 * Buttons padding inline style.
	 *
	 * @return string
	 */
	private function buttons_padding_inline_style() {

		$custom_css = '';

		/**
		 * Gather data from customizer.
		 */
		$hestia_button_padding_dimensions = get_theme_mod(
			'hestia_button_padding_dimensions',
			apply_filters(
				'hestia_button_padding_dimensions_default',
				json_encode(
					array(
						'desktop' => json_encode(
							array(
								'desktop_vertical'   => 15,
								'desktop_horizontal' => 33,
							)
						),
					)
				)
			)
		);

		/**
		 * Transform data into arrays.
		 */
		$hestia_button_padding_dimensions_decode = json_decode( $hestia_button_padding_dimensions );
		$desktop_dimensions                      = json_decode( $hestia_button_padding_dimensions_decode->desktop );
		$dimensions                              = array(
			'horizontal' => '',
			'vertical'   => '',
		);
		if ( isset( $desktop_dimensions->desktop_horizontal ) ) {
			$dimensions['horizontal'] = $desktop_dimensions->desktop_horizontal;
		}
		if ( isset( $desktop_dimensions->desktop_vertical ) ) {
			$dimensions['vertical'] = $desktop_dimensions->desktop_vertical;
		}

		/**
		 * Adding style.
		 */
		$selectors = implode( ', ', $this->padding_radius_hover_selectors ) . ', ' . implode( ', ', $this->padding_radius_selectors );

		/**
		 * Adding padding
		 */
		$custom_css .= $selectors . '{';
		if ( ! empty( $dimensions['vertical'] ) ) {
			$custom_css .= ' padding-top:' . $dimensions['vertical'] . 'px; ';
			$custom_css .= ' padding-bottom:' . $dimensions['vertical'] . 'px; ';
		}
		if ( ! empty( $dimensions['horizontal'] ) ) {
			$custom_css .= ' padding-left:' . $dimensions['horizontal'] . 'px; ';
			$custom_css .= ' padding-right:' . $dimensions['horizontal'] . 'px; ';
		}

		$custom_css .= '}';

		return $custom_css;
	}

	/**
	 * Buttons border radius inline style.
	 *
	 * @return string
	 */
	private function buttons_radius_inline_style() {

		$custom_css = '';

		/**
		 * Gather data from customizer.
		 */
		$hestia_buttons_border_radius = get_theme_mod(
			'hestia_buttons_border_radius',
			apply_filters( 'hestia_buttons_border_radius_default', 3 )
		);

		/**
		 * Adding style.
		 */
		$selectors =
			implode( ', ', $this->padding_radius_hover_selectors ) . ', ' .
			implode( ', ', $this->radius_hover_selectors ) . ', ' .
			implode( ', ', $this->padding_radius_selectors );

		/**
		 * Adding border radius
		 */
		$custom_css .= $selectors . '{';
		$custom_css .= 'border-radius:' . $hestia_buttons_border_radius . 'px;';
		$custom_css .= '}';

		return $custom_css;
	}

}
