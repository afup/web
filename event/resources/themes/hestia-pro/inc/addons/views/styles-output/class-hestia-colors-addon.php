<?php
/**
 * Colors Addon
 *
 * @package Hestia
 */

/**
 * Class Hestia_Colors_Addon
 */
class Hestia_Colors_Addon extends Hestia_Colors {

	/**
	 * Add top bar style.
	 */
	public function add_inline_color_styles() {
		parent::add_inline_color_styles();
		wp_add_inline_style( 'hestia_style', $this->addon_colors_inline_style() );
		wp_add_inline_style( 'hestia_woocommerce_style', $this->woo_colors_inline_style() );
	}

	/**
	 * Add colors inline style.
	 */
	private function addon_colors_inline_style() {
		$color_headings          = get_theme_mod( 'secondary_color', '#2d3359' );
		$color_body              = get_theme_mod( 'body_color', '#999999' );
		$color_overlay           = get_theme_mod( 'header_overlay_color', apply_filters( 'hestia_overlay_color_default', 'rgba(0,0,0,0.5)' ) );
		$color_header_text       = get_theme_mod( 'header_text_color', '#fff' );
		$navbar_background       = get_theme_mod( 'navbar_background_color', '#fff' );
		$navbar_solid_text       = get_theme_mod( 'navbar_text_color', '#555' );
		$navbar_text_hover       = get_theme_mod( 'navbar_text_color_hover', '#e91e63' );
		$navbar_transparent_text = get_theme_mod( 'navbar_transparent_text_color', '#fff' );
		$color_accent            = get_theme_mod( 'accent_color', apply_filters( 'hestia_accent_color_default', '#e91e63' ) );
		$custom_css              = '';

		// Secondary Color
		$custom_css .= ! empty( $color_headings ) ? '
			.title, .title a, 
			.card-title, 
			.card-title a,
			.card-title a:hover,
			.info-title,
			.info-title a,
			.footer-brand, 
			.footer-brand a,
			.media .media-heading, 
			.media .media-heading a,
			.hestia-info .info-title, 
			.card-blog a.moretag,
			.card-blog a.more-link,
			.card .author a,
			.hestia-about:not(.section-image) h1, .hestia-about:not(.section-image) h2, .hestia-about:not(.section-image) h3, .hestia-about:not(.section-image) h4, .hestia-about:not(.section-image) h5,
			aside .widget h5,
			aside .widget a,
			.woocommerce ul.products[class*="columns-"] li.product-category h2,
			.woocommerce #reviews #comments ol.commentlist li .comment-text p.meta .woocommerce-review__author,
			.has-text-color.has-secondary-color {
				color: ' . esc_html( $color_headings ) . ';
			}
			.has-secondary-background-color[class*="has-background"] {
				background-color: ' . esc_html( $color_headings ) . '
			}' : '';

		// Body Colors
		$custom_css .= ! empty( $color_body ) ? '
		.description, .card-description, .footer-big, .hestia-features .hestia-info p, .text-gray,
		.hestia-about:not(.section-image) p, .hestia-about:not(.section-image) h6,
		.has-text-color.has-body-color-color {
			color: ' . esc_html( $color_body ) . ';
		}
		.has-body-color-background-color[class*="has-background"] {
			background-color: ' . esc_html( $color_body ) . '
		}' : '';

		// Header Overlay Color & Opacity
		$custom_css .= ! empty( $color_overlay ) ? ' 
		.header-filter:before,
		.has-header-overlay-color-background-color[class*="has-background"] {
			background-color: ' . esc_html( $color_overlay ) . ';
		}
		.has-text-color.has-header-overlay-color-color {
			color: ' . esc_html( $color_overlay ) . ';
		}' : '';

		// Header Text Color
		$custom_css .= ! empty( $color_header_text ) ? ' 
		.page-header, .page-header .hestia-title, .page-header .sub-title,
		.has-text-color.has-header-text-color-color {
			color: ' . esc_html( $color_header_text ) . ';
		}
		.has-header-text-color-background-color[class*="has-background"] {
			background-color: ' . esc_html( $color_header_text ) . ';
		}' : '';

		if ( ! empty( $navbar_background ) ) {
			$full_screen_menu_bg = hestia_hex_rgba( $navbar_background, 0.9 );
			// Navbar background
			$custom_css .= '
			@media( max-width: 768px ) {
				/* On mobile background-color */
				.header > .navbar,
				.navbar.navbar-fixed-top .navbar-collapse {
					background-color: ' . esc_html( $navbar_background ) . ';
				}
			}
			.navbar:not(.navbar-transparent),
			.navbar .dropdown-menu,
			.nav-cart .nav-cart-content .widget,
			.has-navbar-background-background-color[class*="has-background"] {
				background-color: ' . esc_html( $navbar_background ) . ';
			}
			
			@media ( min-width: 769px ) {
				.navbar.full-screen-menu .nav.navbar-nav { background-color: ' . esc_html( $full_screen_menu_bg ) . ' }
			}
			.has-navbar-background-color[class*="has-background"] {
				color: ' . esc_html( $navbar_background ) . ';
			}';
		}

		// Navbar transparent items color
		$custom_css .= ! empty( $navbar_transparent_text ) ? '
		@media( min-width: 769px ) {
			.navbar.navbar-transparent .navbar-brand,
			.navbar.navbar-transparent .navbar-nav > li:not(.btn) > a,
			.navbar.navbar-transparent .navbar-nav > .active > a,
			.navbar.navbar-transparent.full-screen-menu .navbar-toggle,
			.navbar.navbar-transparent:not(.full-screen-menu) .nav-cart-icon, 
			.navbar.navbar-transparent.full-screen-menu li.responsive-nav-cart > a.nav-cart-icon,
			.navbar.navbar-transparent .hestia-toggle-search,
			.navbar.navbar-transparent .header-widgets-wrapper ul li a[href*="mailto:"],
			.navbar.navbar-transparent .header-widgets-wrapper ul li a[href*="tel:"]{
				color: ' . esc_html( $navbar_transparent_text ) . ';
			}
		}
		.navbar.navbar-transparent .hestia-toggle-search svg{
			fill: ' . esc_html( $navbar_transparent_text ) . ';
		}
		.has-text-color.has-navbar-transparent-text-color-color {
			color: ' . esc_html( $navbar_transparent_text ) . ';
		}
		.has-navbar-transparent-text-color-background-color[class*="has-background"],
		.navbar.navbar-transparent .header-widgets-wrapper ul li a[href*="mailto:"]:before,
		.navbar.navbar-transparent .header-widgets-wrapper ul li a[href*="tel:"]:before{
			background-color: ' . esc_html( $navbar_transparent_text ) . ';
		}' : '';

		// Navbar solid items color
		$custom_css .= ! empty( $navbar_solid_text ) ? '
		@media( min-width: 769px ) {
			.menu-open .navbar.full-screen-menu.navbar-transparent .navbar-toggle,
			.navbar:not(.navbar-transparent) .navbar-brand,
			.navbar:not(.navbar-transparent) li:not(.btn) > a,
			.navbar.navbar-transparent.full-screen-menu li:not(.btn):not(.nav-cart) > a,
			.navbar.navbar-transparent .dropdown-menu li:not(.btn) > a,
			.hestia-mm-heading, .hestia-mm-description, 
			.navbar:not(.navbar-transparent) .navbar-nav > .active > a,
			.navbar:not(.navbar-transparent).full-screen-menu .navbar-toggle,
			.navbar .nav-cart-icon,  
			.navbar:not(.navbar-transparent) .hestia-toggle-search,
			.navbar.navbar-transparent .nav-cart .nav-cart-content .widget li a,
			.navbar .navbar-nav>li .dropdown-menu li.active>a {
				color: ' . esc_html( $navbar_solid_text ) . ';
			}
		}
		@media( max-width: 768px ) {
			.navbar.navbar-default .navbar-brand,
			.navbar.navbar-default .navbar-nav li:not(.btn).menu-item > a,
			.navbar.navbar-default .navbar-nav .menu-item.active > a,
			.navbar.navbar-default .navbar-toggle,
			.navbar.navbar-default .navbar-toggle,
			.navbar.navbar-default .responsive-nav-cart a,
			.navbar.navbar-default .nav-cart .nav-cart-content a,
			.navbar.navbar-default .hestia-toggle-search,
			.hestia-mm-heading, .hestia-mm-description {
				color: ' . esc_html( $navbar_solid_text ) . ';
			}
			
			.navbar .navbar-nav .dropdown:not(.btn) a .caret svg{
				fill: ' . esc_html( $navbar_solid_text ) . ';
			}
			
			
			.navbar .navbar-nav .dropdown:not(.btn) a .caret {
				border-color: ' . esc_html( $navbar_solid_text ) . ';
			}
		}
		.has-text-color.has-navbar-text-color-color {
			color: ' . esc_html( $navbar_solid_text ) . ';
		}
		.has-navbar-text-color-background-color[class*="has-background"] {
			background-color: ' . esc_html( $navbar_solid_text ) . ';
		}
		.navbar:not(.navbar-transparent) .header-widgets-wrapper ul li a[href*="mailto:"]:before,
		.navbar:not(.navbar-transparent) .header-widgets-wrapper ul li a[href*="tel:"]:before{
			background-color:' . esc_html( $navbar_solid_text ) . '
		}
		.hestia-toggle-search svg{
			fill: ' . esc_html( $navbar_solid_text ) . ';
		}
		' : '';

		// Navbar solid items color
		$custom_css .= ! empty( $navbar_text_hover ) ? '
		.navbar.navbar-default:not(.navbar-transparent) li:not(.btn):hover > a,
		.navbar.navbar-default.navbar-transparent .dropdown-menu:not(.btn) li:not(.btn):hover > a,
		.navbar.navbar-default:not(.navbar-transparent) li:not(.btn):hover > a i,
		.navbar.navbar-default:not(.navbar-transparent) .navbar-toggle:hover,
		.navbar.navbar-default:not(.full-screen-menu) .nav-cart-icon .nav-cart-content a:hover, 
		.navbar.navbar-default:not(.navbar-transparent) .hestia-toggle-search:hover,
		.navbar.navbar-transparent .nav-cart .nav-cart-content .widget li:hover a,
		.has-text-color.has-navbar-text-color-hover-color {
			color: ' . esc_html( $navbar_text_hover ) . ';
		}
		.navbar.navbar-default li.on-section:not(.btn) > a {color: ' . esc_html( $navbar_text_hover ) . '!important}
		@media( max-width: 768px ) {
			.navbar.navbar-default.navbar-transparent li:not(.btn):hover > a,
			.navbar.navbar-default.navbar-transparent li:not(.btn):hover > a i,
			.navbar.navbar-default.navbar-transparent .navbar-toggle:hover,
			.navbar.navbar-default .responsive-nav-cart a:hover
			.navbar.navbar-default .navbar-toggle:hover {
				color: ' . esc_html( $navbar_text_hover ) . ' !important;
			}
		}
		.has-navbar-text-color-hover-background-color[class*="has-background"] {
			background-color: ' . esc_html( $navbar_text_hover ) . ';
		}
		.navbar:not(.navbar-transparent) .header-widgets-wrapper ul li:hover a[href*="mailto:"]:before,
		.navbar:not(.navbar-transparent) .header-widgets-wrapper ul li:hover a[href*="tel:"]:before{
			background-color:' . esc_html( $navbar_text_hover ) . '
		}
		.hestia-toggle-search:hover svg{
			fill: ' . esc_html( $navbar_text_hover ) . ';
		}
		' : '';

		// FORMS UNDERLINE COLOR
		$custom_css .= ! empty( $color_accent ) ? '
		.form-group.is-focused .form-control,
		 div.wpforms-container .wpforms-form .form-group.is-focused .form-control,
		 .nf-form-cont input:not([type=button]):focus,
		 .nf-form-cont select:focus,
		 .nf-form-cont textarea:focus {
		 background-image: -webkit-gradient(linear,left top, left bottom,from(' . esc_html( $color_accent ) . '),to(' . esc_html( $color_accent ) . ')),-webkit-gradient(linear,left top, left bottom,from(#d2d2d2),to(#d2d2d2));
		 background-image: -webkit-linear-gradient(linear,left top, left bottom,from(' . esc_html( $color_accent ) . '),to(' . esc_html( $color_accent ) . ')),-webkit-linear-gradient(linear,left top, left bottom,from(#d2d2d2),to(#d2d2d2));
		 background-image: linear-gradient(linear,left top, left bottom,from(' . esc_html( $color_accent ) . '),to(' . esc_html( $color_accent ) . ')),linear-gradient(linear,left top, left bottom,from(#d2d2d2),to(#d2d2d2));
		 }
		
		 .navbar.navbar-transparent.full-screen-menu .navbar-collapse .navbar-nav > li:not(.btn) > a:hover {
		 color: ' . esc_html( $color_accent ) . ';
		 }
		 
		 .hestia-ajax-loading{
		 border-color: ' . esc_html( $color_accent ) . ';
		 }' : '';

		return $custom_css;
	}

	/**
	 * Add woocommerce colors styling.
	 *
	 * @return string
	 */
	private function woo_colors_inline_style() {
		$custom_css_woocommerce = '';

		$color_body = get_theme_mod( 'body_color', '#999999' );
		// Secondary color
		$custom_css_woocommerce .= ! empty( $color_body ) ? '.woocommerce .product .card-product .card-description p,
		 .woocommerce.archive .blog-post .products li.product-category a h2 .count {
			color: ' . esc_html( $color_body ) . ';
		}' : '';

		return $custom_css_woocommerce;
	}
}
