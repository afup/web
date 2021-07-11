<?php
/**
 * The class that handle the cart view in WooCommerce.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Cart_View
 */
class Hestia_Cart_View extends Hestia_Abstract_Main {

	/**
	 * Check if submodule should be loaded.
	 *
	 * @return bool
	 */
	private function should_load() {
		if ( ! is_cart() ) {
			return false;
		}
		return true;
	}

	/**
	 * Initialize the module.
	 */
	public function init() {
		add_action( 'wp', array( $this, 'run' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'add_inline_styles' ) );
	}

	/**
	 * Run product view.
	 */
	public function run() {
		if ( ! $this->should_load() ) {
			return false;
		}

		$this->manage_cross_sales();
		$this->manage_payment_icons();
		return true;
	}

	/**
	 * Inline style for payment icons.
	 *
	 * @return bool
	 */
	public function add_inline_styles() {
		$css = '';

		$payment_icons = get_theme_mod( 'hestia_enable_payment_icons', false );
		if ( $payment_icons === true ) {
			$css .= '.hestia-payment-cart-total{
			  margin-top: 15px;
			}
	
			.hestia-payment-icons-wrapper {
			  display: inline-block;
			  width: 100%;
			  margin: 0 -3px;
			}
			.hestia-payment-icons-wrapper .hestia-payment-icon.choice-customizer_repeater_icon {
				background-color: rgba(0, 0, 0, 0.1);
				padding: 3px 5px 5px;
			}
			.hestia-payment-icons-wrapper .hestia-payment-icon {
			  display: inline-block;
			  opacity: .6;
			  transition: opacity .3s;
			  border-radius: 5px;
			  margin: 3px;
			}
			.hestia-payment-icons-wrapper .hestia-payment-icon:hover {
			  opacity: 1;
			}
			.hestia-payment-icons-wrapper .hestia-payment-icon i {
			  vertical-align: middle;
			  font-size: 25px;
			}
			.hestia-payment-icons-wrapper .hestia-payment-icon img{
				height: 25px;
				width: auto;
			}';
		}

		wp_add_inline_style( 'hestia_style', $css );

		return true;
	}

	/**
	 * Manage cross-sells.
	 */
	private function manage_cross_sales() {
		$enable_cart_upsells = get_theme_mod( 'hestia_enable_cross_sell_products', true );
		if ( $enable_cart_upsells === false ) {
			remove_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display' );
		}
	}

	/**
	 * Manage payment icons
	 */
	private function manage_payment_icons() {
		add_action( 'woocommerce_proceed_to_checkout', array( $this, 'display_payment_icons_after_cart_totals' ), 100 );
		require_once( HESTIA_PHP_INCLUDE . 'addons/modules/woo_enhancements/views/class-hestia-payment-icons-widget.php' );
		add_action( 'widgets_init', array( $this, 'register_payment_icons_widget' ) );
	}

	/**
	 * Display payment icons after cart totals.
	 *
	 * @return bool
	 */
	public function display_payment_icons_after_cart_totals() {
		$payment_icons = get_theme_mod( 'hestia_enable_payment_icons', false );
		if ( $payment_icons === false ) {
			return false;
		}
		echo '<div class="hestia-payment-cart-total">';
		echo self::render_payment_icons();
		echo '</div>';
		return true;
	}

	/**
	 * Register payment icons widget.
	 */
	public function register_payment_icons_widget() {
		if ( ! class_exists( 'Woocommerce' ) ) {
			return false;
		}
		register_widget( 'hestia_payment_icons_widget' );
		return true;
	}

	/**
	 * Static method that renders the payment icons.
	 * This method is static because we need to call it in footer component too.
	 *
	 * @return bool
	 */
	public static function render_payment_icons() {
		$payment_icons = get_theme_mod( 'hestia_enable_payment_icons', false );
		if ( $payment_icons === false ) {
			return false;
		}

		$payment_icons = get_theme_mod( 'hestia_payment_icons', Hestia_Defaults_Models::instance()->get_payment_icons_defaults() );
		$payment_icons = json_decode( $payment_icons, true );
		$html_result   = '';
		if ( empty( $payment_icons ) ) {
			return $html_result;
		}

		$html_result .= '<div class="hestia-payment-icons-wrapper">';

		foreach ( $payment_icons as $payment_method ) {
			$choice = array_key_exists( 'choice', $payment_method ) ? $payment_method['choice'] : 'customizer_repeater_icon';
			if ( $choice === 'customizer_repeater_none' ) {
				continue;
			}
			if ( $choice === 'customizer_repeater_image' && empty( $payment_method['image_url'] ) ) {
				continue;
			}
			if ( $choice === 'customizer_repeater_icon' && empty( $payment_method['icon_value'] ) ) {
				continue;
			}

			$html_result .= '<div class="hestia-payment-icon choice-' . esc_attr( $choice ) . '">';
			if ( $choice === 'customizer_repeater_icon' ) {
				$html_result .= '<i class="' . hestia_display_fa_icon( $payment_method['icon_value'] ) . '"></i>';
			}
			if ( $choice === 'customizer_repeater_image' ) {
				$html_result .= '<img src="' . esc_url( $payment_method['image_url'] ) . '" alt="' . esc_attr__( 'Payment Icon', 'hestia-pro' ) . '">';
			}
			$html_result .= '</div>';
		}
		$html_result .= '</div>';
		return $html_result;
	}
}
