<?php
/**
 * Subscribe section on blog
 *
 * @package hestia
 */

/**
 * Class Hestia_Subscribe_Blog_Section
 */
class Hestia_Subscribe_Blog_Section extends Hestia_Abstract_Main {
	/**
	 * Initialization function for subscribe section on blog.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'hestia_after_archive_content', array( $this, 'render_subscribe_section' ), 20 );
	}

	/**
	 * Render function.
	 *
	 * @access public
	 * @return void
	 */
	public function render_subscribe_section() {

		$hestia_subscribe_title    = get_theme_mod( 'hestia_blog_subscribe_title', esc_html__( 'Subscribe to our Newsletter', 'hestia-pro' ) );
		$hestia_subscribe_subtitle = get_theme_mod( 'hestia_blog_subscribe_subtitle', esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'hestia-pro' ) );

		if ( empty( $hestia_subscribe_title ) && empty( $hestia_subscribe_subtitle ) && ! is_active_sidebar( 'blog-subscribe-widgets' ) ) {
			return;
		}

		echo '<section class="subscribe-line" id="subscribe-on-blog">';
			echo '<div class="container">';
				echo '<div class="row">';
					echo '<div class="col-md-6">';
		if ( ! empty( $hestia_subscribe_title ) || is_customize_preview() ) {
			echo '<h3 class="hestia-title">' . esc_html( $hestia_subscribe_title ) . '</h3>';
		}
		if ( ! empty( $hestia_subscribe_subtitle ) || is_customize_preview() ) {
			echo '<p class="description">' . esc_html( $hestia_subscribe_subtitle ) . '</p>';
		}
					echo '</div>';
					echo '<div class="col-md-6">';
						echo '<div class="card card-plain card-form-horizontal">';
							echo '<div class="content">';
									echo '<div class="row">';
		if ( is_active_sidebar( 'blog-subscribe-widgets' ) ) {
			dynamic_sidebar( 'blog-subscribe-widgets' );
		}
									echo '</div>';
							echo '</div>';
						echo '</div>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</section>';
	}
}
