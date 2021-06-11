<?php
/**
 * Page editor control
 *
 * @package Hestia
 * @since Hestia 1.1.3
 */

/**
 * Class to create a custom tags control
 */
class Hestia_Page_Editor extends WP_Customize_Control {

	/**
	 * Hestia_Page_Editor constructor.
	 *
	 * @param WP_Customize_Manager $manager Manager.
	 * @param string               $id Id.
	 * @param array                $args Constructor args.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
	}

	/**
	 * Enqueue scripts
	 *
	 * @since   1.1.0
	 * @access  public
	 * @updated Changed wp_enqueue_scripts order and dependencies.
	 */
	public function enqueue() {
		wp_enqueue_script(
			'hestia_text_editor',
			get_template_directory_uri() . '/inc/customizer/controls/custom-controls/customizer-page-editor/js/hestia-text-editor.js',
			array(
				'jquery',
			),
			HESTIA_VERSION,
			false
		);
	}

	/**
	 * Render the content on the theme customizer page
	 */
	public function render_content() {
		?>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<input type="hidden" <?php $this->link(); ?> value="<?php echo esc_textarea( $this->value() ); ?>" id="<?php echo esc_attr( $this->id ); ?>" class="editorfield">
			<button data-editor-id="<?php echo esc_attr( $this->id ); ?>" class="button edit-content-button"><?php _e( '(Edit)', 'hestia-pro' ); ?></button>
		</label>
		<?php
	}
}
