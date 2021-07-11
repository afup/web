<?php
/**
 * Customizer typography controls.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Typography_Controls
 */
class Hestia_Typography_Controls extends Hestia_Register_Customizer_Controls {
	/**
	 * Initialize the scripts and anything needed.
	 */
	public function init() {
		parent::init();
		add_action( 'customize_preview_init', array( $this, 'enqueue_customizer_script' ) );
	}

	/**
	 * Add controls
	 */
	public function add_controls() {
		$this->add_typography_section();
		$this->add_section_ui_tabs();
		$this->add_font_family_selectors();
		$this->add_font_subsets_control();
		$this->add_section_ui_headings();
		$this->add_posts_pages_controls();
		$this->add_front_page_controls();
	}

	/**
	 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
	 *
	 * @since 1.1.38
	 */
	public function enqueue_customizer_script() {
		wp_enqueue_script( 'hestia_customizer_typography', get_template_directory_uri() . '/assets/js/admin/typography-customizer-preview.js', array(), HESTIA_VERSION, true );
	}

	/**
	 * Add the customizer section.
	 */
	private function add_typography_section() {
		$this->add_section(
			new Hestia_Customizer_Section(
				'hestia_typography',
				array(
					'title'    => esc_html__( 'Typography', 'hestia-pro' ),
					'panel'    => 'hestia_appearance_settings',
					'priority' => 25,
				)
			)
		);
	}

