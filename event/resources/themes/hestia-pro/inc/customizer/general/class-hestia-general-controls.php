<?php
/**
 * Customizer general controls.
 *
 * @package Hestia
 */

/**
 * Class Hestia_General_Controls
 */
class Hestia_General_Controls extends Hestia_Register_Customizer_Controls {
	/**
	 * Initialize the controls.
	 */
	public function init() {
		parent::init();
		if ( is_rtl() ) {
			add_filter( 'hestia_layout_control_image_left', array( $this, 'rtl_layout_control_right_image' ) );
			add_filter( 'hestia_layout_control_image_right', array( $this, 'rtl_layout_control_left_image' ) );
		}
	}

	/**
	 * Add controls
	 */
	public function add_controls() {
		$this->add_general_settings_section();
		$this->add_general_settings_section();
		$this->add_page_sidebar_layout();
		$this->add_blog_sidebar_layout();
		$this->add_sharing_icons_toggle();
		$this->add_scrolltop_toggle();
		$this->add_boxed_layout_toggle();
		$this->add_shop_sidebar_layout_controls();
	}

	/**
	 * Add General Settings Section
	 */
	private function add_general_settings_section() {
		$this->add_section(
			new Hestia_Customizer_Section(
				'hestia_general',
				array(
					'title'    => esc_html__( 'General Settings', 'hestia-pro' ),
					'panel'    => 'hestia_appearance_settings',
					'priority' => 20,
				)
			)
		);

	}

