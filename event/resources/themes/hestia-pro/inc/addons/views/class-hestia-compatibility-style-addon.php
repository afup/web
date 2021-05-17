<?php
/**
 * File that loads inline style for compatibility with other plugins.
 *
 * @package Inc/Addons/Views
 */

/**
 * Class Hestia_Compatibility_Style_Addon
 */
class Hestia_Compatibility_Style_Addon extends Hestia_Compatibility_Style {

	/**
	 * Load each function with its style.
	 */
	protected function collect_inline_style() {
		parent::collect_inline_style();

		$this->load_formidable_forms();
		$this->load_visual_form_builder();
		$this->load_easy_forms_for_mailchimp();
		$this->load_woocommerce_cart();
		$this->load_woocommerce_germanized();
		$this->load_aos_animations();
	}

	/**
	 * Load Formidable forms styling.
	 *
	 * @return bool
	 */
	private function load_formidable_forms() {
		if ( ! class_exists( 'FrmForm' ) ) {
			return false;
		}
		$css = '
		.frm_forms input[type=text], .frm_forms input[type=text]:focus,
		.frm_forms input[type=password],
		.frm_forms input[type=password]:focus,
		.frm_forms input[type=email],
		.frm_forms input[type=email]:focus,
		.frm_forms input[type=number],
		.frm_forms input[type=number]:focus,
		.frm_forms input[type=url],
		.frm_forms input[type=url]:focus,
		.frm_forms input[type=tel],
		.frm_forms input[type=tel]:focus,
		.frm_forms input[type=search],
		.frm_forms input[type=search]:focus,
		.frm_forms input:not([type=file]),
		.frm_forms input:not([type=file]):focus,
		.frm_forms select,
		.frm_forms select:focus,
		.frm_forms textarea,
		.frm_forms textarea:focus,
		.frm_forms .frm_form_field:invalid,
		.frm_forms .frm_form_field:invalid:focus {
		  border: none !important;
		  border-radius: 0;
		  box-shadow: none !important;
		  outline: none;
		}
		';

		$this->add_css( $css );
		return true;
	}

	/**
	 * Load Visual Form Builder styling.
	 *
	 * @return bool
	 */
	private function load_visual_form_builder() {
		if ( ! class_exists( 'Visual_Form_Builder' ) ) {
			return false;
		}

		$css = '
		.visual-form-builder input:not([type=submit]):focus,
		.visual-form-builder select:focus,
		.visual-form-builder textarea:focus {
		  border: none;
		  box-shadow: none;
		  outline: none;
		}
		.visual-form-builder fieldset {
		  background: none;
		  border: none;
		  border-radius: 0;
		}
		.visual-form-builder .vfb-legend {
		  border-bottom: none;
		  color: #3c4858;
		}
		';

		$this->add_css( $css );
		return true;
	}

	/**
	 * Load Easy Forms for Mailchimp styling
	 *
	 * @return bool
	 */
	private function load_easy_forms_for_mailchimp() {
		if ( ! defined( 'YIKES_MC_VERSION' ) ) {
			return false;
		}

		$css = '
		.yikes-easy-mc-form input[type=text],
		.yikes-easy-mc-form input[type=url],
		.yikes-easy-mc-form input[type=email],
		.yikes-easy-mc-form input[type=number],
		.yikes-easy-mc-form select {
		  background-color: transparent !important;
		  border: none !important;
		}
		.yikes-easy-mc-form input[type=text]:focus,
		.yikes-easy-mc-form input[type=url]:focus,
		.yikes-easy-mc-form input[type=email]:focus,
		.yikes-easy-mc-form input[type=number]:focus,
		.yikes-easy-mc-form select:focus {
		  outline: none !important;
		}
		';

		$this->add_css( $css );
		return true;
	}

