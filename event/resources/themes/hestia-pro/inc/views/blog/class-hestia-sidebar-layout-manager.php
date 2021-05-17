<?php
/**
 * Hestia Sidebar Layout Manager.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Sidebar_Layout_Manager
 */
class Hestia_Sidebar_Layout_Manager extends Hestia_Abstract_Main {
	/**
	 * Init layout manager.
	 */
	public function init() {
		// Single Post
		add_filter( 'hestia_filter_single_post_content_classes', array( $this, 'post_content_classes' ) );

		// Page
		add_filter( 'hestia_filter_page_content_classes', array( $this, 'page_content_classes' ) );
		add_action( 'hestia_page_sidebar', array( $this, 'render_page_sidebar' ) );

		// Index and search
		add_filter( 'hestia_filter_index_search_content_classes', array( $this, 'index_search_content_classes' ) );

		// Archive
		add_filter( 'hestia_filter_archive_content_classes', array( $this, 'index_search_content_classes' ) );

		// Blog Sidebar
		add_filter( 'hestia_filter_blog_sidebar_classes', array( $this, 'blog_sidebar_classes' ) );

		// Shop Sidebar.
		add_filter( 'hestia_filter_woocommerce_content_classes', array( $this, 'content_classes' ) );

		// Sidebar layout filter.
		add_filter( 'hestia_sidebar_layout', array( $this, 'get_sidebar_layout' ) );
	}


	/**
	 * Get the sidebar layout.
	 *
	 * @param string $layout Post layout.
	 *
	 * @return mixed|string
	 */
	public function get_sidebar_layout( $layout ) {

		$pid          = hestia_get_current_page_id();
		$account_page = get_option( 'woocommerce_myaccount_page_id' );
		if ( ! empty( $account_page ) && (int) $pid === (int) $account_page ) {
			return 'full-width';
		}

		if ( empty( $pid ) ) {
			return $layout;
		}

		$individual_layout = get_post_meta( $pid, 'hestia_layout_select', true );
		if ( ! empty( $individual_layout ) && $individual_layout !== 'default' ) {
			return $individual_layout;
		}

		return $layout;
	}


	/**
	 * Page content classes.
	 *
	 * @param string $classes page content classes.
	 *
	 * @return string
	 */
	public function page_content_classes( $classes ) {

		if ( class_exists( 'WooCommerce', false ) && ( is_cart() || is_checkout() ) ) {
			return 'col-md-12';
		}

		$sidebar_layout = apply_filters( 'hestia_sidebar_layout', get_theme_mod( 'hestia_page_sidebar_layout', 'full-width' ) );

		if ( $sidebar_layout === 'full-width' ) {
			return $classes . ' col-md-offset-2';
		}

		return is_customize_preview() ?
			$classes :
			( is_active_sidebar( 'sidebar-1' ) ?
				$classes :
				$classes . ' col-md-offset-2' );
	}

	/**
	 * Post content classes.
	 *
	 * @param string $classes post content classes.
	 *
	 * @return string
	 */
	public function post_content_classes( $classes ) {
		$default        = hestia_get_blog_layout_default();
		$sidebar_layout = apply_filters( 'hestia_sidebar_layout', get_theme_mod( 'hestia_blog_sidebar_layout', $default ) );

		$offset               = 'col-md-offset-1';
		$hestia_sidebar_width = get_theme_mod( 'hestia_sidebar_width', 25 );
		if ( $hestia_sidebar_width >= 80 || $hestia_sidebar_width <= 3 ) {
			$offset = '';
		}
		if ( $sidebar_layout === 'full-width' ) {
			return $classes . ' col-md-offset-2';
		}

		if ( $sidebar_layout === 'sidebar-left' ) {
			return is_customize_preview() ?
				$classes . ' ' . esc_attr( $offset ) :
				( is_active_sidebar( 'sidebar-1' ) ?
					$classes . ' ' . esc_attr( $offset ) :
					$classes . ' col-md-offset-2' );
		}

		if ( $sidebar_layout === 'sidebar-right' ) {
			return is_customize_preview() ?
				$classes :
				( is_active_sidebar( 'sidebar-1' ) ?
					$classes :
					$classes . ' col-md-offset-2' );
		}

		return $classes;
	}

