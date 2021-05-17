<?php
/**
 * Handle the header addons.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Header_Addon
 */
class Hestia_Header_Addon extends Hestia_Header {

	/**
	 * Initialize
	 */
	public function init() {
		parent::init();

		if ( ! $this->should_display_nav_cart() ) {
			return;
		}

		$this->add_navigation_cart();

	}

	/**
	 * Add navigation cart.
	 */
	private function add_navigation_cart() {
		add_action( 'hestia_after_navbar_toggle_hook', array( $this, 'render_responsive_nav_cart' ), 0 );
		add_filter( 'hestia_after_primary_navigation_addons', array( $this, 'add_nav_cart' ) );
		add_action( 'hestia_after_navbar_toggle_hook', array( $this, 'render_nav_cart' ), 0 );
	}

	/**
	 * Add nav cart.
	 *
	 * @param string $markup the markup for the navigation addon.
	 *
	 * @return string
	 */
	public function add_nav_cart( $markup ) {
		if ( $this->is_full_screen_menu() ) {
			return $markup;
		}

		$cart = $this->cart_item();

		$markup .= $cart;

		return $markup;
	}

	/**
	 * Check if nav cart should be displayed.
	 *
	 * @return bool
	 */
	private function should_display_nav_cart() {

		if ( ! class_exists( 'WooCommerce', false ) ) {
			return false;
		}

		$theme_locations = get_nav_menu_locations();
		if ( ! array_key_exists( 'primary', $theme_locations ) ) {
			return false;
		}

		$menu = wp_get_nav_menu_items( $theme_locations['primary'] );
		if ( empty( $menu ) ) {
			return false;
		}

		$top_bar_hidden     = get_theme_mod( 'hestia_top_bar_hide', true );
		$header_has_widgets = get_theme_mod( 'hestia_header_alignment', apply_filters( 'hestia_header_alignment_default', 'left' ) );

		if ( (bool) $top_bar_hidden === false && $this->sidebar_has_widget( 'sidebar-top-bar', 'woocommerce_widget_cart' ) ) {
			return false;
		}

		if ( $header_has_widgets === 'right' && $this->sidebar_has_widget( 'header-sidebar', 'woocommerce_widget_cart' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Display cart item in menu.
	 *
	 * @param boolean $responsive rendering the responsive cart.
	 *
	 * @return string
	 */
	public function cart_item( $responsive = false ) {
		$class = 'nav-cart';
		$cart  = '';
		if ( (bool) $responsive === true ) {
			$class .= ' responsive-nav-cart';
		}
		$cart .= '<li class="' . esc_attr( $class ) . '"><a href="' . esc_url( wc_get_cart_url() ) . '" title="' . esc_attr__( 'View cart', 'hestia-pro' ) . '" class="nav-cart-icon"><i class="fas fa-shopping-cart"></i>' . trim( ( is_object( WC()->cart ) && ( WC()->cart->get_cart_contents_count() > 0 ) ) ? '<span>' . WC()->cart->get_cart_contents_count() . '</span>' : '' ) . '</a>';
		if ( (bool) $responsive !== true ) {
			$cart .= $this->cart_content();
		}
		$cart .= '</li>';
		hestia_load_fa();
		return $cart;
	}

	/**
	 * Function to display cart content.
	 *
	 * @since  1.1.24
	 * @access public
	 */
	private function cart_content() {
		ob_start();
		the_widget(
			'WC_Widget_Cart',
			array(
				'title' => ' ',
			),
			array(
				'before_title' => '',
				'after_title'  => '',
			)
		);
		$cart = ob_get_contents();
		ob_end_clean();

		return '<div class="nav-cart-content">' . $cart . '</div>';
	}

	/**
	 * Render responsive cart.
	 */
	public function render_responsive_nav_cart() {
		echo $this->cart_item( true );
	}

	/**
	 * Render nav cart.
	 */
	public function render_nav_cart() {
		if ( $this->is_full_screen_menu() ) {
			echo $this->cart_item( false );
		}
	}

	/**
	 * Utility function to check if a sidebar has widgets.
	 *
	 * @param string $sidebar_slug sidebar to search in.
	 * @param string $widget_slug  widget to search for.
	 *
	 * @return bool
	 */
	private function sidebar_has_widget( $sidebar_slug, $widget_slug ) {
		$active_widgets = get_option( 'sidebars_widgets' );
		if ( is_active_sidebar( $sidebar_slug ) ) {
			if ( $active_widgets[ $sidebar_slug ] ) {
				foreach ( $active_widgets[ $sidebar_slug ] as $item ) {
					if ( strpos( $item, $widget_slug ) !== false ) {
						return true;
					}
				}
			}
		}

		return false;
	}
}
