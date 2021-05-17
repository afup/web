<?php
/**
 * Page settings metabox in pro.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Metabox_Addon
 */
class Hestia_Metabox_Addon extends Hestia_Metabox_Main {

	/**
	 * Init function.
	 */
	public function init() {
		parent::init();
		add_filter( 'hestia_metabox_post_types', array( $this, 'filter_metabox_post_types' ) );
	}

	/**
	 * Filter metabox post types.
	 *
	 * @param array $post_types Post types where metaboxes should be displayed.
	 *
	 * @return array
	 */
	public function filter_metabox_post_types( $post_types ) {
		$post_types[] = 'product';
		return $post_types;
	}

	/**
	 * Add controls.
	 */
	public function add_controls() {
		parent::add_controls();

		$post_type = '';
		if ( array_key_exists( 'post_type', $_GET ) ) {
			$post_type = $_GET['post_type'];
		}
		if ( empty( $post_type ) && array_key_exists( 'post', $_GET ) ) {
			$post_type = get_post_type( $_GET['post'] );
		}

		$control_settings = array(
			'label'           => esc_html__( 'Header Layout', 'hestia-pro' ),
			'choices'         => array(
				'default'      => array(
					'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqBAMAAACsf7WzAAAAD1BMVEU+yP/////Y9P/G7//V1dUbjhlcAAAAW0lEQVR4Ae3SAQmAYAyE0V9NMDCBCQxh/0wKGGCAIJ7vC3DA28ZvkjRVo49vzVujoeYFbF15i32pu4CtlCTVc+Vu2VqPRi9ssWfPnj179uzZs2fPnj179uwzt07LZ+4ImOW7JwAAAABJRU5ErkJggg==',
				),
				'no-content'   => array(
					'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqBAMAAACsf7WzAAAAElBMVEU+yP////88SFhjbXl1fonV1dUUDrn8AAAAXElEQVR4Ae3SMQ2AYAyEUSwAYOC3gAJE4N8KCztNKEPT9wm44eUmSZL0b3NeXbeWEaj41noEet/yCVs+cW7jqfjW12ztV6D8Lfbs2bNnz549e/bs2bNnz559060bqAJ8azq5sAYAAAAASUVORK5CYII=',
				),
				'classic-blog' => array(
					'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqBAMAAACsf7WzAAAAElBMVEX///88SFhjbXl1fok+yP/V1dWks4cUAAAAXElEQVR4Ae3SMQ2AQBBE0QNAwFlAASKwgH8rNNSwCdfs5j0BU/xMo6ypByTfmveAxmd7Wz5xLP2Rf4tf1jPAli1btl7YsmWL7QoYuoX22lelvfbaa6892mufifbcjgr1IbRYbwEAAAAASUVORK5CYII=',
				),
			),
			'active_callback' => array( $this, 'header_layout_meta_callback' ),
			'default'         => $this->get_header_default_value(),
		);

		if ( $post_type === 'product' ) {
			$control_settings['choices']['default']      = array();
			$control_settings['choices']['no-content']   = array(
				'url' => 'data:image/jpeg;base64,iVBORw0KGgoAAAANSUhEUgAAAEwAAAA2AgMAAAAQQJcfAAAADFBMVEXV1dX///8+yP88SFiChfYKAAAAU0lEQVR42mNYhQkYhpFYKBIIo4aYAwMDA/t/ILiKKsaKRR29xYAAWSyEAQIoFmMBMxmJEaOavUgAr5gDAwww0lUMm1vq//8n1s2g4HNAs2MoiQEA/Bgnc8hOvu4AAAAASUVORK5CYII=',
			);
			$control_settings['choices']['classic-blog'] = array(
				'url' => 'data:image/jpeg;base64,iVBORw0KGgoAAAANSUhEUgAAAEwAAAA2CAMAAABa8I++AAABI1BMVEX////R0dHw8PD7+/vo6OjOzs7u7u7V1dXp6enq6url5eXPz8/39/fz8/Pv7+/Q0NDn5+fZ2dn8/Pzt7e2Xnab09PTT09PW1tbf39/e3t7m5ubt7Ozx8fLs7OzZ3OBUX236+vrKyspQW2ny8vL5+fm/w8irsLdVYG6orrXr6+v9/f3x8fHa2trJycn4+PjGxsbNzc3Hx8fv8PDT1tpTXmzU1NPk5OTMzMzi4uLf4ePn6ev29vaKkZthanf+/v6Ql6BjbHru7e319fXAw8n09fa7v8Tr6+pPWmj19fTw8PH19vjN0NQ6R1fV2NxDTl69wcfZ3N/z8/HT09Gyt72coqpWYW6lqrKdo6zc3d6wtbzl5uaRmKFganhNV2akqbGus7qnrLNW7fgBAAABR0lEQVR42u3XV1ODMADAcUpClFboQFpFUSo4wO7WPeree2/9/p9C4crIXa8N1Ifq5feWC/xfSHKEofqMpiLcde3xdRjTXF8ljM3nOdzN09v7rO2+MeU43NpYIYwJsQHcwsvnx5zt4Xbadrn/81TkmPL8NWO7q7ce6SHGqd5cBmNFiCV5KQXbUFC0GBxsg8ZozI3xMBUUlyLFVMV5e5EFmEyk7QQ052XNCr039UoMl+eZyORcGpdFzD8ERJYMkrvHSnFIhmSdGZAnA8W/HNMOxlzbMN7BsknwNWvjruOdoQ7WjO6x04vGZMvZbq/rbO/8aqKlfsL8oiNhkxU9kgEk0YcEkh+XhCef1pIVb1RV9WrCVyyEO89ioyCX9Q82JHPBQ3OExmiMxsgvFfbeXPJGZVMvc75iIdx1R9UtJHkjswRMFJxkqD7zDZlNTjDybHHOAAAAAElFTkSuQmCC',
			);
		}

		$this->add_control(
			new Hestia_Metabox_Radio_Image(
				'hestia_header_layout',
				2,
				$control_settings
			)
		);
	}

