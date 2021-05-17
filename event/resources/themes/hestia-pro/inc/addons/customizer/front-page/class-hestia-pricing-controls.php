<?php
/**
 * Pricing controls.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Pricing_Controls
 */
class Hestia_Pricing_Controls extends Hestia_Front_Page_Section_Controls_Abstract {

	/**
	 * Set the section data for generating the customizer basic settings
	 *
	 * @return array
	 */
	protected function set_section_data() {
		return array(
			'slug'             => 'pricing',
			'title'            => esc_html__( 'Pricing', 'hestia-pro' ),
			'priority'         => 35,
			'initially_hidden' => true,
		);
	}

	/**
	 * Add controls.
	 */
	public function add_controls() {
		$this->add_tabs();

		$table_one_features_default = sprintf( '<b>%1$s</b> %2$s', esc_html__( '1', 'hestia-pro' ), esc_html__( 'Domain', 'hestia-pro' ) ) .
			sprintf( '\n<b>%1$s</b> %2$s', esc_html__( '1GB', 'hestia-pro' ), esc_html__( 'Storage', 'hestia-pro' ) ) .
			sprintf( '\n<b>%1$s</b> %2$s', esc_html__( '100GB', 'hestia-pro' ), esc_html__( 'Bandwidth', 'hestia-pro' ) ) .
			sprintf( '\n<b>%1$s</b> %2$s', esc_html__( '2', 'hestia-pro' ), esc_html__( 'Databases', 'hestia-pro' ) );

		$table_two_features_default = sprintf( '<b>%1$s</b> %2$s', esc_html__( '5', 'hestia-pro' ), esc_html__( 'Domain', 'hestia-pro' ) ) .
			sprintf( '\n<b>%1$s</b> %2$s', esc_html__( 'Unlimited', 'hestia-pro' ), esc_html__( 'Storage', 'hestia-pro' ) ) .
			sprintf( '\n<b>%1$s</b> %2$s', esc_html__( 'Unlimited', 'hestia-pro' ), esc_html__( 'Bandwidth', 'hestia-pro' ) ) .
			sprintf( '\n<b>%1$s</b> %2$s', esc_html__( 'Unlimited', 'hestia-pro' ), esc_html__( 'Databases', 'hestia-pro' ) );

		$table_one_args = array(
			'id'       => 'hestia_pricing_table_one',
			'defaults' => array(
				'title'       => esc_html__( 'Basic Package', 'hestia-pro' ),
				'price'       => '<small>$</small>0',
				'features'    => $table_one_features_default,
				'button_text' => esc_html__( 'Free Download', 'hestia-pro' ),
				'button_link' => esc_url( '#' ),
			),
			'labels'   => array(
				'title'       => esc_html__( 'Pricing Table One: Title', 'hestia-pro' ),
				'icon'        => esc_html__( 'Pricing Table One: Icon', 'hestia-pro' ),
				'price'       => esc_html__( 'Pricing Table One: Price', 'hestia-pro' ),
				'features'    => esc_html__( 'Pricing Table One: Features', 'hestia-pro' ),
				'button_text' => esc_html__( 'Pricing Table One: Text', 'hestia-pro' ),
				'button_link' => esc_html__( 'Pricing Table One: Link', 'hestia-pro' ),
			),
		);

		$table_two_args = array(
			'id'       => 'hestia_pricing_table_two',
			'defaults' => array(
				'title'       => esc_html__( 'Premium Package', 'hestia-pro' ),
				'price'       => '<small>$</small>49',
				'features'    => $table_two_features_default,
				'button_text' => esc_html__( 'Order Now', 'hestia-pro' ),
				'button_link' => esc_url( '#' ),
			),
			'labels'   => array(
				'title'       => esc_html__( 'Pricing Table Two: Title', 'hestia-pro' ),
				'icon'        => esc_html__( 'Pricing Table Two: Icon', 'hestia-pro' ),
				'price'       => esc_html__( 'Pricing Table Two: Price', 'hestia-pro' ),
				'features'    => esc_html__( 'Pricing Table Two: Features', 'hestia-pro' ),
				'button_text' => esc_html__( 'Pricing Table Two: Text', 'hestia-pro' ),
				'button_link' => esc_html__( 'Pricing Table Two: Link', 'hestia-pro' ),
			),
		);

		$this->add_pricing_table_controls( $table_one_args );
		$this->add_pricing_table_controls( $table_two_args );
	}

