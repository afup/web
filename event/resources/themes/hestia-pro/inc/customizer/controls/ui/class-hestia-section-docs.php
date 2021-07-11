<?php
/**
 * Documentation link in main customizer.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Section_Docs
 *
 * @since  1.0.0
 * @access public
 */
class Hestia_Section_Docs extends WP_Customize_Section {

	/**
	 * The type of customize section being rendered.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'hestia-section-docs';

	/**
	 * Upsell title to output.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $theme_info_title = '';

	/**
	 * Label text to output.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $label_text = '';

	/**
	 * Label URL.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $label_url = '';

	/**
	 * Add custom parameters to pass to the JS via JSON.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function json() {
		$json = parent::json();

		$json['theme_info_title'] = $this->theme_info_title;
		$json['label_text']       = $this->label_text;
		$json['label_url']        = $this->label_url;

		return $json;
	}

	/**
	 * Outputs the Underscore.js template.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	protected function render_template() {
		?>

		<li id="accordion-section-{{ data.id }}"
			class="accordion-section control-section control-section-{{ data.type }} cannot-expand">
			<h3 class="accordion-section-title">
				{{data.theme_info_title}}
				<# if ( data.label_text && data.label_url ) { #>
				<a class="button button-secondary alignright" href="{{data.label_url}}" target="_blank">
					{{data.label_text}}
				</a>
				<# } #>
			</h3>
		</li>
		<?php
	}
}
