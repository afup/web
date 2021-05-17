<?php
/**
 * Customizer blog settings controls.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Blog_Settings_Controls
 */
class Hestia_Blog_Settings_Controls extends Hestia_Register_Customizer_Controls {

	/**
	 * Add controls
	 */
	public function add_controls() {
		$this->add_blog_settings_section();
		$this->add_featured_posts_controls();
		$this->add_blog_layout_controls();
		$this->add_blog_post_content_controls();
	}

	/**
	 * Add blog settings section
	 */
	private function add_blog_settings_section() {
		$this->add_section(
			new Hestia_Customizer_Section(
				'hestia_blog_layout',
				array(
					'title'    => apply_filters( 'hestia_blog_layout_control_label', esc_html__( 'Blog Settings', 'hestia-pro' ) ),
					'priority' => 45,
				)
			)
		);
	}

	/**
	 * Add category dropdown control
	 */
	private function add_featured_posts_controls() {

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_featured_posts_label',
				array(
					'sanitize_callback' => 'wp_kses',
				),
				array(
					'label'    => esc_html__( 'Featured Posts', 'hestia-pro' ),
					'section'  => 'hestia_blog_layout',
					'priority' => 10,
				),
				'Hestia_Customizer_Heading'
			)
		);

		$options    = array(
			0 => ' -- ' . esc_html__( 'Disable section', 'hestia-pro' ) . ' -- ',
		);
		$categories = get_categories();
		if ( ! empty( $categories ) ) {
			foreach ( $categories as $category ) {
				$cat_id             = $category->term_id;
				$cat_name           = $category->name;
				$options[ $cat_id ] = $cat_name;
			}
		}

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_featured_posts_category',
				array(
					'sanitize_callback' => 'hestia_sanitize_array',
					'default'           => apply_filters( 'hestia_featured_posts_category_default', 0 ),
				),
				array(
					'type'     => 'select',
					'section'  => 'hestia_blog_layout',
					'label'    => esc_html__( 'Categories:', 'hestia-pro' ),
					'choices'  => $options,
					'priority' => 15,
				)
			)
		);
	}

	/**
	 * Add blog layout controls
	 */
	private function add_blog_layout_controls() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_alternative_blog_layout',
				array(
					'default'           => 'blog_normal_layout',
					'sanitize_callback' => 'hestia_sanitize_blog_layout_control',
				),
				array(
					'label'       => esc_html__( 'Blog', 'hestia-pro' ) . ' ' . esc_html__( 'Layout', 'hestia-pro' ),
					'section'     => 'hestia_blog_layout',
					'priority'    => 25,
					'choices'     => array(
						'blog_alternative_layout'  => array(
							'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqAgMAAAAjP0ATAAAACVBMVEX///8+yP/V1dXG9YqxAAAAS0lEQVRYw2NgGAXDE4RCQMDAKONahQ5WUKBs1AujXqDEC6NgiANRSDyH0EwZRvJZ1UCBslEvjHqBZl4YBYMUjNb1o14Y9cIoGH4AALJWvPSk+QsLAAAAAElFTkSuQmCC',
						),
						'blog_normal_layout'       => array(
							'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqAgMAAAAjP0ATAAAACVBMVEX///8+yP/V1dXG9YqxAAAAPklEQVR42mNgGAXDE4RCQMDAKONahQ5WUKBs1AujXqDEC6NgtOAazTKjXhgtuEbBaME1mutHvTBacI0C4gEAenW95O4Ccg4AAAAASUVORK5CYII=',
						),
						'blog_alternative_layout2' => array(
							'url'      => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAK4AAAB6CAMAAAARfZjlAAABMlBMVEVYyPpZyPppwOeGs8eaqrKkpqalpaWmpqaqqqqsrKytra2urq6vr6+xsbGx2OmysrKyur60tLS15/225/23t7e4uLi46P25ubm6urq7u7u8vLy+vr6/v7/AxsnCwsLDw8PExMTE6/3FxcXGxsbHx8fIyMjKysrLy8vMzMzOzs7Pz8/Q0NDR0dHS0tLT09PU1NTU8f7V1dXV8f7W1tbX8v7Y2NjZ2dnZ8/7b29vc3Nzd3d3f39/g4ODh4eHi4uLk5OTl5eXm5ubm9/7n5+fo6Ojo9/7p6enq6urq+P7r6+vr+P7s7Ozt7e3t+f7u7u7u+f7v7+/v+v/w8PDx8fHy8vLz8/P09PT19fX29vb2/P/39/f4+Pj5+fn5/f/6+vr6/f/7+/v8/Pz8/v/9/f3+/v7/////r5zuAAADZklEQVR4Ae2be1PaTBTGfQUO+lZta0svaoXWivXSWlulXoBKTVEKXuoFFCiGJvv9v0LnJDvdjCGzs3UzAT2/f3Ayz8TfHJ/dHQMMsQHi6Ih0bwHpki7pki7pki7pXj2T8k2kj+XpYyb4KY8fqun++G9IxmuRXh+Sss4EX+TxT6RLuqRLuqRLuqQbBOmSLumSLumSro/hWDwBGkjEY8Ph68ZAI6++B/NLTff6Yy8egVamw30skgXNZMPUrYB2NvcD6KjpWid+0qCd1FoAdTXdzpqfUdDOg3wATTVdc8tPErSTDLG7EAJh6pLudgCq3S36gRAIcWeAEKgFYKrpdg0/g9Zd0m0EYKnp2h0/g7bUSLcTgK1Yhoafe7LUpjbym/ncTMS6z02r62Cb1SlA9ljXxW7vPgTO7AVzuXgbqW7aZoI8XqkyDzlwyDNBMUrdGZt52BG6nBUMlZiXvYh1cUm2GNJ9zHUb9bp7pTUCsMh/TbnGHJYi1sWhzpt8vKhrjgHAks3/cXZ6a2awOh2nvxHrFgD4DAuuLk4ZoIBXFiCLEfslIC8svDbXB7rzXl1nS/jsTjeHL2VwMXgqct2MLXRNR/fUna7hviA8tRN9d+HEO910JvPOse0+5e3w5mvR6p5vbhXbzqk9i7pe9jzjRv7vYD5SXUEFbujWR6DHdE/6Q/d85IZuFSNld8WF393fB36Cdc1tAK5r51bPGC+puzPsgkup185wEMD17R9H99KtLCwvv38DLryq4xbfEGCFLzlkvCtGLQjx6XnQzsAR+27p7wHWcE5jbG/KOZkbEK1uoYcuH+8KAHxgiG3kDJshq/2oC1/xFpd4qcK8VCE63XRvXfzLjzk93XCPXoEBXiLvbhmLOoY/FfEeZ4AsNplLawki0FUnWzBKRjELELmuOuq6pEu6pEu6pEu6pJsA7SRC1I2DduIh6sZAOzFNuldPJn1MgHYmJgM4vGcfHUKm+/WDWfZlT+ZAI3OXwVi3fxsQyaZGk6CB5Ggqu4boe9dSwqlIN+TpBlOIK+vabSmWUtpmanH6amj/6Hb3pTQ9TZenO0wtfqeXmlnIy/DcsZWX0mJK8SYttX+EdM1tKRci3ZSnm0wtfqd3BqsmpS3SpjxtMrX4PVpqpEu6pEu6pEu6pEu6RwPEwOn+AUlsaWt9I8RTAAAAAElFTkSuQmCC',
							'redirect' => 'https://themeisle.com/themes/hestia-pro/upgrade?utm_medium=customizer&utm_source=image&utm_campaign=blogpro',
						),
					),
					'subcontrols' => array(
						'blog_alternative_layout' => array(),
						'blog_normal_layout'      => array(),
					),
				),
				'Hestia_Customize_Control_Radio_Image'
			)
		);
	}

	/**
	 * Add blog post content controls
	 */
	private function add_blog_post_content_controls() {

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_blog_settings_label',
				array(
					'sanitize_callback' => 'wp_kses',
				),
				array(
					'label'    => esc_html__( 'Blog Settings', 'hestia-pro' ),
					'section'  => 'hestia_blog_layout',
					'priority' => 20,
				),
				'Hestia_Customizer_Heading'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_disable_categories',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'default'           => 'one',
				),
				array(
					'type'     => 'select',
					'priority' => 40,
					'section'  => 'hestia_blog_layout',
					'label'    => esc_html__( 'Display', 'hestia-pro' ) . ' ' . esc_html__( 'Blog', 'hestia-pro' ) . ' ' . esc_html__( 'Categories:', 'hestia-pro' ),
					'choices'  => array(
						'none' => esc_html__( 'None', 'hestia-pro' ),
						'one'  => esc_html__( 'First', 'hestia-pro' ),
						'all'  => esc_html__( 'All', 'hestia-pro' ),
					),
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_blog_post_content_type',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'default'           => 'excerpt',
				),
				array(
					'priority'    => 45,
					'section'     => 'hestia_blog_layout',
					'label'       => esc_html__( 'Blog Post Content', 'hestia-pro' ),
					'choices'     => array(
						'excerpt' => esc_html__( 'Excerpt', 'hestia-pro' ),
						'content' => esc_html__( 'Content', 'hestia-pro' ),
					),
					'subcontrols' => array(
						'excerpt' => array(
							'hestia_excerpt_length',
						),
						'content' => array(),
					),
				),
				'Hestia_Select_Hiding'
			)
		);

		$excerpt_default = hestia_get_excerpt_default();
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_excerpt_length',
				array(
					'default'           => $excerpt_default,
					'sanitize_callback' => 'absint',
				),
				array(
					'label'    => esc_html__( 'Excerpt length', 'hestia-pro' ),
					'section'  => 'hestia_blog_layout',
					'priority' => 50,
					'type'     => 'number',
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_pagination_type',
				array(
					'default'           => 'number',
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'label'    => esc_html__( 'Post Pagination', 'hestia-pro' ),
					'section'  => 'hestia_blog_layout',
					'priority' => 55,
					'type'     => 'select',
					'choices'  => array(
						'number'   => esc_html__( 'Number', 'hestia-pro' ),
						'infinite' => esc_html__( 'Infinite Scroll', 'hestia-pro' ),
					),
				)
			)
		);

	}
}