	/**
	 * Add ui tabs
	 */
	private function add_section_ui_tabs() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_typography_tabs',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'section'  => 'hestia_typography',
					'priority' => 1,
					'tabs'     => array(
						'font_family' => array(
							'label' => esc_html__( 'font family', 'hestia-pro' ),
							'icon'  => 'editor-spellcheck',
						),
						'font_sizes'  => array(
							'label' => esc_html__( 'font size', 'hestia-pro' ),
							'icon'  => 'editor-textcolor',
						),
					),
					'controls' => array(
						'font_family' => array(
							'hestia_headings_font'     => array(),
							'hestia_body_font'         => array(),
							'hestia_font_subsets'      => array(),
							'hestia_typography_upsell' => array(),
						),
						'font_sizes'  => array(
							'hestia_posts_and_pages_title' => array(),
							'hestia_header_titles_fs'      => array(),
							'hestia_post_page_headings_fs' => array(),
							'hestia_post_page_content_fs'  => array(),
							'hestia_frontpage_sections_title' => array(),
							'hestia_big_title_fs'          => array(),
							'hestia_section_primary_headings_fs' => array(),
							'hestia_section_secondary_headings_fs' => array(),
							'hestia_section_content_fs'    => array(),
							'hestia_generic_title'         => array(),
							'hestia_menu_fs'               => array(),
							'hestia_typography_upsell'     => array(),
						),
					),
				),
				'Hestia_Customize_Control_Tabs'
			)
		);
	}

	/**
	 *
	 * ---------------------------------
	 * 1.a. Headings font family control
	 * This control allows the user to choose a font family for all Headings used in the theme ( h1 - h6 )
	 * ---------------------------------
	 * 1.b. Body font family control
	 * This control allows the user to choose a font family for all elements in the body tag
	 * --------------------------------
	 */
	private function add_font_family_selectors() {

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_headings_font',
				array(
					'type'              => 'theme_mod',
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'label'    => esc_html__( 'Headings', 'hestia-pro' ) . ' ' . esc_html__( 'font family', 'hestia-pro' ),
					'section'  => 'hestia_typography',
					'priority' => 5,
					'type'     => 'select',
				),
				'Hestia_Font_Selector'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_body_font',
				array(
					'type'              => 'theme_mod',
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'label'    => esc_html__( 'Body', 'hestia-pro' ) . ' ' . esc_html__( 'font family', 'hestia-pro' ),
					'section'  => 'hestia_typography',
					'priority' => 10,
					'type'     => 'select',
				),
				'Hestia_Font_Selector'
			)
		);
	}

	/**
	 * This control allows the user to choose a subset for the font family ( for e.g. lating, cyrillic etc )
	 */
	private function add_font_subsets_control() {

		/**
		 * Font Subsets control
		 */
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_font_subsets',
				array(
					'sanitize_callback' => 'hestia_sanitize_array',
					'default'           => array( 'latin' ),
				),
				array(
					'section'  => 'hestia_typography',
					'label'    => esc_html__( 'Font Subsets', 'hestia-pro' ),
					'choices'  => array(
						'latin'        => 'latin',
						'latin-ext'    => 'latin-ext',
						'cyrillic'     => 'cyrillic',
						'cyrillic-ext' => 'cyrillic-ext',
						'greek'        => 'greek',
						'greek-ext'    => 'greek-ext',
						'vietnamese'   => 'vietnamese',
					),
					'priority' => 45,
				),
				'Hestia_Select_Multiple'
			)
		);
	}

	/**
	 * Add headings
	 */
	private function add_section_ui_headings() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_posts_and_pages_title',
				array(
					'sanitize_callback' => 'wp_kses',
				),
				array(
					'label'    => esc_html__( 'Posts & Pages', 'hestia-pro' ),
					'section'  => 'hestia_typography',
					'priority' => 100,
				),
				'Hestia_Customizer_Heading'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_frontpage_sections_title',
				array(
					'sanitize_callback' => 'wp_kses',
				),
				array(
					'label'    => esc_html__( 'Frontpage Sections', 'hestia-pro' ),
					'section'  => 'hestia_typography',
					'priority' => 200,
				),
				'Hestia_Customizer_Heading'
			)
		);
	}

	/**
	 * Font size controls for Posts & Pages
	 */
	private function add_posts_pages_controls() {
		/**
		 * Title control [Posts & Pages]
		 * This control allows the user to choose a font size for the main titles
		 * that appear in the header for pages and posts.
		 *
		 * The values area between -25 and +25 px.
		 */
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_header_titles_fs',
				array(
					'sanitize_callback' => 'hestia_sanitize_range_value',
					'default'           => apply_filters( 'hestia_header_titles_fs_default', 0 ),
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'       => esc_html__( 'Title', 'hestia-pro' ),
					'section'     => 'hestia_typography',
					'type'        => 'range-value',
					'input_attr'  => array(
						'min'  => - 25,
						'max'  => 25,
						'step' => 1,
					),
					'priority'    => 110,
					'media_query' => true,
					'sum_type'    => true,
				),
				'Hestia_Customizer_Range_Value_Control'
			)
		);

		/**
		 * Headings control [Posts & Pages]
		 *
		 * This control allows the user to choose a font size for all headings
		 * ( h1 - h6 ) from pages and posts.
		 *
		 * The values area between -25 and +25 px.
		 */
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_post_page_headings_fs',
				array(
					'sanitize_callback' => 'hestia_sanitize_range_value',
					'default'           => 0,
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'      => esc_html__( 'Headings', 'hestia-pro' ),
					'section'    => 'hestia_typography',
					'type'       => 'range-value',
					'input_attr' => array(
						'min'  => -25,
						'max'  => 25,
						'step' => 1,
					),
					'priority'   => 115,
					'sum_type'   => true,
				),
				'Hestia_Customizer_Range_Value_Control'
			)
		);

		/**
		 * Content control [Posts & Pages]
		 *
		 * This control allows the user to choose a font size for the main content
		 * area in pages and posts.
		 *
		 * The values area between -25 and +25 px.
		 */
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_post_page_content_fs',
				array(
					'sanitize_callback' => 'hestia_sanitize_range_value',
					'default'           => 0,
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'      => esc_html__( 'Content', 'hestia-pro' ),
					'section'    => 'hestia_typography',
					'type'       => 'range-value',
					'input_attr' => array(
						'min'  => -25,
						'max'  => 25,
						'step' => 1,
					),
					'priority'   => 120,
					'sum_type'   => true,
				),
				'Hestia_Customizer_Range_Value_Control'
			)
		);
	}

	/**
	 * Font size controls for Front Page
	 */
	private function add_front_page_controls() {
		/**
		 * Big Title Section / Header Slider font size control. [Front Page Sections]
		 *
		 * This is changing the big title/slider titles, the
		 * subtitle and the button in the big title section.
		 *
		 * The values are between -25 and +25 px.
		 */
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_big_title_fs',
				array(
					'sanitize_callback' => 'hestia_sanitize_range_value',
					'default'           => 0,
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'      => apply_filters( 'hestia_big_title_fs_label', esc_html__( 'Big Title Section', 'hestia-pro' ) ),
					'section'    => 'hestia_typography',
					'type'       => 'range-value',
					'input_attr' => array(
						'min'  => -25,
						'max'  => 25,
						'step' => 1,
					),
					'priority'   => 210,
					'sum_type'   => true,
				),
				'Hestia_Customizer_Range_Value_Control'
			)
		);

		/**
		 * Section Title [Front Page Sections]
		 *
		 * This control is changing sections titles and card titles
		 * The values are between -25 and +25 px.
		 */
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_section_primary_headings_fs',
				array(
					'sanitize_callback' => 'hestia_sanitize_range_value',
					'default'           => '0',
					'transport'         => 'postMessage',
				),
				array(
					'label'      => esc_html__( 'Section Title', 'hestia-pro' ),
					'section'    => 'hestia_typography',
					'type'       => 'range-value',
					'input_attr' => array(
						'min'  => -25,
						'max'  => 25,
						'step' => 1,
					),
					'priority'   => 215,
					'sum_type'   => true,
				),
				'Hestia_Customizer_Range_Value_Control'
			)
		);

		/**
		 * Subtitles control [Front Page Sections]
		 *
		 * This control allows the user to choose a font size
		 * for all Subtitles on Front Page sections.
		 * The values area between -25 and +25 px.
		 */
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_section_secondary_headings_fs',
				array(
					'sanitize_callback' => 'hestia_sanitize_range_value',
					'default'           => 0,
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'      => esc_html__( 'Section Subtitle', 'hestia-pro' ),
					'section'    => 'hestia_typography',
					'type'       => 'range-value',
					'input_attr' => array(
						'min'  => -25,
						'max'  => 25,
						'step' => 1,
					),
					'priority'   => 220,
					'sum_type'   => true,
				),
				'Hestia_Customizer_Range_Value_Control'
			)
		);

		/**
		 * Content control [Front Page Sections]
		 *
		 * This control allows the user to choose a font size
		 * for the Main content for Frontpage Sections
		 * The values area between -25 and +25 px.
		 */
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_section_content_fs',
				array(
					'sanitize_callback' => 'hestia_sanitize_range_value',
					'default'           => 0,
					'transport'         => 'postMessage',
				),
				array(
					'label'      => esc_html__( 'Content', 'hestia-pro' ),
					'section'    => 'hestia_typography',
					'type'       => 'range-value',
					'input_attr' => array(
						'min'  => -25,
						'max'  => 25,
						'step' => 1,
					),
					'priority'   => 225,
					'sum_type'   => true,
				),
				'Hestia_Customizer_Range_Value_Control'
			)
		);
	}

}