	/**
	 * Load WooCommerce Cart styling.
	 *
	 * @return bool
	 */
	private function load_woocommerce_cart() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return false;
		}

		$css = '
		.nav-cart {
		  position: relative;
		  vertical-align: middle;
		  display: block;
		}
		.nav-cart .woocommerce-mini-cart {
		  max-height: 265px;
		  width: 400px;
		  overflow-y: scroll;
		  overflow-x: hidden;
		}
		.nav-cart .widget_shopping_cart_content {
		  overflow: hidden;
		}
		.nav-cart .nav-cart-icon {
		  position: relative;
		}
		.nav-cart .nav-cart-icon i,
		.nav-cart .nav-cart-icon span {
		  display: table-cell;
		}
		.nav-cart .nav-cart-icon span {
		  background: #fff;
		  border: 1px solid #ddd;
		  border-radius: 3px;
		  color: #555;
		  display: inline-block;
		  padding: 1px 3px;
		  position: absolute;
		  top: 24px;
		  left: 28px;
		}
		.nav-cart .nav-cart-content {
		  display: inline-block;
		  opacity: 0;
		  position: absolute;
		  right: 0;
		  top: 100%;
		  visibility: hidden;
		  transform: translateY(-10px);
		}
		.nav-cart .nav-cart-content .woocommerce-mini-cart__empty-message {
		  white-space: nowrap;
		  text-align: center;
		}
		.nav-cart .nav-cart-content .widget {
		  background: #fff;
		  border-radius: 0 0 6px 6px;
		  margin: 0;
		  padding: 15px;
		  max-width: 350px;
		  -webkit-box-shadow: 0 10px 20px -12px rgba(0, 0, 0, 0.42), 0 12px 20px 0 rgba(0, 0, 0, 0.12), 0 8px 10px -5px rgba(0, 0, 0, 0.2);
		  box-shadow: 0 10px 20px -12px rgba(0, 0, 0, 0.42), 0 12px 20px 0 rgba(0, 0, 0, 0.12), 0 8px 10px -5px rgba(0, 0, 0, 0.2);
		}
		.nav-cart .nav-cart-content .widget li {
		  width: 320px;
		  padding-top: 0;
		  padding-bottom: 25px;
		  padding-right: 0;
		  padding-left: 2em;
		}
		.nav-cart .nav-cart-content .widget li .variation {
		  padding-top: 5px;
		  padding-bottom: 0;
		  padding-right: 0;
		  padding-left: 70px;
		}
		.nav-cart .nav-cart-content .widget li img {
		  position: absolute;
		  left: 30px;
		  border-radius: 6px;
		  float: left;
		  width: 50px;
		  margin-left: 0;
		  margin-right: 15px;
		  -webkit-transition: all 300ms cubic-bezier(0.34, 1.61, 0.7, 1);
		  -moz-transition: all 300ms cubic-bezier(0.34, 1.61, 0.7, 1);
		  -o-transition: all 300ms cubic-bezier(0.34, 1.61, 0.7, 1);
		  -ms-transition: all 300ms cubic-bezier(0.34, 1.61, 0.7, 1);
		  transition: all 300ms cubic-bezier(0.34, 1.61, 0.7, 1);
		  -webkit-box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 3px 1px -2px rgba(0, 0, 0, 0.2), 0 1px 5px 0 rgba(0, 0, 0, 0.12);
		  -moz-box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 3px 1px -2px rgba(0, 0, 0, 0.2), 0 1px 5px 0 rgba(0, 0, 0, 0.12);
		  box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 3px 1px -2px rgba(0, 0, 0, 0.2), 0 1px 5px 0 rgba(0, 0, 0, 0.12);
		}
		.nav-cart .nav-cart-content .widget li:hover img {
		  transform: translateY(-3px);
		  -webkit-box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 3px 6px -2px rgba(0, 0, 0, 0.2), 0 4px 5px 0 rgba(0, 0, 0, 0.12);
		  -moz-box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 3px 6px -2px rgba(0, 0, 0, 0.2), 0 4px 5px 0 rgba(0, 0, 0, 0.12);
		  box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 3px 6px -2px rgba(0, 0, 0, 0.2), 0 4px 5px 0 rgba(0, 0, 0, 0.12);
		}
		.nav-cart .nav-cart-content .widget .blockUI.blockOverlay {
		  background-color: white !important;
		  opacity: 0.9;
		}
		.nav-cart .nav-cart-content .widget .blockUI.blockOverlay:before {
		  display: none;
		}
		.nav-cart .nav-cart-content .widget a,
		.nav-cart .nav-cart-content .widget p,
		.nav-cart .nav-cart-content .widget .quantity {
		  color: #555;
		}
		.nav-cart .nav-cart-content .widget p,
		.nav-cart .nav-cart-content .widget .quantity {
		  margin: 0;
		}
		.nav-cart .nav-cart-content .widget .quantity {
		  display: block;
		  text-align: left;
		  padding-left: 70px;
		}
		.nav-cart .nav-cart-content .widget .total {
		  border-top: 1px solid #ddd;
		  margin-top: 15px;
		  padding-top: 10px;
		}
		.nav-cart .nav-cart-content .widget .total strong {
		  margin-right: 5px;
		}
		.nav-cart .nav-cart-content .widget .total .amount {
		  float: none;
		}
		.nav-cart .nav-cart-content .widget .buttons .button {
		  display: block;
		  margin: 15px 0 0;
		  text-align: center;
		  padding: 12px 30px;
		  border-radius: 3px;
		  color: #fff;
		}
		.nav-cart .nav-cart-content .widget .buttons .button.checkout {
		  display: none;
		}
		.nav-cart .nav-cart-content ul li {
		  display: block;
		  margin-top: 15px;
		  padding-bottom: 0;
		}
		.nav-cart .nav-cart-content ul li:first-child {
		  margin-top: 0;
		}
		.nav-cart .nav-cart-content ul li a:not(.remove) {
		  margin: 0;
		  text-align: left;
		  padding-left: 70px;
		}
		.nav-cart .nav-cart-content ul li img {
		  float: left;
		  width: 50px;
		  margin-left: 0;
		  margin-right: 15px;
		}
		.nav-cart:hover .nav-cart-content, .nav-cart.hestia-anim-cart .nav-cart-content {
		  opacity: 1;
		  visibility: visible;
		  transform: translateY(0);
		  z-index: 9999;
		}
		
		.navbar-transparent .nav-cart:not(.responsive-nav-cart) .nav-cart-icon {
		  color: #fff;
		}
		
		.navbar.full-screen-menu .nav-cart {
		  padding-left: 0;
		}
		
		.nav-cart.responsive-nav-cart .nav-cart-icon {
		  display: table;
		}
		.nav-cart.responsive-nav-cart .nav-cart-icon i {
		  font-size: 22px;
		}
		.nav-cart.responsive-nav-cart span {
		  position: relative;
		  top: 5px;
		  left: 0;
		  font-size: 10px;
		  min-width: 14px;
		  text-align: center;
		}
		
		.responsive-nav-cart {
		  display: none;
		}
		
		li.nav-cart a.nav-cart-icon > i {
		  font-size: 18px;
		}
		li.nav-cart a.nav-cart-icon span {
		  font-size: 9px;
		  line-height: 1;
		}
		li.nav-cart .nav-cart-content .widget li a:not(.remove) {
		  line-height: normal;
		  font-weight: 400;
		}
		li.nav-cart .nav-cart-content .widget .total {
		  line-height: 1;
		}
		li.nav-cart .nav-cart-content .widget .buttons .button {
		  font-size: 12px;
		  font-weight: 400;
		}';

		if ( is_rtl() ) {
			$css = '
			.nav-cart {
			    position: relative;
			    vertical-align: middle;
			    display: block;
			}
			.nav-cart .woocommerce-mini-cart {
			    max-height: 265px;
			    width: 400px;
			    overflow-y: scroll;
			    overflow-x: hidden;
			}
			.nav-cart .widget_shopping_cart_content {
			    overflow: hidden;
			}
			.nav-cart .nav-cart-icon {
			    position: relative;
			}
			.nav-cart .nav-cart-icon i,
			.nav-cart .nav-cart-icon span {
			    display: table-cell;
			}
			.nav-cart .nav-cart-icon span {
			    background: #fff;
			    border: 1px solid #ddd;
			    border-radius: 3px;
			    color: #555;
			    display: inline-block;
			    padding: 1px 3px;
			    position: absolute;
			    top: 24px;
			    right: 28px;
			}
			.nav-cart .nav-cart-content {
			    display: inline-block;
			    opacity: 0;
			    position: absolute;
			    left: 0;
			    top: 100%;
			    visibility: hidden;
			    transform: translateY(-10px);
			}
			.nav-cart .nav-cart-content .woocommerce-mini-cart__empty-message {
			    white-space: nowrap;
			    text-align: center;
			}
			.nav-cart .nav-cart-content .widget {
			    background: #fff;
			    border-radius: 0 0 6px 6px;
			    margin: 0;
			    padding: 15px;
			    max-width: 350px;
			    -webkit-box-shadow: 0 10px 20px -12px rgba(0, 0, 0, 0.42), 0 12px 20px 0 rgba(0, 0, 0, 0.12), 0 8px 10px -5px rgba(0, 0, 0, 0.2);
			    box-shadow: 0 10px 20px -12px rgba(0, 0, 0, 0.42), 0 12px 20px 0 rgba(0, 0, 0, 0.12), 0 8px 10px -5px rgba(0, 0, 0, 0.2);
			}
			.nav-cart .nav-cart-content .widget li {
			    width: 320px;
			    padding-top: 0;
			    padding-bottom: 25px;
			    padding-left: 0;
			    padding-right: 2em;
			}
			.nav-cart .nav-cart-content .widget li .variation {
			    padding-top: 5px;
			    padding-bottom: 0;
			    padding-left: 0;
			    padding-right: 70px;
			}
			.nav-cart .nav-cart-content .widget li img {
			    position: absolute;
			    right: 30px;
			    border-radius: 6px;
			    float: right;
			    width: 50px;
			    margin-right: 0;
			    margin-left: 15px;
			    -webkit-transition: all 300ms cubic-bezier(0.34, 1.61, 0.7, 1);
			    -moz-transition: all 300ms cubic-bezier(0.34, 1.61, 0.7, 1);
			    -o-transition: all 300ms cubic-bezier(0.34, 1.61, 0.7, 1);
			    -ms-transition: all 300ms cubic-bezier(0.34, 1.61, 0.7, 1);
			    transition: all 300ms cubic-bezier(0.34, 1.61, 0.7, 1);
			    -webkit-box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 3px 1px -2px rgba(0, 0, 0, 0.2), 0 1px 5px 0 rgba(0, 0, 0, 0.12);
			    -moz-box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 3px 1px -2px rgba(0, 0, 0, 0.2), 0 1px 5px 0 rgba(0, 0, 0, 0.12);
			    box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 3px 1px -2px rgba(0, 0, 0, 0.2), 0 1px 5px 0 rgba(0, 0, 0, 0.12);
			}
			.nav-cart .nav-cart-content .widget li:hover img {
			    transform: translateY(-3px);
			    -webkit-box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 3px 6px -2px rgba(0, 0, 0, 0.2), 0 4px 5px 0 rgba(0, 0, 0, 0.12);
			    -moz-box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 3px 6px -2px rgba(0, 0, 0, 0.2), 0 4px 5px 0 rgba(0, 0, 0, 0.12);
			    box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 3px 6px -2px rgba(0, 0, 0, 0.2), 0 4px 5px 0 rgba(0, 0, 0, 0.12);
			}
			.nav-cart .nav-cart-content .widget .blockUI.blockOverlay {
			    background-color: white !important;
			    opacity: 0.9;
			}
			.nav-cart .nav-cart-content .widget .blockUI.blockOverlay:before {
			    display: none;
			}
			.nav-cart .nav-cart-content .widget a,
			.nav-cart .nav-cart-content .widget p,
			.nav-cart .nav-cart-content .widget .quantity {
			    color: #555;
			}
			.nav-cart .nav-cart-content .widget p,
			.nav-cart .nav-cart-content .widget .quantity {
			    margin: 0;
			}
			.nav-cart .nav-cart-content .widget .quantity {
			    display: block;
			    text-align: right;
			    padding-right: 70px;
			}
			.nav-cart .nav-cart-content .widget .total {
			    border-top: 1px solid #ddd;
			    margin-top: 15px;
			    padding-top: 10px;
			}
			.nav-cart .nav-cart-content .widget .total strong {
			    margin-left: 5px;
			}
			.nav-cart .nav-cart-content .widget .total .amount {
			    float: none;
			}
			.nav-cart .nav-cart-content .widget .buttons .button {
			    display: block;
			    margin: 15px 0 0;
			    text-align: center;
			    padding: 12px 30px;
			    border-radius: 3px;
			    color: #fff;
			}
			.nav-cart .nav-cart-content .widget .buttons .button.checkout {
			    display: none;
			}
			.nav-cart .nav-cart-content ul li {
			    display: block;
			    margin-top: 15px;
			    padding-bottom: 0;
			}
			.nav-cart .nav-cart-content ul li:first-child {
			    margin-top: 0;
			}
			.nav-cart .nav-cart-content ul li a:not(.remove) {
			    margin: 0;
			    text-align: right;
			    padding-right: 70px;
			}
			.nav-cart .nav-cart-content ul li img {
			    float: right;
			    width: 50px;
			    margin-right: 0;
			    margin-left: 15px;
			}
			.nav-cart:hover .nav-cart-content, .nav-cart.hestia-anim-cart .nav-cart-content {
			    opacity: 1;
			    visibility: visible;
			    transform: translateY(0);
			    z-index: 9999;
			}
			
			.navbar-transparent .nav-cart:not(.responsive-nav-cart) .nav-cart-icon {
			    color: #fff;
			}
			
			.navbar.full-screen-menu .nav-cart {
			    padding-right: 0;
			}
			
			.nav-cart.responsive-nav-cart .nav-cart-icon {
			    display: table;
			}
			.nav-cart.responsive-nav-cart .nav-cart-icon i {
			    font-size: 22px;
			}
			.nav-cart.responsive-nav-cart span {
			    position: relative;
			    top: 5px;
			    right: 0;
			    font-size: 10px;
			    min-width: 14px;
			    text-align: center;
			}
			
			.responsive-nav-cart {
			    display: none;
			}
			
			li.nav-cart a.nav-cart-icon > i {
			    font-size: 18px;
			}
			li.nav-cart a.nav-cart-icon span {
			    font-size: 9px;
			    line-height: 1;
			}
			li.nav-cart .nav-cart-content .widget li a:not(.remove) {
			    line-height: normal;
			    font-weight: 400;
			}
			li.nav-cart .nav-cart-content .widget .total {
			    line-height: 1;
			}
			li.nav-cart .nav-cart-content .widget .buttons .button {
			    font-size: 12px;
			    font-weight: 400;
			}';
		}

		$this->add_css( $css );
		return true;
	}

	/**
	 * Load WooCommerce Germanized styling.
	 *
	 * @return bool
	 */
	private function load_woocommerce_germanized() {
		if ( ! class_exists( 'WooCommerce_Germanized' ) ) {
			return false;
		}

		$css = '
		.woocommerce .product .wc-gzd-additional-info {
		  display: block;
		  float: left;
		  margin: -20px 0 0 !important;
		  text-align: left;
		  width: 100%;
		}
		.woocommerce .product .wc-gzd-additional-info,
		.woocommerce .product .wc-gzd-additional-info a {
		  color: #3c4858;
		}
		.woocommerce .product .wc-gzd-additional-info a:hover {
		  text-decoration: underline;
		}
		.woocommerce .product .wc-gzd-additional-info:first-of-type {
		  margin: -13px 0 0 !important;
		}
		.woocommerce.archive .product .wc-gzd-additional-info {
		  padding: 0 30px 20px;
		}
		.woocommerce.single .product .wc-gzd-additional-info {
		  padding: 0 0 20px;
		}
		.woocommerce.single .products .wc-gzd-additional-info {
		  padding: 0 30px 20px;
		}
		
		.woocommerce-cart .product .wc-gzd-additional-info {
		  padding: 0 30px 20px;
		}
		.woocommerce-cart p.units-info {
		  margin-bottom: 0;
		}
		.woocommerce-cart .wc-gzd-additional-wrapper p {
		  border-top: none;
		  padding: 0;
		}
		
		.woocommerce-checkout .shop_table .wc-gzd-additional-info {
		  text-align: right;
		}
		.woocommerce-checkout #order_review .legal .input-checkbox {
		  margin-top: -2px;
		  margin-bottom: 0;
		  margin-right: 8px; 
		  margin-left: 0;
		}';

		$this->add_css( $css );
		return true;
	}

	/**
	 * Load AOS animations if they are enabled.
	 *
	 * @return bool
	 */
	private function load_aos_animations() {
		$enable_animations = apply_filters( 'hestia_enable_animations', true );
		if ( ! $enable_animations ) {
			return false;
		}

		$css = '
		.aos-init[data-aos] {
		  transition-duration: 1.25s;
		  -webkit-transition-duration: 1.25s;
		  -moz-transition-duration: 1.25s;
		  transition-timing-function: ease-out;
		  -webkit-transition-timing-function: ease-out;
		  -moz-transition-timing-function: ease-out;
		  will-change: transform, opacity;
		}
		.aos-init[data-aos].hestia-table-two {
		  transition-duration: 1s;
		  -webkit-transition-duration: 1s;
		  -moz-transition-duration: 1s;
		}
		.aos-init[data-aos^=fade][data-aos^=fade] {
		  opacity: 0;
		  transition-property: opacity, transform;
		}
		.aos-init[data-aos^=fade][data-aos^=fade].aos-animate {
		  opacity: 1;
		  transform: translate3d(0, 0, 0);
		}
		.aos-init[data-aos=fade-up] {
		  transform: translate3d(0, 35px, 0);
		}
		.aos-init[data-aos=fade-down] {
		  transform: translate3d(0, -35px, 0);
		}
		.aos-init[data-aos=fade-right] {
		  transform: translate3d(-35px, 0, 0);
		}
		.aos-init[data-aos=fade-left] {
		  transform: translate3d(35px, 0, 0);
		}
		';

		$this->add_css( $css );
		return true;
	}
}
