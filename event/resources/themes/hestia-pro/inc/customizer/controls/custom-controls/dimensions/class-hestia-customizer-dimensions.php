<?php
/**
 * Customizer Control: hestia-dimensions.
 *
 * @package Hestia
 */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Buttonset control
 */
class Hestia_Customizer_Dimensions extends WP_Customize_Control {

	/**
	 * The control type.
	 *
	 * @access public
	 * @var string
	 */
	public $type = 'hestia-dimensions';

	/**
	 * Flag that enables media queries
	 *
	 * @var bool
	 */
	public $media_query = false;

	/**
	 * Enqueue control related scripts/styles.
	 *
	 * @access public
	 */
	public function enqueue() {
		wp_enqueue_script( 'hestia-dimensions-control', get_template_directory_uri() . '/inc/customizer/controls/custom-controls/dimensions/dimensions.js', array( 'jquery', 'customize-base' ), false, true );
		wp_enqueue_style( 'hestia-dimensions-control', get_template_directory_uri() . '/inc/customizer/controls/custom-controls/dimensions/dimensions.css' );
		if ( ! empty( $this->media_query ) ) {
			wp_enqueue_script( 'responsive-switchers', get_template_directory_uri() . '/inc/customizer/controls/custom-controls/common-functionalities/responsive-switchers.js', array( 'jquery' ), HESTIA_VERSION, true );
		}
	}

	/**
	 * Renders the control wrapper and calls $this->render_content() for the internals.
	 *
	 * @see WP_Customize_Control::render()
	 */
	protected function render() {
		$id    = 'customize-control-' . str_replace( array( '[', ']' ), array( '-', '' ), $this->id );
		$class = 'customize-control has-switchers customize-control-' . $this->type;
		?><li id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $class ); ?>">
		<?php $this->render_content(); ?>
		</li>
		<?php
	}

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @see WP_Customize_Control::to_json()
	 */
	public function json() {
		$json                  = parent::json();
		$json['id']            = $this->id;
		$json['link']          = $this->get_link();
		$json['media_query']   = $this->media_query;
		$json['inputAttrs']    = '';
		$json['default_value'] = ! empty( $this->setting->default ) ? $this->setting->default : '';
		foreach ( $this->input_attrs as $attr => $value ) {
			$json['inputAttrs'] .= $attr . '="' . esc_attr( $value ) . '" ';
		}
		$value         = $this->value();
		$media_queries = array( 'desktop', 'tablet', 'mobile' );
		foreach ( $media_queries as $query ) {
			$json[ $query ] = array(
				$query . '_vertical'   => '',
				$query . '_horizontal' => '',
			);
		}
		if ( ! empty( $value ) ) {
			$decoded_value = json_decode( $value );
			if ( ! empty( $decoded_value ) ) {
				foreach ( $decoded_value as $media => $value ) {
					$media_values = json_decode( $value );
					if ( ! empty( $media_values ) ) {
						$json[ $media ] = $media_values;
					}
				}
			}
		}
		return $json;
	}

	/**
	 * An Underscore (JS) template for this control's content (but not its container).
	 *
	 * Class variables for this control class are available in the `data` JS object;
	 * export custom variables by overriding {@see WP_Customize_Control::to_json()}.
	 *
	 * @see WP_Customize_Control::print_template()
	 *
	 * @access protected
	 */
	protected function content_template() {
		?>
		<# if ( data.label ) { #>
		<span class="customize-control-title">
				<span>{{{ data.label }}}</span>
				<# if ( data.media_query ) { #>
				<ul class="responsive-switchers">
					<li class="desktop">
						<button type="button" class="preview-desktop active" data-device="desktop">
							<i class="dashicons dashicons-desktop"></i>
						</button>
					</li>
					<li class="tablet">
						<button type="button" class="preview-tablet" data-device="tablet">
							<i class="dashicons dashicons-tablet"></i>
						</button>
					</li>
					<li class="mobile">
						<button type="button" class="preview-mobile" data-device="mobile">
							<i class="dashicons dashicons-smartphone"></i>
						</button>
					</li>
				</ul>
				<# } #>
			</span>
		<# } #>

		<# if ( data.description ) { #>
		<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>
		<div class="dimensions-wrap <# if ( data.media_query ) { #>has-media-queries<# }#>">
		<ul class="desktop control-wrap active">
			<# _.each( data.desktop, function( value, key ) {
				var label = key.split('_').pop();
			#>
				<li class="dimension-wrap {{ key }}">
					<input {{{ data.inputAttrs }}} type="number" class="dimension-{{ key }}" value="{{{ value }}}" title="{{{label}}}" />
				</li>
				<# } ); #>

					<li class="dimension-wrap">
						<div class="link-dimensions">
							<span class="dashicons dashicons-admin-links hestia-linked" data-element="{{ data.id }}"></span>
							<span class="dashicons dashicons-editor-unlink hestia-unlinked" data-element="{{ data.id }}"></span>
						</div>
					</li>
					<li class="dimensions-reset-container" data-default="{{data.default_value}}">
						<span class="reset-dimensions"><span class="dashicons dashicons-image-rotate"></span></span>
					</li>

		</ul>
		<# if ( data.media_query ) { #>
		<ul class="tablet control-wrap">
			<# _.each( data.tablet, function( value, key ) {
				var label = key.split('_').pop();
			#>
				<li class="dimension-wrap {{ key }}">
					<input {{{ data.inputAttrs }}} type="number" class="dimension-{{ key }}" value="{{{ value }}}" title="{{{label}}}" />
				</li>
				<# } ); #>

					<li class="dimension-wrap">
						<div class="link-dimensions">
							<span class="dashicons dashicons-admin-links hestia-linked" data-element="{{ data.id }}_tablet"></span>
							<span class="dashicons dashicons-editor-unlink hestia-unlinked" data-element="{{ data.id }}_tablet"></span>
						</div>
					</li>
		</ul>

		<ul class="mobile control-wrap">
			<# _.each( data.mobile, function( value, key ) {
				var label = key.split('_').pop();
			#>
				<li class="dimension-wrap {{ key }}">
					<input {{{ data.inputAttrs }}} type="number" class="dimension-{{ key }}" value="{{{ value }}}" title="{{{label}}}" />
				</li>
				<# } ); #>

					<li class="dimension-wrap">
						<div class="link-dimensions">
							<span class="dashicons dashicons-admin-links hestia-linked" data-element="{{ data.id }}_mobile"></span>
							<span class="dashicons dashicons-editor-unlink hestia-unlinked" data-element="{{ data.id }}_mobile"></span>
						</div>
					</li>
		</ul>
		<# } #>
		</div>

		<input type="hidden" class="dimensions-collector" title="{{{data.label}}}" value="{{ data.value }}" {{{ data.link }}} >
		<?php
	}
}
