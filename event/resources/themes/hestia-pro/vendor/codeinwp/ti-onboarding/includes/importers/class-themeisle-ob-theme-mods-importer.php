<?php
/**
 * Theme Mods Importer.
 *
 * Author:  Andrei Baicus <andrei@themeisle.com>
 * On:      21/06/2018
 *
 * @package    themeisle-onboarding
 * @soundtrack Twentieth Century Fox - The Doors
 */

/**
 * Class Themeisle_OB_Theme_Mods_Importer
 */
class Themeisle_OB_Theme_Mods_Importer {
	use Themeisle_OB;

	/**
	 * Log
	 *
	 * @var
	 */
	private $log = '';

	/**
	 * Source URL.
	 *
	 * @var string
	 */
	private $source_url = '';

	/**
	 * Theme mods array.
	 *
	 * @var array
	 */
	private $theme_mods = array();

	/**
	 * Options array.
	 *
	 * @var array
	 */
	private $options = array();

	/**
	 * Import theme mods.
	 *
	 * @param WP_REST_Request $request the async request.
	 *
	 * @return WP_REST_Response
	 */
	public function import_theme_mods( WP_REST_Request $request ) {
		if ( ! current_user_can( 'customize' ) ) {
			return new WP_REST_Response(
				array(
					'data'    => 'ti__ob_permission_err_2',
					'success' => false,
				)
			);
		}

		do_action( 'themeisle_ob_before_customizer_import' );

		$data = $request->get_body_params();
		$data = $data['data'];

		if ( ! isset( $data['source_url'] ) || empty( $data['source_url'] ) ) {

			return new WP_REST_Response(
				array(
					'data'    => 'ti__ob_theme_mods_err_1',
					'success' => false,
				)
			);
		}

		if ( ! isset( $data['theme_mods'] ) || empty( $data['theme_mods'] ) ) {
			return new WP_REST_Response(
				array(
					'data'    => 'ti__ob_theme_mods_err_2',
					'success' => false,
				)
			);
		}
		$this->source_url = $data['source_url'];
		$this->theme_mods = $data['theme_mods'];
		array_walk( $this->theme_mods, array( $this, 'change_theme_mods_root_url' ) );

		foreach ( $this->theme_mods as $mod => $value ) {
			if ( $mod === '__ti_import_menus_location' ) {
				continue;
			}
			if ( $value === 'true' ) {
				$value = true;
			}

			if ( $value === 'false' ) {
				$value = false;
			}
			set_theme_mod( $mod, $value );
		}

		$this->options = isset( $data['wp_options'] ) ? $data['wp_options'] : array();
		foreach ( $this->options as $key => $value ) {
			if ( is_array( $value ) ) {
				array_walk_recursive(
					$value,
					function ( &$item ) {
						if ( $item == 'true' ) {
							$item = true;
						} elseif ( $item == 'false' ) {
							$item = false;
						} elseif ( is_numeric( $item ) ) {
							$item = intval( $item );
						}
					}
				);
			}
			update_option( $key, $value );
		}

		// Set nav menu locations.
		if ( isset( $this->theme_mods['__ti_import_menus_location'] ) ) {
			$menus = $this->theme_mods['__ti_import_menus_location'];
			$this->setup_nav_menus( $menus );
		}

		do_action( 'themeisle_ob_after_customizer_import' );

		return new WP_REST_Response(
			array(
				'data'    => 'success',
				'success' => true,
				'log'     => $this->log,
			)
		);
	}

	/**
	 * Set up the `nav_menu_locations` theme mod.
	 *
	 * @param array $menus represents the menu data as as [location => slug] retrieved from the API.
	 */
	public function setup_nav_menus( $menus ) {
		do_action( 'themeisle_ob_before_nav_menus_setup' );

		if ( empty( $menus ) || ! is_array( $menus ) ) {
			return;
		}

		$setup_menus = array();
		foreach ( $menus as $location => $menu_slug ) {

			$menu_object              = wp_get_nav_menu_object( $menu_slug );
			$term_id                  = $menu_object->term_id;
			$setup_menus[ $location ] = $term_id;
		}
		if ( empty( $setup_menus ) ) {
			$this->log .= 'No menus to set up locations for.' . "\n";

			return;
		}
		set_theme_mod( 'nav_menu_locations', $setup_menus );

		$this->log .= 'Menus are set up.' . "\n";

		do_action( 'themeisle_ob_after_nav_menus_setup' );
	}

	/**
	 * Change the theme mods root url.
	 *
	 * @param string $item theme mod.
	 *
	 * @return void
	 */
	private function change_theme_mods_root_url( &$item ) {
		do_action( 'themeisle_ob_before_change_theme_mods_root_url' );
		$item = $this->replace_image_urls( $item );
		do_action( 'themeisle_ob_after_change_theme_mods_root_url' );
	}

}
