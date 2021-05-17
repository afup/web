<?php
/**
 * WooCommerce Header Layout Manager.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Header_Layout_Manager
 */
class Hestia_Woocommerce_Header_Manager extends Hestia_Abstract_Main {

	/**
	 * Init layout manager.
	 */
	public function init() {
		if ( ! class_exists( 'WooCommerce', false ) ) {
			return;
		}
		// WooCommerce.
		add_action( 'hestia_before_woocommerce_wrapper', array( $this, 'woocommerce_page_header' ) );
		add_action( 'hestia_before_woocommerce_content', array( $this, 'woocommerce_title_in_content' ) );
		add_action( 'hestia_before_woocommerce_content', array( $this, 'woocommerce_image_in_content' ) );
	}

	/**
	 * Header for WooCommerce pages.
	 *
	 * @return bool|string
	 */
	public function woocommerce_page_header() {
		$layout = apply_filters( 'hestia_header_layout', get_theme_mod( 'hestia_header_layout', 'default' ) );

		if ( is_product_category() ) {
			$layout = 'default';
		}

		if ( 'classic-blog' === $layout ) {
			add_filter( 'hestia_boxed_layout', '__return_empty_string' );
			return false;
		}

		$header_wrapper_class = 'page-header header-small';
		if ( is_product() ) {
			$header_wrapper_class = 'page-header';
		}

		$general_layout = get_theme_mod( 'hestia_general_layout', apply_filters( 'hestia_boxed_layout_default', 1 ) );
		if ( isset( $general_layout ) && true === (bool) $general_layout ) {
			$header_wrapper_class .= ' boxed-layout-header';
		}

		$parallax_attribute = '';
		if ( ! is_product() ) {
			$parallax_attribute = 'data-parallax="active"';
		}

		echo '<div id="primary" class="' . esc_attr( $header_wrapper_class ) . '" ' . $parallax_attribute . '>';
		if ( 'default' === $layout ) {
			echo wp_kses_post( $this->render_woocommerce_page_title( $layout ) );
		}

		$this->render_woocommerce_header_background();
		echo '</div>';

		return '';
	}

	/**
	 *  Display titile in content for WooCommerce pages.
	 */
	public function woocommerce_title_in_content() {
		$layout = apply_filters( 'hestia_header_layout', get_theme_mod( 'hestia_header_layout', 'default' ) );
		if ( 'default' === $layout ) {
			return;
		}

		if ( is_product_category() ) {
			return;
		}

		echo $this->render_woocommerce_page_title( $layout );
	}

	/**
	 * Display image in content.
	 *
	 * @return void
	 */
	public function woocommerce_image_in_content() {
		if ( is_product() || is_cart() || is_checkout() || is_product_category() ) {
			return;
		}

		$layout = apply_filters( 'hestia_header_layout', get_theme_mod( 'hestia_header_layout', 'default' ) );
		if ( 'classic-blog' !== $layout ) {
			return;
		}

		$image_url = $this->get_woocommerce_header_image_url();
		if ( empty( $image_url ) ) {
			return;
		}

		$image_id   = attachment_url_to_postid( $image_url );
		$image1_alt = '';
		if ( $image_id ) {
			$image1_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
		}

		$image_markup = '<img class="wp-post-image image-in-page" src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $image1_alt ) . '">';
		if ( is_shop() ) {
			$image_markup = '<div class="col-md-12 image-in-page-wrapper">' . $image_markup . '</div>';
		}

		echo $image_markup;
	}

	/**
	 * Render WooCommerce header content.
	 *
	 * @param string $layout Layout of the header.
	 *
	 * @return string
	 */
	private function render_woocommerce_page_title( $layout ) {
		$title_class   = 'hestia-title';
		$wrapper_class = 'col-md-10 col-md-offset-1 text-center';
		if ( 'default' !== $layout ) {
			$title_class  .= ' title-in-content';
			$wrapper_class = 'col-md-12';
		}
		$header_content_output  = '<div class="container">';
		$header_content_output .= '<div class="row">';
		$header_content_output .= '<div class="' . esc_attr( $wrapper_class ) . '">';
		$header_content_output .= '<h1 class="' . esc_attr( $title_class ) . '">';
		if ( is_shop() ) {
			$header_content_output .= woocommerce_page_title( false );
		}
		if ( is_product() || is_cart() || is_checkout() ) {
			$header_content_output .= the_title( '', '', false );
		}
		if ( is_product_category() || is_product_tag() || is_product_taxonomy() ) {
			$header_content_output .= get_the_archive_title();
		}
		$header_content_output .= '</h1>';
		if ( is_product_category() || is_product_tag() ) {
			$header_content_output .= '<h5 class="description">' . get_the_archive_description() . '</h5>';
		}

		$header_content_output .= '</div>';
		$header_content_output .= '</div>';
		$header_content_output .= '</div>';

		return $header_content_output;
	}

