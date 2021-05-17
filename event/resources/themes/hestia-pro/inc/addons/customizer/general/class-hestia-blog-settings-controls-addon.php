<?php
/**
 * Customizer general controls.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Blog_Settings_Controls_Addon
 */
class Hestia_Blog_Settings_Controls_Addon extends Hestia_Blog_Settings_Controls {

	/**
	 * Add controls
	 */
	public function add_controls() {
		parent::add_controls();
		$this->add_blog_settings_panel();
		$this->add_blog_general_controls();
		$this->add_blog_authors_section();
		$this->add_blog_authors_controls();
		$this->add_blog_subscribe_section();
		$this->add_blog_subscribe_controls();
		$this->add_blog_subscribe_info();
	}

	/**
	 * Add sidebar and container width controls.
	 */
	private function add_blog_settings_panel() {
		$this->add_panel(
			new Hestia_Customizer_Panel(
				'hestia_blog_settings',
				array(
					'priority' => 45,
					'title'    => esc_html__( 'Blog Settings', 'hestia-pro' ),
				)
			)
		);
	}

	/**
	 * Add blog settings in pro.
	 */
	private function add_blog_general_controls() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_grid_layout',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'default'           => '1',
				),
				array(
					'priority'    => 25,
					'section'     => 'hestia_blog_layout',
					'label'       => esc_html__( 'Grid Layout', 'hestia-pro' ),
					'choices'     => array(
						'1' => esc_html__( '1 Column', 'hestia-pro' ),
						'2' => esc_html__( '2 Columns', 'hestia-pro' ),
						'3' => esc_html__( '3 Columns', 'hestia-pro' ),
						'4' => esc_html__( '4 Columns', 'hestia-pro' ),
					),
					'subcontrols' => array(
						'1' => array(),
						'2' => array(
							'hestia_enable_masonry',
						),
						'3' => array(
							'hestia_enable_masonry',
						),
						'4' => array(
							'hestia_enable_masonry',
						),
					),
					'parent'      => 'hestia_alternative_blog_layout',
				),
				'Hestia_Select_Hiding'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_enable_masonry',
				array(
					'sanitize_callback' => 'hestia_sanitize_checkbox',
					'default'           => false,
				),
				array(
					'type'     => 'checkbox',
					'priority' => 30,
					'section'  => 'hestia_blog_layout',
					'label'    => esc_html__( 'Enable Masonry', 'hestia-pro' ),
				)
			)
		);
	}
	/**
	 * Blog authors section.
	 */
	private function add_blog_authors_section() {
		$this->add_section(
			new Hestia_Customizer_Section(
				'hestia_blog_authors',
				array(
					'title'    => esc_html__( 'Authors Section', 'hestia-pro' ),
					'panel'    => 'hestia_blog_settings',
					'priority' => 20,
				)
			)
		);
	}

	/**
	 * Blog authors controls.
	 */
	private function add_blog_authors_controls() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_authors_on_blog',
				array(
					'sanitize_callback' => 'hestia_sanitize_array',
				),
				array(
					'section'      => 'hestia_blog_authors',
					'description'  => wp_kses(
						__( 'Select the team members to appear at the bottom of the blog archive pages. Hold down <b>control / cmd</b> key to select multiple members. To deselect a member, click on it, while the control / cmd key is pressed.', 'hestia-pro' ),
						array(
							'b' => array(),
						)
					),
					'label'        => esc_html__( 'Team members to appear on blog page', 'hestia-pro' ),
					'choices'      => $this->get_team_on_blog(),
					'priority'     => 1,
					'custom_class' => 'repeater-multiselect-team',
				),
				'Hestia_Select_Multiple'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_authors_on_blog_background',
				array(
					'sanitize_callback' => 'esc_url_raw',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'    => esc_html__( 'Background Image', 'hestia-pro' ),
					'section'  => 'hestia_blog_authors',
					'priority' => 2,
				),
				'WP_Customize_Image_Control'
			)
		);
	}

	/**
	 * Blog subscribe section.
	 */
	private function add_blog_subscribe_section() {
		$this->add_section(
			new Hestia_Customizer_Section(
				'hestia_blog_subscribe',
				array(
					'title'    => esc_html__( 'Subscribe Section', 'hestia-pro' ),
					'panel'    => 'hestia_blog_settings',
					'priority' => 30,
				)
			)
		);
	}

	/**
	 * Blog subscribe controls.
	 */
	private function add_blog_subscribe_controls() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_blog_subscribe_title',
				array(
					'default'           => esc_html__( 'Subscribe to our Newsletter', 'hestia-pro' ),
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'    => esc_html__( 'Section Title', 'hestia-pro' ),
					'section'  => 'hestia_blog_subscribe',
					'priority' => 10,
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_blog_subscribe_subtitle',
				array(
					'default'           => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'hestia-pro' ),
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'    => esc_html__( 'Section Subtitle', 'hestia-pro' ),
					'section'  => 'hestia_blog_subscribe',
					'priority' => 15,
				)
			)
		);
	}

	/**
	 * Add blog subscribe info.
	 */
	private function add_blog_subscribe_info() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_blog_subscribe_info',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'label'      => esc_html__( 'Instructions', 'hestia-pro' ),
					'section'    => 'hestia_blog_subscribe',
					'capability' => 'install_plugins',
					'priority'   => 20,
				),
				'Hestia_Subscribe_Info'
			)
		);
	}

	/**
	 * Get choices for team on blog control.
	 *
	 * @since 1.1.40
	 */
	private function get_team_on_blog() {
		$result_array = array();

		$default             = Hestia_Defaults_Models::instance()->get_team_default();
		$hestia_team_content = get_theme_mod( 'hestia_team_content', $default );
		if ( ! empty( $hestia_team_content ) ) {
			$json = json_decode( $hestia_team_content, true );
			if ( empty( $json ) ) {
				__return_empty_array();
			}
			foreach ( $json as $team_member ) {
				if ( ! empty( $team_member['id'] ) && ! empty( $team_member['title'] ) ) {
					$result_array[ $team_member['id'] ] = $team_member['title'];
				}
			}
		}

		return $result_array;
	}

	/**
	 * Change controls that may need to be changed.
	 */
	public function change_controls() {
		$choices = array(
			'blog_alternative_layout'  => array(
				'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqAgMAAAAjP0ATAAAACVBMVEX///8+yP/V1dXG9YqxAAAAS0lEQVRYw2NgGAXDE4RCQMDAKONahQ5WUKBs1AujXqDEC6NgiANRSDyH0EwZRvJZ1UCBslEvjHqBZl4YBYMUjNb1o14Y9cIoGH4AALJWvPSk+QsLAAAAAElFTkSuQmCC',
			),
			'blog_normal_layout'       => array(
				'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqAgMAAAAjP0ATAAAACVBMVEX///8+yP/V1dXG9YqxAAAAPklEQVR42mNgGAXDE4RCQMDAKONahQ5WUKBs1AujXqDEC6NgtOAazTKjXhgtuEbBaME1mutHvTBacI0C4gEAenW95O4Ccg4AAAAASUVORK5CYII=',
			),
			'blog_alternative_layout2' => array(
				'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqCAMAAABpj1iyAAAACVBMVEUAyv/V1dX////o4eoDAAAAfUlEQVR42u3ZoQ0AMAgAQej+Q3cDCI6QQyNOvKGNt3KwsLCwsLB2sKKc4V6/iIWFhYWFhYWFhXWN5cQ4xcpyhos9K8tZytKW5CWvLclLXltYWFhYWFj+Ez0kYWFhYWFhYWFhYTkxrrGyHC/N2pK85LUleclrCwsLCwvrMOsDUDxdDThzw38AAAAASUVORK5CYII=',
			),
		);
		$this->change_customizer_object( 'control', 'hestia_alternative_blog_layout', 'choices', $choices );
		$this->change_customizer_object(
			'control',
			'hestia_alternative_blog_layout',
			'subcontrols',
			array(
				'blog_alternative_layout'  => array(),
				'blog_normal_layout'       => array(),
				'blog_alternative_layout2' => array(
					'hestia_grid_layout',
				),
			)
		);
		$this->change_customizer_object( 'section', 'hestia_blog_layout', 'panel', 'hestia_blog_settings' );
		$this->change_customizer_object( 'section', 'hestia_blog_layout', 'priority', 30 );
	}
}
