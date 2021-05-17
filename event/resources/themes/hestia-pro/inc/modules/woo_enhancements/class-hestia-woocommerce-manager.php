<?php
/**
 * Class that manages WooCommerce appearance.
 *
 * @package Inc/Modules/Woo_Enhancements
 */

/**
 * Class Hestia_Woocommerce_Module
 */
class Hestia_Woocommerce_Manager extends Hestia_Abstract_Module {

	/**
	 * Check if this module should load.
	 *
	 * @return bool|void
	 */
	protected function should_load() {
		return class_exists( 'WooCommerce', false );
	}

	/**
	 * Run module.
	 */
	function run_module() {
		$submodules = array(
			'Hestia_Woo_Shop_Page'    => HESTIA_PHP_INCLUDE . 'modules/woo_enhancements/views/class-hestia-woo-shop-page.php',
			'Hestia_Woo_Product_Page' => HESTIA_PHP_INCLUDE . 'modules/woo_enhancements/views/class-hestia-woo-product-page.php',
			'Hestia_Woo_Account_Page' => HESTIA_PHP_INCLUDE . 'modules/woo_enhancements/views/class-hestia-woo-account-page.php',
		);
		foreach ( $submodules as $module => $path ) {
			if ( ! is_file( $path ) ) {
				continue;
			}
			require $path;
			$instance = new $module();
			add_action( 'wp', array( $instance, 'run' ) );
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Check if there is an elementor template for shop.
	 */
	public static function is_elementor_template( $location, $cond ) {
		if ( ! did_action( 'elementor_pro/init' ) ) {
			return false;
		}
		if ( ! class_exists( '\ElementorPro\Plugin', false ) ) {
			return false;
		}
		$conditions_manager = \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'theme-builder' )->get_conditions_manager();
		$documents          = $conditions_manager->get_documents_for_location( $location );
		foreach ( $documents as $document ) {
			$conditions = $conditions_manager->get_document_conditions( $document );
			foreach ( $conditions as $condition ) {
				if ( 'include' === $condition['type'] && $cond === $condition['name'] ) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Enqueue theme scripts.
	 */
	public function enqueue_scripts() {
		$is_elementor_shop_template    = self::is_elementor_template( 'archive', 'product_archive' );
		$is_elementor_product_template = self::is_elementor_template( 'single', 'product' );
		if ( $is_elementor_shop_template || $is_elementor_product_template ) {
			return false;
		}

		wp_enqueue_style( 'hestia_woocommerce_style', get_template_directory_uri() . '/assets/css/woocommerce' . ( ( HESTIA_DEBUG ) ? '' : '.min' ) . '.css', array(), HESTIA_VERSION );
		wp_style_add_data( 'hestia_woocommerce_style', 'rtl', 'replace' );
		if ( ! HESTIA_DEBUG ) {
			wp_style_add_data( 'hestia_woocommerce_style', 'suffix', '.min' );
		}

		$hestia_cart_url = '';
		if ( function_exists( 'wc_get_cart_url' ) ) {
			$hestia_cart_url = wc_get_cart_url();
		}

		wp_localize_script(
			'hestia_scripts',
			'hestiaViewcart',
			array(
				'view_cart_label' => esc_html__( 'View cart', 'hestia-pro' ), // label of View cart button,
				'view_cart_link'  => esc_url( $hestia_cart_url ), // link of View cart button
			)
		);

		wp_add_inline_style( 'hestia_woocommerce_style', $this->woo_colors_inline_style() );
	}

	/**
	 * WooCommerce inline color style.
	 *
	 * @return string
	 */
	private function woo_colors_inline_style() {
		if ( ! class_exists( 'WooCommerce', false ) ) {
			return '';
		}

		$color_accent           = get_theme_mod( 'accent_color', apply_filters( 'hestia_accent_color_default', '#e91e63' ) );
		$custom_css_woocommerce = '';

		$custom_css_woocommerce .= ! empty( $color_accent ) ? '
		.woocommerce-cart .shop_table .actions .coupon .input-text:focus,
		.woocommerce-checkout #customer_details .input-text:focus, .woocommerce-checkout #customer_details select:focus,
		.woocommerce-checkout #order_review .input-text:focus,
		.woocommerce-checkout #order_review select:focus,
		.woocommerce-checkout .woocommerce-form .input-text:focus,
		.woocommerce-checkout .woocommerce-form select:focus,
		.woocommerce div.product form.cart .variations select:focus,
		.woocommerce .woocommerce-ordering select:focus {
			background-image: -webkit-gradient(linear,left top, left bottom,from(' . esc_html( $color_accent ) . '),to(' . esc_html( $color_accent ) . ')),-webkit-gradient(linear,left top, left bottom,from(#d2d2d2),to(#d2d2d2));
			background-image: -webkit-linear-gradient(linear,left top, left bottom,from(' . esc_html( $color_accent ) . '),to(' . esc_html( $color_accent ) . ')),-webkit-linear-gradient(linear,left top, left bottom,from(#d2d2d2),to(#d2d2d2));
			background-image: linear-gradient(linear,left top, left bottom,from(' . esc_html( $color_accent ) . '),to(' . esc_html( $color_accent ) . ')),linear-gradient(linear,left top, left bottom,from(#d2d2d2),to(#d2d2d2));
		}

		.woocommerce div.product .woocommerce-tabs ul.tabs.wc-tabs li.active a {
			color:' . esc_html( $color_accent ) . ';
		}
		
		.woocommerce div.product .woocommerce-tabs ul.tabs.wc-tabs li.active a,
		.woocommerce div.product .woocommerce-tabs ul.tabs.wc-tabs li a:hover {
			border-color:' . esc_html( $color_accent ) . '
		}
		
		.woocommerce div.product form.cart .reset_variations:after{
			background-color:' . esc_html( $color_accent ) . '
		}
		
		.added_to_cart.wc-forward:hover,
		#add_payment_method .wc-proceed-to-checkout a.checkout-button:hover,
		#add_payment_method .wc-proceed-to-checkout a.checkout-button,
		.added_to_cart.wc-forward,
		.woocommerce nav.woocommerce-pagination ul li span.current,
		.woocommerce ul.products li.product .onsale,
		.woocommerce span.onsale,
		.woocommerce .single-product div.product form.cart .button,
		.woocommerce #respond input#submit,
		.woocommerce button.button,
		.woocommerce input.button,
		.woocommerce-cart .wc-proceed-to-checkout a.checkout-button,
		.woocommerce-checkout .wc-proceed-to-checkout a.checkout-button,
		.woocommerce #respond input#submit.alt,
		.woocommerce a.button.alt,
		.woocommerce button.button.alt,
		.woocommerce input.button.alt,
		.woocommerce input.button:disabled,
		.woocommerce input.button:disabled[disabled],
		.woocommerce a.button.wc-backward,
		.woocommerce .single-product div.product form.cart .button:hover,
		.woocommerce #respond input#submit:hover,
		.woocommerce button.button:hover,
		.woocommerce input.button:hover,
		.woocommerce-cart .wc-proceed-to-checkout a.checkout-button:hover,
		.woocommerce-checkout .wc-proceed-to-checkout a.checkout-button:hover,
		.woocommerce #respond input#submit.alt:hover,
		.woocommerce a.button.alt:hover,
		.woocommerce button.button.alt:hover,
		.woocommerce input.button.alt:hover,
		.woocommerce input.button:disabled:hover,
		.woocommerce input.button:disabled[disabled]:hover,
		.woocommerce #respond input#submit.alt.disabled,
		.woocommerce #respond input#submit.alt.disabled:hover,
		.woocommerce #respond input#submit.alt:disabled,
		.woocommerce #respond input#submit.alt:disabled:hover,
		.woocommerce #respond input#submit.alt:disabled[disabled],
		.woocommerce #respond input#submit.alt:disabled[disabled]:hover,
		.woocommerce a.button.alt.disabled,
		.woocommerce a.button.alt.disabled:hover,
		.woocommerce a.button.alt:disabled,
		.woocommerce a.button.alt:disabled:hover,
		.woocommerce a.button.alt:disabled[disabled],
		.woocommerce a.button.alt:disabled[disabled]:hover,
		.woocommerce button.button.alt.disabled,
		.woocommerce button.button.alt.disabled:hover,
		.woocommerce button.button.alt:disabled,
		.woocommerce button.button.alt:disabled:hover,
		.woocommerce button.button.alt:disabled[disabled],
		.woocommerce button.button.alt:disabled[disabled]:hover,
		.woocommerce input.button.alt.disabled,
		.woocommerce input.button.alt.disabled:hover,
		.woocommerce input.button.alt:disabled,
		.woocommerce input.button.alt:disabled:hover,
		.woocommerce input.button.alt:disabled[disabled],
		.woocommerce input.button.alt:disabled[disabled]:hover,
		.woocommerce-button,
		.woocommerce-Button,
		.woocommerce-button:hover,
		.woocommerce-Button:hover,
		#secondary div[id^=woocommerce_price_filter] .price_slider .ui-slider-range,
		.footer div[id^=woocommerce_price_filter] .price_slider .ui-slider-range,
		div[id^=woocommerce_product_tag_cloud].widget a,
		div[id^=woocommerce_widget_cart].widget .buttons .button,
		div.woocommerce table.my_account_orders .button {
		    background-color: ' . esc_html( $color_accent ) . ';
		}
		
		.added_to_cart.wc-forward,
		.woocommerce .single-product div.product form.cart .button,
		.woocommerce #respond input#submit,
		.woocommerce button.button,
		.woocommerce input.button,
		#add_payment_method .wc-proceed-to-checkout a.checkout-button,
		.woocommerce-cart .wc-proceed-to-checkout a.checkout-button,
		.woocommerce-checkout .wc-proceed-to-checkout a.checkout-button,
		.woocommerce #respond input#submit.alt,
		.woocommerce a.button.alt,
		.woocommerce button.button.alt,
		.woocommerce input.button.alt,
		.woocommerce input.button:disabled,
		.woocommerce input.button:disabled[disabled],
		.woocommerce a.button.wc-backward,
		.woocommerce div[id^=woocommerce_widget_cart].widget .buttons .button,
		.woocommerce-button,
		.woocommerce-Button,
		div.woocommerce table.my_account_orders .button {
		    -webkit-box-shadow: 0 2px 2px 0 ' . hestia_hex_rgba( $color_accent, '0.14' ) . ',0 3px 1px -2px ' . hestia_hex_rgba( $color_accent, '0.2' ) . ',0 1px 5px 0 ' . hestia_hex_rgba( $color_accent, '0.12' ) . ';
		    box-shadow: 0 2px 2px 0 ' . hestia_hex_rgba( $color_accent, '0.14' ) . ',0 3px 1px -2px ' . hestia_hex_rgba( $color_accent, '0.2' ) . ',0 1px 5px 0 ' . hestia_hex_rgba( $color_accent, '0.12' ) . ';
		}
		
		.woocommerce nav.woocommerce-pagination ul li span.current,
		.added_to_cart.wc-forward:hover,
		.woocommerce .single-product div.product form.cart .button:hover,
		.woocommerce #respond input#submit:hover,
		.woocommerce button.button:hover,
		.woocommerce input.button:hover,
		#add_payment_method .wc-proceed-to-checkout a.checkout-button:hover,
		.woocommerce-cart .wc-proceed-to-checkout a.checkout-button:hover,
		.woocommerce-checkout .wc-proceed-to-checkout a.checkout-button:hover,
		.woocommerce #respond input#submit.alt:hover,
		.woocommerce a.button.alt:hover,
		.woocommerce button.button.alt:hover,
		.woocommerce input.button.alt:hover,
		.woocommerce input.button:disabled:hover,
		.woocommerce input.button:disabled[disabled]:hover,
		.woocommerce a.button.wc-backward:hover,
		.woocommerce div[id^=woocommerce_widget_cart].widget .buttons .button:hover,
		.hestia-sidebar-open.btn.btn-rose:hover,
		.hestia-sidebar-close.btn.btn-rose:hover,
		.pagination span.current:hover,
		.woocommerce-button:hover,
		.woocommerce-Button:hover,
		div.woocommerce table.my_account_orders .button:hover {
			-webkit-box-shadow: 0 14px 26px -12px ' . hestia_hex_rgba( $color_accent, '0.42' ) . ',0 4px 23px 0 rgba(0,0,0,0.12),0 8px 10px -5px ' . hestia_hex_rgba( $color_accent, '0.2' ) . ';
		    box-shadow: 0 14px 26px -12px ' . hestia_hex_rgba( $color_accent, '0.42' ) . ',0 4px 23px 0 rgba(0,0,0,0.12),0 8px 10px -5px ' . hestia_hex_rgba( $color_accent, '0.2' ) . ';
			color: #fff;
		}
		
		#secondary div[id^=woocommerce_price_filter] .price_slider .ui-slider-handle,
		.footer div[id^=woocommerce_price_filter] .price_slider .ui-slider-handle {
			border-color: ' . esc_html( $color_accent ) . ';
		}
		' : '';

		return $custom_css_woocommerce;
	}
}
