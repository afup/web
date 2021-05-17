<?php
/**
 * About controls.
 *
 * @package Hestia
 */

/**
 * Class Hestia_About_Controls
 */
class Hestia_Shop_Controls extends Hestia_Front_Page_Section_Controls_Abstract {

	/**
	 * Set the section data for generating the customizer basic settings
	 *
	 * @return array | null
	 */
	protected function set_section_data() {
		if ( ! class_exists( 'WooCommerce', false ) ) {
			return null;
		}
		return array(
			'slug'     => 'shop',
			'title'    => esc_html__( 'Shop', 'hestia-pro' ),
			'priority' => 20,
		);
	}

	/**
	 * Add controls.
	 */
	public function add_controls() {
		if ( ! class_exists( 'WooCommerce', false ) ) {
			return;
		}
		$this->add_content_controls();
	}

	/**
	 * Add about section content editor control.
	 */
	private function add_content_controls() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_shop_items',
				array(
					'default'           => 4,
					'sanitize_callback' => 'absint',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'    => esc_html__( 'Number of Items', 'hestia-pro' ),
					'section'  => 'hestia_shop',
					'priority' => 15,
					'type'     => 'number',
				),
				null,
				array(
					'selector'            => '.hestia-shop .hestia-shop-content',
					'render_callback'     => array( $this, 'shop_content_render_callback' ),
					'container_inclusive' => true,
				)
			)
		);
	}

	/**
	 * Shop Content render callback
	 */
	public function shop_content_render_callback() {
		$shop_section = new Hestia_Shop_Section();
		$content      = $shop_section->shop_content();

		return $content;
	}

	/**
	 * Change necessary controls.
	 *
	 * @return void
	 */
	public function change_controls() {
		$this->change_customizer_object( 'setting', 'hestia_shop_title', 'default', esc_html__( 'Products', 'hestia-pro' ) );
		$this->change_customizer_object( 'setting', 'hestia_shop_subtitle', 'default', esc_html__( 'Change this subtitle in the Customizer', 'hestia-pro' ) );
	}

}
