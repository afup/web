<?php
/**
 * Hestia Header Layout Manager.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Header_Layout_Manager
 */
class Hestia_Header_Layout_Manager extends Hestia_Abstract_Main {
	/**
	 * Init layout manager.
	 */
	public function init() {

		add_filter( 'get_the_archive_title', array( $this, 'filter_archive_title' ) );
		add_filter( 'hestia_header_layout', array( $this, 'get_header_layout' ) );

		// Single Post.
		add_action( 'hestia_before_single_post_wrapper', array( $this, 'post_page_header' ) );
		add_action( 'hestia_before_single_post_content', array( $this, 'post_page_before_content' ) );

		// Page.
		add_action( 'hestia_before_single_page_wrapper', array( $this, 'post_page_header' ) );
		add_action( 'hestia_before_page_content', array( $this, 'post_page_before_content' ) );

		// Index.
		add_action( 'hestia_before_index_wrapper', array( $this, 'post_page_header' ) );
		add_action( 'hestia_before_index_posts_loop', array( $this, 'maybe_render_header' ), 0 );
		add_action( 'hestia_before_index_content', array( $this, 'maybe_render_header' ), 0 );

		// Search.
		add_action( 'hestia_before_search_wrapper', array( $this, 'post_page_header' ) );
		add_action( 'hestia_before_search_content', array( $this, 'post_page_before_content' ) );

		// Attachment.
		add_action( 'hestia_before_attachment_wrapper', array( $this, 'generic_header' ) );
		// Archive.
		add_action( 'hestia_before_archive_content', array( $this, 'generic_header' ) );

		add_filter( 'body_class', array( $this, 'header_layout_body_class' ) );

		add_action( 'hestia_before_single_post_wrapper', array( $this, 'do_page_header' ), 1 );
		add_action( 'hestia_before_single_page_wrapper', array( $this, 'do_page_header' ), 1 );
		add_action( 'hestia_before_index_wrapper', array( $this, 'do_page_header' ), 1 );
		add_action( 'hestia_before_search_wrapper', array( $this, 'do_page_header' ), 1 );
		add_action( 'hestia_before_attachment_wrapper', array( $this, 'do_page_header' ), 1 );
		add_action( 'hestia_before_archive_content', array( $this, 'do_page_header' ), 1 );
		add_action( 'hestia_before_woocommerce_wrapper', array( $this, 'do_page_header' ), 1 );
	}

	/**
	 * Do page header hook.
	 */
	public function do_page_header() {
		$hook_name = current_filter();
		ob_start();
		do_action( 'hestia_do_page_header' );
		$markup = ob_get_clean();

		if ( ! empty( $markup ) ) {
			remove_all_actions( $hook_name );
			echo $markup;
		}
	}

	/**
	 * Remove "Category:", "Tag:", "Author:" from the archive title.
	 *
	 * @param string $title Archive title.
	 *
	 * @return string
	 */
	public function filter_archive_title( $title ) {
		if ( is_category() ) {
			$title = single_cat_title( '', false );
		} elseif ( is_tag() ) {
			$title = single_tag_title( '', false );
		} elseif ( is_author() ) {
			$title = '<span class="vcard">' . get_the_author() . '</span>';
		} elseif ( is_year() ) {
			$title = get_the_date( 'Y' );
		} elseif ( is_month() ) {
			$title = get_the_date( 'F Y' );
		} elseif ( is_day() ) {
			$title = get_the_date( 'F j, Y' );
		} elseif ( is_post_type_archive() ) {
			$title = post_type_archive_title( '', false );
		} elseif ( is_tax() ) {
			$title = single_term_title( '', false );
		}

		return $title;
	}


	/**
	 * Get page specific layout or the customizer setting.
	 *
	 * @param string $layout Header layout.
	 *
	 * @return string
	 */
	public function get_header_layout( $layout ) {
		$page_id                    = hestia_get_current_page_id();
		$frontpage_id               = get_option( 'page_on_front' );
		$page_template              = get_page_template_slug( $page_id );
		$disable_frontpage_sections = get_theme_mod( 'disable_frontpage_sections', false );

		/**
		 * Is Hestia frontpage layout.
		 */
		if ( (int) $frontpage_id === (int) $page_id && empty( $page_template ) && $disable_frontpage_sections !== true ) {
			$layout = 'default';
		}

		/**
		 * If it's blog, default will be 'default'
		 */
		if ( ( is_home() && is_front_page() ) || is_archive() ) {
			$layout = 'default';
		}

		if ( get_post_type() === 'jetpack-portfolio' ) {
			$layout = 'default';
		}

		/**
		 * By default, get value from customizer. If it's cart or checkout, the default will be no-content.
		 */
		if ( class_exists( 'WooCommerce', false ) ) {

			if ( is_cart() || is_checkout() ) {
				$layout = 'no-content';
			}

			if ( is_shop() ) {
				$layout = 'default';
			}

			if ( is_product() ) {
				$layout = get_theme_mod( 'hestia_product_layout', 'no-content' );

			}
		}

		/**
		 * Try to get individual layout.
		 */
		$individual_layout = $this->should_use_individual_layout() ? get_post_meta( $page_id, 'hestia_header_layout', true ) : '';

		return ! empty( $individual_layout ) ? $individual_layout : $layout;
	}