	/**
	 * Get default value for header layout.
	 *
	 * @return string
	 */
	private function get_header_default_value() {

		if ( empty( $_GET['post'] ) ) {
			return '';
		}

		if ( array_key_exists( 'post_type', $_GET ) ) {
			$post_type = $_GET['post_type'];
		}
		if ( empty( $post_type ) && array_key_exists( 'post', $_GET ) ) {
			$post_type = get_post_type( $_GET['post'] );
		}

		$page_id = (int) $_GET['post'];

		if ( 'jetpack-portfolio' === $post_type ) {
			return 'default';
		}

		if ( class_exists( 'WooCommerce', false ) ) {
			if ( is_product() ) {
				return get_theme_mod( 'hestia_product_layout', 'no-content' );
			}

			$shop_id = get_option( 'woocommerce_shop_page_id' );
			if ( ! empty( $shop_id ) && $page_id === (int) $shop_id ) {
				return 'default';
			}

			$cart_id = get_option( 'woocommerce_cart_page_id' );
			if ( ! empty( $cart_id ) && $page_id === (int) $cart_id ) {
				return 'no-content';
			}

			$checkout_id = get_option( 'woocommerce_checkout_page_id' );
			if ( ! empty( $checkout_id ) && $page_id === (int) $checkout_id ) {
				return 'no-content';
			}
		}

		if ( 'page' === get_option( 'show_on_front' ) ) {
			if ( get_option( 'page_for_posts' ) === $page_id ) {
				return 'default';
			}
		}

		return get_theme_mod( 'hestia_header_layout', 'default' );

	}

	/**
	 * Function that decide if sidebar metabox should be shown.
	 *
	 * @return bool
	 */
	public function header_layout_meta_callback() {
		if ( $this->is_sections_front_page() ) {
			return false;
		}

		global $post;

		if ( empty( $post ) ) {
			return false;
		}

		$post_type = get_post_type( $post->ID );
		if ( 'jetpack-portfolio' === $post_type ) {
			return true;
		}

		return $this->is_allowed_template( $post->ID );
	}

	/**
	 * Detect if is a page with sidebar template
	 *
	 * @param string $post_id Post id.
	 *
	 * @return bool
	 */
	protected function is_allowed_template( $post_id ) {
		$allowed_templates = array(
			'default',
			'page-templates/template-fullwidth.php',
			'page-templates/template-page-sidebar.php',
		);

		$page_template = get_post_meta( $post_id, '_wp_page_template', true );
		if ( empty( $page_template ) ) {
			return true;
		}

		return in_array( $page_template, $allowed_templates, true );
	}
}
