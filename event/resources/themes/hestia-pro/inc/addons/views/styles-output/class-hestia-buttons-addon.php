<?php
/**
 * Inline style for buttons.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Buttons_Addon
 */
class Hestia_Buttons_Addon extends Hestia_Buttons {


	/**
	 * Add inline style for buttons.
	 */
	public function add_inline_buttons_styles() {
		parent::add_inline_buttons_styles();
		wp_add_inline_style( apply_filters( 'hestia_custom_button_hover_style', 'hestia_style' ), $this->buttons_hover_inline_style() );
		wp_add_inline_style( apply_filters( 'hestia_featured_posts_buttons_hover_style', 'hestia_style' ), $this->featured_posts_buttons_hover() );
	}

	/**
	 * Add hover color effect on featured posts buttons.
	 *
	 * @return string
	 */
	private function featured_posts_buttons_hover() {

		$custom_css = '';

		/**
		 * Gather data from customizer.
		 */
		$hestia_buttons_hover_effect = get_theme_mod( 'hestia_buttons_hover_effect', 'shadow' );
		if ( $hestia_buttons_hover_effect !== 'color' ) {
			return $custom_css;
		}

		$color_rose          = hestia_adjust_brightness( '#e91e63', -20 );
		$color_primary_hover = hestia_adjust_brightness( '#89229b', -20 );
		$color_info          = hestia_adjust_brightness( '#00bcd4', -20 );
		$color_success       = hestia_adjust_brightness( '#4caf50', -20 );
		$color_danger        = hestia_adjust_brightness( '#f44336', -20 );
		$color_warning       = hestia_adjust_brightness( '#ff9800', -20 );

		$custom_css = '
		.hestia-blog-featured-posts article:nth-child(6n) .btn:hover,
		.hestia-blogs article:nth-of-type(6n) .card-body .btn:hover{
            background-color:' . $color_success . ';
            box-shadow: none;
        }
         
        .hestia-blog-featured-posts article:nth-child(6n+1) .btn:hover,
        .hestia-blogs article:nth-of-type(6n+1) .card-body .btn:hover{
            background-color: ' . $color_primary_hover . ';
            box-shadow: none;
        }
        
        .hestia-blog-featured-posts article:nth-child(6n+2) .btn:hover,
        .hestia-blogs article:nth-of-type(6n+2) .card-body .btn:hover{
            background-color: ' . $color_info . ';
            box-shadow: none;
        }
         
        .hestia-blog-featured-posts article:nth-child(6n+3) .btn:hover,
        .hestia-blogs article:nth-of-type(6n+3) .card-body .btn:hover{
            background-color: ' . $color_danger . ';
            box-shadow: none;
        }
        
        .hestia-blog-featured-posts article:nth-child(6n+4) .btn:hover,
        .hestia-blogs article:nth-of-type(6n+4) .card-body .btn:hover{
            background-color: ' . $color_warning . ';
            box-shadow: none;
        }
        .hestia-blog-featured-posts article:nth-child(6n+5) .btn:hover,
        .hestia-blogs article:nth-of-type(6n+5) .card-body .btn:hover{
            background-color: ' . $color_rose . ';
            box-shadow: none;
        }
        ';

		return $custom_css;
	}

	/**
	 * Buttons hover inline style.
	 *
	 * @return string
	 */
	private function buttons_hover_inline_style() {

		$custom_css = '
		.btn:hover{
			background-color: #858585;
		}
		';

		/**
		 * Gather data from customizer.
		 */
		$hestia_buttons_hover_effect = get_theme_mod( 'hestia_buttons_hover_effect', 'shadow' );
		if ( $hestia_buttons_hover_effect !== 'color' ) {
			return $custom_css;
		}

		$color_accent = get_theme_mod( 'accent_color', apply_filters( 'hestia_accent_color_default', '#e91e63' ) );

		/**
		 * Get hover selectors
		 */
		$all_buttons_selectors = array_merge(
			$this->padding_radius_hover_selectors,
			$this->radius_hover_selectors
		);

		$hover_buttons_selectors = array_map(
			function( $item ) {
				return $item . ':hover';
			},
			$all_buttons_selectors
		);

		/**
		 * Adding color on hover.
		 */
		$custom_css .= implode( ', ', $hover_buttons_selectors ) . '{';
		$custom_css .= '
	    background-color:' . hestia_adjust_brightness( $color_accent, -20 ) . ';
	    border-color:' . hestia_adjust_brightness( $color_accent, -20 ) . ';
	    opacity: 1;
	    -webkit-box-shadow: none!important;
        box-shadow: none!important;
		}
		';
		$custom_css .= '
		.btn.menu-item:hover {
			box-shadow: none;
			color: #fff;
		}
		';
		$custom_css .= '
		.hestia-scroll-to-top:hover {
			box-shadow: none;
		}
		';
		return $custom_css;
	}

}
