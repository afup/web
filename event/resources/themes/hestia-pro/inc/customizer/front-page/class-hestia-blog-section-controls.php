<?php
/**
 * Blog section controls.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Blog_Section_Controls
 */
class Hestia_Blog_Section_Controls extends Hestia_Front_Page_Section_Controls_Abstract {
	/**
	 * Implement set_section_data from parent.
	 * Add section details
	 *
	 * @return array
	 */
	protected function set_section_data() {
		return array(
			'slug'     => 'blog',
			'title'    => esc_html__( 'Blog', 'hestia-pro' ),
			'priority' => 60,
		);
	}

	/**
	 * Add controls.
	 */
	public function add_controls() {
		$this->add_items_number_control();
	}

	/**
	 * Items number control.
	 */
	private function add_items_number_control() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_blog_items',
				array(
					'default'           => 3,
					'sanitize_callback' => 'absint',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'       => esc_html__( 'Number of Items', 'hestia-pro' ),
					'section'     => 'hestia_blog',
					'priority'    => 15,
					'type'        => 'number',
					'input_attrs' => array(
						'min' => 1,
					),
				),
				null,
				array(
					'selector'        => '.hestia-blog-content',
					'settings'        => 'hestia_blog_items',
					'render_callback' => array( $this, 'blog_content_callback' ),
				)
			)
		);
	}

	/**
	 * Render callback function
	 */
	public function blog_content_callback() {
		$blog_section = new Hestia_Blog_Section();
		$blog_section->blog_content();
	}


	/**
	 * Change necessary controls.
	 *
	 * @return void
	 */
	public function change_controls() {
		$this->change_customizer_object( 'setting', 'hestia_blog_title', 'default', esc_html__( 'Blog', 'hestia-pro' ) );
		$this->change_customizer_object( 'setting', 'hestia_blog_subtitle', 'default', esc_html__( 'Change this subtitle in the Customizer', 'hestia-pro' ) );
	}

}
