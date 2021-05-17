<?php
/**
 * The class that exposes hooks for recommend.
 *
 * @package     ThemeIsleSDK
 * @subpackage  Rollback
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       1.0.0
 */

namespace ThemeisleSDK\Modules;

// Exit if accessed directly.
use ThemeisleSDK\Common\Abstract_Module;
use ThemeisleSDK\Product;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Expose endpoints for ThemeIsle SDK.
 */
class Recommendation extends Abstract_Module {


	/**
	 * Load module logic.
	 *
	 * @param Product $product Product to load.
	 */
	public function load( $product ) {
		$this->product = $product;
		$this->setup_hooks();

		return $this;
	}

	/**
	 * Setup endpoints.
	 */
	private function setup_hooks() {
		add_action( $this->product->get_key() . '_recommend_products', array( $this, 'render_products_box' ), 10, 4 );
		add_action( 'admin_head', array( $this, 'enqueue' ) );
	}

	/**
	 * Check if we should load the module for this product.
	 *
	 * @param Product $product Product data.
	 *
	 * @return bool Should we load the module?
	 */
	public function can_load( $product ) {
		return true;
	}

	/**
	 * Render products box content.
	 *
	 * @param array $plugins_list - list of useful plugins (in slug => nicename format).
	 * @param array $themes_list - list of useful themes (in slug => nicename format).
	 * @param array $strings - list of translated strings.
	 * @param array $preferences - list of preferences.
	 */
	function render_products_box( $plugins_list, $themes_list, $strings, $preferences = array() ) {

		if ( empty( $plugins_list ) && empty( $themes_list ) ) {
			return;
		}

		if ( ! empty( $plugins_list ) && ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		if ( ! empty( $themes_list ) && ! current_user_can( 'install_themes' ) ) {
			return;
		}

		add_thickbox();

		if ( ! empty( $themes_list ) ) {
			$list = $this->get_themes( $themes_list, $preferences );

			if ( has_action( $this->product->get_key() . '_recommend_products_theme_template' ) ) {
				do_action( $this->product->get_key() . '_recommend_products_theme_template', $list, $strings, $preferences );
			} else {
				echo '<div class="recommend-product">';

				foreach ( $list as $theme ) {
					echo '<div class="plugin_box">';
					echo '  <img class="theme-banner" src="' . $theme->screenshot_url . '">';
					echo '	<div class="title-action-wrapper">';
					echo '		<span class="plugin-name">' . esc_html( $theme->custom_name ) . '</span>';
					if ( ! isset( $preferences['description'] ) || ( isset( $preferences['description'] ) && $preferences['description'] ) ) {
						echo '<span class="plugin-desc">' . esc_html( substr( $theme->description, 0, strpos( $theme->description, '.' ) ) ) . '.</span>';
					}
					echo '	</div>';
					echo '<div class="plugin-box-footer">';
					echo '		<div class="button-wrap">';
					echo '          <a class="button button-primary  " href="' . esc_url( $theme->custom_url ) . '"><span class="dashicons dashicons-external"></span>' . esc_html( $strings['install'] ) . '</a>';
					echo '		</div>';
					echo '	</div>';
					echo '</div>';
				}

				echo '</div>';
			}
		}
		if ( ! empty( $plugins_list ) ) {
			$list = $this->get_plugins( $plugins_list, $preferences );

			if ( has_action( $this->product->get_key() . '_recommend_products_plugin_template' ) ) {
				do_action( $this->product->get_key() . '_recommend_products_plugin_template', $list, $strings, $preferences );
			} else {
				echo '<div class="recommend-product">';

				foreach ( $list as $current_plugin ) {
					echo '<div class="plugin_box">';
					echo '      <img class="plugin-banner" src="' . $current_plugin->custom_image . '">';
					echo '	<div class="title-action-wrapper">';
					echo '		<span class="plugin-name">' . esc_html( $current_plugin->custom_name ) . '</span>';
					if ( ! isset( $preferences['description'] ) || ( isset( $preferences['description'] ) && $preferences['description'] ) ) {
						echo '<span class="plugin-desc">' . esc_html( substr( $current_plugin->short_description, 0, strpos( $current_plugin->short_description, '.' ) ) ) . '. </span>';
					}
					echo '	</div>';
					echo '	<div class="plugin-box-footer">';
					echo '      <a class="button button-primary thickbox open-plugin-details-modal" href="' . esc_url( $current_plugin->custom_url ) . '"><span class="dashicons dashicons-external"></span>' . esc_html( $strings['install'] ) . '</a>';
					echo '	</div>';
					echo '</div>';
				}

				echo '</div>';
			}
		}

	}

	/**
	 * Collect all the information for the themes list.
	 *
	 * @param array $themes_list - list of useful themes (in slug => nicename format).
	 * @param array $preferences - list of preferences.
	 *
	 * @return array
	 */
	private function get_themes( $themes_list, $preferences ) {
		$list = array();
		foreach ( $themes_list as $slug => $nicename ) {
			$theme = $this->call_theme_api( $slug );
			if ( ! $theme ) {
				continue;
			}

			$url = add_query_arg(
				array(
					'theme' => $theme->slug,
				),
				network_admin_url( 'theme-install.php' )
			);

			$name = empty( $nicename ) ? $theme->name : $nicename;

			$theme->custom_url  = $url;
			$theme->custom_name = $name;

			$list[] = $theme;
		}

		return $list;
	}

	/**
	 * Call theme api
	 *
	 * @param string $slug theme slug.
	 *
	 * @return array|mixed|object
	 */
	private function call_theme_api( $slug ) {
		$theme = get_transient( 'ti_theme_info_' . $slug );

		if ( false !== $theme ) {
			return $theme;
		}

		$products = wp_remote_get(
			'https://api.wordpress.org/themes/info/1.1/?action=query_themes&request[theme]=' . $slug . '&request[per_page]=1'
		);
		$products = json_decode( wp_remote_retrieve_body( $products ) );
		if ( is_object( $products ) ) {
			$theme = $products->themes[0];
			set_transient( 'ti_theme_info_' . $slug, $theme, 6 * HOUR_IN_SECONDS );
		}

		return $theme;
	}

	/**
	 * Collect all the information for the plugins list.
	 *
	 * @param array $plugins_list - list of useful plugins (in slug => nicename format).
	 * @param array $preferences - list of preferences.
	 *
	 * @return array
	 */
	private function get_plugins( $plugins_list, $preferences ) {
		$list = array();
		foreach ( $plugins_list as $plugin => $nicename ) {
			$current_plugin = $this->call_plugin_api( $plugin );

			$name = empty( $nicename ) ? $current_plugin->name : $nicename;

			$image = $current_plugin->banners['low'];
			if ( isset( $preferences['image'] ) && 'icon' === $preferences['image'] ) {
				$image = $current_plugin->icons['1x'];
			}

			$url = add_query_arg(
				array(
					'tab'       => 'plugin-information',
					'plugin'    => $current_plugin->slug,
					'TB_iframe' => true,
					'width'     => 800,
					'height'    => 800,
				),
				network_admin_url( 'plugin-install.php' )
			);

			$current_plugin->custom_url   = $url;
			$current_plugin->custom_name  = $name;
			$current_plugin->custom_image = $image;

			$list[] = $current_plugin;
		}

		return $list;
	}

	/**
	 * Call plugin api
	 *
	 * @param string $slug plugin slug.
	 *
	 * @return array|mixed|object
	 */
	private function call_plugin_api( $slug ) {
		include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );

		$call_api = get_transient( 'ti_plugin_info_' . $slug );

		if ( false === $call_api ) {
			$call_api = plugins_api(
				'plugin_information',
				array(
					'slug'   => $slug,
					'fields' => array(
						'downloaded'        => false,
						'rating'            => false,
						'description'       => false,
						'short_description' => true,
						'donate_link'       => false,
						'tags'              => false,
						'sections'          => true,
						'homepage'          => true,
						'added'             => false,
						'last_updated'      => false,
						'compatibility'     => false,
						'tested'            => false,
						'requires'          => false,
						'downloadlink'      => false,
						'icons'             => true,
						'banners'           => true,
					),
				)
			);
			set_transient( 'ti_plugin_info_' . $slug, $call_api, 30 * MINUTE_IN_SECONDS );
		}

		return $call_api;
	}

