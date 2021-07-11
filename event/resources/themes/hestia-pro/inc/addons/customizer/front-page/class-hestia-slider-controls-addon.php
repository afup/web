<?php
/**
 * Slider Controls Addon.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Slider_Controls_Addon
 */
class Hestia_Slider_Controls_Addon extends Hestia_Big_Title_Controls {

	/**
	 * Initialize the addon.
	 */
	public function init() {
		parent::init();
		add_action( 'customize_register', array( $this, 'remove_control_from_lite' ) );
		add_filter( 'hestia_parallax_layer1_default', array( $this, 'parallax_layer1_default' ) );
		add_filter( 'hestia_parallax_layer2_default', array( $this, 'parallax_layer2_default' ) );
	}

	/**
	 * Remove big title title control that was added via Hestia_Front_Page_Section_Controls_Abstract class.
	 *
	 * @param object $wp_customize Customize object.
	 */
	public function remove_control_from_lite( $wp_customize ) {
		$wp_customize->remove_section( 'hestia_big_title' );
		$wp_customize->remove_control( 'hestia_big_title_title' );
	}

	/**
	 * Add background control.
	 * Overwrite parent method to remove the control.
	 */
	public function add_background_image_control() {
	}

	/**
	 * Add button controls.
	 * Overwrite parent method to remove the control.
	 */
	public function add_button_controls() {
	}

	/**
	 * Add content control.
	 * Overwrites the parent function to add repeater control.
	 */
	public function add_content_controls() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_slider_disable_autoplay',
				array(
					'sanitize_callback' => 'hestia_sanitize_checkbox',
				),
				array(
					'type'     => 'checkbox',
					'label'    => esc_html__( 'Disable auto-play', 'hestia-pro' ),
					'section'  => 'sidebar-widgets-sidebar-big-title',
					'priority' => -2,
				)
			)
		);

		$slider_default = Hestia_Defaults_Models::instance()->get_slider_default();

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_slider_content',
				array(
					'sanitize_callback' => 'hestia_repeater_sanitize',
					'default'           => json_encode( $slider_default ),
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'                                => esc_html__( 'Slider Content', 'hestia-pro' ),
					'section'                              => 'sidebar-widgets-sidebar-big-title',
					'priority'                             => 20,
					'item_name'                            => esc_html__( 'Slide', 'hestia-pro' ),
					'customizer_repeater_image_control'    => true,
					'customizer_repeater_title_control'    => true,
					'customizer_repeater_subtitle_control' => true,
					'customizer_repeater_text_control'     => true,
					'customizer_repeater_link_control'     => true,
					'customizer_repeater_text2_control'    => true,
					'customizer_repeater_link2_control'    => true,
					'customizer_repeater_color_control'    => true,
					'customizer_repeater_color2_control'   => true,
				),
				'Hestia_Repeater',
				array(
					'selector'        => '.carousel-inner',
					'settings'        => 'hestia_slider_content',
					'render_callback' => array( $this, 'slider_render_callback' ),
				)
			)
		);
	}


	/**
	 * Change controls from lite version.
	 */
	public function change_controls() {

		$this->change_customizer_object( 'control', 'header_video', 'section', 'sidebar-widgets-sidebar-big-title' );
		$this->change_customizer_object( 'control', 'hestia_big_title_hide', 'priority', -3 );
		$this->change_customizer_object( 'control', 'header_video', 'priority', 10 );
		$this->change_customizer_object( 'setting', 'header_image', 'transport', 'refresh' );
		$this->change_customizer_object( 'control', 'external_header_video', 'section', 'sidebar-widgets-sidebar-big-title' );
		$this->change_customizer_object( 'control', 'external_header_video', 'priority', 15 );

		$this->change_customizer_object(
			'control',
			'hestia_slider_type',
			'choices',
			array(
				'image'    => esc_html__( 'Image', 'hestia-pro' ),
				'parallax' => esc_html__( 'Parallax', 'hestia-pro' ),
				'video'    => esc_html__( 'Video', 'hestia-pro' ),
			)
		);

		$this->change_customizer_object(
			'control',
			'hestia_slider_tabs',
			'controls',
			array(
				'slider' => array(
					'hestia_big_title_hide' => array(),
					'hestia_slider_type'    => array(
						'image'    => array(
							'hestia_slider_content',
						),
						'parallax' => array(
							'hestia_slider_content',
							'hestia_parallax_layer1',
							'hestia_parallax_layer2',
						),
						'video'    => array(
							'hestia_slider_content',
							'header_video',
							'external_header_video',
						),
					),
				),
				'extra'  => array(
					'hestia_slider_alignment'        => array(
						'left'   => array(
							'hestia_big_title_widgets_title',
							'widgets',
						),
						'center' => array(),
						'right'  => array(
							'hestia_big_title_widgets_title',
							'widgets',
						),
					),
					'hestia_slider_disable_autoplay' => array(),
				),

			)
		);

		$this->change_customizer_object(
			'control',
			'hestia_slider_type',
			'subcontrols',
			array(
				'image'    => array(
					'hestia_slider_content',
				),
				'parallax' => array(
					'hestia_slider_content',
					'hestia_parallax_layer1',
					'hestia_parallax_layer2',
				),
				'video'    => array(
					'hestia_slider_content',
					'header_video',
					'external_header_video',
				),
			)
		);
	}

	/**
	 * Add filter for default value of parallax layer 1.
	 *
	 * @return string
	 */
	public function parallax_layer1_default() {
		return HESTIA_ADDONS_URI . 'assets/img/parallax_1.jpg';
	}

	/**
	 * Add filter for default value of parallax layer 2.
	 *
	 * @return string
	 */
	public function parallax_layer2_default() {
		return HESTIA_ADDONS_URI . 'assets/img/parallax_2.png';
	}

	/**
	 * Selective refresh for slider content.
	 */
	public function slider_render_callback() {
		$slider = new Hestia_Slider_Section_Addon();
		echo '<div class="carousel slide" data-ride="carousel">';
		echo '<div class="carousel-inner">';
		echo $slider->render_slider_content();
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Slider render callback.
	 */
	public function alignment_render_callback() {
		$this->slider_render_callback();
	}
}
