<?php
/**
 * General inline style for Pro Version.
 *
 * @package Hestia
 */

/**
 * Class Hestia_General_Inline_Style
 */
class Hestia_General_Inline_Style extends Hestia_Inline_Style_Manager {

	/**
	 * Add all the hooks necessary.
	 */
	public function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'add_inline_styles' ) );
	}

	/**
	 * Add inline styles
	 */
	public function add_inline_styles() {
		wp_add_inline_style( 'hestia_style', $this->layout_inline_style_addon() );
		wp_add_inline_style( 'hestia_style', $this->sidebar_width_style_addon() );
	}

	/**
	 * Layout inline style addon.
	 *
	 * @return string
	 */
	private function layout_inline_style_addon() {
		$custom_css = '';

		/**
		 * Container width.
		 */
		$custom_css .= $this->get_inline_style( 'hestia_container_width', array( $this, 'get_container_width_style' ) );

		return $custom_css;
	}

	/**
	 * Adds inline style for sidebar width
	 *
	 * @since 1.1.31
	 */
	private function sidebar_width_style_addon() {

		$default             = hestia_get_blog_layout_default();
		$blog_sidebar_layout = apply_filters( 'hestia_sidebar_layout', get_theme_mod( 'hestia_blog_sidebar_layout', $default ) );
		$custom_css          = $this->sidebar_style( $blog_sidebar_layout );
		if ( is_page() || ( function_exists( 'is_shop' ) && is_shop() ) ) {
			$page_sidebar_layout = apply_filters( 'hestia_sidebar_layout', get_theme_mod( 'hestia_page_sidebar_layout', 'full-width' ) );
			$custom_css          = $this->sidebar_style( $page_sidebar_layout );
		}

		$custom_css .= $this->get_shop_sidebar_style();

		return $custom_css;
	}

	/**
	 * Get shop sidebar inline style
	 *
	 * @return string
	 */
	private function get_shop_sidebar_style() {
		$css = '';
		if ( ! class_exists( 'WooCommerce' ) ) {
			return $css;
		}
		if ( ! is_active_sidebar( 'sidebar-woocommerce' ) ) {
			return $css;
		}

		if ( ! is_shop() && ! is_product_category() && ! is_product_tag() ) {
			return $css;
		}

		$sidebar_width = get_theme_mod( 'hestia_sidebar_width', 25 );
		if ( empty( $sidebar_width ) ) {
			return $css;
		}

		$hestia_shop_sidebar_layout = apply_filters( 'hestia_sidebar_layout', get_theme_mod( 'hestia_shop_sidebar_layout', Hestia_General_Controls::get_shop_sidebar_layout_default() ) );
		if ( $hestia_shop_sidebar_layout === 'full-width' ) {
			return $css;
		}

		$hestia_content_width = 100 - $sidebar_width;
		if ( $sidebar_width <= 3 || $sidebar_width >= 80 ) {
			$hestia_content_width = 100;
			$sidebar_width        = 100;
		}

		if ( $hestia_shop_sidebar_layout === 'off-canvas' ) {
			$css .= '
			@media (min-width: 992px){
				.shop-sidebar-wrapper{
					width: ' . (float) $sidebar_width . '%;
				}
			}';
			return $css;
		}

		$css = '
		@media (min-width: 992px){
			.shop-sidebar.card.card-raised.col-md-3, .shop-sidebar-wrapper {
				width: ' . (float) $sidebar_width . '%;
			}
			.content-sidebar-left,
			.content-sidebar-right{
				width: ' . (float) $hestia_content_width . '%;
			}
		}';

		return $css;
	}

	/**
	 * Add inline style for sidebar width.
	 *
	 * @param string $layout Page layout.
	 *
	 * @return string
	 */
	public function sidebar_style( $layout ) {
		$sidebar_width = get_theme_mod( 'hestia_sidebar_width', 25 );
		if ( empty( $sidebar_width ) ) {
			return '';
		}

		$custom_css = '';

		if ( $layout !== 'full-width' && is_active_sidebar( 'sidebar-1' ) ) {
			$hestia_content_width = 100 - $sidebar_width;
			if ( $sidebar_width <= 3 || $sidebar_width >= 80 ) {
				$hestia_content_width = 100;
				$sidebar_width        = 100;
			}
			$content_width = $hestia_content_width - 8.33333333;
			$custom_css   .= '
				@media (min-width: 992px){
					.blog-sidebar-wrapper:not(.no-variable-width){
						width: ' . (float) $sidebar_width . '%;
						display: inline-block;
					}
					.single-post-container,
					.blog-posts-wrap, 
					.archive-post-wrap {
						width: ' . (float) $content_width . '%;
					}
					.page-content-wrap{
						width: ' . (float) $hestia_content_width . '%;
					}
					.blog-sidebar-wrapper:not(.no-variable-width){
						width: ' . (float) $sidebar_width . '%;
					}
				}';
		}

		return $custom_css;
	}

	/**
	 * Function that returns custom style for container width.
	 *
	 * @param float  $value Container width.
	 * @param string $dimension Query dimension.
	 *
	 * @since 1.1.53
	 * @return string
	 */
	public function get_container_width_style( $value, $dimension = 'desktop' ) {
		$custom_css = '';
		switch ( $dimension ) {
			case 'tablet':
				$custom_css .= '@media (max-width:768px){
					div.container{
						width: ' . (float) $value . 'px;
					}
				}';
				break;
			case 'mobile':
				$custom_css .= '
				@media (max-width:480px){
					div.container{
						width: ' . (float) $value . 'px;
					}
				}';
				break;
			default:
				$custom_css .= '
				div.container{
					width: ' . (float) $value . 'px;
				}';
		}

		return $custom_css;
	}
}