	/**
	 * Load css and scripts for the plugin recommend page.
	 */
	public function enqueue() {
		$screen = get_current_screen();

		if ( ! isset( $screen->id ) ) {
			return;
		}
		if ( false === apply_filters( $this->product->get_key() . '_enqueue_recommend', false, $screen->id ) ) {
			return;
		}

		?>
		<style type="text/css">
			.recommend-product {
				display: flex;
				justify-content: space-between;
				flex-wrap: wrap;
			}

			.recommend-product .theme-banner {
				width:200px;
				margin: auto;
			}
			.recommend-product .plugin-banner {
				width: 100px;
				margin: auto;
			}

			.recommend-product .plugin_box .button span{

				margin-top: 2px;
				margin-right: 7px;
			}
			.recommend-product .plugin_box .button{
				margin-bottom:10px;
			}
			.recommend-product .plugin_box {
				margin-bottom: 20px;
				padding-top: 5px;
				display: flex;
				box-shadow: 0px 0px 10px -5px rgba(0,0,0,0.55);
				background: #fff;
				border-radius: 5px;
				flex-direction: column;
				justify-content: flex-start;
				width: 95%;
			}

			.recommend-product .title-action-wrapper {
				padding: 15px 20px 5px 20px;
			}

			.recommend-product .plugin-name {
				font-size: 18px;
				display: block;
				white-space: nowrap;
				text-overflow: ellipsis;
				margin-bottom: 10px;
				overflow: hidden;
				line-height: normal;
			}


			.recommend-product .plugin-desc {
				display: block;
				margin-bottom: 10px;
				font-size: 13px;
				color: #777;
				line-height: 1.6;
			}

			.recommend-product .button-wrap > div {
				padding: 0;
				margin: 0;
			}

			.plugin-box-footer {
				display: flex;
				justify-content: space-around;
				vertical-align: middle;
				align-items: center;
				padding: 0px 10px 5px;
				flex: 1;
				margin-top: auto;
			}
		</style>
		<?php
	}
}
