<?php
/**
 * Big title controls.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Big_Title_Controls
 */
class Hestia_Big_Title_Controls extends Hestia_Front_Page_Section_Controls_Abstract {


	/**
	 * Add section details
	 *
	 * @return array
	 */
	protected function set_section_data() {
		return array(
			'slug'     => 'big_title',
			'title'    => esc_html__( 'Big Title Section', 'hestia-pro' ),
			'priority' => 55,
			'section'  => 'sidebar-widgets-sidebar-big-title',
			'controls' => array(
				'hide',
				'title',
			),
		);

	}

	/**
	 * Initialize the control. Add all the hooks necessary.
	 */
	public function init() {
		parent::init();
		add_filter( 'customizer_widgets_section_args', array( $this, 'move_widgets_section' ), 10, 3 );
	}

	/**
	 * Filter to move widgets section to Fronptage section panel.
	 *
	 * @param array  $section_args Sections args.
	 * @param string $section_id Section id.
	 * @param string $sidebar_id Sidebar id.
	 *
	 * @return mixed
	 */
	public function move_widgets_section( $section_args, $section_id, $sidebar_id ) {
		if ( $sidebar_id === 'sidebar-big-title' ) {
			$section_args['panel'] = 'hestia_frontpage_sections';
		}
		return $section_args;
	}

	/**
	 * Add controls.
	 */
	public function add_controls() {
		$this->add_section_tabs();
		$this->add_general_controls();
		$this->add_background_image_control();
		$this->add_content_controls();
		$this->add_button_controls();
		$this->add_parallax_controls();
		$this->add_alignment_control();
	}

	/**
	 * Change controls.
	 */
	public function change_controls() {
		$this->change_customizer_object( 'control', 'hestia_big_title_title', 'priority', 20 );
		$this->change_customizer_object( 'control', 'hestia_big_title_title', 'label', esc_html__( 'Title', 'hestia-pro' ) );
		$this->change_customizer_object( 'control', 'hestia_big_title_hide', 'priority', -2 );
		$this->maybe_add_defaults_for_big_title();
	}