	/**
	 * Index content classes.
	 *
	 * @param string $classes index classes.
	 *
	 * @return string
	 */
	public function index_search_content_classes( $classes ) {
		$default        = hestia_get_blog_layout_default();
		$sidebar_layout = apply_filters( 'hestia_sidebar_layout', get_theme_mod( 'hestia_blog_sidebar_layout', $default ) );

		$offset               = 'col-md-offset-1';
		$hestia_sidebar_width = get_theme_mod( 'hestia_sidebar_width', 25 );
		if ( $hestia_sidebar_width >= 80 || $hestia_sidebar_width <= 3 ) {
			$offset = '';
		}

		if ( $sidebar_layout === 'full-width' ) {
			return 'col-md-10 col-md-offset-1 blog-posts-wrap';
		}

		if ( $sidebar_layout === 'sidebar-left' ) {
			return is_customize_preview() ?
				$classes . ' ' . esc_attr( $offset ) :
				( is_active_sidebar( 'sidebar-1' ) ?
					$classes . ' ' . esc_attr( $offset ) :
					'col-md-10 col-md-offset-1 blog-posts-wrap' );
		}

		if ( $sidebar_layout === 'sidebar-right' ) {
			return is_customize_preview() ?
				$classes :
				( is_active_sidebar( 'sidebar-1' ) ?
					$classes :
					'col-md-10 col-md-offset-1 blog-posts-wrap' );
		}

		return $classes;
	}

	/**
	 * Adjust blog sidebar classes.
	 *
	 * @param string $classes the classes for blog sidebar.
	 *
	 * @return string
	 */
	public function blog_sidebar_classes( $classes ) {
		if ( is_page() ) {
			return $classes;
		}

		$default        = hestia_get_blog_layout_default();
		$sidebar_layout = apply_filters( 'hestia_sidebar_layout', get_theme_mod( 'hestia_blog_sidebar_layout', $default ) );

		if ( $sidebar_layout === 'sidebar-left' ) {
			return $classes;
		}

		if ( function_exists( 'is_bbpress' ) && is_bbpress() ) {
			return $classes;
		}

		if ( function_exists( 'is_buddypress' ) && is_buddypress() ) {
			return $classes;
		}

		$offset               = 'col-md-offset-1';
		$hestia_sidebar_width = get_theme_mod( 'hestia_sidebar_width', 25 );
		if ( $hestia_sidebar_width >= 80 || $hestia_sidebar_width <= 3 ) {
			$offset = '';
		}

		$classes .= ' ' . $offset;

		return $classes;
	}

	/**
	 * Render the page sidebar.
	 */
	public function render_page_sidebar() {
		if ( class_exists( 'WooCommerce', false ) ) {
			if ( is_cart() || is_checkout() || is_account_page() ) {
				return;
			}
			if ( is_shop() ) {
				get_sidebar( 'woocommerce' );

				return;
			}
		}
		get_sidebar();

		return;
	}

	/**
	 * Post content classes.
	 *
	 * @param string $classes post content classes.
	 *
	 * @return string
	 */
	public function content_classes( $classes ) {
		if ( ! class_exists( 'WooCommerce', false ) ) {
			return $classes;
		}

		$sidebar_layout = $this->get_page_sidebar_layout();
		if ( is_product() ) {
			return 'col-md-12';
		}

		if ( $this->should_have_sidebar() ) {
			$classes = 'content-' . $sidebar_layout . ' col-md-9';
		}

		return $classes;
	}

	/**
	 * Change shop columns when we have a shop sidebar.
	 */
	public function sidebar_columns() {
		return apply_filters( 'hestia_shop_loop_columns', 3 ); // 3 products per row
	}

	/**
	 * Utility to check if should have sidebar.
	 *
	 * @return bool
	 */
	private function should_have_sidebar() {
		$page_sidebar = $this->get_page_sidebar_layout();
		if ( is_customize_preview() && $page_sidebar !== 'full-width' && $page_sidebar !== 'off-canvas' ) {
			return true;
		}
		if ( is_active_sidebar( 'sidebar-woocommerce' ) && $page_sidebar !== 'full-width' && $page_sidebar !== 'off-canvas' ) {
			return true;
		}

		return false;
	}

	/**
	 * Get the sidebar layout.
	 *
	 * @return mixed|string
	 */
	public function get_page_sidebar_layout() {
		if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) {
			return hestia_get_shop_sidebar_layout();
		}

		return apply_filters( 'hestia_sidebar_layout', get_theme_mod( 'hestia_page_sidebar_layout', 'full-width' ) );
	}
}
