<?php
/**
 * Content 404 page
 *
 * @package Inc/Views/Main
 */

/**
 * Class Hestia_Content_404
 */
class Hestia_Content_404 extends Hestia_Abstract_Main {

	/**
	 * Init Content 404 view
	 */
	public function init() {
		add_action( 'hestia_do_404', array( $this, 'render_404_page' ) );
	}

	/**
	 * Render 404 page.
	 */
	public function render_404_page() {

		$default        = hestia_get_blog_layout_default();
		$sidebar_layout = apply_filters( 'hestia_sidebar_layout', get_theme_mod( 'hestia_blog_sidebar_layout', $default ) );
		$wrap_class     = apply_filters( 'hestia_filter_index_search_content_classes', 'col-md-8 blog-posts-wrap' );
		$layout_classes = hestia_layout();
		do_action( 'hestia_before_index_wrapper' );

		echo '<div class="' . esc_attr( $layout_classes ) . '">';
		echo '<div class="hestia-blogs" data-layout="' . esc_attr( $sidebar_layout ) . '">';
		echo '<div class="container">';
		do_action( 'hestia_before_index_posts_loop' );
		echo '<div class="row">';
		if ( $sidebar_layout === 'sidebar-left' ) {
			get_sidebar();
		}

		echo '<div class="' . esc_attr( $wrap_class ) . '">';
		do_action( 'hestia_before_index_content' );

		echo '<article id="post-0" class="section section-text">';
		echo '<div class="row">';
		echo '<div class="col-md-8 col-md-offset-2">';
		echo '<p>';
		esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'hestia-pro' );
		echo '</p>';
		get_search_form();
		echo '</div>';
		echo '</div>';
		echo '</article>';

		echo '</div>';

		if ( $sidebar_layout === 'sidebar-right' ) {
			get_sidebar();
		}

		echo '</div>';
		echo '</div>';
		echo '</div>';
		do_action( 'hestia_after_archive_content' );
	}

}
