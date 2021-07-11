<?php
/**
 * All the inline style for typography ( font family, font sizes ).
 *
 * @package Hestia
 */

/**
 * Class Hestia_Public_Typography
 */
class Hestia_Public_Typography extends Hestia_Inline_Style_Manager {

	/**
	 * Add all the hooks necessary.
	 */
	public function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'add_inline_font_styles' ) );
	}

	/**
	 * Add top bar style.
	 */
	public function add_inline_font_styles() {
		wp_add_inline_style( 'hestia_style', $this->fonts_inline_style() );
		wp_add_inline_style( 'hestia_style', $this->typography_inline_style() );
	}

	/**
	 * Add inline style for custom fonts.
	 *
	 * @since 1.1.59
	 */
	private function fonts_inline_style() {

		$custom_css = '';

		/**
		 * Headings font family.
		 */
		$hestia_headings_font = get_theme_mod( 'hestia_headings_font', apply_filters( 'hestia_headings_default', false ) );
		if ( ! empty( $hestia_headings_font ) ) {
			$custom_css .=
				'h1, h2, h3, h4, h5, h6, .hestia-title, .hestia-title.title-in-content, p.meta-in-content , .info-title, .card-title,
		.page-header.header-small .hestia-title, .page-header.header-small .title, .widget h5, .hestia-title,
		.title, .footer-brand, .footer-big h4, .footer-big h5, .media .media-heading,
		.carousel h1.hestia-title, .carousel h2.title,
		.carousel span.sub-title, .hestia-about h1, .hestia-about h2, .hestia-about h3, .hestia-about h4, .hestia-about h5 {
			font-family: ' . esc_html( $hestia_headings_font ) . ';
		}';
			if ( class_exists( 'WooCommerce', false ) ) {
				$custom_css .=
				'.woocommerce.single-product h1.product_title, .woocommerce section.related.products h2, .woocommerce section.exclusive-products h2, .woocommerce span.comment-reply-title, .woocommerce ul.products[class*="columns-"] li.product-category h2 {
				font-family: ' . esc_html( $hestia_headings_font ) . ';
			}';
			}
		}

		/**
		 * Body font family.
		 */
		$hestia_body_font = get_theme_mod( 'hestia_body_font', apply_filters( 'hestia_body_font_default', false ) );
		if ( ! empty( $hestia_body_font ) ) {
			$custom_css .= '
		body, ul, .tooltip-inner {
			font-family: ' . esc_html( $hestia_body_font ) . ';
		}';

			if ( class_exists( 'WooCommerce', false ) ) {
				$custom_css .= '
		.products .shop-item .added_to_cart,
		.woocommerce-checkout #payment input[type=submit], .woocommerce-checkout input[type=submit],
		.woocommerce-cart table.shop_table td.actions input[type=submit],
		.woocommerce .cart-collaterals .cart_totals .checkout-button, .woocommerce button.button,
		.woocommerce div[id^=woocommerce_widget_cart].widget .buttons .button, .woocommerce div.product form.cart .button,
		.woocommerce #review_form #respond .form-submit , .added_to_cart.wc-forward, .woocommerce div#respond input#submit,
		.woocommerce a.button {
			font-family: ' . esc_html( $hestia_body_font ) . ';
		}';
			}
		}

		return $custom_css;
	}


	/**
	 * Add inline style for font sizes.
	 *
	 * @since 1.1.48
	 */
	private function typography_inline_style() {

		$custom_css = '';

		/**
		 * Title control [Posts & Pages]
		 */
		$custom_css .= $this->get_inline_style(
			'hestia_header_titles_fs',
			array(
				$this,
				'get_header_titles_style',
			)
		);

		/**
		 * Headings control [Posts & Pages]
		 */
		$custom_css .= $this->get_inline_style(
			'hestia_post_page_headings_fs',
			array(
				$this,
				'get_post_page_headings_style',
			)
		);

		/**
		 * Content control [Posts & Pages]
		 */
		$custom_css .= $this->get_inline_style(
			'hestia_post_page_content_fs',
			array(
				$this,
				'get_post_page_content_style',
			)
		);

		/**
		 * Big Title Section / Header Slide [Frontpage sections]
		 */
		$custom_css .= $this->get_inline_style(
			'hestia_big_title_fs',
			array(
				$this,
				'get_big_title_content_style',
			)
		);

		/**
		 * Titles control [Frontpage sections]
		 */
		$custom_css .= $this->get_inline_style(
			'hestia_section_primary_headings_fs',
			array(
				$this,
				'get_fp_titles_style',
			)
		);

		/**
		 * Subitles control [Frontpage sections]
		 */
		$custom_css .= $this->get_inline_style(
			'hestia_section_secondary_headings_fs',
			array(
				$this,
				'get_fp_subtitles_style',
			)
		);

		/**
		 * Content control [Blog, Frontpage & WooCommerce]
		 */
		$custom_css .= $this->get_inline_style(
			'hestia_section_content_fs',
			array(
				$this,
				'get_fp_content_style',
			)
		);

		return $custom_css;
	}


	/**
	 * [Posts and Pages] Title font size.
	 *
	 * This function is called by get_inline_style to change the font size for:
	 * pages/posts titles
	 * Slider/Big title title/subtitle
	 *
	 * @param string $value Font value.
	 * @param string $dimension Media query.
	 *
	 * @return string
	 */
	public function get_header_titles_style( $value, $dimension = 'desktop' ) {
		$custom_css = '';
		switch ( $dimension ) {
			case 'desktop':
				$v3 = ( 42 + (int) $value ) > 0 ? ( 42 + (int) $value ) : 0;
				break;
			case 'tablet':
			case 'mobile':
				$v3 = ( 26 + (int) $value ) > 0 ? ( 26 + (int) $value ) : 0;
				break;
		}
		$custom_css .= ! empty( $v3 ) ? '
			.page-header.header-small .hestia-title,
			.page-header.header-small .title,
			h1.hestia-title.title-in-content,
			.main article.section .has-title-font-size {
				font-size: ' . absint( $v3 ) . 'px;
			}' : '';

		$custom_css = $this->add_media_query( $dimension, $custom_css );

		return $custom_css;
	}

	/**
	 * [Posts & Pages] Headings.
	 * This function is called by hestia_get_inline_style to change the font size for:
	 * headings ( h1 - h6 ) on pages and single post pages
	 *
	 * @param string $value Font value.
	 * @param string $dimension Media query.
	 *
	 * @return string
	 */
	public function get_post_page_headings_style( $value, $dimension = 'desktop' ) {
		$custom_css = '';
		switch ( $dimension ) {
			case 'desktop':
				$v1 = ( 42 + (int) $value ) > 0 ? ( 42 + (int) $value ) : 0;
				$v2 = ( 37 + (int) $value ) > 0 ? ( 37 + (int) $value ) : 0;
				$v3 = ( 32 + (int) $value ) > 0 ? ( 32 + (int) $value ) : 0;
				$v4 = ( 27 + (int) $value ) > 0 ? ( 27 + (int) $value ) : 0;
				$v5 = ( 23 + (int) $value ) > 0 ? ( 23 + (int) $value ) : 0;
				$v6 = ( 18 + (int) $value ) > 0 ? ( 18 + (int) $value ) : 0;
				break;
			case 'tablet':
			case 'mobile':
				$v1 = ( 30 + (int) $value ) > 0 ? ( 30 + (int) $value ) : 0;
				$v2 = ( 28 + (int) $value ) > 0 ? ( 28 + (int) $value ) : 0;
				$v3 = ( 24 + (int) $value ) > 0 ? ( 24 + (int) $value ) : 0;
				$v4 = ( 22 + (int) $value ) > 0 ? ( 22 + (int) $value ) : 0;
				$v5 = ( 20 + (int) $value ) > 0 ? ( 20 + (int) $value ) : 0;
				$v6 = ( 18 + (int) $value ) > 0 ? ( 18 + (int) $value ) : 0;
				break;
		}

		$custom_css .= ! empty( $v1 ) ? '
		.single-post-wrap h1:not(.title-in-content),
		.page-content-wrap h1:not(.title-in-content),
		.page-template-template-fullwidth article h1:not(.title-in-content) {
			font-size: ' . absint( $v1 ) . 'px;
		}' : '';

		$custom_css .= ! empty( $v2 ) ? '
		.single-post-wrap h2,
		.page-content-wrap h2,
		.page-template-template-fullwidth article h2,
		.main article.section .has-heading-font-size {
			font-size: ' . absint( $v2 ) . 'px;
		}' : '';

		$custom_css .= ! empty( $v3 ) ? '
		.single-post-wrap h3,
		.page-content-wrap h3,
		.page-template-template-fullwidth article h3 {
			font-size: ' . absint( $v3 ) . 'px;
		}' : '';

		$custom_css .= ! empty( $v4 ) ? '
		.single-post-wrap h4,
		.page-content-wrap h4,
		.page-template-template-fullwidth article h4 {
			font-size: ' . absint( $v4 ) . 'px;
		}' : '';

		$custom_css .= ! empty( $v5 ) ? '
		.single-post-wrap h5,
		.page-content-wrap h5,
		.page-template-template-fullwidth article h5 {
			font-size: ' . absint( $v5 ) . 'px;
		}' : '';

		$custom_css .= ! empty( $v6 ) ? '
		.single-post-wrap h6,
		.page-content-wrap h6,
		.page-template-template-fullwidth article h6 {
			font-size: ' . absint( $v6 ) . 'px;
		}' : '';

		$custom_css = $this->add_media_query( $dimension, $custom_css );

		return $custom_css;
	}

	/**
	 * [Posts & Pages] Content.
	 * This function is called by hestia_get_inline_style to change the font size for:
	 * content ( p ) on pages
	 * single post pages
	 *
	 * @param string $value Font value.
	 * @param string $dimension Media query.
	 *
	 * @return string
	 */
	public function get_post_page_content_style( $value, $dimension = 'desktop' ) {
		$custom_css = '';
		switch ( $dimension ) {
			case 'desktop':
				$v1 = ( 18 + (int) $value ) > 0 ? ( 18 + (int) $value ) : 0;
				break;
			case 'tablet':
			case 'mobile':
				$v1 = ( 16 + (int) $value ) > 0 ? ( 16 + (int) $value ) : 0;
				break;
		}

		$custom_css .= ! empty( $v1 ) ? '.single-post-wrap, .page-content-wrap, .single-post-wrap ul, .page-content-wrap ul, .single-post-wrap ol, .page-content-wrap ol, .single-post-wrap dl, .page-content-wrap dl, .single-post-wrap table, .page-content-wrap table, .page-template-template-fullwidth article, .main article.section .has-body-font-size {
		font-size: ' . absint( $v1 ) . 'px;
		}' : '';

		$custom_css = $this->add_media_query( $dimension, $custom_css );

		return $custom_css;
	}

	/**
	 * [Front Page Sections] Big Title Section / Header Slider.
	 *
	 * This function is called by hestia_get_inline_style to change big title/slider titles, the
	 * subtitle and the button in the big title section.
	 *
	 * How to calculate values:
	 * Hardcoded values (67, 18 and 14 on desktop or 36, 18, 14 on tablet and mobile) are the default values from css.
	 * In this case 67 is for big title, 18 for subtitle and 14 for button.
	 * The main formula for calculating is this:
	 * $initial_value + ($variable_value / $correlation)
	 * $initial_value -> value from css
	 * $variable_value -> controls value that is between -25 and 25
	 * $correlation -> this variable says we increase the value every X units.
	 * There is another variable to set a lower limit. Just change the value compared to.
	 *
	 * @param string $value Font value.
	 * @param string $dimension Dimension.
	 *
	 * @return string
	 */
	public function get_big_title_content_style( $value, $dimension = 'desktop' ) {
		$custom_css = '';
		switch ( $dimension ) {
			case 'desktop':
				$v1 = ( 67 + (int) $value ) > 0 ? ( 67 + (int) $value ) : 0;
				$v2 = ( 18 + (int) ( $value / 8 ) ) > 0 ? ( 18 + (int) ( $value / 8 ) ) : 0;
				$v3 = ( 14 + (int) ( $value / 12 ) ) > 0 ? ( 14 + (int) ( $value / 12 ) ) : 0;
				break;
			case 'tablet':
			case 'mobile':
				$v1 = ( 36 + (int) ( $value / 4 ) ) > 0 ? ( 36 + (int) ( $value / 4 ) ) : 0;
				$v2 = ( 18 + (int) ( $value / 4 ) ) > 0 ? ( 18 + (int) ( $value / 4 ) ) : 0;
				$v3 = ( 14 + (int) ( $value / 6 ) ) > 0 ? ( 14 + (int) ( $value / 6 ) ) : 0;
				break;
		}

		$custom_css .= ! empty( $v1 ) ? '#carousel-hestia-generic .hestia-title{
		font-size: ' . absint( $v1 ) . 'px;
		}' : '';

		$custom_css .= ! empty( $v2 ) ? '#carousel-hestia-generic span.sub-title{
		font-size: ' . absint( $v2 ) . 'px;
		}' : '';

		$custom_css .= ! empty( $v3 ) ? '#carousel-hestia-generic .btn{
		font-size: ' . absint( $v3 ) . 'px;
		}' : '';

		$custom_css = $this->add_media_query( $dimension, $custom_css );

		return $custom_css;
	}

	/**
	 * [Front Page Sections] Front Page Titles font size.
	 *
	 * This function is called by hestia_get_inline_style to change the font size for:
	 * all front page sections titles and small headings ( Feature box title, Shop box title, Team box title, Testimonial box title, Blog box title )
	 *
	 * The main formula for calculating is this:
	 * $initial_value + ($variable_value / $correlation)
	 * $initial_value -> value from css
	 * $variable_value -> controls value that is between -25 and 25
	 * $correlation -> this variable says we increase the value every X units.
	 * There is another variable to set a lower limit. Just change the value compared to.
	 *
	 * @param string $value Font value.
	 * @param string $dimension Media query.
	 *
	 * @return string
	 */
	public function get_fp_titles_style( $value, $dimension = 'desktop' ) {
		$custom_css = '';
		switch ( $dimension ) {
			case 'desktop':
				$v1 = ( 37 + (int) $value ) > 18 ? ( 37 + (int) $value ) : 18;
				break;
			case 'tablet':
			case 'mobile':
				$v1 = ( 24 + (int) $value ) > 18 ? ( 24 + (int) $value ) : 18;
				break;
		}

		$v2 = ( 18 + (int) ( $value / 3 ) ) > 14 ? ( 18 + (int) ( $value / 3 ) ) : 14;
		$v3 = ( 23 + (int) ( $value / 3 ) ) > 0 ? ( 23 + (int) ( $value / 3 ) ) : 0;
		$h1 = ( 42 + (int) ( $value / 3 ) ) > 0 ? ( 42 + (int) ( $value / 3 ) ) : 0;
		$h2 = ( 37 + (int) ( $value / 3 ) ) > 0 ? ( 37 + (int) ( $value / 3 ) ) : 0;
		$h3 = ( 32 + (int) ( $value / 3 ) ) > 0 ? ( 32 + (int) ( $value / 3 ) ) : 0;
		$h4 = ( 27 + (int) ( $value / 3 ) ) > 0 ? ( 27 + (int) ( $value / 3 ) ) : 0;

		$custom_css .= ! empty( $v1 ) ? '
		section.hestia-features .hestia-title,
		section.hestia-shop .hestia-title,
		section.hestia-work .hestia-title,
		section.hestia-team .hestia-title,
		section.hestia-pricing .hestia-title,
		section.hestia-ribbon .hestia-title,
		section.hestia-testimonials .hestia-title,
		section.hestia-subscribe h2.title,
		section.hestia-blogs .hestia-title,
		.section.related-posts .hestia-title,
		section.hestia-contact .hestia-title{
			font-size: ' . absint( $v1 ) . 'px;
		}' : '';

		$custom_css .= ! empty( $v2 ) ? '
		section.hestia-features .hestia-info h4.info-title,
		section.hestia-shop h4.card-title,
		section.hestia-team h4.card-title,
		section.hestia-testimonials h4.card-title,
		section.hestia-blogs h4.card-title,
		.section.related-posts h4.card-title,
		section.hestia-contact h4.card-title,
		section.hestia-contact .hestia-description h6{
			font-size: ' . absint( $v2 ) . 'px;
		}' : '';

		$custom_css .= ! empty( $v3 ) ? '
		section.hestia-work h4.card-title,
		section.hestia-contact .hestia-description h5{
			font-size: ' . absint( $v3 ) . 'px;
		}' : '';

		$custom_css .= ! empty( $h1 ) ? '
		section.hestia-contact .hestia-description h1{
			font-size: ' . absint( $h1 ) . 'px;
		}' : '';

		$custom_css .= ! empty( $h2 ) ? '
		section.hestia-contact .hestia-description h2{
			font-size: ' . absint( $h2 ) . 'px;
		}' : '';

		$custom_css .= ! empty( $h2 ) ? '
		section.hestia-contact .hestia-description h3{
			font-size: ' . absint( $h3 ) . 'px;
		}' : '';

		$custom_css .= ! empty( $h4 ) ? '
		section.hestia-contact .hestia-description h4{
			font-size: ' . absint( $h4 ) . 'px;
		}' : '';

		$custom_css = $this->add_media_query( $dimension, $custom_css );

		return $custom_css;
	}

	/**
	 * [Front Page Sections] Subtitles font size.
	 *
	 * This function is called by hestia_get_inline_style to change the font size for:
	 * all front page sections subtitles
	 *
	 * The main formula for calculating is this:
	 * $initial_value + ($variable_value / $correlation)
	 * $initial_value -> value from css
	 * $variable_value -> controls value that is between -25 and 25
	 * $correlation -> this variable says we increase the value every X units.
	 * There is another variable to set a lower limit. Just change the value compared to.
	 *
	 * @param string $value Font value.
	 * @param string $dimension Media query.
	 *
	 * @return string
	 */
	public function get_fp_subtitles_style( $value, $dimension = 'desktop' ) {
		$custom_css = '';
		switch ( $dimension ) {
			case 'desktop':
			case 'tablet':
			case 'mobile':
				$v1 = ( 18 + (int) ( $value / 3 ) ) > 12 ? ( 18 + (int) ( $value / 3 ) ) : 12;
				break;
		}

		$custom_css .= ! empty( $v1 ) ? '
			section.hestia-features h5.description,
			section.hestia-shop h5.description,
			section.hestia-work h5.description,
			section.hestia-team h5.description,
			section.hestia-testimonials h5.description,
			section.hestia-subscribe h5.subscribe-description,
			section.hestia-blogs h5.description,
			section.hestia-contact h5.description{
				font-size: ' . absint( $v1 ) . 'px;
			}' : '';

		$custom_css = $this->add_media_query( $dimension, $custom_css );

		return $custom_css;
	}

	/**
	 * [Front Page Sections] Content font size.
	 *
	 * This function is called by hestia_get_inline_style to change the font size for:
	 * all front page sections box content
	 *
	 * @param string $value Font value.
	 * @param string $dimension Media query.
	 *
	 * @return string
	 */
	public function get_fp_content_style( $value, $dimension = 'desktop' ) {
		$custom_css = '';
		switch ( $dimension ) {
			case 'desktop':
			case 'tablet':
			case 'mobile':
				$v1 = ( 14 + (int) ( $value / 3 ) ) > 12 ? ( 14 + (int) ( $value / 3 ) ) : 12;
				$v2 = ( 12 + (int) ( $value / 3 ) ) > 12 ? ( 12 + (int) ( $value / 3 ) ) : 12;
				break;
		}

		$custom_css .= ! empty( $v1 ) ? '
		section.hestia-team p.card-description,
		section.hestia-pricing p.text-gray,
		section.hestia-testimonials p.card-description,
		section.hestia-blogs p.card-description,
		.section.related-posts p.card-description,
		.hestia-contact p,
		section.hestia-features .hestia-info p,
		section.hestia-shop .card-description p{
			font-size: ' . absint( $v1 ) . 'px;
		}' : '';

		$custom_css .= ! empty( $v2 ) ? '
		section.hestia-shop h6.category,
		section.hestia-work .label-primary,
		section.hestia-team h6.category,
		section.hestia-pricing .card-pricing h6.category,
		section.hestia-testimonials h6.category,
		section.hestia-blogs h6.category,
		.section.related-posts h6.category{
			font-size: ' . absint( $v2 ) . 'px;
		}' : '';

		$custom_css = $this->add_media_query( $dimension, $custom_css );

		return $custom_css;
	}

}
