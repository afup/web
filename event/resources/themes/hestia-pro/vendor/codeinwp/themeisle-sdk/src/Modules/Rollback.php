<?php
/**
 * The rollback class for ThemeIsle SDK.
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
 * Rollback for ThemeIsle SDK.
 */
class Rollback extends Abstract_Module {

	/**
	 * Add js scripts for themes rollback.
	 */
	public function add_footer() {
		$screen = get_current_screen();
		if ( ! isset( $screen->parent_file ) ) {
			return;
		}
		if ( 'themes.php' !== $screen->parent_file ) {
			return;
		}
		if ( ! $this->product->is_theme() ) {
			return;
		}
		$version = $this->get_rollback();
		if ( empty( $version ) ) {
			return;
		}
		?>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				setInterval(checkTheme, 500);

				function checkTheme() {
					var theme = '<?php echo esc_attr( $this->product->get_slug() ); ?>-action';

					if (jQuery('#' + theme).length > 0) {
						if (jQuery('.theme-overlay.active').is(':visible')) {
							if (jQuery('#' + theme + '-rollback').length === 0) {
								jQuery('.theme-actions .active-theme').prepend('<a class="button" style="float:left" id="' + theme + '-rollback" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=' . $this->product->get_key() . '_rollback' ), $this->product->get_key() . '_rollback' ) ); ?>">Rollback to v<?php echo esc_attr( $version['version'] ); ?></a>')
							}
						}

					}
				}
			})

		</script>
		<?php

	}

	/**
	 * Get the last rollback for this product.
	 *
	 * @return array The rollback version.
	 */
	public function get_rollback() {
		$rollback = array();
		$versions = $this->get_api_versions();
		$versions = apply_filters( $this->product->get_key() . '_rollbacks', $versions );
		if ( empty( $versions ) ) {
			return $rollback;
		}
		if ( $versions ) {
			usort( $versions, array( $this, 'sort_rollback_array' ) );
			foreach ( $versions as $version ) {
				if ( isset( $version['version'] ) && isset( $version['url'] ) && version_compare( $this->product->get_version(), $version['version'], '>' ) ) {
					$rollback = $version;
					break;
				}
			}
		}

		return $rollback;
	}

	/**
	 * Get versions array from wp.org
	 *
	 * @return array Array of versions.
	 */
	private function get_api_versions() {

		$cache_key      = $this->product->get_key() . '_' . preg_replace( '/[^0-9a-zA-Z ]/m', '', $this->product->get_version() ) . 'versions';
		$cache_versions = get_transient( $cache_key );
		if ( false === $cache_versions ) {
			$versions = $this->get_remote_versions();
			set_transient( $cache_key, $versions, 5 * DAY_IN_SECONDS );
		} else {
			$versions = is_array( $cache_versions ) ? $cache_versions : array();
		}

		return $versions;
	}

	/**
	 * Get remote versions zips.
	 *
	 * @return array Array of available versions.
	 */
	private function get_remote_versions() {
		$url = $this->get_versions_api_url();
		if ( empty( $url ) ) {
			return [];
		}
		$response = wp_remote_get( $url );
		if ( is_wp_error( $response ) ) {
			return array();
		}
		$response = wp_remote_retrieve_body( $response );

		if ( is_serialized( $response ) ) {
			$response = maybe_unserialize( $response );
		} else {
			$response = json_decode( $response );
		}

		if ( ! is_object( $response ) ) {
			return array();
		}
		if ( ! isset( $response->versions ) ) {
			return array();
		}

		$versions = array();
		foreach ( $response->versions as $key => $value ) {
			$versions[] = array(
				'version' => is_object( $value ) ? $value->version : $key,
				'url'     => is_object( $value ) ? $value->file : $value,
			);
		}

		return $versions;
	}

	/**
	 * Return url where to check for versions.
	 *
	 * @return string Url where to check for versions.
	 */
	private function get_versions_api_url() {
		if ( $this->product->is_wordpress_available() && $this->product->is_plugin() ) {
			return sprintf( 'https://api.wordpress.org/plugins/info/1.0/%s', $this->product->get_slug() );
		}
		if ( $this->product->is_wordpress_available() && $this->product->is_theme() ) {
			return sprintf( 'https://api.wordpress.org/themes/info/1.1/?action=theme_information&request[slug]=%s&request[fields][versions]=true', $this->product->get_slug() );
		}
		$license = $this->product->get_license();
		if ( $this->product->requires_license() && strlen( $license ) < 10 ) {
			return '';
		}

		return sprintf( '%slicense/versions/%s/%s/%s/%s', Product::API_URL, rawurlencode( $this->product->get_name() ), $license, urlencode( get_site_url() ), $this->product->get_version() );
	}

	/**
	 * Show the rollback links in the plugin page.
	 *
	 * @param array $links Plugin links.
	 *
	 * @return array $links Altered links.
	 */
	public function add_rollback_link( $links ) {
		$version = $this->get_rollback();
		if ( empty( $version ) ) {
			return $links;
		}
		$links[] = '<a href="' . wp_nonce_url( admin_url( 'admin-post.php?action=' . $this->product->get_key() . '_rollback' ), $this->product->get_key() . '_rollback' ) . '">' . sprintf( apply_filters( $this->product->get_key() . '_rollback_label', 'Rollback to v%s' ), $version['version'] ) . '</a>';

		return $links;
	}

	/**
	 * Start the rollback operation.
	 */
	public function start_rollback() {
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], $this->product->get_key() . '_rollback' ) ) {
			wp_nonce_ays( '' );
		}

		if ( $this->product->is_plugin() ) {
			$this->start_rollback_plugin();

			return;
		}
		if ( $this->product->is_theme() ) {
			$this->start_rollback_theme();

			return;
		}
	}

	/**
	 * Start the rollback operation for the plugin.
	 */
	private function start_rollback_plugin() {
		$rollback         = $this->get_rollback();
		$plugin_transient = get_site_transient( 'update_plugins' );
		$plugin_folder    = $this->product->get_slug();
		$plugin_file      = $this->product->get_file();
		$version          = $rollback['version'];
		$temp_array       = array(
			'slug'        => $plugin_folder,
			'new_version' => $version,
			'package'     => $rollback['url'],
		);

		$temp_object = (object) $temp_array;
		$plugin_transient->response[ $plugin_folder . '/' . $plugin_file ] = $temp_object;
		set_site_transient( 'update_plugins', $plugin_transient );

		$transient = get_transient( $this->product->get_key() . '_warning_rollback' );

		// Style fix for the api link that gets outside the content.
		echo '<style>body#error-page{word-break:break-word;}</style>';

		if ( false === $transient ) {
			set_transient( $this->product->get_key() . '_warning_rollback', 'in progress', 30 );
			require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
			$title         = sprintf( apply_filters( $this->product->get_key() . '_rollback_message', 'Rolling back %s to v%s' ), $this->product->get_name(), $version );
			$plugin        = $plugin_folder . '/' . $plugin_file;
			$nonce         = 'upgrade-plugin_' . $plugin;
			$url           = 'update.php?action=upgrade-plugin&plugin=' . urlencode( $plugin );
			$upgrader_skin = new \Plugin_Upgrader_Skin( compact( 'title', 'nonce', 'url', 'plugin' ) );
			$upgrader      = new \Plugin_Upgrader( $upgrader_skin );
			$upgrader->upgrade( $plugin );
			delete_transient( $this->product->get_key() . '_warning_rollback' );
			wp_die(
				'',
				$title,
				array(
					'response' => 200,
				)
			);
		}
	}

	/**
	 * Start the rollback operation for the theme.
	 */
	private function start_rollback_theme() {
		add_filter( 'update_theme_complete_actions', array( $this, 'alter_links_theme_upgrade' ) );
		$rollback   = $this->get_rollback();
		$transient  = get_site_transient( 'update_themes' );
		$folder     = $this->product->get_slug();
		$version    = $rollback['version'];
		$temp_array = array(
			'new_version' => $version,
			'package'     => $rollback['url'],
		);

		$transient->response[ $folder . '/style.css' ] = $temp_array;
		set_site_transient( 'update_themes', $transient );

		$transient = get_transient( $this->product->get_key() . '_warning_rollback' );

		// Style fix for the api link that gets outside the content.
		echo '<style>body#error-page{word-break:break-word;}</style>';

		if ( false === $transient ) {
			set_transient( $this->product->get_key() . '_warning_rollback', 'in progress', 30 );
			require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
			$title = sprintf( apply_filters( $this->product->get_key() . '_rollback_message', 'Rolling back %s to v%s' ), $this->product->get_name(), $version );
			$theme = $folder . '/style.css';
			$nonce = 'upgrade-theme_' . $theme;
			$url   = 'update.php?action=upgrade-theme&theme=' . urlencode( $theme );

			$upgrader = new \Theme_Upgrader( new \Theme_Upgrader_Skin( compact( 'title', 'nonce', 'url', 'theme' ) ) );
			$upgrader->upgrade( $theme );
			delete_transient( $this->product->get_key() . '_warning_rollback' );
			wp_die(
				'',
				$title,
				array(
					'response' => 200,
				)
			);
		}
	}

	/**
	 * Alter links and remove duplicate customize message.
	 *
	 * @param array $links Array of old links.
	 *
	 * @return mixed Array of links.
	 */
	public function alter_links_theme_upgrade( $links ) {
		if ( isset( $links['preview'] ) ) {
			$links['preview'] = str_replace( '<span aria-hidden="true">Customize</span>', '', $links['preview'] );
		}

		return $links;
	}

	/**
	 * Loads product object.
	 *
	 * @param Product $product Product object.
	 *
	 * @return bool Should we load the module?
	 */
	public function can_load( $product ) {
		if ( $this->is_from_partner( $product ) ) {
			return false;
		}
		if ( $product->is_theme() && ! current_user_can( 'switch_themes' ) ) {
			return false;
		}

		if ( $product->is_plugin() && ! current_user_can( 'install_plugins' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Sort the rollbacks array in descending order.
	 *
	 * @param mixed $a First version to compare.
	 * @param mixed $b Second version to compare.
	 *
	 * @return bool Which version is greater?
	 */
	public function sort_rollback_array( $a, $b ) {
		return version_compare( $a['version'], $b['version'], '<' ) > 0;
	}

	/**
	 * Load module logic.
	 *
	 * @param Product $product Product object.
	 *
	 * @return $this Module object.
	 */
	public function load( $product ) {
		$this->product = $product;
		$this->show_link();
		$this->add_hooks();

		return $this;
	}

	/**
	 * If product can be rolled back, show the link to rollback.
	 */
	private function show_link() {
		add_filter(
			'plugin_action_links_' . plugin_basename( $this->product->get_basefile() ),
			array(
				$this,
				'add_rollback_link',
			)
		);
	}

	/**
	 * Set the rollback hook. Strangely, this does not work if placed in the ThemeIsle_SDK_Rollback class, so it is being called from there instead.
	 */
	public function add_hooks() {
		add_action( 'admin_post_' . $this->product->get_key() . '_rollback', array( $this, 'start_rollback' ) );
		add_action( 'admin_footer', array( $this, 'add_footer' ) );
	}
}
