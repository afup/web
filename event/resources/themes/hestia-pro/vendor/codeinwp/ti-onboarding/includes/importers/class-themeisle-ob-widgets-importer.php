<?php
/**
 * Widgets Importer.
 *
 * Author:  Andrei Baicus <andrei@themeisle.com>
 * On:      21/06/2018
 *
 * @package    themeisle-onboarding
 * @soundtrack Milk Carton Kid - The Milk Carton Kids
 */

/**
 * Class Themeisle_OB_Widgets_Importer
 */
class Themeisle_OB_Widgets_Importer {

	/**
	 * Import Widgets.
	 *
	 * @param WP_REST_Request $request contains the widgets that should be imported.
	 *
	 * @return WP_REST_Response
	 */
	public function import_widgets( WP_REST_Request $request ) {
		$params  = $request->get_body_params();
		$widgets = $params['data'];
		if ( empty( $widgets ) || ! is_array( $widgets ) ) {
			return new WP_REST_Response(
				array(
					'success' => true,
				)
			);
		}

		do_action( 'themeisle_ob_before_widgets_import' );

		$import = $this->actually_import( $widgets );

		if ( is_wp_error( $import ) ) {
			return new WP_REST_Response(
				array(
					'data'    => 'ti__ob_widgets_err_1',
					'success' => false,
				)
			);
		}

		do_action( 'themeisle_ob_after_widgets_import' );

		return new WP_REST_Response(
			array(
				'success' => true,
			)
		);
	}

	/**
	 * Widget import process.
	 *
	 * @param array $data Widgets data.
	 */
	public function actually_import( $data ) {
		global $wp_registered_sidebars;
		if ( empty( $data ) || ! is_array( $data ) ) {
			return new WP_Error( 'ti__ob_widget_err_1' );
		}

		$available_widgets = $this->available_widgets();

		$widget_instances = array();
		foreach ( $available_widgets as $widget_data ) {
			$widget_instances[ $widget_data['id_base'] ] = get_option( 'widget_' . $widget_data['id_base'] );
		}

		foreach ( $data as $sidebar_id => $widgets ) {
			if ( 'wp_inactive_widgets' === $sidebar_id ) {
				continue;
			}

			if ( isset( $wp_registered_sidebars[ $sidebar_id ] ) ) {
				$sidebar_available = true;
				$use_sidebar_id    = $sidebar_id;
			} else {
				$sidebar_available = false;
				$use_sidebar_id    = 'wp_inactive_widgets'; // Add to inactive if sidebar does not exist in theme.
			}

			// Loop widgets.
			foreach ( $widgets as $widget_instance_id => $widget ) {

				$fail = false;

				// Get id_base (remove -# from end) and instance ID number.
				$id_base            = preg_replace( '/-[0-9]+$/', '', $widget_instance_id );
				$instance_id_number = str_replace( $id_base . '-', '', $widget_instance_id );

				// Does site support this widget?
				if ( ! $fail && ! isset( $available_widgets[ $id_base ] ) ) {
					$fail = true;
				}

				// Convert multidimensional objects to multidimensional arrays
				$widget = json_decode( wp_json_encode( $widget ), true );

				// Does widget with identical settings already exist in same sidebar?
				if ( ! $fail && isset( $widget_instances[ $id_base ] ) ) {

					// Get existing widgets in this sidebar.
					$sidebars_widgets = get_option( 'sidebars_widgets' );
					$sidebar_widgets  = isset( $sidebars_widgets[ $use_sidebar_id ] ) ? $sidebars_widgets[ $use_sidebar_id ] : array(); // Check Inactive if that's where will go.

					// Loop widgets with ID base.
					$single_widget_instances = ! empty( $widget_instances[ $id_base ] ) ? $widget_instances[ $id_base ] : array();
					foreach ( $single_widget_instances as $check_id => $check_widget ) {

						// Is widget in same sidebar and has identical settings?
						if ( in_array( "$id_base-$check_id", $sidebar_widgets, true ) && (array) $widget === $check_widget ) {
							$fail = true;
							break;

						}
					}
				}

				// No failure.
				if ( ! $fail ) {

					// Add widget instance
					$single_widget_instances   = get_option( 'widget_' . $id_base ); // All instances for that widget ID base, get fresh every time.
					$single_widget_instances   = ! empty( $single_widget_instances ) ? $single_widget_instances : array(
						'_multiwidget' => 1, // Start fresh if have to.
					);
					$single_widget_instances[] = $widget; // Add it.

					// Get the key it was given.
					end( $single_widget_instances );
					$new_instance_id_number = key( $single_widget_instances );

					// If key is 0, make it 1
					// When 0, an issue can occur where adding a widget causes data from other widget to load,
					// and the widget doesn't stick (reload wipes it).
					if ( '0' === strval( $new_instance_id_number ) ) {
						$new_instance_id_number                             = 1;
						$single_widget_instances[ $new_instance_id_number ] = $single_widget_instances[0];
						unset( $single_widget_instances[0] );
					}

					// Move _multiwidget to end of array for uniformity.
					if ( isset( $single_widget_instances['_multiwidget'] ) ) {
						$multiwidget = $single_widget_instances['_multiwidget'];
						unset( $single_widget_instances['_multiwidget'] );
						$single_widget_instances['_multiwidget'] = $multiwidget;
					}

					// Update option with new widget.
					update_option( 'widget_' . $id_base, $single_widget_instances );

					// Assign widget instance to sidebar.
					// Which sidebars have which widgets, get fresh every time.
					$sidebars_widgets = get_option( 'sidebars_widgets' );

					// Avoid rarely fatal error when the option is an empty string
					if ( ! $sidebars_widgets ) {
						$sidebars_widgets = array();
					}

					// Use ID number from new widget instance.
					$new_instance_id = $id_base . '-' . $new_instance_id_number;

					// Add new instance to sidebar.
					$sidebars_widgets[ $use_sidebar_id ][] = $new_instance_id;

					// Save the amended data.
					update_option( 'sidebars_widgets', $sidebars_widgets );

					// After widget import action.
					$after_widget_import = array(
						'sidebar'           => $use_sidebar_id,
						'sidebar_old'       => $sidebar_id,
						'widget'            => $widget,
						'widget_type'       => $id_base,
						'widget_id'         => $new_instance_id,
						'widget_id_old'     => $widget_instance_id,
						'widget_id_num'     => $new_instance_id_number,
						'widget_id_num_old' => $instance_id_number,
					);
					do_action( 'themeisle_ob_after_single_widget_import', $after_widget_import );

				}
			}
		}
	}

	/**
	 * Available widgets
	 *
	 * Gather site's widgets into array with ID base, name, etc.
	 * Used by export and import functions.
	 *
	 * @since 0.4
	 * @global array $wp_registered_widget_updates
	 * @return array Widget information
	 */
	public function available_widgets() {

		global $wp_registered_widget_controls;

		$widget_controls = $wp_registered_widget_controls;

		$available_widgets = array();

		foreach ( $widget_controls as $widget ) {

			// No duplicates.
			if ( ! empty( $widget['id_base'] ) && ! isset( $available_widgets[ $widget['id_base'] ] ) ) {
				$available_widgets[ $widget['id_base'] ]['id_base'] = $widget['id_base'];
				$available_widgets[ $widget['id_base'] ]['name']    = $widget['name'];
			}
		}

		return $available_widgets;

	}

}
