<?php
/**
 * This class allows developers to implement an icon picker control.
 *
 * @package     Hestia
 * @since       1.1.74
 * @author      Andrei Baicus <andrei@themeisle.com>
 * @copyright   Copyright (c) 2017, Themeisle
 * @link        http://themeisle.com/
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Class Hestia_Iconpicker - icon picker
 *
 * @since  1.1.74
 * @access public
 */
class Hestia_Iconpicker extends WP_Customize_Control {

	/**
	 * Control id
	 *
	 * @var - id
	 */
	public $id;

	/**
	 * Control default value
	 *
	 * @var string - default value.
	 */
	public $customizer_icon_container = '';

	/**
	 * Hestia_Iconpicker constructor
	 *
	 * @param WP_Customize_Manager $manager wp_customize manager.
	 * @param string               $id      control id.
	 * @param array                $args    public parameters.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
		$icons = 'inc/customizer/controls/custom-controls/repeater/icon-picker/icons';
		if ( is_file( trailingslashit( get_template_directory() ) . $icons . '.php' ) ) {
			$this->customizer_icon_container = $icons;
		}
	}

	/**
	 * Render icon picker control
	 */
	public function render_content() {
		?>

		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<span class="description customize-control-description">
				<?php
				echo sprintf(
					/* translators: 1. link to font awesome icons */
					esc_html__( 'Note: Some icons may not be displayed here. You can see the full list of icons at %1$s.', 'hestia-pro' ),
					sprintf( '<a href="http://fontawesome.io/icons/" rel="nofollow">%s</a>', esc_html__( 'http://fontawesome.io/icons/', 'hestia-pro' ) )
				);
				?>
			</span>


		<div class="input-group icp-container">
			<input data-placement="bottomRight" class="icp icp-auto"
					value="
				<?php
					$icon_value = $this->value();
				if ( ! empty( $icon_value ) ) {
					echo esc_attr( $icon_value );
				}
				?>
				"
					type="text" <?php $this->link(); ?>">
			<span class="input-group-addon">
					<i class="<?php echo esc_attr( hestia_display_fa_icon( $this->value() ) ); ?>"></i>
				</span>
		</div>
		<?php
		get_template_part( $this->customizer_icon_container );

	}
}
