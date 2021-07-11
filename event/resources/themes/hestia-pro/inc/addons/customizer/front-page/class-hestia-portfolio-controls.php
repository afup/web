<?php
/**
 * Portfolio section controls.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Portfolio_Controls
 */
class Hestia_Portfolio_Controls extends Hestia_Front_Page_Section_Controls_Abstract {

	/**
	 * Initialize portfolio controls.
	 */
	public function init() {
		if ( ! class_exists( 'Jetpack', false ) ) {
			return;
		}

		if ( ! Jetpack::is_module_active( 'custom-content-types' ) ) {
			return;
		}

		parent::init();
	}

	/**
	 * Implement set_section_data from parent.
	 * Add section details
	 *
	 * @return array
	 */
	protected function set_section_data() {
		return array(
			'slug'     => 'portfolio',
			'title'    => esc_html__( 'Portfolio', 'hestia-pro' ),
			'priority' => 25,
		);
	}

	/**
	 * Add controls.
	 */
	public function add_controls() {
		$this->add_items_number_control();
		$this->add_box_type_control();
		$this->add_lightbox_toggle();
	}

	/**
	 * Add items number control.
	 */
	private function add_items_number_control() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_portfolio_items',
				array(
					'default'           => 3,
					'sanitize_callback' => 'absint',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'    => esc_html__( 'Number of Items', 'hestia-pro' ),
					'section'  => 'hestia_portfolio',
					'priority' => 15,
					'type'     => 'number',
				),
				null,
				array(
					'selector'            => '.hestia-portfolio-content',
					'settings'            => 'hestia_portfolio_items',
					'container_inclusive' => true,
					'render_callback'     => array( $this, 'portfolio_content_callback' ),
				)
			)
		);
	}

	/**
	 * Add box type control.
	 */
	private function add_box_type_control() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_portfolio_boxes_type',
				array(
					'default'           => false,
					'sanitize_callback' => 'hestia_sanitize_checkbox',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'       => esc_html__( 'Enable big boxes?', 'hestia-pro' ),
					'description' => esc_html__( 'You must have more then 3 portfolio items displayed in this section in order to see the difference.', 'hestia-pro' ),
					'section'     => 'hestia_portfolio',
					'priority'    => 20,
					'type'        => 'checkbox',
				),
				null,
				array(
					'selector'        => '.hestia-portfolio-content',
					'render_callback' => array( $this, 'portfolio_content_callback' ),
				)
			)
		);
	}

	/**
	 * Add lightbox toggle.
	 */
	private function add_lightbox_toggle() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_enable_portfolio_lightbox',
				array(
					'default'           => false,
					'sanitize_callback' => 'hestia_sanitize_checkbox',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'    => esc_html__( 'Open in Lightbox', 'hestia-pro' ),
					'section'  => 'hestia_portfolio',
					'priority' => 25,
					'type'     => 'checkbox',
				)
			)
		);
	}

	/**
	 * Render callback function
	 */
	public function portfolio_content_callback() {
		$portfolio_section = new Hestia_Portfolio_Section();
		$portfolio_items   = get_theme_mod( 'hestia_portfolio_items', 3 );
		$portfolio_section->portfolio_content( $portfolio_items );
	}

	/**
	 * Change necessary controls.
	 *
	 * @return void
	 */
	public function change_controls() {
		$this->change_customizer_object( 'setting', 'hestia_portfolio_title', 'default', esc_html__( 'Portfolio', 'hestia-pro' ) );
		$this->change_customizer_object( 'setting', 'hestia_portfolio_subtitle', 'default', esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'hestia-pro' ) );
	}

}