	/**
	 * Add page sidebar layout
	 */
	private function add_page_sidebar_layout() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_page_sidebar_layout',
				array(
					'sanitize_callback' => 'sanitize_key',
					'default'           => 'full-width',
				),
				array(
					'label'    => esc_html__( 'Page Sidebar Layout', 'hestia-pro' ),
					'section'  => 'hestia_general',
					'priority' => 15,
					'choices'  => $this->get_layout_choices(),
				),
				'Hestia_Customize_Control_Radio_Image'
			)
		);
	}

	/**
	 * Add blog sidebar layout
	 */
	private function add_blog_sidebar_layout() {
		$default = hestia_get_blog_layout_default();
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_blog_sidebar_layout',
				array(
					'default'           => $default,
					'sanitize_callback' => 'sanitize_key',
				),
				array(
					'label'    => esc_html__( 'Blog Sidebar Layout', 'hestia-pro' ),
					'section'  => 'hestia_general',
					'priority' => 20,
					'choices'  => $this->get_layout_choices(),
				),
				'Hestia_Customize_Control_Radio_Image'
			)
		);
	}

	/**
	 * Sharing icons control.
	 */
	private function add_sharing_icons_toggle() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_enable_sharing_icons',
				array(
					'default'           => true,
					'sanitize_callback' => 'hestia_sanitize_checkbox',
				),
				array(
					'label'    => esc_html__( 'Enable Sharing Icons', 'hestia-pro' ),
					'section'  => 'hestia_general',
					'priority' => 30,
					'type'     => 'checkbox',
				)
			)
		);
	}

	/**
	 * Add scroll to top control.
	 */
	private function add_scrolltop_toggle() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_enable_scroll_to_top',
				array(
					'sanitize_callback' => 'hestia_sanitize_checkbox',
					'default'           => apply_filters( 'hestia_scroll_to_top_default', 0 ),
				),
				array(
					'label'    => esc_html__( 'Enable Scroll to Top', 'hestia-pro' ),
					'section'  => 'hestia_general',
					'priority' => 40,
					'type'     => 'checkbox',
				)
			)
		);
	}

	/**
	 * ADd the boxed layout control.
	 */
	private function add_boxed_layout_toggle() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_general_layout',
				array(
					'default'           => apply_filters( 'hestia_boxed_layout_default', 1 ),
					'sanitize_callback' => 'hestia_sanitize_checkbox',
					'transport'         => 'postMessage',
				),
				array(
					'label'       => esc_html__( 'Boxed Layout', 'hestia-pro' ),
					'description' => esc_html__( 'If enabled, the theme will use a boxed layout.', 'hestia-pro' ),
					'section'     => 'hestia_general',
					'priority'    => 50,
					'type'        => 'checkbox',
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'disable_frontpage_sections',
				array(
					'sanitize_callback' => 'hestia_sanitize_checkbox',
					'default'           => false,
				),
				array(
					'label'    => esc_html__( 'Disable', 'hestia-pro' ) . ' ' . esc_html__( 'Frontpage Sections', 'hestia-pro' ),
					'section'  => 'hestia_general',
					'type'     => 'checkbox',
					'priority' => 55,
				)
			)
		);
	}

	/**
	 * Get layout choices for sidebar layout controls.
	 *
	 * @return array
	 */
	protected function get_layout_choices( $additional_options = array() ) {
		$options = array(
			'full-width'    => array(
				'url'   => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqAQMAAABknzrDAAAABlBMVEX////V1dXUdjOkAAAAPUlEQVRIx2NgGAUkAcb////Y/+d/+P8AdcQoc8vhH/X/5P+j2kG+GA3CCgrwi43aMWrHqB2jdowEO4YpAACyKSE0IzIuBgAAAABJRU5ErkJggg==',
				'label' => esc_html__( 'Full Width', 'hestia-pro' ),
			),
			'sidebar-left'  => array(
				'url'   => apply_filters( 'hestia_layout_control_image_left', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqAgMAAAAjP0ATAAAACVBMVEX///8+yP/V1dXG9YqxAAAAWElEQVR42mNgGAXDE4RCQMDAKONaBQINWqtWrWBatQDIaxg8ygYqQIAOYwC6bwHUmYNH2eBPSMhgBQXKRr0w6oVRL4x6YdQLo14Y9cKoF0a9QCO3jYLhBADvmFlNY69qsQAAAABJRU5ErkJggg==' ),
				'label' => esc_html__( 'Left Sidebar', 'hestia-pro' ),
			),
			'sidebar-right' => array(
				'url'   => apply_filters( 'hestia_layout_control_image_right', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqAgMAAAAjP0ATAAAACVBMVEX///8+yP/V1dXG9YqxAAAAWUlEQVR42mNgGAUjB4iGgkEIzZStAoEVTECiQWsVkLdiECkboAABOmwBF9BtUGcOImUDEiCkJCQU0ECBslEvjHph1AujXhj1wqgXRr0w6oVRLwyEF0bBUAUAz/FTNXm+R/MAAAAASUVORK5CYII=' ),
				'label' => esc_html__( 'Right Sidebar', 'hestia-pro' ),
			),
		);
		return array_merge( $options, $additional_options );
	}


	/**
	 * Change the right image.
	 *
	 * @return string
	 */
	public function rtl_layout_control_right_image() {
		return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqAgMAAAAjP0ATAAAACVBMVEX///8+yP/V1dXG9YqxAAAAWUlEQVR42mNgGAUjB4iGgkEIzZStAoEVTECiQWsVkLdiECkboAABOmwBF9BtUGcOImUDEiCkJCQU0ECBslEvjHph1AujXhj1wqgXRr0w6oVRLwyEF0bBUAUAz/FTNXm+R/MAAAAASUVORK5CYII=';
	}

	/**
	 * Change the left image.
	 *
	 * @return string
	 */
	public function rtl_layout_control_left_image() {
		return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqAgMAAAAjP0ATAAAACVBMVEX///8+yP/V1dXG9YqxAAAAWElEQVR42mNgGAXDE4RCQMDAKONaBQINWqtWrWBatQDIaxg8ygYqQIAOYwC6bwHUmYNH2eBPSMhgBQXKRr0w6oVRL4x6YdQLo14Y9cKoF0a9QCO3jYLhBADvmFlNY69qsQAAAABJRU5ErkJggg==';
	}

	/**
	 * Add shop sidebar layout controls.
	 *
	 * @return bool
	 */
	private function add_shop_sidebar_layout_controls() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return false;
		}

		$shop_sidebar_options = apply_filters( 'hestia_shop_sidebar_options', array() );
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_shop_sidebar_layout',
				array(
					'default'           => self::get_shop_sidebar_layout_default(),
					'sanitize_callback' => array( $this, 'sanitize_shop_sidebar_value' ),
				),
				array(
					'label'    => esc_html__( 'Shop Sidebar Layout', 'hestia-pro' ),
					'section'  => 'hestia_general',
					'priority' => 22,
					'choices'  => $this->get_layout_choices( $shop_sidebar_options ),
				),
				'Hestia_Customize_Control_Radio_Image'
			)
		);

		return true;
	}

	/**
	 * Get default value for shop sidebar.
	 *
	 * @return string
	 */
	public static function get_shop_sidebar_layout_default() {
		return get_theme_mod( 'hestia_page_sidebar_layout', 'full-width' );
	}

	/**
	 * Sanitize shop sidebar value.
	 *
	 * @param string $value Shop sidebar value.
	 *
	 * @return string
	 */
	public function sanitize_shop_sidebar_value( $value ) {
		$accepted_vals = array( 'full-width', 'sidebar-left', 'sidebar-right', 'off-canvas' );
		if ( ! in_array( $value, $accepted_vals, true ) ) {
			return 'full-width';
		}
		return $value;
	}
}
