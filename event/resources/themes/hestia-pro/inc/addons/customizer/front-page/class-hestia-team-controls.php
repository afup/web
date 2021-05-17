<?php
/**
 * Team section controls.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Features_Section_Controls
 */
class Hestia_Team_Controls extends Hestia_Front_Page_Section_Controls_Abstract {

	/**
	 * Implement set_section_data from parent.
	 * Add section details
	 *
	 * @return array
	 */
	protected function set_section_data() {
		return array(
			'slug'     => 'team',
			'title'    => esc_html__( 'Team', 'hestia-pro' ),
			'priority' => 30,
		);
	}

	/**
	 * Implement change_controls from parent.
	 *
	 * @return void
	 */
	public function change_controls() {
		$this->change_customizer_object( 'setting', 'hestia_team_title', 'default', esc_html__( 'Meet our team', 'hestia-pro' ) );
		$this->change_customizer_object( 'setting', 'hestia_team_subtitle', 'default', esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'hestia-pro' ) );
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
				'hestia_team_content',
				array(
					'sanitize_callback' => 'hestia_repeater_sanitize',
					'transport'         => $this->selective_refresh,
					'default'           => Hestia_Defaults_Models::instance()->get_team_default(),
				),
				array(
					'label'                                => esc_html__( 'Team Content', 'hestia-pro' ),
					'section'                              => 'hestia_team',
					'priority'                             => 15,
					'item_name'                            => esc_html__( 'Team Member', 'hestia-pro' ),
					'customizer_repeater_image_control'    => true,
					'customizer_repeater_title_control'    => true,
					'customizer_repeater_subtitle_control' => true,
					'customizer_repeater_text_control'     => true,
					'customizer_repeater_link_control'     => true,
					'customizer_repeater_repeater_control' => true,
				),
				'Hestia_Repeater',
				array(
					'selector'            => '.hestia-team-content',
					'settings'            => 'hestia_team_content',
					'render_callback'     => array( $this, 'team_content_callback' ),
					'container_inclusive' => true,
				)
			)
		);
	}

	/**
	 * Render callback function
	 */
	public function team_content_callback() {
		$team_section        = new Hestia_Team_Section();
		$hestia_team_content = get_theme_mod( 'hestia_team_content' );
		$team_section->team_content( $hestia_team_content );
	}
}
