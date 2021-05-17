<?php
/**
 * Shop Controls Addon
 *
 * @package Hestia
 */

/**
 * Class Hestia_Blog_Section_Controls_Addon
 */
class Hestia_Blog_Section_Controls_Addon extends Hestia_Blog_Section_Controls {
	/**
	 * Add controls.
	 */
	public function add_controls() {
		parent::add_controls();

		$this->add_category_control();
	}

	/**
	 * Add category selector control.
	 */
	private function add_category_control() {

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_blog_categories',
				array(
					'sanitize_callback' => 'hestia_sanitize_array',
					'transport'         => $this->selective_refresh,
				),
				array(
					'section'  => 'hestia_blog',
					'label'    => esc_html__( 'Categories:', 'hestia-pro' ),
					'choices'  => $this->get_posts_categories(),
					'priority' => 20,
				),
				'Hestia_Select_Multiple',
				array(
					'selector'            => '.hestia-blog-content',
					'render_callback'     => array( $this, 'blog_content_callback' ),
					'container_inclusive' => true,
				)
			)
		);
	}

	/**
	 * Get product categories.
	 *
	 * @return array
	 */
	private function get_posts_categories() {

		$categories_array = array();
		$categories       = get_categories(
			array(
				'taxonomy'   => 'category',
				'hide_empty' => 1,
				'title_li'   => '',
			)
		);
		if ( empty( $categories ) ) {
			return array();
		}
		foreach ( $categories as $category ) {
			if ( ! empty( $category->term_id ) && ! empty( $category->name ) ) {
				$categories_array[ $category->term_id ] = $category->name;
			}
		}

		return $categories_array;
	}
}