	/**
	 * Check if we should use the individual layout.
	 *
	 * @return bool
	 */
	private function should_use_individual_layout() {
		if ( is_singular() ) {
			return true;
		}

		$page_id = hestia_get_current_page_id();

		$page_for_posts = get_option( 'page_for_posts' );
		if ( $page_for_posts === $page_id ) {
			return true;
		}

		if ( class_exists( 'WooCommerce', false ) ) {
			$shop = get_option( 'woocommerce_shop_page_id' );
			if ( $shop === $page_id ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * The single post header.
	 */
	public function post_page_header() {
		$layout = apply_filters( 'hestia_header_layout', get_theme_mod( 'hestia_header_layout', 'default' ) );
		if ( 'classic-blog' === $layout ) {
			add_filter( 'hestia_boxed_layout', '__return_empty_string' );

			return;
		}
		$this->display_header( $layout, 'post' );
	}

	/**
	 * Single post before content.
	 * This function display the title in page if layout is not default.
	 */
	public function post_page_before_content() {
		$layout = apply_filters( 'hestia_header_layout', get_theme_mod( 'hestia_header_layout', 'default' ) );
		if ( 'default' === $layout ) {
			return;
		}
		echo $this->render_header( $layout );
	}

	/**
	 * Display page header on single page and on full width page template.
	 *
	 * @param string $layout header layout.
	 * @param string $type   post / page / other.
	 */
	private function display_header( $layout, $type ) {
		echo '<div id="primary" class="' . esc_attr( $this->boxed_page_layout_class() ) . ' page-header header-small" data-parallax="active" >';

		switch ( $type ) {
			case 'post':
			case 'page':
				if ( 'no-content' !== $layout ) {
					echo $this->render_header( $layout );
				}
				break;
			case 'generic':
				echo $this->render_header( $layout );
				break;
		}

		$this->render_header_background();
		echo '</div>';
	}

	/**
	 * Decide if header should be before featured post or before content.
	 */
	public function maybe_render_header() {
		$hook = current_action();
		if ( 'hestia_before_index_posts_loop' === $hook && false !== hestia_featured_posts_enabled() ) {
			$this->post_page_before_content();
		}
		if ( 'hestia_before_index_content' === $hook && false === hestia_featured_posts_enabled() ) {
			$this->post_page_before_content();
		}
	}

	/**
	 * Get classic blog header class.
	 *
	 * @return string
	 */
	private function get_classic_header_class() {
		$default        = hestia_get_blog_layout_default();
		$sidebar_layout = apply_filters( 'hestia_sidebar_layout', get_theme_mod( 'hestia_blog_sidebar_layout', $default ) );

		if ( class_exists( 'WooCommerce', false ) && is_shop() ) {
			return 'col-md-12';
		}

		if ( get_post_type() === 'jetpack-portfolio' ) {
			return 'col-md-12';
		}

		if ( is_404() ) {
			return 'col-md-10 col-md-offset-2';
		}
		if ( is_page() ) {
			return 'col-md-12';
		}

		if ( is_active_sidebar( 'sidebar-1' ) ) {
			return 'col-md-12';
		}

		if ( 'full-width' !== $sidebar_layout && is_customize_preview() && ! is_active_sidebar( 'sidebar-1' ) ) {
			return 'col-md-12';
		}

		if ( ( 'full-width' === $sidebar_layout || ! is_active_sidebar( 'sidebar-1' ) ) && false === hestia_featured_posts_enabled() ) {
			return 'col-md-12';
		}

		return 'col-md-10 col-md-offset-1';
	}

	/**
	 * Display header content based on layout.
	 *
	 * @param string $layout header layout.
	 *
	 * @return string
	 */
	private function render_header( $layout ) {
		if ( is_attachment() ) {
			$layout = 'default';
		}
		$header_output = '';
		if ( 'default' !== $layout ) {
			$header_output .= '	<div class="row"><div class="' . $this->get_classic_header_class() . '">';
		}
		$header_output .= $this->header_content( $layout );
		$header_output .= $this->maybe_render_post_meta( $layout );

		if ( 'classic-blog' === $layout ) {
			$header_output .= $this->add_image_in_content();
		}
		if ( 'default' !== $layout ) {
			$header_output .= '</div></div>';
		}
		if ( 'default' === $layout ) {
			$header_output = '<div class="container"><div class="row"><div class="col-md-10 col-md-offset-1 text-center">' . $header_output . '</div></div></div>';
		}

		return $header_output;
	}

	/**
	 * Add image in content for classic-blog layout.
	 *
	 * @return string
	 */
	private function add_image_in_content() {
		if ( class_exists( 'WooCommerce', false ) && ( is_product() || is_cart() || is_checkout() ) ) {
			return '';
		}
		$image_url = $this->get_page_background();
		if ( empty( $image_url ) ) {
			return '';
		}

		$image_id   = attachment_url_to_postid( $image_url );
		$image1_alt = '';
		if ( $image_id ) {
			$image1_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
		}

		$image_markup = '<img class="wp-post-image image-in-page" src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $image1_alt ) . '">';
		if ( class_exists( 'WooCommerce', false ) && is_shop() ) {
			$image_markup = '<div class="col-md-12 image-in-page-wrapper">' . $image_markup . '</div>';
		}

		return $image_markup;
	}

	/**
	 * Header content display.
	 *
	 * @param string $header_layout the header layout.
	 */
	private function header_content( $header_layout ) {

		$title_class = 'hestia-title';

		if ( 'default' !== $header_layout ) {
			$title_class .= ' title-in-content';
		}
		if ( is_404() ) {
			$header_content_output = '<h1 class="hestia-title">' . esc_html__( 'Oops! That page can&rsquo;t be found.', 'hestia-pro' ) . '</h1>';

			return $header_content_output;
		}
		if ( is_archive() ) {
			$title                 = get_the_archive_title();
			$header_content_output = '';

			if ( ! empty( $title ) ) {
				$header_content_output .= '<h1 class="hestia-title">' . $title . '</h1>';
			}

			$description = get_the_archive_description();
			if ( $description ) {
				$header_content_output .= '<h5 class="description">' . $description . '</h5>';
			}

			return $header_content_output;
		}
		if ( is_search() ) {
			$header_content_output = '<h1 class="' . esc_attr( $title_class ) . '">';
			/* translators: search result */
			$header_content_output .= sprintf( esc_html__( 'Search Results for: %s', 'hestia-pro' ), get_search_query() );
			$header_content_output .= '</h1>';

			return $header_content_output;
		}

		$disabled_frontpage = get_theme_mod( 'disable_frontpage_sections', false );
		if ( is_front_page() && get_option( 'show_on_front' ) === 'page' && true === (bool) $disabled_frontpage ) {
			$header_content_output = '<h1 class="' . esc_attr( $title_class ) . '">';

			$header_content_output .= single_post_title( '', false );
			$header_content_output .= '</h1>';

			return $header_content_output;
		}

		if ( is_front_page() && get_option( 'show_on_front' ) === 'posts' ) {
			$header_content_output = '<h1 class="' . esc_attr( $title_class ) . '">';

			$header_content_output .= get_bloginfo( 'description' );
			$header_content_output .= '</h1>';

			return $header_content_output;
		}

		if ( is_home() ) {
			$header_content_output = '<h1 class="' . esc_attr( $title_class ) . '">' . single_post_title( '', false ) . '</h1>';

			return $header_content_output;
		}

		$entry_class = '';
		if ( ! is_page() ) {
			$entry_class = 'entry-title';
		}
		$header_content_output = '<h1 class="' . esc_attr( $title_class ) . ' ' . esc_attr( $entry_class ) . '">' . wp_kses_post( get_the_title() ) . '</h1>';

		return $header_content_output;
	}

	/**
	 * Check if post meta should be displayed.
	 *
	 * @param string $header_layout the header layout.
	 */
	private function maybe_render_post_meta( $header_layout ) {
		if ( ! is_single() ) {
			return '';
		}

		if ( class_exists( 'WooCommerce', false ) ) {
			if ( is_product() ) {
				return '';
			}
		}

		global $post;
		$post_meta_output = '';
		$author_id        = $post->post_author;
		$author_name      = get_the_author_meta( 'display_name', $author_id );
		$author_posts_url = get_author_posts_url( get_the_author_meta( 'ID', $author_id ) );

		if ( 'default' === $header_layout ) {
			$post_meta_output .= '<h4 class="author">';
		} else {
			$post_meta_output .= '<p class="author meta-in-content">';
		}

		$post_meta_output .= apply_filters(
			'hestia_single_post_meta',
			sprintf(
				/* translators: %1$s is Author name wrapped, %2$s is Date*/
				esc_html__( 'Published by %1$s on %2$s', 'hestia-pro' ),
				/* translators: %1$s is Author name, %2$s is Author link*/
				sprintf(
					'<a href="%2$s" class="vcard author"><strong class="fn">%1$s</strong></a>',
					esc_html( $author_name ),
					esc_url( $author_posts_url )
				),
				$this->get_time_tags()
			)
		);
		if ( 'default' === $header_layout ) {
			$post_meta_output .= '</h4>';
		} else {
			$post_meta_output .= '</p>';
		}

		return $post_meta_output;
	}

	/**
	 * Get <time> tags.
	 *
	 * @return string
	 */
	private function get_time_tags() {
		$time = '';

		$time .= '<time class="entry-date published" datetime="' . esc_attr( get_the_date( 'c' ) ) . '" content="' . esc_attr( get_the_date( 'Y-m-d' ) ) . '">';
		$time .= esc_html( get_the_time( get_option( 'date_format' ) ) );
		$time .= '</time>';
		if ( get_the_time( 'U' ) === get_the_modified_time( 'U' ) ) {
			return $time;
		}
		$time .= '<time class="updated hestia-hidden" datetime="' . esc_attr( get_the_modified_date( 'c' ) ) . '">';
		$time .= esc_html( get_the_time( get_option( 'date_format' ) ) );
		$time .= '</time>';

		return $time;
	}

	/**
	 * Add the class to account for boxed page layout.
	 *
	 * @return string
	 */
	private function boxed_page_layout_class() {
		$layout = get_theme_mod( 'hestia_general_layout', apply_filters( 'hestia_boxed_layout_default', 1 ) );

		if ( isset( $layout ) && true === (bool) $layout ) {
			return 'boxed-layout-header';
		}

		return '';
	}


	/**
	 * Render the header background div.
	 */
	private function render_header_background() {
		$background_image            = apply_filters( 'hestia_header_image_filter', $this->get_page_background() );
		$customizer_background_image = get_background_image();

		$header_filter_div = '<div class="header-filter';

		/* Header Image */
		if ( ! empty( $background_image ) ) {
			$header_filter_div .= '" style="background-image: url(' . esc_url( $background_image ) . ');"';
			/* Gradient Color */
		} elseif ( empty( $customizer_background_image ) ) {
			$header_filter_div .= ' header-filter-gradient"';
			/* Background Image */
		} else {
			$header_filter_div .= '"';
		}
		$header_filter_div .= '></div>';

		echo apply_filters( 'hestia_header_wrapper_background_filter', $header_filter_div );

	}


	/**
	 * Get header background image for single page.
	 *
	 * @return false|string
	 */
	private function get_post_page_background() {

		if ( class_exists( 'WooCommerce', false ) && is_product() ) {
			return false;
		}

		if ( is_archive() ) {
			return false;
		}

		$pid = hestia_get_current_page_id();
		if ( empty( $pid ) ) {
			return false;
		}

		// Get featured image.
		$thumb_tmp = get_the_post_thumbnail_url( $pid );
		if ( is_home() && 'page' === get_option( '`show_on_front`' ) ) {
			$page_for_posts_id = get_option( 'page_for_posts' );
			if ( ! empty( $page_for_posts_id ) ) {
				$thumb_tmp = get_the_post_thumbnail_url( $page_for_posts_id );
			}
		}

		return $thumb_tmp;
	}


	/**
	 *  Handle Pages and Posts Header image.
	 *  Single Product: Product Category Image > Header Image > Gradient
	 *  Product Category: Product Category Image > Header Image > Gradient
	 *  Shop Page: Shop Page Featured Image > Header Image > Gradient
	 *  Blog Page: Page Featured Image > Header Image > Gradient
	 *  Single Post: Featured Image > Gradient
	 */
	private function get_page_background() {
		// Default header image.
		$thumbnail                 = get_header_image();
		$use_header_image_sitewide = get_theme_mod( 'hestia_header_image_sitewide', false );
		// If the option to use Header Image Sitewide is enabled, return header image and exit function.
		if ( true === (bool) $use_header_image_sitewide ) {
			return esc_url( $thumbnail );
		}

		$thumbnail = $this->get_post_page_background();
		if ( ! empty( $thumbnail ) ) {
			return esc_url( $thumbnail );
		}

		return esc_url( get_header_image() );

	}

	/**
	 * Generic header used for index | search | attachment | WooCommerce.
	 */
	public function generic_header() {
		$this->display_header( 'default', 'generic' );
	}

	/**
	 * Add header body classes.
	 *
	 * @param array $classes body classes.
	 *
	 * @return array
	 */
	public function header_layout_body_class( $classes ) {
		$layout = apply_filters( 'hestia_header_layout', get_theme_mod( 'hestia_header_layout', 'default' ) );

		$classes[] = 'header-layout-' . $layout;

		return $classes;
	}
}
