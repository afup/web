<?php
/**
 * Class for sections that are hiding.
 *
 * @since 1.1.47
 * @package hestia
 */

/**
 * Class Hestia_Hiding_Section
 *
 * @since  1.1.49
 * @access public
 */
class Hestia_Hiding_Section extends WP_Customize_Section {

	/**
	 * The type of customize section being rendered.
	 *
	 * @since  1.1.47
	 * @access public
	 * @var    string
	 */
	public $type = 'hiding-section';

	/**
	 * Flag to display icon when entering in customizer
	 *
	 * @since  1.1.47
	 * @access public
	 * @var bool
	 */
	public $visible;

	/**
	 * Name of customizer hiding control.
	 *
	 * @since  1.1.47
	 * @access public
	 * @var bool
	 */
	public $hiding_control;

	/**
	 * Id of customizer hiding control.
	 *
	 * @since  1.1.82
	 * @access public
	 * @var integer
	 */
	public $id;


	/**
	 * Hestia_Hiding_Section constructor.
	 *
	 * @param WP_Customize_Manager $manager Customizer Manager.
	 * @param string               $id Control id.
	 * @param array                $args Arguments.
	 */
	public function __construct( WP_Customize_Manager $manager, $id, array $args = array() ) {
		parent::__construct( $manager, $id, $args );

		if ( ! empty( $args['hiding_control'] ) ) {
			$this->visible = ! get_theme_mod( $args['hiding_control'] );
		}
	}

	/**
	 * Add custom parameters to pass to the JS via JSON.
	 *
	 * @since  1.1.47
	 * @access public
	 */
	public function json() {
		$json                   = parent::json();
		$json['visible']        = $this->visible;
		$json['hiding_control'] = $this->hiding_control;
		return $json;
	}

	/**
	 * Outputs the Underscore.js template.
	 *
	 * @since  1.1.47
	 * @access public
	 * @return void
	 */
	protected function render_template() {
		?>
		<li id="accordion-section-{{ data.id }}" class="accordion-section control-section control-section-{{ data.type }}">
			<h3 class="accordion-section-title <# if ( data.visible ) { #> hestia-section-visible <# } else { #> hestia-section-hidden <# }#>" tabindex="0">
				{{ data.title }}
				<# if ( data.visible ) { #>
					<a data-control="{{ data.hiding_control }}" class="hestia-toggle-section" href="#"><span class="dashicons dashicons-visibility"></span></a>
				<# } else { #>
					<a data-control="{{ data.hiding_control }}" class="hestia-toggle-section" href="#"><span class="dashicons dashicons-hidden"></span></a>
				<# } #>
			</h3>
			<ul class="accordion-section-content">
				<li class="customize-section-description-container section-meta <# if ( data.description_hidden ) { #>customize-info<# } #>">
					<div class="customize-section-title">
						<button class="customize-section-back" tabindex="-1">
						</button>
						<h3>
							<span class="customize-action">
								{{{ data.customizeAction }}}
							</span>
							{{ data.title }}
						</h3>
						<# if ( data.description && data.description_hidden ) { #>
							<button type="button" class="customize-help-toggle dashicons dashicons-editor-help" aria-expanded="false"></button>
							<div class="description customize-section-description">
								{{{ data.description }}}
							</div>
							<# } #>
					</div>

					<# if ( data.description && ! data.description_hidden ) { #>
						<div class="description customize-section-description">
							{{{ data.description }}}
						</div>
						<# } #>
				</li>
			</ul>
		</li>
		<?php
	}
}