	/**
	 * Add section tabs.
	 */
	private function add_tabs() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_pricing_tabs',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'section'  => 'hestia_pricing',
					'priority' => 1,
					'tabs'     => array(
						'general' => array(
							'label' => esc_html__( 'General', 'hestia-pro' ),
							'icon'  => 'admin-tools',
						),
						'first'   => array(
							'label' => esc_html__( 'First', 'hestia-pro' ),
							'icon'  => 'list-view',
						),
						'second'  => array(
							'label' => esc_html__( 'Second', 'hestia-pro' ),
							'icon'  => 'list-view',
						),
					),
					'controls' => array(
						'general' => array(
							'hestia_pricing_hide'     => array(),
							'hestia_pricing_title'    => array(),
							'hestia_pricing_subtitle' => array(),
						),
						'first'   => array(
							'hestia_pricing_table_one_title' => array(),
							'hestia_pricing_table_one_icon' => array(),
							'hestia_pricing_table_one_price' => array(),
							'hestia_pricing_table_one_features' => array(),
							'hestia_pricing_table_one_link' => array(),
							'hestia_pricing_table_one_text' => array(),
						),
						'second'  => array(
							'hestia_pricing_table_two_title' => array(),
							'hestia_pricing_table_two_icon' => array(),
							'hestia_pricing_table_two_price' => array(),
							'hestia_pricing_table_two_features' => array(),
							'hestia_pricing_table_two_link' => array(),
							'hestia_pricing_table_two_text' => array(),
						),

					),
				),
				'Hestia_Customize_Control_Tabs'
			)
		);
	}

	/**
	 * Add pricing table controls.
	 */
	private function add_pricing_table_controls( $args ) {
		$this->add_control(
			new Hestia_Customizer_Control(
				$args['id'] . '_title',
				array(
					'default'           => $args['defaults']['title'],
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'    => $args['labels']['title'],
					'section'  => 'hestia_pricing',
					'priority' => 15,
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				$args['id'] . '_icon',
				array(
					'transport' => 'postMessage',
				),
				array(
					'label'    => $args['labels']['icon'],
					'section'  => 'hestia_pricing',
					'priority' => 16,
				),
				'Hestia_Iconpicker'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				$args['id'] . '_price',
				array(
					'default'           => $args['defaults']['price'],
					'sanitize_callback' => 'wp_kses_post',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'    => $args['labels']['price'],
					'section'  => 'hestia_pricing',
					'priority' => 20,
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				$args['id'] . '_features',
				array(
					'default'           => $args['defaults']['features'],
					'sanitize_callback' => 'wp_kses_post',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'       => $args['labels']['features'],
					'description' => esc_html__( 'Separate your features by adding \n between lines.', 'hestia-pro' ),
					'section'     => 'hestia_pricing',
					'priority'    => 25,
					'type'        => 'textarea',
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				$args['id'] . '_link',
				array(
					'default'           => $args['defaults']['button_link'],
					'sanitize_callback' => 'esc_url_raw',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'    => $args['labels']['button_link'],
					'section'  => 'hestia_pricing',
					'priority' => 30,
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				$args['id'] . '_text',
				array(
					'default'           => $args['defaults']['button_text'],
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'    => $args['labels']['button_text'],
					'section'  => 'hestia_pricing',
					'priority' => 35,
				)
			)
		);
	}

	/**
	 * Change controls.
	 *
	 * @return void
	 */
	public function change_controls() {
		$this->change_customizer_object( 'setting', 'hestia_pricing_title', 'default', esc_html__( 'Choose a plan for your next project', 'hestia-pro' ) );
		$this->change_customizer_object( 'setting', 'hestia_pricing_subtitle', 'default', esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'hestia-pro' ) );
	}
}