	/**
	 * Render header on WooCommerce pages.
	 *
	 * @return void
	 */
	private function render_woocommerce_header_background() {
		$background_image            = apply_filters( 'hestia_header_image_filter', $this->get_woocommerce_header_image_url() );
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
	 *  Get url for header on WooCommerce pages.
	 *
	 * @return string
	 */
	private function get_woocommerce_header_image_url() {

		$thumbnail                 = get_header_image();
		$use_header_image_sitewide = get_theme_mod( 'hestia_header_image_sitewide', false );
		// If the option to use Header Image Sitewide is enabled, return header image and exit function.
		if ( true === (bool) $use_header_image_sitewide ) {
			return esc_url( $thumbnail );
		}

		if ( is_product() ) {
			$thumbnail = $this->get_single_product_background();
			if ( ! empty( $thumbnail ) ) {
				return esc_url( $thumbnail );
			}
		}

		$shop_id = get_option( 'woocommerce_shop_page_id' );

		if ( is_product_category() ) {
			$thumbnail = $this->get_product_category_background( $shop_id );
			if ( ! empty( $thumbnail ) ) {
				return esc_url( $thumbnail );
			}
		}

		if ( is_shop() ) {
			$thumbnail = $this->get_shop_page_background( $shop_id );
			if ( ! empty( $thumbnail ) ) {
				return esc_url( $thumbnail );
			}
		}

		return esc_url( $thumbnail );
	}

	/**
	 * This is the single product header image.
	 * This function searches in all categories of a product for a thumbnail.
	 * If the category have a thumbnail, get the image and search further
	 * to find the last category that have a thumbnail.
	 *
	 * @return bool|string
	 */
	private function get_single_product_background() {
		// Bail if it's not WooCommerce or if not single product.
		if ( ! hestia_check_woocommerce() || ! is_product() ) {
			return false;
		}

		$terms = get_the_terms( get_queried_object_id(), 'product_cat' );
		if ( empty( $terms ) ) {
			return false;
		}

		$thumb_tmp = '';
		foreach ( $terms as $term ) {
			$categ_thumb = $this->get_category_thumbnail( $term->term_id );
			if ( ! empty( $categ_thumb ) ) {
				$thumb_tmp = $categ_thumb;
			}
		}

		return $thumb_tmp;
	}

	/**
	 *  This is the product category header image.
	 *
	 * @param string $shop_id Shop page id.
	 * @return string
	 */
	private function get_product_category_background( $shop_id ) {

		$category = get_queried_object();

		/**
		 * Try to get category thumbnail.
		 */
		$category_thumbnail = $this->get_category_thumbnail( $category->term_id );
		if ( ! empty( $category_thumbnail ) ) {
			return $category_thumbnail;
		}

		/**
		 * If category does not have a thumbnail, try to get page thumbnail
		 */
		if ( empty( $shop_id ) ) {
			return '';
		}

		$thumb_tmp = get_the_post_thumbnail_url( $shop_id );
		if ( ! empty( $thumb_tmp ) ) {
			return $thumb_tmp;
		}

		return '';
	}

	/**
	 * Get background for shop page.
	 *
	 * @param string $shop_id Shop page id.
	 *
	 * @return bool
	 */
	private function get_shop_page_background( $shop_id ) {
		// Bail if it's not WooCommerce or if not single product.
		if ( empty( $shop_id ) ) {
			return false;
		}

		return get_the_post_thumbnail_url( $shop_id );
	}

	/**
	 * Get Woo category thumbnail.
	 *
	 * @param int $term_id Term ID.
	 * @return string|bool
	 */
	private function get_category_thumbnail( $term_id ) {
		if ( ! empty( $term_id ) ) {
			$category_thumbnail = get_term_meta( $term_id, 'thumbnail_id', true );
		}

		// Get product category's image.
		return ! empty( $category_thumbnail ) ? wp_get_attachment_url( $category_thumbnail ) : false;
	}
}
