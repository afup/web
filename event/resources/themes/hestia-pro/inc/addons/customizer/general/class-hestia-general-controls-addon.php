<?php
/**
 * Customizer general controls.
 *
 * @package Hestia
 */

/**
 * Class Hestia_General_Controls
 */
class Hestia_General_Controls_Addon extends Hestia_General_Controls {

	/**
	 * Init function.
	 */
	public function init() {
		parent::init();
		add_filter( 'hestia_shop_sidebar_options', array( $this, 'filter_shop_sidebar_options' ) );
	}

	/**
	 * Add controls
	 */
	public function add_controls() {
		parent::add_controls();
		$this->add_layout_width_controls();
	}

	/**
	 * Filter shop sidebar options from lite.
	 *
	 * @return array
	 */
	public function filter_shop_sidebar_options() {
		return array(
			'off-canvas' => array(
				'url'   => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqCAMAAABpj1iyAAAACVBMVEX///8+yP/V1dXG9YqxAAAACXBIWXMAAAsTAAALEwEAmpwYAAAG0mlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxNDUgNzkuMTYzNDk5LCAyMDE4LzA4LzEzLTE2OjQwOjIyICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIgeG1sbnM6cGhvdG9zaG9wPSJodHRwOi8vbnMuYWRvYmUuY29tL3Bob3Rvc2hvcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxOSAoTWFjaW50b3NoKSIgeG1wOkNyZWF0ZURhdGU9IjIwMTktMTItMTlUMTA6NDQ6MTkrMDI6MDAiIHhtcDpNb2RpZnlEYXRlPSIyMDE5LTEyLTE5VDExOjE5OjI0KzAyOjAwIiB4bXA6TWV0YWRhdGFEYXRlPSIyMDE5LTEyLTE5VDExOjE5OjI0KzAyOjAwIiBkYzpmb3JtYXQ9ImltYWdlL3BuZyIgcGhvdG9zaG9wOkNvbG9yTW9kZT0iMiIgcGhvdG9zaG9wOklDQ1Byb2ZpbGU9InNSR0IgSUVDNjE5NjYtMi4xIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjliNzJmY2RjLWU4MjgtNDQyMC1iOTBmLTJmNWQ4ZGRmOTkxMiIgeG1wTU06RG9jdW1lbnRJRD0iYWRvYmU6ZG9jaWQ6cGhvdG9zaG9wOmJmMGE1MTJlLTg1NzctMGY0My1iMzY3LTQ1ZDU2NTZiN2M3ZSIgeG1wTU06T3JpZ2luYWxEb2N1bWVudElEPSJ4bXAuZGlkOjE1YWZmYmE0LWZjNjItNGU2Yi05ZGI3LTNmNzYxZGQ4MTE5NSI+IDx4bXBNTTpIaXN0b3J5PiA8cmRmOlNlcT4gPHJkZjpsaSBzdEV2dDphY3Rpb249ImNyZWF0ZWQiIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6MTVhZmZiYTQtZmM2Mi00ZTZiLTlkYjctM2Y3NjFkZDgxMTk1IiBzdEV2dDp3aGVuPSIyMDE5LTEyLTE5VDEwOjQ0OjE5KzAyOjAwIiBzdEV2dDpzb2Z0d2FyZUFnZW50PSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxOSAoTWFjaW50b3NoKSIvPiA8cmRmOmxpIHN0RXZ0OmFjdGlvbj0ic2F2ZWQiIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6Mjk3NDU4ZDktY2M0YS00M2M2LWIxZmEtMjRkMTNmMTVlNTM1IiBzdEV2dDp3aGVuPSIyMDE5LTEyLTE5VDEwOjU0OjUzKzAyOjAwIiBzdEV2dDpzb2Z0d2FyZUFnZW50PSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxOSAoTWFjaW50b3NoKSIgc3RFdnQ6Y2hhbmdlZD0iLyIvPiA8cmRmOmxpIHN0RXZ0OmFjdGlvbj0ic2F2ZWQiIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6OWI3MmZjZGMtZTgyOC00NDIwLWI5MGYtMmY1ZDhkZGY5OTEyIiBzdEV2dDp3aGVuPSIyMDE5LTEyLTE5VDExOjE5OjI0KzAyOjAwIiBzdEV2dDpzb2Z0d2FyZUFnZW50PSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxOSAoTWFjaW50b3NoKSIgc3RFdnQ6Y2hhbmdlZD0iLyIvPiA8L3JkZjpTZXE+IDwveG1wTU06SGlzdG9yeT4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz5LRiqyAAAA6klEQVRoge3UQQ7CMBBD0WHE/Y88sCBUjRoCRil48d8GhUbUZNxGAAAAAJi6vL6U3apODtK7fr41I6IiK7LaKmr/sTK4EKu/b0v3/LKirdfI91v+YXJahz+fozm18eXPhnio/PC+2xCXnvu6H6uVuZTKb/bP3epn8MH0vQXFZIjDS5P93xmXw/R1qp7W7awgPdPTMo1F5RVUXmEai8orqLzCNBaVV1B5hWksKq+g8grTWFReQeUVprGovILKK0xjUXkFlVeYxqLyCiqvMI1F5RVUXmEai8orqLzCNBaVV1B5hWksKg8AAHCuO0cqMnC+e7cbAAAAAElFTkSuQmCC',
				'label' => esc_html__( 'Off Canvas', 'hestia-pro' ),
			),
		);
	}

	/**
	 * Add sidebar and container width controls.
	 */
	private function add_layout_width_controls() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_sidebar_width',
				array(
					'sanitize_callback' => 'absint',
					'default'           => 25,
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'      => esc_html__( 'Sidebar width (%)', 'hestia-pro' ),
					'section'    => 'hestia_general',
					'type'       => 'range-value',
					'input_attr' => array(
						'min'  => 15,
						'max'  => 80,
						'step' => 1,
					),
					'priority'   => 25,
				),
				'Hestia_Customizer_Range_Value_Control'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_container_width',
				array(
					'sanitize_callback' => 'hestia_sanitize_range_value',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'       => esc_html__( 'Container width (px)', 'hestia-pro' ),
					'section'     => 'hestia_general',
					'type'        => 'range-value',
					'media_query' => true,
					'input_attr'  => array(
						'mobile'  => array(
							'min'           => 200,
							'max'           => 748,
							'step'          => 0.1,
							'default_value' => 748,
						),
						'tablet'  => array(
							'min'           => 300,
							'max'           => 992,
							'step'          => 0.1,
							'default_value' => 992,
						),
						'desktop' => array(
							'min'           => 700,
							'max'           => 2000,
							'step'          => 0.1,
							'default_value' => 1170,
						),
					),
					'priority'    => 25,
				),
				'Hestia_Customizer_Range_Value_Control'
			)
		);
	}
}
