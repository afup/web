<?php
/**
 * Upsell Manager
 *
 * @package Hestia
 */

/**
 * Class Hestia_Upsell_Manager
 */
class Hestia_Upsell_Manager extends Hestia_Register_Customizer_Controls {
	/**
	 * Add the controls.
	 */
	public function add_controls() {
		$this->register_type( 'Hestia_Section_Upsell', 'section' );
		$this->register_type( 'Hestia_Control_Upsell', 'control' );
		$this->add_main_upsell();

		if ( function_exists( 'hestia_check_passed_time' ) && hestia_check_passed_time( '21600' ) ) {
			$this->add_front_page_sections_upsells();
			$this->add_typography_upsells();
			$this->add_big_title_upsells();
			$this->add_small_pro_notices();
		}

	}

	/**
	 * Change controls
	 */
	public function change_controls() {
		$this->change_customizer_object( 'section', 'hestia_front_page_sections_upsell_section', 'active_callback', '__return_true' );
		$this->change_customizer_object( 'section', 'hestia_front_page_translation_upsell_section', 'active_callback', '__return_true' );
	}

	/**
	 * Adds main
	 */
	private function add_main_upsell() {
		$this->add_section(
			new Hestia_Customizer_Section(
				'hestia_upsell_main_section',
				array(
					'title'    => esc_html__( 'View PRO Features', 'hestia-pro' ),
					'priority' => 0,
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_upsell_main_control',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'section'            => 'hestia_upsell_main_section',
					'priority'           => 100,
					'options'            => array(
						esc_html__( 'Header Slider', 'hestia-pro' ),
						esc_html__( 'Fully Customizable Colors', 'hestia-pro' ),
						esc_html__( 'Jetpack Portfolio', 'hestia-pro' ),
						esc_html__( 'Pricing Plans Section', 'hestia-pro' ),
						esc_html__( 'Section Reordering', 'hestia-pro' ),
						esc_html__( 'Quality Support', 'hestia-pro' ),
					),
					'explained_features' => array(
						esc_html__( 'You will be able to add more content to your site header with an awesome slider.', 'hestia-pro' ),
						esc_html__( 'Change colors for the header overlay, header text and navbar.', 'hestia-pro' ),
						esc_html__( 'Portfolio section with two possible layouts.', 'hestia-pro' ),
						esc_html__( 'A fully customizable pricing plans section.', 'hestia-pro' ),
						esc_html__( 'Drag and drop panels to change the order of sections.', 'hestia-pro' ),
						esc_html__( 'The ability to reorganize your Frontpage Sections more easily and quickly.', 'hestia-pro' ),
						esc_html__( '24/7 HelpDesk Professional Support', 'hestia-pro' ),
					),
					'button_url'         => esc_url( apply_filters( 'hestia_upgrade_link_from_child_theme_filter', 'https://themeisle.com/themes/hestia-pro/upgrade/?utm_medium=customizer&utm_source=button&utm_campaign=profeatures' ) ),
					'button_text'        => esc_html__( 'Get the PRO version!', 'hestia-pro' ),
				),
				'Hestia_Control_Upsell'
			)
		);
	}

