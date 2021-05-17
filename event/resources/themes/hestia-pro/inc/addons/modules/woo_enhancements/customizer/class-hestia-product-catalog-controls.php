<?php
/**
 * Customizer controls for Product Catalog page.
 *
 * @package Inc/Addons/Modules/Woo_Enhancements/Customizer
 */
/**
 * Class Hestia_Product_Catalog_Controls
 */
class Hestia_Product_Catalog_Controls extends Hestia_Register_Customizer_Controls {

	/**
	 * Add customizer controls for Single product page.
	 *
	 * @return void
	 */
	public function add_controls() {
		$this->add_product_catalog_section();
		$this->add_product_catalog_controls();
	}

	/**
	 * Add Shop settings section
	 */
	private function add_product_catalog_section() {
		$this->add_section(
			new Hestia_Customizer_Section(
				'hestia_shop_settings',
				array(
					'title'    => apply_filters( 'hestia_shop_settings_control_label', esc_html__( 'Shop', 'hestia-pro' ) ),
					'priority' => 45,
					'panel'    => 'hestia_shop_settings',
				)
			)
		);
	}

	/**
	 * Add Shop settings controls
	 */
	private function add_product_catalog_controls() {

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_product_card_label',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'label'    => esc_html__( 'Product card', 'hestia-pro' ),
					'section'  => 'hestia_shop_settings',
					'priority' => 10,
				),
				'Hestia_Customizer_Heading'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_shop_hide_categories',
				array(
					'sanitize_callback' => 'hestia_sanitize_checkbox',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'    => esc_html__( 'Hide categories', 'hestia-pro' ),
					'section'  => 'hestia_shop_settings',
					'priority' => 20,
					'type'     => 'checkbox',
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_product_style',
				array(
					'default'           => 'boxed',
					'sanitize_callback' => array( $this, 'sanitize_shop_settings_control' ),
				),
				array(
					'label'    => esc_html__( 'Style', 'hestia-pro' ),
					'section'  => 'hestia_shop_settings',
					'priority' => 30,
					'choices'  => array(
						'boxed' => array(
							'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqCAMAAABpj1iyAAAAG1BMVEUAyv+fn5+3t7fDw8PV1dXf39/n5+f39/f///9JXZIaAAAA0UlEQVR4Ae3bsarDQAxE0Y0djfb/vzhF0gqPEAQb7u3ncR5s1HntWwYLFqxvuZxyD/ddlpaT9nDfZcVyij3e+6xUxLmczgjlfO+x1PpvNd97rGj92RjvYcGCBQsWLFiwYMGCBQsWLFiwYMGCBQsWLFhFsGDBgqXjdd2hktXfW6xUXKcsWf29xfKL+R5WFSxYb6ea5e9hPZzFL5EDwZOHxYGAxZOHxYH4LwsWrPXLYBn7wYFosuo9LFh3fPJ+3C3tTprv7c+P/JTzPR8C+sGCBesDvVBbUhWXtrMAAAAASUVORK5CYII=',
						),
						'plain' => array(
							'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqCAMAAABpj1iyAAAACVBMVEUAyv/V1dX////o4eoDAAAAfklEQVR4Ae3VMQqAAAwDwOr/H62D4OAU6lDKZRQiN9RY58hgYWHdD7N0+1hYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWAOChZUFC+vI0u0vYGHt+BKxnDyWgcAyEFgGwkBgYWFh1ZNf+vnPJ3vtt4+FNffk39gtLCysC43dUGnuqLwbAAAAAElFTkSuQmCC',
						),
					),
				),
				'Hestia_Customize_Control_Radio_Image'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_card_image_label',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'label'    => esc_html__( 'Card image', 'hestia-pro' ),
					'section'  => 'hestia_shop_settings',
					'priority' => 40,
				),
				'Hestia_Customizer_Heading'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_product_hover_style',
				array(
					'sanitize_callback' => array( $this, 'sanitize_image_style' ),
					'default'           => 'pop-and-glow',
				),
				array(
					'section'  => 'hestia_shop_settings',
					'label'    => esc_html__( 'Image style', 'hestia-pro' ),
					'type'     => 'select',
					'choices'  => array(
						'none'         => esc_html__( 'None', 'hestia-pro' ),
						'pop-and-glow' => esc_html__( 'Pop', 'hestia-pro' ),
						'zoom'         => esc_html__( 'Zoom', 'hestia-pro' ),
						'swap-images'  => esc_html__( 'Swipe Next Image', 'hestia-pro' ),
						'blur'         => esc_html__( 'Blur', 'hestia-pro' ),
						'fadein'       => esc_html__( 'Fade In', 'hestia-pro' ),
						'fadeout'      => esc_html__( 'Fade Out', 'hestia-pro' ),
						'glow'         => esc_html__( 'Glow', 'hestia-pro' ),
						'colorize'     => esc_html__( 'Colorize', 'hestia-pro' ),
						'grayscale'    => esc_html__( 'Grayscale', 'hestia-pro' ),
					),
					'priority' => 50,
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_product_catalog_pagination_label',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'label'    => esc_html__( 'Pagination', 'hestia-pro' ),
					'section'  => 'hestia_shop_settings',
					'priority' => 60,
				),
				'Hestia_Customizer_Heading'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_shop_pagination_type',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'default'           => 'number',
				),
				array(
					'type'     => 'select',
					'section'  => 'hestia_shop_settings',
					'label'    => esc_html__( 'Type', 'hestia-pro' ),
					'choices'  => array(
						'number'   => esc_html__( 'Number', 'hestia-pro' ),
						'infinite' => esc_html__( 'Infinite Scroll', 'hestia-pro' ),
					),
					'priority' => 70,
				)
			)
		);
	}

	/**
	 * Sanitize Shop Layout control.
	 *
	 * @param string $value Control output.
	 *
	 * @return string
	 */
	public function sanitize_shop_settings_control( $value ) {
		$value        = sanitize_text_field( $value );
		$valid_values = array(
			'boxed',
			'plain',
		);

		if ( ! in_array( $value, $valid_values, true ) ) {
			wp_die( 'Invalid value, go back and try again.' );
		}

		return $value;
	}

	/**
	 * Sanitize image style function.
	 *
	 * @param string $image_style Image style sanitization.
	 *
	 * @return string
	 */
	public function sanitize_image_style( $image_style ) {
		$choices = array(
			'none',
			'pop-and-glow',
			'zoom',
			'swap-images',
			'blur',
			'fadein',
			'fadeout',
			'glow',
			'colorize',
			'grayscale',
		);
		if ( ! in_array( $image_style, $choices, true ) ) {
			return 'none';
		}
		return $image_style;
	}
}
