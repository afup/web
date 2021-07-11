<?php
/**
 * Class that handle the show/hide hooks.
 *
 * @package Inc/Addons/Modules/Custom_Layouts/Admin
 */

/**
 * Class Hestia_View_Hooks
 */
class Hestia_View_Hooks {

	/**
	 * Initialize function.
	 */
	public function init() {
		add_action( 'admin_bar_menu', array( $this, 'admin_bar_menu' ), 99 );
		add_action( 'wp', array( $this, 'render_hook_placeholder' ) );
	}
	/**
	 * Admin Bar Menu
	 *
	 * @param array $wp_admin_bar Admin bar menus.
	 */
	function admin_bar_menu( $wp_admin_bar = array() ) {
		if ( is_admin() ) {
			return;
		}
		$title = __( 'Show Hooks', 'hestia-pro' );

		$href = add_query_arg( 'hestia_preview_hook', 'show' );
		if ( isset( $_GET['hestia_preview_hook'] ) && 'show' === $_GET['hestia_preview_hook'] ) {
			$title = __( 'Hide Hooks', 'hestia-pro' );
			$href  = remove_query_arg( 'hestia_preview_hook' );
		}

		$wp_admin_bar->add_menu(
			array(
				'title'  => $title,
				'id'     => 'hestia_preview_hook',
				'parent' => false,
				'href'   => $href,
			)
		);
	}

	/**
	 * Beautify hook names.
	 *
	 * @param string $hook Hook name.
	 *
	 * @return string
	 */
	public static function beautify_hook( $hook ) {
		$hook_label = str_replace( '_', ' ', $hook );
		$hook_label = str_replace( 'hestia', ' ', $hook_label );
		$hook_label = str_replace( 'woocommerce', ' ', $hook_label );
		$hook_label = ucwords( $hook_label );
		return $hook_label;
	}

	/**
	 * Render hook placeholder.
	 */
	public function render_hook_placeholder() {
		if ( ! isset( $_GET['hestia_preview_hook'] ) || 'show' !== $_GET['hestia_preview_hook'] ) {
			return;
		}
		$hooks = Hestia_Custom_Layouts_Module::$hooks;
		foreach ( $hooks as $hook_category => $hooks_in_category ) {
			foreach ( $hooks_in_category as $hook_value ) {
				$hook_label = self::beautify_hook( $hook_value );
				add_action(
					$hook_value,
					function () use ( $hook_label ) {
						$css_style = 'width: 98%; margin: 10px auto; border: 2px dashed #e22222; font-size: 14px; padding: 6px 10px; display: inline-block; color: #404248; text-align: left;';
						echo '<div class="hestia-hook-wrapper" style="text-align: center;">';
						echo '<div class="hestia-hook-placeholder" style="' . esc_attr( $css_style ) . '">';
						echo $hook_label;
						echo '</div>';
						echo '</div>';
					}
				);
			}
		}
	}


}
