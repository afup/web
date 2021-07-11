<?php
/**
 * Customizer controls for Cart page.
 *
 * @package Inc/Addons/Modules/Woo_Enhancements/Customizer
 */
/**
 * Class Hestia_Woo_Product_Controls
 */
class Hestia_Cart_Controls extends Hestia_Register_Customizer_Controls {

	/**
	 * Init function.
	 */
	public function init() {
		parent::init();
		add_filter( 'repeater_input_labels_filter', array( $this, 'image_type_repeater_label' ), 10, 3 );
	}

	/**
	 * Filter to modify input label in repeater control
	 * You can filter by control id and input name.
	 *
	 * @param string $string  Input label.
	 * @param string $id      Input id.
	 * @param string $control Control name.
	 *
	 * @return string
	 */
	public function image_type_repeater_label( $string, $id, $control ) {

		if ( $id !== 'hestia_payment_icons' ) {
			return $string;
		}

		if ( $control === 'customizer_repeater_choice_control' ) {
			return esc_html__( 'Type', 'hestia-pro' );
		}

		return $string;
	}

	/**
	 * Add customizer controls.
	 */
	public function add_controls() {
		$this->add_cart_sections();
		$this->add_cart_settings_controls();
	}

	/**
	 * Add cart section and payment icons section
	 */
	private function add_cart_sections() {
		$this->add_section(
			new Hestia_Customizer_Section(
				'hestia_payment_icon_builder',
				array(
					'title'    => apply_filters( 'hestia_payment_icon_settings_control_label', esc_html__( 'Payment icons', 'hestia-pro' ) ),
					'priority' => 47,
					'panel'    => 'hestia_shop_settings',
				)
			)
		);

		$this->add_section(
			new Hestia_Customizer_Section(
				'hestia_cart',
				array(
					'title'    => apply_filters( 'hestia_cart_settings_control_label', esc_html__( 'Cart', 'hestia-pro' ) ),
					'priority' => 48,
					'panel'    => 'hestia_shop_settings',
				)
			)
		);
	}

	/**
	 * Cart settings controls
	 */
	private function add_cart_settings_controls() {

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_enable_cross_sell_products',
				array(
					'sanitize_callback' => 'hestia_sanitize_checkbox',
					'default'           => true,
				),
				array(
					'type'     => 'checkbox',
					'label'    => esc_html__( 'Enable Cross-Sell Products', 'hestia-pro' ),
					'section'  => 'hestia_cart',
					'priority' => 10,
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_enable_payment_icons',
				array(
					'sanitize_callback' => 'hestia_sanitize_checkbox',
					'default'           => false,
				),
				array(
					'type'        => 'checkbox',
					'label'       => esc_html__( 'Enable payment icons', 'hestia-pro' ),
					'description' => sprintf(
						/* translators: %s is link to section */
						esc_html__( 'Click %s to edit payment icons', 'hestia-pro' ),
						sprintf(
							/* translators: %s is link label */
							'<span class="quick-links"><a href="#" data-control-focus="hestia_payment_icons">%s</a></span>',
							esc_html__( 'here', 'hestia-pro' )
						)
					),
					'section'     => 'hestia_cart',
					'priority'    => 20,
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_payment_icons',
				array(
					'sanitize_callback' => 'hestia_repeater_sanitize',
					'transport'         => $this->selective_refresh,
					'default'           => Hestia_Defaults_Models::instance()->get_payment_icons_defaults(),
				),
				array(
					'label'                             => esc_html__( 'Payment Icons', 'hestia-pro' ),
					'section'                           => 'hestia_payment_icon_builder',
					'priority'                          => 10,
					'item_name'                         => esc_html__( 'Payment Icon', 'hestia-pro' ),
					'customizer_repeater_icon_control'  => true,
					'customizer_repeater_image_control' => true,
					'dropdown_icons'                    => $this->get_payment_dropdown_icons(),
				),
				'Hestia_Repeater',
				array(
					'selector'        => '.hestia-payment-icons-wrapper',
					'settings'        => 'hestia_payment_icons',
					'render_callback' => array( 'Hestia_Cart_View', 'render_payment_icons' ),
				)
			)
		);
	}

	/**
	 * Icons to show in dropdown for the hestia_payment_icons control.
	 */
	private function get_payment_dropdown_icons() {
		return '<i data-type="iconpicker-item" title=".fa-behance-square" class="fab fa-behance-square"></i>
		<i data-type="iconpicker-item" title=".fa-alipay" class="fab fa-alipay"></i>
		<i data-type="iconpicker-item" title=".fa-amazon-pay" class="fab fa-amazon-pay"></i>
		<i data-type="iconpicker-item" title=".fa-apple-pay" class="fab fa-apple-pay"></i>
		<i data-type="iconpicker-item" title=".fa-bitcoin" class="fab fa-bitcoin"></i>
		<i data-type="iconpicker-item" title=".fa-cc-amazon-pay" class="fab fa-cc-amazon-pay"></i>
		<i data-type="iconpicker-item" title=".fa-cc-amex" class="fab fa-cc-amex"></i>
		<i data-type="iconpicker-item" title=".fa-cc-apple-pay" class="fab fa-cc-apple-pay"></i>
		<i data-type="iconpicker-item" title=".fa-cc-diners-club" class="fab fa-cc-diners-club"></i>
		<i data-type="iconpicker-item" title=".fa-cc-discover" class="fab fa-cc-discover"></i>
		<i data-type="iconpicker-item" title=".fa-cc-jcb" class="fab fa-cc-jcb"></i>
		<i data-type="iconpicker-item" title=".fa-cc-mastercard" class="fab fa-cc-mastercard"></i>
		<i data-type="iconpicker-item" title=".fa-cc-paypal" class="fab fa-cc-paypal"></i>
		<i data-type="iconpicker-item" title=".fa-cc-stripe" class="fab fa-cc-stripe"></i>
		<i data-type="iconpicker-item" title=".fa-cc-visa" class="fab fa-cc-visa"></i>
		<i data-type="iconpicker-item" title=".fa-credit-card" class="fas fa-credit-card"></i>
		<i data-type="iconpicker-item" title=".fa-credit-card" class="far fa-credit-card"></i>
		<i data-type="iconpicker-item" title=".fa-google-wallet" class="fab fa-google-wallet"></i>
		<i data-type="iconpicker-item" title=".fa-paypal" class="fab fa-paypal"></i>
		<i data-type="iconpicker-item" title=".fa-stripe" class="fab fa-stripe"></i>
		<i data-type="iconpicker-item" title=".fa-stripe-s" class="fab fa-stripe-s"></i>';
	}
}
