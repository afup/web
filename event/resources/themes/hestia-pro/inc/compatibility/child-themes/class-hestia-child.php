<?php
/**
 * Child theme compatibility.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Child
 */
class Hestia_Child extends Hestia_Abstract_Main {
	/**
	 * Add all the hooks necessary.
	 */
	public function init() {

		if ( ! is_child_theme() ) {
			return;
		}

		if ( wp_get_theme()->Name === 'Orfeo' || wp_get_theme()->Name === 'Orfeo Pro' ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'add_orfeo_inline_styles' ) );
		}

		if ( wp_get_theme()->Template === 'hestia-pro' && ( wp_get_theme()->Name === 'Orfeo' || wp_get_theme()->Name === 'Orfeo Pro' ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'add_orfeo_pro_inline_styles' ) );
		}

		if ( wp_get_theme()->Name === 'Fagri' || wp_get_theme()->Name === 'Fagri Pro' ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'add_fagri_inline_styles' ) );
		}

		if ( wp_get_theme()->Template === 'hestia-pro' && ( wp_get_theme()->Name === 'Fagri' || wp_get_theme()->Name === 'Fagri Pro' ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'add_fagri_pro_inline_styles' ) );
		}

		if ( wp_get_theme()->Template === 'hestia' && ( wp_get_theme()->Name === 'Jinsy Magazine' || wp_get_theme()->Name === 'Jinsy Magazine Pro' ) ) {
			add_filter( 'theme_mod_hestia_navbar_transparent', array( $this, 'jinsy_magazine_navbar_transparency' ) );
			add_filter( 'hestia_header_titles_fs_default', array( $this, 'jinsy_magazine_header_titles_fs_default' ) );
		}

		if ( wp_get_theme()->Template === 'hestia-pro' && ( wp_get_theme()->Name === 'Jinsy Magazine' || wp_get_theme()->Name === 'Jinsy Magazine Pro' ) ) {
			add_filter( 'hestia_navbar_transparent_default', array( $this, 'jinsy_magazine_navbar_transparency' ) );
			add_filter( 'hestia_header_titles_fs_default', array( $this, 'jinsy_magazine_header_titles_fs_default' ) );
		}
	}

	/**
	 * Add inline styles for Orfeo
	 */
	public function add_orfeo_inline_styles() {
		wp_add_inline_style( apply_filters( 'hestia_orfeo_inline_style_handle', 'hestia_style' ), $this->orfeo_inline_style() );
	}

	/**
	 * Add inline styles for Orfeo Pro
	 */
	public function add_orfeo_pro_inline_styles() {
		wp_add_inline_style( apply_filters( 'hestia_orfeo_inline_style_handle', 'hestia_style' ), $this->orfeo_pro_inline_style() );
	}

	/**
	 * Add inline styles for Fagri
	 */
	public function add_fagri_inline_styles() {
		wp_add_inline_style( apply_filters( 'hestia_fagri_inline_style_handle', 'hestia_style' ), $this->fagri_inline_style() );
	}

	/**
	 * Add inline styles for Fagri Pro
	 */
	public function add_fagri_pro_inline_styles() {
		wp_add_inline_style( apply_filters( 'hestia_fagri_inline_style_handle', 'hestia_style' ), $this->fagri_pro_inline_style() );
	}

	/**
	 * Orfeo inline style
	 */
	private function orfeo_inline_style() {

		$custom_css = '';

		/* When Home is Blog (Your lastest posts) make background color white */
		$custom_css .= '
			.home.blog .hestia-blogs {
				background-color: #fff !important;
			}
		';

		/* Limit notification width on WooCommerce Checkout Page */
		$custom_css .= '
			.woocommerce-checkout #hestia-checkout-coupon .woocommerce-message,
			.woocommerce-checkout #hestia-checkout-coupon .woocommerce-error {
				margin-left: auto;
				margin-right: auto;
			}
		';

		/**
		 * Remove box shadow from all buttons
		 * Add opacity 0.75 on buttons hover
		 */
		$custom_css .= '
			.btn,
			button,
			.button {
				box-shadow: none !important;
			}
			
			.btn:hover,
			button:hover,
			.button:hover {
				opacity: 0.75;
			}
		';

		/* Align button buttons in Big Title section */
		$custom_css .= '
			.carousel .buttons .btn-primary + .btn-right {
				margin-left: 15px;
			}		
			.carousel .buttons .btn,
			.carousel .buttons .btn-right {
				margin: 15px;
			}
		';

		/* Style Big Title Section because .header class is not its wrapper anymore */
		$custom_css .= '
			.carousel .hestia-big-title-content .hestia-title {
				font-weight: 800;
			}
			.carousel .hestia-big-title-content .sub-title {
				font-family: inherit;
				font-size: 19px;
				font-weight: 300;
				line-height: 26px;
				margin: 0 0 8px;
			}
			.carousel .hestia-big-title-content .buttons .btn,
			.carousel .hestia-big-title-content .buttons .btn-right {
				border-radius: 30px;
				font-family: inherit;
				font-size: 14px;
				font-weight: 600;
				line-height: 24px;
				padding: 11px 30px;
			}
			.carousel .hestia-big-title-content .buttons .btn-right {
				background-color: transparent;
				border: 2px solid #fff;
				padding: 9px 28px;
			}
		';

		/* Style Big Title second button as it was before buttons styling feature */
		$custom_css .= '
            .carousel .hestia-big-title-content .buttons > a.btn.btn-primary,
            .carousel .hestia-big-title-content .buttons > a.btn.btn-right {
                border-radius: 30px !important;
                padding: 11px 30px !important;
            }
        ';

		/* Fix reply button on comments */
		$custom_css .= '
			.section-comments .comment .media-body .media-footer {
				top: 5px;
				right: 0;
			}
			.section-comments .comment .media-body .media-heading {
				padding-right: 50px;
			}
			.section-comments .comment {
				margin-bottom: 30px;
			}
		';

		/* Fix spacing between lines with maximum font-size in Big Title section */
		$custom_css .= '
			.carousel .hestia-title {
				line-height: 1.1;
			}
		';

		return $custom_css;
	}

	/**
	 * Orfeo pro inline style
	 */
	private function orfeo_pro_inline_style() {

		$body_text_color = get_theme_mod( 'body_color', '#9A9A9A' );
		$secondary_color = get_theme_mod( 'secondary_color', '#444444' );
		$custom_css      = '';

		/* Body text color */
		$custom_css .= '                                    
		    .home .hestia-features .description,
		    .home .hestia-features .hestia-info p,
		    .home .hestia-team .description,
		    .home .hestia-team .hestia-team-content .card .content .card-description,
		    .home .hestia-testimonials .description,
		    .home .hestia-pricing .text-gray,
		    .home .hestia-shop .description,
		    .home .hestia-shop .card-product .content .card-description p,
		    .home .hestia-work .description,
		    .home:not(.blog) .hestia-blogs .description,
		    .home:not(.blog) .hestia-blogs .hestia-blog-content .card-blog .content .card-description {
		        color: ' . esc_html( $body_text_color ) . ';
		    }
        ';

		/* Remove the white border from the slider second button */
		$custom_css .= '
			.carousel .big-title-slider-content .buttons a.btn-right {
				border: 2px solid transparent;
			}
		';

		/* Inherit secondary color from Hestia Pro */
		$custom_css .= '
			.hestia-features .hestia-features-content .hestia-info .info-title,
			.hestia-team .hestia-team-content .card .content .card-title {
				color: ' . esc_html( $secondary_color ) . ';
			}
			.hestia-pricing .card-pricing .card-title {
				color: #444444;
			}
		';

		/* Packages section, compatibility with icon instead of price */
		$custom_css .= '
			.card-pricing .hestia-pricing-icon-wrapper.pricing-has-icon + .card-title small {
				font-size: 60%;
			}
			.hestia-pricing .hestia-table-two .card-pricing .hestia-pricing-icon-wrapper i {
				color: #fff;
			}
		';

		return $custom_css;
	}

	/**
	 * Fagri inline style
	 */
	private function fagri_inline_style() {
		/* Icons in Pirate Forms contact form on front page */
		$custom_css = '';

		$custom_css .= '
			.hestia-contact .pirate_forms_wrap .contact_name_wrap .form-group::before, 
		    .hestia-contact .pirate_forms_wrap .contact_email_wrap .form-group::before, 
		    .hestia-contact .pirate_forms_wrap .contact_subject_wrap .form-group::before {
				top: 6px;
		    }
	    ';

		/* Related products */
		$custom_css .= '
			.woocommerce.single-product .related.products {
				margin-top: 0;
			}
			.woocommerce.single-product .upsells.products {
				padding-bottom: 0;
			}
		';

		/* Fix spacing between lines with maximum font-size in Big Title section */
		$custom_css .= '
			.carousel .hestia-title {
				line-height: 1.1;
			}
		';

		return $custom_css;
	}

	/**
	 * Fagri pro inline style
	 */
	private function fagri_pro_inline_style() {

		$body_text_color = get_theme_mod( 'body_color', '#9A9A9A' );
		$custom_css      = '';

		/* Body text color */
		$custom_css .= '                                    
		    .home .hestia-features .description,
		    .home .hestia-features .hestia-info p,
		    .home .hestia-team .hestia-team-content .card .content .card-description,
		    .home .hestia-pricing .text-gray,
		    .fagri-testimonials-wrapper .hestia-testimonials .hestia-testimonials-content .card-testimonial .content .card-description,
		    .home .hestia-shop .description,
		    .home .hestia-shop .card-product .content .card-description p,
		    .home .hestia-work .description,
		    .home:not(.blog) .hestia-blogs .description,
		    .home:not(.blog) .hestia-blogs .hestia-blog-content .card-blog .content .card-description {
		        color: ' . esc_html( $body_text_color ) . ';
		    }
        ';

		return $custom_css;
	}

	/**
	 * Jinsy Magazine remove navbar transparency
	 */
	public function jinsy_magazine_navbar_transparency() {
		return false;
	}

	/**
	 * Jinsy Magazine header titles font size default
	 */
	public function jinsy_magazine_header_titles_fs_default() {
		return '{"desktop":-10,"tablet":-6,"mobile":-11}';
	}
}
