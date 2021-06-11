<?php
/**
 * Testimonials Section
 *
 * @package Hestia
 */

/**
 * Class Hestia_Testimonials_Section
 */
class Hestia_Testimonials_Controls extends Hestia_Front_Page_Section_Controls_Abstract {

	/**
	 * Implement set_section_data from parent.
	 * Add section details
	 *
	 * @return array
	 */
	protected function set_section_data() {
		return array(
			'slug'     => 'testimonials',
			'title'    => esc_html__( 'Testimonials', 'hestia-pro' ),
			'priority' => 45,
		);
	}

	/**
	 * Implement change_controls from parent.
	 *
	 * @return void
	 */
	public function change_controls() {
		$this->change_customizer_object( 'setting', 'hestia_testimonials_title', 'default', esc_html__( 'What clients say', 'hestia-pro' ) );
		$this->change_customizer_object( 'setting', 'hestia_testimonials_subtitle', 'default', esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'hestia-pro' ) );
	}

	/**
	 * Add controls.
	 */
	public function add_controls() {
		$this->add_content_control();
	}

	/**
	 * Add controls that are specific for this section.
	 */
	private function add_content_control() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_testimonials_content',
				array(
					'sanitize_callback' => 'hestia_repeater_sanitize',
					'transport'         => $this->selective_refresh,
					'default'           => Hestia_Defaults_Models::instance()->get_testimonials_default(),
				),
				array(
					'label'                                => esc_html__( 'Testimonials Content', 'hestia-pro' ),
					'section'                              => 'hestia_testimonials',
					'priority'                             => 15,
					'item_name'                            => esc_html__( 'Testimonial', 'hestia-pro' ),
					'customizer_repeater_image_control'    => true,
					'customizer_repeater_title_control'    => true,
					'customizer_repeater_subtitle_control' => true,
					'customizer_repeater_text_control'     => true,
					'customizer_repeater_link_control'     => true,
				),
				'Hestia_Repeater',
				array(
					'selector'            => '.hestia-testimonials-content',
					'settings'            => 'hestia_testimonials_content',
					'render_callback'     => array( $this, 'testimonials_content_callback' ),
					'container_inclusive' => true,
				)
			)
		);
	}

	/**
	 * Render callback function
	 */
	public function testimonials_content_callback() {
		$team_section                = new Hestia_Testimonials_Section();
		$hestia_testimonials_content = get_theme_mod( 'hestia_testimonials_content' );
		$team_section->testimonials_content( $hestia_testimonials_content );
	}
}
