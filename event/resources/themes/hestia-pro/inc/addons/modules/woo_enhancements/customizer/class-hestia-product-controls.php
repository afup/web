<?php
/**
 * Customizer controls for Single Product page.
 *
 * @package Inc/Addons/Modules/Woo_Enhancements/Customizer
 */
/**
 * Class Hestia_Woo_Product_Controls
 */
class Hestia_Product_Controls extends Hestia_Register_Customizer_Controls {

	/**
	 * Add customizer controls for Single product page.
	 *
	 * @return void
	 */
	public function add_controls() {
		$this->add_product_page_section();
		$this->add_general_controls();
		$this->add_related_products_controls();
		$this->add_exclusive_products_controls();
	}

	/**
	 * Add products customizer section.
	 *
	 * @return void
	 */
	private function add_product_page_section() {
		$this->add_section(
			new Hestia_Customizer_Section(
				'hestia_product_page',
				array(
					'title'    => __( 'Single Product', 'hestia-pro' ),
					'priority' => 60,
					'panel'    => 'hestia_shop_settings',
				)
			)
		);
	}

	/**
	 * Add product general controls.
	 *
	 * @return void
	 */
	private function add_general_controls() {

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_product_general_label',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'label'    => esc_html__( 'General', 'hestia-pro' ),
					'section'  => 'hestia_product_page',
					'priority' => 10,
				),
				'Hestia_Customizer_Heading'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_enable_seamless_add_to_cart',
				array(
					'sanitize_callback' => 'hestia_sanitize_checkbox',
					'default'           => false,
				),
				array(
					'label'    => esc_html__( 'Enable seamless add to cart', 'hestia-pro' ),
					'section'  => 'hestia_product_page',
					'priority' => 20,
					'type'     => 'checkbox',
				)
			)
		);
	}

	/**
	 * Add related products customizer controls.
	 *
	 * @return void
	 */
	private function add_related_products_controls() {

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_related_products_label',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'label'    => esc_html__( 'Related products', 'hestia-pro' ),
					'section'  => 'hestia_product_page',
					'priority' => 30,
				),
				'Hestia_Customizer_Heading'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_hide_related_products',
				array(
					'default'           => false,
					'sanitize_callback' => 'hestia_sanitize_checkbox',
				),
				array(
					'label'    => esc_html__( 'Hide related products', 'hestia-pro' ),
					'section'  => 'hestia_product_page',
					'priority' => 40,
					'type'     => 'checkbox',
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_related_products_title',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'default'           => __( 'Related products', 'hestia-pro' ),
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'    => esc_html__( 'Section Title', 'hestia-pro' ),
					'section'  => 'hestia_product_page',
					'priority' => 50,
				),
				null,
				array(
					'selector'        => '.related.products h2',
					'render_callback' => array( $this, 'related_products_title_render_callback' ),
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_related_products_number',
				array(
					'default'           => 4,
					'sanitize_callback' => 'absint',
				),
				array(
					'label'       => esc_html__( 'Number of related products', 'hestia-pro' ),
					'section'     => 'hestia_product_page',
					'priority'    => 60,
					'type'        => 'number',
					'input_attrs' => array(
						'min'  => 1,
						'step' => 1,
					),
				)
			)
		);
	}

	/**
	 * Add exclusive products customizer controls.
	 *
	 * @return void
	 */
	private function add_exclusive_products_controls() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_exclusive_products_label',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'label'    => esc_html__( 'Exclusive products', 'hestia-pro' ),
					'section'  => 'hestia_product_page',
					'priority' => 70,
				),
				'Hestia_Customizer_Heading'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_enable_exclusive_products',
				array(
					'sanitize_callback' => 'hestia_sanitize_checkbox',
					'default'           => false,
				),
				array(
					'label'    => esc_html__( 'Enable exclusive products', 'hestia-pro' ),
					'section'  => 'hestia_product_page',
					'priority' => 80,
					'type'     => 'checkbox',
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_exclusive_products_title',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
					'default'           => __( 'Exclusive products', 'hestia-pro' ),
				),
				array(
					'label'    => esc_html__( 'Section Title', 'hestia-pro' ),
					'section'  => 'hestia_product_page',
					'priority' => 90,
				),
				null,
				array(
					'selector'        => '.exclusive-products h2',
					'render_callback' => array( $this, 'exclusive_products_title_render_callback' ),
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_exclusive_products_categories',
				array(
					'sanitize_callback' => 'hestia_sanitize_array',
					'transport'         => $this->selective_refresh,
				),
				array(
					'section'  => 'hestia_product_page',
					'label'    => esc_html__( 'Categories', 'hestia-pro' ),
					'choices'  => Hestia_Woocommerce_Module::get_products_categories(),
					'priority' => 100,
				),
				'Hestia_Select_Multiple',
				array(
					'selector'            => '.exclusive-products',
					'render_callback'     => array( $this, 'exclusive_products_render_callback' ),
					'container_inclusive' => true,
				)
			)
		);
	}

	/**
	 * Render callback function for Related products section.
	 *
	 * @return mixed
	 */
	public function related_products_title_render_callback() {
		return get_theme_mod( 'hestia_related_products_title', __( 'Related products', 'hestia-pro' ) );
	}

	/**
	 * Render callback function for Exclusive products title.
	 *
	 * @return mixed
	 */
	public function exclusive_products_title_render_callback() {
		return get_theme_mod( 'hestia_exclusive_products_title', __( 'Exclusive products', 'hestia-pro' ) );
	}

	/**
	 * Render callback function for Exclusive products section.
	 *
	 * @return false|string
	 */
	public function exclusive_products_render_callback() {
		ob_start();
		Hestia_Product_View::render_exclusive_products();
		$related_content = ob_get_clean();
		return $related_content;
	}
}
