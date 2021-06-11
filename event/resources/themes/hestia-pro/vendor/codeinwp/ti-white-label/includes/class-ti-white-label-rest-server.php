<?php
/**
 * White Label Rest Endpoints Handler.
 *
 * @package         ti-white-label
 */

/**
 * Class Themeisle_OB_Rest_Server
 *
 * @package themeisle-onboarding
 */
class Ti_White_Label_Rest_Server {

	/**
	 * Rest api namespace.
	 *
	 * @var string Namespace.
	 */
	private $namespace;

	/**
	 * Initialize the rest functionality.
	 */
	public function __construct() {
		$this->namespace = WHITE_LABEL_NAMESPACE . '/v1';
		add_action( 'rest_api_init', array( $this, 'register_endpoints' ) );
	}

	/**
	 * Register endpoints.
	 */
	public function register_endpoints() {
		register_rest_route(
			$this->namespace,
			'/input_save',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'save_inputs' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
				'args'                => array(
					'agency_form'      => array(
						'type'              => 'Array',
						'validate_callback' => array( $this, 'validate_agency_form' ),
					),
					'plugin_form'      => array(
						'type'              => 'Array',
						'validate_callback' => array( $this, 'validate_plugin_form' ),
					),
					'theme_form'       => array(
						'type'              => 'Array',
						'validate_callback' => array( $this, 'validate_theme_form' ),
					),
					'white_label_form' => array(
						'type'              => 'Array',
						'validate_callback' => array( $this, 'validate_white_label_form' ),
					),
				),
			)
		);
	}

	/**
	 * Validate Agency form.
	 *
	 * @param array $input Agency form's values.
	 *
	 * @return bool
	 */
	public function validate_agency_form( $input ) {
		$valid_values = array( 'author_name', 'author_url', 'starter_sites' );
		foreach ( $input as $key => $value ) {
			if ( ! in_array( $key, $valid_values, true ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Validate Plugin form.
	 *
	 * @param array $input Plugin form's values.
	 *
	 * @return bool
	 */
	public function validate_plugin_form( $input ) {
		$valid_values = array( 'plugin_description', 'plugin_name' );
		foreach ( $input as $key => $value ) {
			if ( ! in_array( $key, $valid_values, true ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Validate Theme Form form.
	 *
	 * @param array $input Theme form's values.
	 *
	 * @return bool
	 */
	public function validate_theme_form( $input ) {
		$valid_values = array( 'screenshot_url', 'theme_description', 'theme_name' );
		foreach ( $input as $key => $value ) {
			if ( ! in_array( $key, $valid_values, true ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Validate White Label form.
	 *
	 * @param array $input White Label form's values.
	 *
	 * @return bool
	 */
	public function validate_white_label_form( $input ) {
		$valid_values = array( 'white_label', 'license' );
		foreach ( $input as $key => $value ) {
			if ( ! in_array( $key, $valid_values, true ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Save inputs.
	 *
	 * @param \WP_Rest_Request $request Rest request.
	 *
	 * @return \WP_REST_Response
	 */
	public function save_inputs( \WP_REST_Request $request ) {
		$data = $request->get_json_params();
		if ( empty( $data ) ) {
			return new \WP_REST_Response(
				array(
					'code'    => 'error',
					'message' => esc_html__( 'White Label error: The form data was not sent.', 'hestia-pro' ),
					'markup'  => esc_html__( 'It seems that there is an error. The options were not saved.', 'hestia-pro' ),
				),
				400
			);
		}

		$new_input = array();
		foreach ( $data as $key => $value ) {
			$new_input = array_merge( $new_input, $value );
		}

		$save_data = json_encode( $new_input );
		update_option( 'ti_white_label_inputs', $save_data );

		return new \WP_REST_Response(
			array(
				'code'    => 'success',
				'message' => esc_html__( 'Options saved.', 'hestia-pro' ),
			),
			200
		);
	}

}
