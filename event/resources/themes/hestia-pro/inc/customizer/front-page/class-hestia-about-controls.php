<?php
/**
 * About controls.
 *
 * @package Hestia
 */

/**
 * Class Hestia_About_Controls
 */
class Hestia_About_Controls extends Hestia_Register_Customizer_Controls {
	/**
	 * Add controls.
	 */
	public function add_controls() {
		$this->add_about_section();
		$this->add_hiding_control();
		$this->add_content_control();
		$this->add_pagebuilder_button_control();
		$this->add_background_control();
		$this->add_content_shortcut();
	}

	/**
	 * Add the section.
	 */
	private function add_about_section() {
		$this->add_section(
			new Hestia_Customizer_Section(
				'hestia_about',
				array(
					'title'          => esc_html__( 'About', 'hestia-pro' ),
					'panel'          => 'hestia_frontpage_sections',
					'priority'       => apply_filters( 'hestia_section_priority', 15, 'hestia_about' ),
					'hiding_control' => 'hestia_about_hide',
				),
				'Hestia_Hiding_Section'
			)
		);
	}

	/**
	 * Add hiding control.
	 */
	private function add_hiding_control() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_about_hide',
				array(
					'sanitize_callback' => 'hestia_sanitize_checkbox',
					'default'           => false,
					'transport'         => $this->selective_refresh,
				),
				array(
					'type'     => 'checkbox',
					'label'    => esc_html__( 'Disable section', 'hestia-pro' ),
					'section'  => 'hestia_about',
					'priority' => 1,
				)
			)
		);
	}

	/**
	 * Add about section content editor control.
	 */
	private function add_content_control() {
		if ( $this->should_display_content_editor() === false ) {
			return;
		}

		if ( ! current_user_can( 'editor' ) && ! current_user_can( 'administrator' ) ) {
			return;
		}

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_page_editor',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'type'     => 'hidden',
					'section'  => 'hestia_about',
					'priority' => 10,
				)
			)
		);
	}

	/**
	 * Callback for About section content editor
	 *
	 * @return bool
	 */
	public function should_display_content_editor() {
		return ! hestia_edited_with_pagebuilder();
	}

	/**
	 * Add the page builder button control.
	 */
	private function add_pagebuilder_button_control() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_elementor_edit',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'label'           => esc_html__( 'About Content', 'hestia-pro' ),
					'section'         => 'hestia_about',
					'priority'        => 14,
					'active_callback' => array( $this, 'page_edited_with_page_builder' ),
				),
				'Hestia_PageBuilder_Button'
			)
		);
	}

	/**
	 * Add the background image control.
	 */
	private function add_background_control() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_feature_thumbnail',
				array(
					'sanitize_callback' => 'esc_url_raw',
					'default'           => get_template_directory_uri() . '/assets/img/contact.jpg',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'           => esc_html__( 'About background', 'hestia-pro' ),
					'section'         => 'hestia_about',
					'priority'        => 15,
					'active_callback' => array( $this, 'is_static_page' ),
				),
				'WP_Customize_Image_Control'
			)
		);
	}

	/**
	 * Shortcut for page editor.
	 */
	private function add_content_shortcut() {

		$frontpage_id = get_option( 'page_on_front' );

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_shortcut_editor',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'label'           => esc_html__( 'About Content', 'hestia-pro' ),
					'section'         => 'hestia_about',
					'priority'        => 10,
					'button_text'     => esc_html__( '(Edit)', 'hestia-pro' ),
					'button_class'    => 'open-editor',
					'icon_class'      => 'fa-pencil',
					'link'            => get_edit_post_link( $frontpage_id ),
					'active_callback' => array( $this, 'content_shortcut_callback' ),
				),
				'Hestia_Button'
			)
		);
	}

	/**
	 * Active callback for displaying page editor shortcut.
	 */
	public function content_shortcut_callback() {
		if ( 'posts' === get_option( 'show_on_front' ) ) {
			return false;
		}
		$frontpage_id = get_option( 'page_on_front' );
		if ( hestia_edited_with_pagebuilder( $frontpage_id ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Active callback for displaying page builder button.
	 */
	public function page_edited_with_page_builder() {
		if ( 'posts' === get_option( 'show_on_front' ) ) {
			return false;
		}
		$frontpage_id = get_option( 'page_on_front' );
		if ( ! hestia_edited_with_pagebuilder( $frontpage_id ) ) {
			return false;
		}

		return true;
	}

	/**
	 * About section content render callback.
	 */
	public function about_content_render_callback() {
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', 'frontpage' );
			endwhile;
			else : // I'm not sure it's possible to have no posts when this page is shown, but WTH
				get_template_part( 'template-parts/content', 'none' );
		endif;
	}

	/**
	 * Page editor control active callback function
	 *
	 * @return bool
	 */
	public function is_static_page() {
		return 'page' === get_option( 'show_on_front' );
	}

	/**
	 * Get default content for page editor control.
	 *
	 * @return string
	 */
	private function get_about_content_default() {
		$front_page_id = get_option( 'page_on_front' );
		if ( empty( $front_page_id ) ) {
			return '';
		}
		$content = get_post_field( 'post_content', $front_page_id );

		return $content;
	}
}
