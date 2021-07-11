<?php
/**
 * Shop Controls Addon
 *
 * @package Hestia
 */

/**
 * Class Hestia_Shop_Controls_Addon
 */
class Hestia_Shop_Controls_Addon extends Hestia_Shop_Controls {
	/**
	 * Add controls.
	 */
	public function add_controls() {
		parent::add_controls();

		$this->add_tabs();
		$this->add_category_control();
		$this->add_order_control();
		$this->add_shortcode_control();
	}

	/**
	 * Add tabs control
	 */
	private function add_tabs() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_shop_tabs',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'section'  => 'hestia_shop',
					'priority' => 1,
					'tabs'     => array(
						'general' => array(
							'label' => esc_html__( 'General Settings', 'hestia-pro' ),
							'icon'  => 'admin-tools',
						),
						'contact' => array(
							'label' => esc_html__( 'Products', 'hestia-pro' ),
							'icon'  => 'star-filled',
						),
					),
					'controls' => array(
						'general' => array(
							'hestia_shop_hide'       => array(),
							'hestia_shop_title'      => array(),
							'hestia_shop_subtitle'   => array(),
							'hestia_shop_items'      => array(),
							'hestia_shop_categories' => array(),
						),
						'contact' => array(
							'hestia_shop_order'     => array(),
							'hestia_shop_shortcode' => array(),
						),
					),
				),
				'Hestia_Customize_Control_Tabs'
			)
		);
	}

	/**
	 * Add category selector control.
	 */
	private function add_category_control() {

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_shop_categories',
				array(
					'sanitize_callback' => 'hestia_sanitize_array',
					'transport'         => $this->selective_refresh,
				),
				array(
					'section'  => 'hestia_shop',
					'label'    => esc_html__( 'Categories:', 'hestia-pro' ),
					'choices'  => $this->get_products_categories(),
					'priority' => 20,
				),
				'Hestia_Select_Multiple',
				array(
					'selector'            => '.hestia-shop-content',
					'render_callback'     => array( $this, 'shop_content_render_callback' ),
					'container_inclusive' => true,
				)
			)
		);
	}

	/**
	 * Add orderiing control.
	 */
	private function add_order_control() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_shop_order',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
					'default'           => 'DESC',
				),
				array(
					'label'    => esc_html__( 'Order', 'hestia-pro' ),
					'section'  => 'hestia_shop',
					'priority' => 25,
					'type'     => 'select',
					'choices'  => array(
						'ASC'  => esc_html__( 'Ascending', 'hestia-pro' ),
						'DESC' => esc_html__( 'Descending', 'hestia-pro' ),
					),
				),
				null,
				array(
					'selector'            => '.hestia-shop-content',
					'render_callback'     => array( $this, 'shop_content_render_callback' ),
					'container_inclusive' => true,
				)
			)
		);
	}

	/**
	 * Add shortcode input.
	 */
	private function add_shortcode_control() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_shop_shortcode',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'    => esc_html__( 'WooCommerce shortcode', 'hestia-pro' ),
					'section'  => 'hestia_shop',
					'priority' => 30,
				),
				null,
				array(
					'selector'            => '.hestia-shop-content',
					'render_callback'     => array( $this, 'shop_content_render_callback' ),
					'container_inclusive' => true,
				)
			)
		);
	}

	/**
	 * Get product categories.
	 *
	 * @return array
	 */
	private function get_products_categories() {

		$categories_array = array();
		$categories       = get_categories(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => 1,
				'title_li'   => '',
			)
		);
		if ( empty( $categories ) ) {
			return array();
		}
		foreach ( $categories as $category ) {
			if ( ! empty( $category->term_id ) && ! empty( $category->name ) ) {
				$categories_array[ $category->term_id ] = $category->name;
			}
		}
		return $categories_array;
	}
}
