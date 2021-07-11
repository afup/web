<?php
/**
 * Hestia_Front_Page_Section_Controls_Abstract
 *
 * @package Hestia
 */

/**
 * Class Hestia_Front_Page_Section_Controls_Abstract
 */
abstract class Hestia_Front_Page_Section_Controls_Abstract extends Hestia_Register_Customizer_Controls {

	/**
	 * Main section data.
	 *
	 * @var array Section data.
	 */
	private $section_data;

	/**
	 * Set variable fields when creating a section.
	 *
	 * @return array
	 */
	abstract protected function set_section_data();

	/**
	 * Initialize the section and common controls.
	 */
	public function init() {
		parent::init();
		$this->section_data = $this->set_section_data();
	}

	/**
	 * Hook to add things between controls.
	 */
	public function after_add_controls() {
		if ( $this->section_data === null ) {
			return;
		}

		$this->register_frontpage_section();
		$this->register_common_controls();
	}

	/**
	 * Register a front page section.
	 */
	private function register_frontpage_section() {

		$title    = $this->section_data['title'];
		$priority = $this->section_data['priority'];
		$slug     = $this->section_data['slug'];
		$this->add_section(
			new Hestia_Customizer_Section(
				'hestia_' . $slug,
				array(
					'title'          => $title,
					'panel'          => 'hestia_frontpage_sections',
					'priority'       => apply_filters( 'hestia_section_priority', $priority, 'hestia_' . $slug ),
					'hiding_control' => 'hestia_' . $slug . '_hide',
				),
				'Hestia_Hiding_Section'
			)
		);
	}

	/**
	 * Register controls that are in each section: Title, Subtitle and Show.
	 */
	private function register_common_controls() {
		$slug             = $this->section_data['slug'];
		$initially_hidden = $this->get_initial_section_status();
		$control_section  = $this->get_control_section();

		$controls_to_add = array();
		if ( array_key_exists( 'controls', $this->section_data ) ) {
			$controls_to_add = $this->section_data['controls'];
		}

		$controls = array();
		if ( in_array( 'hide', $controls_to_add, true ) || empty( $controls_to_add ) ) {
			$controls[ 'hestia_' . $slug . '_hide' ] = array(
				'setting' => array(
					'sanitize_callback' => 'hestia_sanitize_checkbox',
					'default'           => $initially_hidden,
					'transport'         => $this->selective_refresh,
				),
				'control' => array(
					'type'     => 'checkbox',
					'label'    => esc_html__( 'Disable section', 'hestia-pro' ),
					'section'  => $control_section,
					'priority' => 1,
				),
			);
		}

		if ( in_array( 'title', $controls_to_add, true ) || empty( $controls_to_add ) ) {
			$controls[ 'hestia_' . $slug . '_title' ] = array(
				'setting' => array(
					'sanitize_callback' => 'wp_kses_post',
					'transport'         => $this->selective_refresh,
				),
				'control' => array(
					'label'    => esc_html__( 'Section Title', 'hestia-pro' ),
					'section'  => $control_section,
					'priority' => 5,
				),
			);
		}

		if ( in_array( 'subtitle', $controls_to_add, true ) || empty( $controls_to_add ) ) {
			$controls[ 'hestia_' . $slug . '_subtitle' ] = array(
				'setting' => array(
					'sanitize_callback' => 'wp_kses_post',
					'transport'         => $this->selective_refresh,
				),
				'control' => array(
					'label'    => esc_html__( 'Section Subtitle', 'hestia-pro' ),
					'section'  => $control_section,
					'priority' => 10,
				),
			);
		}

		foreach ( $controls as $control_name => $control_data ) {
			$this->add_control(
				new Hestia_Customizer_Control(
					$control_name,
					$control_data['setting'],
					$control_data['control']
				)
			);
		}
	}

	/**
	 * Get initial section status.
	 *
	 * @return bool
	 */
	private function get_initial_section_status() {
		if ( ! isset( $this->section_data['initially_hidden'] ) ) {
			return false;
		}

		if ( $this->section_data['initially_hidden'] === true ) {
			return true;
		}

		return false;
	}

	/**
	 * Get section for control if it's set.
	 *
	 * @return string
	 */
	private function get_control_section() {
		if ( isset( $this->section_data['section'] ) ) {
			return $this->section_data['section'];
		}

		return 'hestia_' . $this->section_data['slug'];

	}
}
