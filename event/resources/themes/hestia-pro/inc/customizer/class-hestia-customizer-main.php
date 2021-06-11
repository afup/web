<?php
/**
 * The main customizer manager.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Customizer_Main
 */
class Hestia_Customizer_Main extends Hestia_Register_Customizer_Controls {

	/**
	 * Add controls.
	 */
	public function add_controls() {
		$this->register_types();
		$this->add_main_panels();
		$this->maybe_add_partial_refresh_to_blog_description();
	}

	/**
	 * Register customizer controls type.
	 */
	private function register_types() {
		$this->register_type( 'Hestia_Hiding_Section', 'section' );
		$this->register_type( 'Hestia_Customizer_Range_Value_Control', 'control' );
		$this->register_type( 'Hestia_Customizer_Heading', 'control' );
		$this->register_type( 'Hestia_Select_Multiple', 'control' );
		$this->register_type( 'Hestia_PageBuilder_Button', 'control' );
		$this->register_type( 'Hestia_Customize_Control_Radio_Image', 'control' );
		$this->register_type( 'Hestia_Customize_Control_Tabs', 'control' );
		$this->register_type( 'Hestia_Customizer_Dimensions', 'control' );
		$this->register_type( 'Hestia_Select_Hiding', 'control' );
	}

	/**
	 * Add main panels.
	 */
	private function add_main_panels() {
		$this->add_panel(
			new Hestia_Customizer_Panel(
				'hestia_appearance_settings',
				array(
					'priority' => 25,
					'title'    => esc_html__( 'Appearance Settings', 'hestia-pro' ),
				)
			)
		);

		$this->add_panel(
			new Hestia_Customizer_Panel(
				'hestia_frontpage_sections',
				array(
					'priority'        => 30,
					'title'           => esc_html__( 'Frontpage Sections', 'hestia-pro' ),
					'active_callback' => array( $this, 'hestia_display_frontpage_section' ),
				)
			)
		);

		$this->add_panel(
			new Hestia_Customizer_Panel(
				'hestia_blog_settings',
				array(
					'priority' => 45,
					'title'    => esc_html__( 'Blog Settings', 'hestia-pro' ),
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_link_header_background',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'priority'     => 25,
					'section'      => 'background_image',
					'button_text'  => esc_html__( 'Header Background', 'hestia-pro' ),
					'button_class' => 'focus-customizer-header-image',
					'icon_class'   => 'fa-image',
				),
				'Hestia_Button'
			)
		);
	}

	/**
	 * Add selective refresh to blog description if that's the case.
	 */
	private function maybe_add_partial_refresh_to_blog_description() {
		if ( ! 'posts' === get_option( 'show_on_front' ) ) {
			return;
		}
		$this->add_partial(
			new Hestia_Customizer_Partial(
				'blogdescription',
				array(
					'selector'        => '.home.blog .page-header .hestia-title',
					'render_callback' => array( $this, 'blog_description_callback' ),
				)
			)
		);
	}

	/**
	 * Change controls.
	 *
	 * @return void
	 */
	public function change_controls() {
		$this->change_customizer_object( 'section', 'header_image', 'panel', 'hestia_appearance_settings' );
		$this->change_customizer_object( 'section', 'header_image', 'description', __return_empty_string() );
		$this->change_customizer_object( 'section', 'background_image', 'panel', 'hestia_appearance_settings' );
		$this->change_customizer_object( 'setting', 'blogdescription', 'transport', $this->selective_refresh );
		$this->change_customizer_object( 'section', 'colors', 'panel', 'hestia_appearance_settings' );
	}

	/**
	 * Blog description callback function
	 */
	public function blog_description_callback() {
		bloginfo( 'description' );
	}

	/**
	 * Checks if the front-page sections should be displayed or not.
	 * Small tweak for Customizer when some filers are not aware of update theme_mods yet.
	 *
	 * @return bool
	 */
	function hestia_display_frontpage_section() {
		if ( ! empty( $_REQUEST['customized'] ) ) {
			$customized = json_decode( wp_unslash( $_REQUEST['customized'] ), true );
			if ( is_array( $customized ) && isset( $customized['disable_frontpage_sections'] ) ) {
				return ! (bool) $customized['disable_frontpage_sections'];
			}
		}

		return ! (bool) get_theme_mod( 'disable_frontpage_sections' );
	}
}
