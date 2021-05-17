<?php
/**
 * Features section controls.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Features_Section_Controls
 */
class Hestia_Features_Controls extends Hestia_Front_Page_Section_Controls_Abstract {

	/**
	 * Implement set_section_data from parent.
	 * Add section details
	 *
	 * @return array
	 */
	protected function set_section_data() {
		return array(
			'slug'     => 'features',
			'title'    => esc_html__( 'Features', 'hestia-pro' ),
			'priority' => 10,
		);
	}

	/**
	 * Add controls.
	 */
	public function add_controls() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_features_content',
				array(
					'sanitize_callback' => 'hestia_repeater_sanitize',
					'transport'         => $this->selective_refresh,
					'default'           => Hestia_Defaults_Models::instance()->get_features_default(),
				),
				array(
					'label'                             => esc_html__( 'Features Content', 'hestia-pro' ),
					'section'                           => 'hestia_features',
					'priority'                          => 15,
					'item_name'                         => esc_html__( 'Feature', 'hestia-pro' ),
					'customizer_repeater_icon_control'  => true,
					'customizer_repeater_image_control' => true,
					'customizer_repeater_title_control' => true,
					'customizer_repeater_text_control'  => true,
					'customizer_repeater_link_control'  => true,
					'customizer_repeater_color_control' => true,
				),
				'Hestia_Repeater',
				array(
					'selector'        => '.hestia-features-content',
					'settings'        => 'hestia_features_content',
					'render_callback' => array( $this, 'features_content_callback' ),
				)
			)
		);
	}

	/**
	 * Render callback function
	 */
	public function features_content_callback() {
		$features_section = new Hestia_Features_Section();
		$features_content = get_theme_mod( 'hestia_features_content' );
		$features_section->features_content( $features_content, true );
	}

	/**
	 * Change controls related to features section.
	 *
	 * @return void
	 */
	public function change_controls() {
		$this->change_customizer_object( 'setting', 'hestia_features_title', 'default', esc_html__( 'Why our product is the best', 'hestia-pro' ) );
		$this->change_customizer_object( 'setting', 'hestia_features_subtitle', 'default', esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'hestia-pro' ) );
	}

}
