<?php
/**
 * ThemeIsle White Label data store.
 *
 * @package ti-white-label
 */

/**
 * Class Ti_White_Label_Data
 */
class Ti_White_Label_Data {

	/**
	 * Module's data.
	 *
	 * @var array
	 */
	private $inputs = array(
		'author_name'        => '',
		'author_url'         => '',
		'starter_sites'      => '',
		'theme_name'         => '',
		'theme_description'  => '',
		'screenshot_url'     => '',
		'plugin_name'        => '',
		'plugin_description' => '',
		'white_label'        => '',
	);

	/**
	 * Return site settings.
	 *
	 * @return array Site settings.
	 * @since 1.0.0
	 */
	public function get_input_fields() {
		$this->update_field_data();

		return $this->inputs;
	}

	/**
	 * Update field data.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	private function update_field_data() {
		$new_input = get_option( 'ti_white_label_inputs' );
		$new_input = json_decode( $new_input, true );
		if ( empty( $new_input ) ) {
			return false;
		}

		$this->inputs = wp_parse_args( $new_input, $this->inputs );

		return true;
	}
}