	/**
	 * Add upsell section under Front Page Sections panel.
	 */
	private function add_front_page_sections_upsells() {

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_control_to_enable_translation_upsell_section',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'section' => 'hestia_front_page_translation_upsell_section',
					'type'    => 'hidden',
				)
			)
		);

		$notification_settings = array(
			'panel'              => 'hestia_frontpage_sections',
			'priority'           => 500,
			'explained_features' => array(
				esc_html__( 'Portfolio section with two possible layouts.', 'hestia-pro' ),
				esc_html__( 'A fully customizable pricing plans section.', 'hestia-pro' ),
				esc_html__( 'The ability to reorganize your Frontpage sections more easily and quickly.', 'hestia-pro' ),
			),
			'options'            => array(
				esc_html__( 'Jetpack Portfolio', 'hestia-pro' ),
				esc_html__( 'Pricing Plans Section', 'hestia-pro' ),
				esc_html__( 'Section Reordering', 'hestia-pro' ),
			),
		);

		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( defined( 'POLYLANG_VERSION' ) || defined( 'TRP_PLUGIN_VERSION' ) || ( get_option( 'icl_sitepress_settings' ) !== false ) ) {
			/* translators: %s Required action */
			array_push( $notification_settings['options'], sprintf( esc_html__( 'Hestia front-page is not multi-language compatible, for this feature %s.', 'hestia-pro' ), sprintf( '<a href="%1$s" target="_blank" class="button button-primary" style="margin-top: 20px; margin-bottom: -20px;">%2$s</a>', esc_url( apply_filters( 'hestia_upgrade_link_from_child_theme_filter', 'https://docs.themeisle.com/article/753-hestia-doc?utm_medium=customizer&utm_source=button&utm_campaign=multilanguage#translatehestia' ) ), esc_html__( 'Get the PRO version!', 'hestia-pro' ) ) ) );
		} else {
			$notification_settings['button_url']  = esc_url( apply_filters( 'hestia_upgrade_link_from_child_theme_filter', 'https://themeisle.com/themes/hestia-pro/upgrade?utm_medium=customizer&utm_source=button&utm_campaign=frontpagesection' ) );
			$notification_settings['button_text'] = esc_html__( 'Get the PRO version!', 'hestia-pro' );
		}

		$this->add_section(
			new Hestia_Customizer_Section(
				'hestia_front_page_sections_upsell_section',
				$notification_settings,
				'Hestia_Section_Upsell'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_control_to_enable_upsell_section',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'section' => 'hestia_front_page_sections_upsell_section',
					'type'    => 'hidden',
				)
			)
		);
	}

	/**
	 * Typography upsells
	 */
	private function add_typography_upsells() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_typography_upsell',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'section'        => 'hestia_typography',
					'priority'       => 230,
					'options'        => array(
						sprintf(
							/* translators: %s is Feature name */
							esc_html__( 'More Options Available for %s in the PRO version.', 'hestia-pro' ),
							esc_html__( 'Typography', 'hestia-pro' )
						),
					),
					'show_pro_label' => false,
					'button_url'     => esc_url( apply_filters( 'hestia_upgrade_link_from_child_theme_filter', 'https://docs.themeisle.com/article/920-typography-options-in-hestia-pro?utm_medium=customizer&utm_source=button&utm_campaign=typography' ) ),
					'button_text'    => esc_html__( 'Read more', 'hestia-pro' ),
				),
				'Hestia_Control_Upsell'
			)
		);
	}

	/**
	 * Big title upsells
	 */
	private function add_big_title_upsells() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_big_title_upsell',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'section'        => 'sidebar-widgets-sidebar-big-title',
					'priority'       => -3,
					'options'        => array(
						sprintf(
							/* translators: %s Feature name*/
							esc_html__( 'More Options Available for %s in the PRO version.', 'hestia-pro' ),
							esc_html__( 'Big Title Background', 'hestia-pro' )
						),
					),
					'show_pro_label' => false,
					'button_url'     => esc_url( apply_filters( 'hestia_upgrade_link_from_child_theme_filter', 'https://docs.themeisle.com/article/921-big-title-background-options-in-hestia-pro?utm_medium=customizer&utm_source=button&utm_campaign=bigtitle' ) ),
					'button_text'    => esc_html__( 'Read more', 'hestia-pro' ),
				),
				'Hestia_Control_Upsell'
			)
		);
	}

	/**
	 * Small pro notices in the extended sections area.
	 */
	private function add_small_pro_notices() {

		$sections    = array(
			'hestia_general',
			'colors',
			'hestia_shop',
			'hestia_blog',
			'hestia_footer_content',
			'hestia_blog_layout',
		);
		$description = sprintf(
			/* translators: %s is the Learn more link */
			__( 'More options are available for this section in our premium version. %s', 'hestia-pro' ),
			/* translators: %s is the Learn more label*/
			sprintf(
				'<a class="button button-primary" target="_blank" href="https://themeisle.com/themes/hestia-pro/upgrade/" style="display: block; clear: both; width: fit-content; margin-top: 5px;">%s</a>',
				__( 'Learn more', 'hestia-pro' )
			)
		);

		foreach ( $sections as $section ) {
			$this->add_control(
				new Hestia_Customizer_Control(
					'hestia_pro_notice_' . $section,
					array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					array(
						'section'     => $section,
						'description' => '<hr style="width: 80px; margin-left: 0px; border-bottom: none;">' . $description,
						'priority'    => 900,
						'type'        => 'hidden',
					)
				)
			);
		}
	}
}
