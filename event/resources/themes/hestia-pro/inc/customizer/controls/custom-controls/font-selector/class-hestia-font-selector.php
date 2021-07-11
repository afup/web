<?php
/**
 * Font selector.
 *
 * @package hestia
 * @since 1.1.38
 */

/**
 * Class Hestia_Font_Selector
 */
class Hestia_Font_Selector extends WP_Customize_Control {
	/**
	 * The control type.
	 *
	 * @access public
	 * @var string
	 */
	public $type = 'selector-font';

	/**
	 * Render the control's content.
	 * Allows the content to be overriden without having to rewrite the wrapper in $this->render().
	 *
	 * @access protected
	 */
	protected function render_content() {
		$std_fonts    = $this->get_standard_fonts();
		$google_fonts = hestia_get_google_fonts();
		$value        = $this->value();
		?>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
			<?php endif; ?>
		</label>
		<div class="hestia-ss-wrap">
			<input class="hestia-fs-main-input" type="text"
					name="<?php echo esc_attr( $this->id ); ?>"
					value="<?php echo ( ! empty( $value ) ) ? esc_html( $value ) : esc_attr__( 'Default', 'hestia-pro' ); ?>"
					readonly>
			<span class="hestia-fs-input-addon"><i class="dashicons dashicons-arrow-down"></i></span>
			<div class="hestia-fs-dropdown">
				<span class="hestia-fs-search">
						<input type="search" placeholder="<?php echo esc_attr_x( 'Search for:', 'label', 'hestia-pro' ) . '...'; ?>">
				</span>
				<div class="hestia-fs-options-wrapper">
						<span class="hestia-fs-option"
								data-option="<?php esc_attr_e( 'Default', 'hestia-pro' ); ?>"><?php esc_html_e( 'Default', 'hestia-pro' ); ?></span>
					<?php
					$this->render_dropdown_options_group( $std_fonts, esc_html__( 'Standard Fonts', 'hestia-pro' ) );
					$this->render_dropdown_options_group( $google_fonts, esc_html__( 'Google Fonts', 'hestia-pro' ) );
					?>
				</div>
			</div>
			<input type="hidden" class="hestia-ss-collector" <?php $this->link(); ?> >
		</div>
		<?php
	}

	/**
	 * Render the dropdown option group.
	 *
	 * @param array  $options Options in group.
	 *
	 * @param string $title Title of options group.
	 */
	protected function render_dropdown_options_group( $options, $title ) {
		if ( ! empty( $options ) ) {
			?>
			<span class="hestia-fs-options-group">
					<span class="hestia-fs-options-heading"><?php echo esc_html( $title ); ?></span>
				<?php foreach ( $options as $option ) { ?>
					<span class="hestia-fs-option"
							data-filter="<?php echo strtolower( esc_html( $option ) ); ?>"
							data-option="<?php echo esc_html( $option ); ?>"><?php echo esc_html( $option ); ?></span>
				<?php } ?>
				</span>
			<?php
		}
	}


	/**
	 * List of standard fonts
	 *
	 * @since 1.1.38
	 */
	function get_standard_fonts() {
		return apply_filters(
			'hestia_standard_fonts_array',
			array(
				'Arial, Helvetica, sans-serif',
				'Arial Black, Gadget, sans-serif',
				'Bookman Old Style, serif',
				'Comic Sans MS, cursive',
				'Courier, monospace',
				'Georgia, serif',
				'Garamond, serif',
				'Impact, Charcoal, sans-serif',
				'Lucida Console, Monaco, monospace',
				'Lucida Sans Unicode, Lucida Grande, sans-serif',
				'MS Sans Serif, Geneva, sans-serif',
				'MS Serif, New York, sans-serif',
				'Palatino Linotype, Book Antiqua, Palatino, serif',
				'Tahoma, Geneva, sans-serif',
				'Times New Roman, Times, serif',
				'Trebuchet MS, Helvetica, sans-serif',
				'Verdana, Geneva, sans-serif',
				'Paratina Linotype',
				'Trebuchet MS',
			)
		);
	}

}
