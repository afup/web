<?php
/**
 * Page settings metabox.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Metabox_Main
 *
 * @package Hestia
 */
class Hestia_Metabox_Main extends Hestia_Metabox_Controls_Base {
	/**
	 * Add controls.
	 */
	public function add_controls() {
		$post_type = '';
		if ( array_key_exists( 'post_type', $_GET ) ) {
			$post_type = $_GET['post_type'];
		}
		if ( empty( $post_type ) && array_key_exists( 'post', $_GET ) ) {
			$post_type = get_post_type( $_GET['post'] );
		}
		switch ( $post_type ) {
			case 'jetpack-portfolio':
				$this->content_toggles();
				break;
			case 'product':
				break;
			default:
				$this->sidebar_control();
				$this->content_toggles();
		}
	}

	/**
	 * Add sidebar layout control.
	 */
	private function sidebar_control() {
		$sidebar_control_choices = array(
			'full-width'    => array(
				'url'   => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqAQMAAABknzrDAAAABlBMVEX////V1dXUdjOkAAAAPUlEQVRIx2NgGAUkAcb////Y/+d/+P8AdcQoc8vhH/X/5P+j2kG+GA3CCgrwi43aMWrHqB2jdowEO4YpAACyKSE0IzIuBgAAAABJRU5ErkJggg==',
				'label' => esc_html__( 'Full Width', 'hestia-pro' ),
			),
			'sidebar-left'  => array(
				'url'   => apply_filters( 'hestia_layout_control_image_left', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqAgMAAAAjP0ATAAAACVBMVEX///8+yP/V1dXG9YqxAAAAWElEQVR42mNgGAXDE4RCQMDAKONaBQINWqtWrWBatQDIaxg8ygYqQIAOYwC6bwHUmYNH2eBPSMhgBQXKRr0w6oVRL4x6YdQLo14Y9cKoF0a9QCO3jYLhBADvmFlNY69qsQAAAABJRU5ErkJggg==' ),
				'label' => esc_html__( 'Left Sidebar', 'hestia-pro' ),
			),
			'sidebar-right' => array(
				'url'   => apply_filters( 'hestia_layout_control_image_right', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqAgMAAAAjP0ATAAAACVBMVEX///8+yP/V1dXG9YqxAAAAWUlEQVR42mNgGAUjB4iGgkEIzZStAoEVTECiQWsVkLdiECkboAABOmwBF9BtUGcOImUDEiCkJCQU0ECBslEvjHph1AujXhj1wqgXRr0w6oVRLwyEF0bBUAUAz/FTNXm+R/MAAAAASUVORK5CYII=' ),
				'label' => esc_html__( 'Right Sidebar', 'hestia-pro' ),
			),
		);

		$page_template = get_post_meta( $this->post_id, '_wp_page_template', true );
		if ( $page_template === 'page-templates/template-page-sidebar.php' ) {
			unset( $sidebar_control_choices['full-width'] );
		}

		$control_settings = array(
			'label'           => esc_html__( 'Sidebar', 'hestia-pro' ),
			'choices'         => $sidebar_control_choices,
			'active_callback' => array( $this, 'show_sidebar_meta' ),
			'default'         => $this->get_sidebar_default_value(),
		);

		$this->add_control(
			new Hestia_Metabox_Radio_Image(
				'hestia_layout_select',
				1,
				$control_settings
			)
		);
	}

	/**
	 * Get default value.
	 */
	private function get_sidebar_default_value() {
		if ( empty( $_GET['post'] ) ) {
			return '';
		}

		$default           = hestia_get_blog_layout_default();
		$post_type         = get_post_type( $_GET['post'] );
		$page_for_posts_id = get_option( 'page_for_posts' );
		$shop_page         = get_option( 'woocommerce_shop_page_id' );

		if ( (int) $_GET['post'] === (int) $shop_page ) {
			return get_theme_mod( 'hestia_shop_sidebar_layout', Hestia_General_Controls::get_shop_sidebar_layout_default() );
		}
		if ( (int) $_GET['post'] === (int) $page_for_posts_id ) {
			return get_theme_mod( 'hestia_blog_sidebar_layout', $default );
		}
		if ( 'page' === $post_type ) {
			return get_theme_mod( 'hestia_page_sidebar_layout', 'full-width' );
		}

		return get_theme_mod( 'hestia_blog_sidebar_layout', $default );
	}

	/**
	 * Content toggles controls.
	 */
	private function content_toggles() {

		$content_controls = array(
			'hestia_disable_navigation' => array(
				'default'     => 'off',
				'label'       => __( 'Components', 'hestia-pro' ),
				'input_label' => __( 'Disable Navigation', 'hestia-pro' ),
				'priority'    => 3,
			),
			'hestia_disable_footer'     => array(
				'default'     => 'off',
				'input_label' => __( 'Disable Footer', 'hestia-pro' ),
				'priority'    => 4,
			),
		);

		$default_control_args = array(
			'default'         => 'off',
			'label'           => '',
			'input_label'     => '',
			'active_callback' => '__return_true',
			'priority'        => 10,
		);

		foreach ( $content_controls as $control_id => $args ) {
			$args = wp_parse_args( $args, $default_control_args );

			$this->add_control(
				new Hestia_Metabox_Checkbox(
					$control_id,
					$args['priority'],
					array(
						'default'         => $args['default'],
						'label'           => $args['label'],
						'input_label'     => $args['input_label'],
						'active_callback' => $args['active_callback'],
					)
				)
			);
		}
	}

	/**
	 * Display callback for the sidebar position metabox control.
	 *
	 * @return bool
	 */
	public function show_sidebar_meta() {

		if ( $this->post_id === null ) {
			return true;
		}

		if ( $this->is_restricted_woocommerce_page() ) {
			return false;
		}

		if ( $this->is_sections_front_page() ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if front-page is enabled and it's the current page.
	 *
	 * @return bool
	 */
	protected function is_sections_front_page() {

		if ( get_option( 'show_on_front' ) !== 'page' ) {
			return false;
		}

		$frontpage_id = get_option( 'page_on_front' );

		if ( empty( $frontpage_id ) ) {
			return false;
		}

		if ( $this->post_id !== $frontpage_id ) {
			return false;
		}

		$page_template = get_post_meta( $frontpage_id, '_wp_page_template', true );
		if ( ! empty( $page_template ) && 'default' !== $page_template ) {
			return false;
		}

		$disabled_frontpage = get_theme_mod( 'disable_frontpage_sections', false );
		if ( true === (bool) $disabled_frontpage ) {
			return false;
		}

		return true;

	}

	/**
	 * Check if is restricted Woo page.
	 *
	 * @return bool
	 */
	private function is_restricted_woocommerce_page() {
		$woo_page_options = array(
			'woocommerce_cart_page_id',
			'woocommerce_checkout_page_id',
			'woocommerce_myaccount_page_id',
		);

		foreach ( $woo_page_options as $key ) {
			if ( get_option( $key ) !== $this->post_id ) {
				continue;
			}

			return true;
		}

		return false;
	}
}