	/**
	 * Add drop-down for header type.
	 */
	private function add_general_controls() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_slider_type',
				array(
					'sanitize_callback' => 'hestia_sanitize_big_title_type',
					'default'           => apply_filters( 'hestia_slider_type_default', 'image' ),
				),
				array(
					'priority'    => -1,
					'section'     => 'sidebar-widgets-sidebar-big-title', // Add a default or your own section
					'label'       => esc_html__( 'Big Title Background', 'hestia-pro' ),
					'choices'     => array(
						'image'    => esc_html__( 'Image', 'hestia-pro' ),
						'parallax' => esc_html__( 'Parallax', 'hestia-pro' ),
					),
					'subcontrols' => array(
						'image'    => array(
							'hestia_big_title_background',
							'hestia_big_title_title',
							'hestia_big_title_text',
							'hestia_big_title_button_text',
							'hestia_big_title_button_link',
						),
						'parallax' => array(
							'hestia_parallax_layer1',
							'hestia_parallax_layer2',
							'hestia_big_title_title',
							'hestia_big_title_text',
							'hestia_big_title_button_text',
							'hestia_big_title_button_link',

						),
					),
				),
				'Hestia_Select_Hiding'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_big_title_widgets_title',
				array(
					'sanitize_callback' => 'wp_kses',
				),
				array(
					'label'    => esc_html__( 'Big Title Section', 'hestia-pro' ) . ' ' . esc_html__( 'Sidebar', 'hestia-pro' ),
					'section'  => 'sidebar-widgets-sidebar-big-title',
					'priority' => -1,
				),
				'Hestia_Customizer_Heading'
			)
		);

	}

	/**
	 * Add background control.
	 */
	public function add_background_image_control() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_big_title_background',
				array(
					'sanitize_callback' => 'esc_url_raw',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'    => esc_html__( 'Image', 'hestia-pro' ),
					'section'  => 'sidebar-widgets-sidebar-big-title',
					'priority' => 10,
				),
				'WP_Customize_Image_Control',
				array(
					'selector'        => '.big-title-image',
					'settings'        => 'hestia_big_title_background',
					'render_callback' => array( $this, 'background_render_callback' ),
				)
			)
		);
	}

	/**
	 * Add title subtitle.
	 */
	public function add_content_controls() {

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_big_title_title',
				array(
					'sanitize_callback' => 'wp_kses_post',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'    => esc_html__( 'Title', 'hestia-pro' ),
					'section'  => 'sidebar-widgets-sidebar-big-title',
					'priority' => 15,
				),
				null,
				array(
					'selector'        => '.carousel .hestia-title',
					'settings'        => 'hestia_big_title_title',
					'render_callback' => array( $this, 'title_render_callback' ),
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_big_title_text',
				array(
					'sanitize_callback' => 'wp_kses_post',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'    => esc_html__( 'Text', 'hestia-pro' ),
					'section'  => 'sidebar-widgets-sidebar-big-title',
					'priority' => 25,
				),
				null,
				array(
					'selector'        => '.carousel .sub-title',
					'settings'        => 'hestia_big_title_text',
					'render_callback' => array( $this, 'text_render_callback' ),
				)
			)
		);
	}

	/**
	 * Add button controls.
	 */
	public function add_button_controls() {

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_big_title_button_text',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'    => esc_html__( 'Button text', 'hestia-pro' ),
					'section'  => 'sidebar-widgets-sidebar-big-title',
					'priority' => 30,
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_big_title_button_link',
				array(
					'sanitize_callback' => 'esc_url_raw',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'    => esc_html__( 'Button URL', 'hestia-pro' ),
					'section'  => 'sidebar-widgets-sidebar-big-title',
					'priority' => 35,
				)
			)
		);

		$this->add_partial(
			new Hestia_Customizer_Partial(
				'hestia_big_title_button_text',
				array(
					'selector'        => '.carousel .buttons',
					'settings'        => 'hestia_big_title_button_text',
					'render_callback' => array( $this, 'button_render_callback' ),
				)
			)
		);
	}

	/**
	 * Add alignment control.
	 */
	protected function add_alignment_control() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_slider_alignment',
				array(
					'default'           => 'center',
					'sanitize_callback' => 'hestia_sanitize_alignment_options',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'       => esc_html__( 'Layout', 'hestia-pro' ),
					'priority'    => -2,
					'section'     => 'sidebar-widgets-sidebar-big-title',
					'choices'     => array(
						'left'   => array(
							'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqBAMAAACsf7WzAAAAD1BMVEX////V1dUAhbo+yP/u9/pRM+FMAAAAZElEQVR42u3WsQ2AIBRFUd0AV3AFV3D/mSwsBI2BRIofPKchobjVK/7EQJZSit+az5/aq/WjVs99AQAjWxs8L4ZL0hqutTcoWt0OSa2orfdVaWl9b/XcqpbWvbXltLQCtwCA3AHhDKjAJvDMEwAAAABJRU5ErkJggg==',
						),
						'center' => array(
							'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqBAMAAACsf7WzAAAAD1BMVEX///8AhbrV1dU+yP/u9/q7NurVAAAAV0lEQVR42u3SsQ2AMAxFwYBYgA0QK7AC+89EQQOiIIoogn3XWHLxql8IZL1b+m+N5+ftaiVqfbkvACC8YW6iFbg17U0KCVQNTUvr0YK+bFdaWklaAPAXB4dWiADE72glAAAAAElFTkSuQmCC',
						),
						'right'  => array(
							'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqBAMAAACsf7WzAAAAD1BMVEX////V1dUAhbo+yP/u9/pRM+FMAAAAYElEQVR42u3SuQ2AMBBFQaAC3AIt0AL910RAAkICS1xrPJOstMGLfsOPpK0+fqtdPmdXq6LWnfsCAKJJe4+0hhxaVbWmHB9sVStCq7u8Ly2td7aqpXVsXNPSKrAFAOWbASNgr0b3Lh1kAAAAAElFTkSuQmCC',
						),
					),
					'subcontrols' => array(
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
				),
				'Hestia_Customize_Control_Radio_Image',
				array(
					'selector'        => '#carousel-hestia-generic',
					'settings'        => 'hestia_slider_alignment',
					'render_callback' => array( $this, 'alignment_render_callback' ),
				)
			)
		);
	}

	/**
	 * Add controls for parallax.
	 */
	private function add_parallax_controls() {

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_parallax_layer1',
				array(
					'sanitize_callback' => 'esc_url_raw',
					'default'           => apply_filters( 'hestia_parallax_layer1_default', false ),
				),
				array(
					'label'    => esc_html__( 'First Layer', 'hestia-pro' ),
					'section'  => 'sidebar-widgets-sidebar-big-title',
					'priority' => 10,
				),
				'WP_Customize_Image_Control'
			)
		);
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_parallax_layer2',
				array(
					'sanitize_callback' => 'esc_url_raw',
					'default'           => apply_filters( 'hestia_parallax_layer2_default', false ),
				),
				array(
					'label'    => esc_html__( 'Second Layer', 'hestia-pro' ),
					'section'  => 'sidebar-widgets-sidebar-big-title',
					'priority' => 15,
				),
				'WP_Customize_Image_Control'
			)
		);
	}

	/**
	 * Add tabs control in hestia_big_title section.
	 */
	public function add_section_tabs() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_slider_tabs',
				array(
					'transport' => $this->selective_refresh,
				),
				array(
					'section'  => 'sidebar-widgets-sidebar-big-title',
					'priority' => -3,
					'tabs'     => array(
						'slider' => array(
							'label' => esc_html__( 'Big Title Section', 'hestia-pro' ),
							'icon'  => 'format-gallery',
						),
						'extra'  => array(
							'label' => esc_html__( 'Extra', 'hestia-pro' ),
							'icon'  => 'welcome-add-page',
						),
					),
					'controls' => array(
						'slider' => array(
							'hestia_big_title_upsell' => array(),
							'hestia_big_title_hide'   => array(),
							'hestia_slider_type'      => array(
								'image'    => array(
									'hestia_big_title_background',
									'hestia_big_title_title',
									'hestia_big_title_text',
									'hestia_big_title_button_text',
									'hestia_big_title_button_link',
								),
								'parallax' => array(
									'hestia_parallax_layer1',
									'hestia_parallax_layer2',
									'hestia_big_title_title',
									'hestia_big_title_text',
									'hestia_big_title_button_text',
									'hestia_big_title_button_link',

								),
							),
						),
						'extra'  => array(
							'hestia_slider_alignment' => array(
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
						),
					),
				),
				'Hestia_Customize_Control_Tabs'
			)
		);
	}

	/**
	 * Maybe throw in some defaults.
	 */
	private function maybe_add_defaults_for_big_title() {
		$hestia_slider_content = get_theme_mod( 'hestia_slider_content' );
		if ( ! empty( $hestia_slider_content ) ) {
			return;
		}

		$this->change_customizer_object( 'setting', 'hestia_big_title_background', 'default', esc_url( apply_filters( 'hestia_big_title_background_default', get_template_directory_uri() . '/assets/img/slider1.jpg' ) ) );
		$this->change_customizer_object( 'setting', 'hestia_big_title_title', 'default', esc_html__( 'Change in the Customizer', 'hestia-pro' ) );
		$this->change_customizer_object( 'setting', 'hestia_big_title_text', 'default', esc_html__( 'Change in the Customizer', 'hestia-pro' ) );
		$this->change_customizer_object( 'setting', 'hestia_big_title_button_text', 'default', esc_html__( 'Change in the Customizer', 'hestia-pro' ) );
		$this->change_customizer_object( 'setting', 'hestia_big_title_button_link', 'default', esc_url( '#' ) );
	}

	/**
	 * Callback for title.
	 *
	 * @return string
	 */
	public function title_render_callback() {
		return get_theme_mod( 'hestia_big_title_title' );
	}

	/**
	 * Callback for subtitle.
	 *
	 * @return string
	 */
	public function text_render_callback() {
		return get_theme_mod( 'hestia_big_title_text' );
	}

	/**
	 * Callback for button.
	 *
	 * @return bool|string
	 */
	public function button_render_callback() {
		$button_text = get_theme_mod( 'hestia_big_title_button_text' );
		$button_link = get_theme_mod( 'hestia_big_title_button_link' );
		if ( empty( $button_text ) ) {
			return false;
		}
		if ( empty( $button_link ) ) {
			return false;
		}

		$output = '<a href="' . $button_link . '" title="' . $button_text . '" class="btn btn-primary">' . $button_text . '</a>';

		return wp_kses_post( $output );
	}

	/**
	 * Alignment callback.
	 */
	public function alignment_render_callback() {
		$big_title_section_view = new Hestia_Big_Title_Section();
		$big_title_section_view->render_big_title_content();
	}

	/**
	 * Background callback.
	 */
	public function background_render_callback() {
		$hestia_parallax_layer1 = get_theme_mod( 'hestia_parallax_layer1' );
		$hestia_parallax_layer2 = get_theme_mod( 'hestia_parallax_layer2' );
		if ( empty( $hestia_parallax_layer1 ) || empty( $hestia_parallax_layer2 ) ) {
			$hestia_big_title_background = get_theme_mod( 'hestia_big_title_background' );
			echo '<style class="big-title-image-css">';
			echo '#carousel-hestia-generic .header-filter {';
			echo ! empty( $hestia_big_title_background ) ? 'background-image: url("' . esc_url( $hestia_big_title_background ) . '") !important;' : 'background-image: none !important;';
			echo '}';
			echo '</style>';
		}
	}

}
