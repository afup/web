<?php
/**
 * Repeater functionality in customize
 *
 * @package Hestia
 */

/**
 * Class Hestia_Repeater
 */
class Hestia_Repeater extends WP_Customize_Control {

	/**
	 * ID of the field
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Repeater box title
	 *
	 * @var string
	 */
	public $item_name;

	/**
	 * Repeater Icon container
	 *
	 * @var string
	 */
	private $customizer_icon_container = '';

	/**
	 * Repeater Allowed HTML tags
	 *
	 * @var array
	 */
	private $allowed_html = array();

	/**
	 * Check if image control is added in the repetear
	 *
	 * @var bool
	 */
	public $customizer_repeater_image_control = false;

	/**
	 * Check if icon control is added in the repetear
	 *
	 * @var bool
	 */
	public $customizer_repeater_icon_control = false;

	/**
	 * Check if color control is added in the repetear
	 *
	 * @var bool
	 */
	public $customizer_repeater_color_control = false;

	/**
	 * Check if second color control is added in the repetear
	 *
	 * @var bool
	 */
	public $customizer_repeater_color2_control = false;

	/**
	 * Check if title control is added in the repetear
	 *
	 * @var bool
	 */
	public $customizer_repeater_title_control = false;

	/**
	 * Check if subtitle control is added in the repetear
	 *
	 * @var bool
	 */
	public $customizer_repeater_subtitle_control = false;

	/**
	 * Check if text control is added in the repetear
	 *
	 * @var bool
	 */
	public $customizer_repeater_text_control = false;

	/**
	 * Check if link control is added in the repetear
	 *
	 * @var bool
	 */
	public $customizer_repeater_link_control = false;

	/**
	 * Check if second text control is added in the repetear
	 *
	 * @var bool
	 */
	public $customizer_repeater_text2_control = false;

	/**
	 * Check if second link control is added in the repetear
	 *
	 * @var bool
	 */
	public $customizer_repeater_link2_control = false;

	/**
	 * Check if shortcode control is added in the repetear
	 *
	 * @var bool
	 */
	public $customizer_repeater_shortcode_control = false;

	/**
	 * Check if internal repeater control is added in the repetear
	 *
	 * @var bool
	 */
	public $customizer_repeater_repeater_control = false;

	/**
	 * Dropdown icons for the current instance.\
	 *
	 * @var string
	 */
	public $dropdown_icons = '';

	/**
	 * Default value.
	 *
	 * @var string
	 */
	public $default;

	/**
	 * Value of setting.
	 *
	 * @var string
	 */
	private $value;

	/**
	 * Repeater constructor.
	 *
	 * @param \WP_Customize_Manager $manager customize manager instance.
	 * @param string                $id      control id.
	 * @param array                 $args    arguments array.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );

		if ( empty( $this->item_name ) ) {
			$this->item_name = ! empty( $this->label ) ? $this->label : esc_html__( 'Customizer Repeater', 'hestia-pro' );
		}

		if ( is_file( get_template_directory() . '/inc/customizer/controls/custom-controls/repeater/icon-picker/icons.php' ) ) {
			$this->customizer_icon_container = '/inc/customizer/controls/custom-controls/repeater/icon-picker/icons';
		}

		$allowed_array1 = wp_kses_allowed_html( 'post' );
		$allowed_array2 = array(
			'input' => array(
				'type'        => array(),
				'class'       => array(),
				'placeholder' => array(),
			),
		);

		$this->allowed_html = array_merge( $allowed_array1, $allowed_array2 );

		$value       = json_decode( $this->value(), true );
		$this->value = $this->rec_wp_parse_args( $value, json_decode( $this->default, true ) );
		if ( empty( $this->value ) ) {
			$this->value = $this->get_empty_value();
		}

	}

	/**
	 * Get empty value if the option is empty.
	 *
	 * @return array
	 */
	private function get_empty_value() {

		$input_types = array(
			array(
				'should_add' => $this->customizer_repeater_image_control,
				'id'         => 'image',
			),
			array(
				'should_add' => $this->customizer_repeater_icon_control,
				'id'         => 'icon',
			),
			array(
				'should_add' => $this->customizer_repeater_color_control,
				'id'         => 'color',
			),
			array(
				'should_add' => $this->customizer_repeater_color2_control,
				'id'         => 'color2',
			),
			array(
				'should_add' => $this->customizer_repeater_title_control,
				'id'         => 'title',
			),
			array(
				'should_add' => $this->customizer_repeater_subtitle_control,
				'id'         => 'subtitle',
			),
			array(
				'should_add' => $this->customizer_repeater_text_control,
				'id'         => 'text',
			),
			array(
				'should_add' => $this->customizer_repeater_link_control,
				'id'         => 'link',
			),
			array(
				'should_add' => $this->customizer_repeater_text2_control,
				'id'         => 'text2',
			),
			array(
				'should_add' => $this->customizer_repeater_link2_control,
				'id'         => 'link2',
			),
			array(
				'should_add' => $this->customizer_repeater_shortcode_control,
				'id'         => 'shortcode',
			),
			array(
				'should_add' => $this->customizer_repeater_repeater_control,
				'id'         => 'social_repeater',
			),
		);
		$value       = array( array() );
		foreach ( $input_types as $input ) {
			if ( $input['should_add'] === true ) {
				$value[0][ $input['id'] ] = '';
			}
		}
		return $value;
	}

	/**
	 * Enqueue resources for the control
	 */
	public function enqueue() {
		if ( $this->customizer_repeater_icon_control === true ) {
			wp_enqueue_style( 'font-awesome-5-all', get_template_directory_uri() . '/assets/font-awesome/css/all.min.css', array(), HESTIA_VENDOR_VERSION );
			wp_enqueue_style( 'font-awesome-4-shim', get_template_directory_uri() . '/assets/font-awesome/css/v4-shims.min.css', array(), HESTIA_VENDOR_VERSION );
		}
		if ( $this->customizer_repeater_color_control === true || $this->customizer_repeater_color2_control === true ) {
			wp_enqueue_style( 'wp-color-picker' );
		}
	}

	/**
	 * Render the control
	 */
	public function render_content() {

		/*Get values (json format)*/
		$values = $this->value();

		/*Decode values*/
		$json = json_decode( $values, true );

		if ( ! is_array( $json ) ) {
			$json = array( $values );
		}

		?>
		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<div class="customizer-repeater-general-control-repeater customizer-repeater-general-control-droppable">
			<?php $this->iterate_array( $json ); ?>
			<input type="hidden" id="customizer-repeater-<?php echo esc_attr( $this->id ); ?>-colector" <?php esc_attr( $this->link() ); ?> class="customizer-repeater-colector" value="<?php echo esc_textarea( $this->value() ); ?>"/>
		</div>
		<button type="button" class="button add_field customizer-repeater-new-field">
			<?php echo __( 'Add new', 'hestia-pro' ) . ' ' . esc_html( $this->item_name ); ?>
		</button>
		<?php
	}

	/**
	 * Iterate array
	 *
	 * @param array $array Array to iterate.
	 */
	private function iterate_array( $array = array() ) {
		/*Counter that helps checking if the box is first and should have the delete button disabled*/
		$it = 0;
		foreach ( $array as $icon ) {
			?>
			<div class="customizer-repeater-general-control-repeater-container customizer-repeater-draggable">
				<div class="customizer-repeater-customize-control-title">
					<?php echo esc_html( $this->item_name ); ?>
				</div>
				<div class="customizer-repeater-box-content-hidden">
					<?php
					$image_url  = ! empty( $icon['image_url'] ) ? $icon['image_url'] : '';
					$icon_value = ! empty( $icon['icon_value'] ) ? $icon['icon_value'] : '';
					$title      = ! empty( $icon['title'] ) ? $icon['title'] : '';
					$subtitle   = ! empty( $icon['subtitle'] ) ? $icon['subtitle'] : '';
					$text       = ! empty( $icon['text'] ) ? $icon['text'] : '';
					$text2      = ! empty( $icon['text2'] ) ? $icon['text2'] : '';
					$link       = ! empty( $icon['link'] ) ? $icon['link'] : '';
					$link2      = ! empty( $icon['link2'] ) ? $icon['link2'] : '';
					$shortcode  = ! empty( $icon['shortcode'] ) ? $icon['shortcode'] : '';
					$repeater   = ! empty( $icon['social_repeater'] ) ? $icon['social_repeater'] : '';
					$color      = ! empty( $icon['color'] ) ? $icon['color'] : '';
					$color2     = ! empty( $icon['color2'] ) ? $icon['color2'] : '';
					$choice     = ! empty( $icon['choice'] ) ? $icon['choice'] : 'customizer_repeater_icon';

					if ( ! empty( $icon['id'] ) ) {
						$id = $icon['id'];
					}

					if ( $this->customizer_repeater_image_control === true && $this->customizer_repeater_icon_control === true ) {

						$this->icon_type_choice( $choice );
						$this->image_control( $image_url, $choice );
						$this->icon_picker_control( $icon_value, $choice );

					} else {

						if ( $this->customizer_repeater_image_control === true ) {
							$this->image_control( $image_url );
						}

						if ( $this->customizer_repeater_icon_control === true ) {
							$this->icon_picker_control( $icon_value );
						}
					}

					if ( $this->customizer_repeater_color_control === true ) {
						$this->input_control(
							array(
								'label'             => apply_filters( 'repeater_input_labels_filter', esc_html__( 'Color', 'hestia-pro' ), $this->id, 'customizer_repeater_color_control' ),
								'class'             => 'customizer-repeater-color-control',
								'type'              => apply_filters( 'hestia_repeater_input_types_filter', 'color', $this->id, 'customizer_repeater_color_control' ),
								'sanitize_callback' => 'sanitize_hex_color',
								'choice'            => $choice,
							),
							$color
						);
					}
					if ( $this->customizer_repeater_color2_control === true ) {
						$this->input_control(
							array(
								'label'             => apply_filters( 'repeater_input_labels_filter', esc_html__( 'Color', 'hestia-pro' ), $this->id, 'customizer_repeater_color2_control' ),
								'class'             => 'customizer-repeater-color2-control',
								'type'              => apply_filters( 'hestia_repeater_input_types_filter', 'color', $this->id, 'customizer_repeater_color2_control' ),
								'sanitize_callback' => 'sanitize_hex_color',
							),
							$color2
						);
					}
					if ( $this->customizer_repeater_title_control === true ) {
						$this->input_control(
							array(
								'label' => apply_filters( 'repeater_input_labels_filter', esc_html__( 'Title', 'hestia-pro' ), $this->id, 'customizer_repeater_title_control' ),
								'class' => 'customizer-repeater-title-control',
								'type'  => apply_filters( 'hestia_repeater_input_types_filter', '', $this->id, 'customizer_repeater_title_control' ),
							),
							$title
						);
					}
					if ( $this->customizer_repeater_subtitle_control === true ) {
						$this->input_control(
							array(
								'label' => apply_filters( 'repeater_input_labels_filter', esc_html__( 'Subtitle', 'hestia-pro' ), $this->id, 'customizer_repeater_subtitle_control' ),
								'class' => 'customizer-repeater-subtitle-control',
								'type'  => apply_filters( 'hestia_repeater_input_types_filter', '', $this->id, 'customizer_repeater_subtitle_control' ),
							),
							$subtitle
						);
					}
					if ( $this->customizer_repeater_text_control === true ) {
						$this->input_control(
							array(
								'label' => apply_filters( 'repeater_input_labels_filter', esc_html__( 'Text', 'hestia-pro' ), $this->id, 'customizer_repeater_text_control' ),
								'class' => 'customizer-repeater-text-control',
								'type'  => apply_filters( 'hestia_repeater_input_types_filter', 'textarea', $this->id, 'customizer_repeater_text_control' ),
							),
							$text
						);
					}
					if ( $this->customizer_repeater_link_control ) {
						$this->input_control(
							array(
								'label'             => apply_filters( 'repeater_input_labels_filter', esc_html__( 'Link', 'hestia-pro' ), $this->id, 'customizer_repeater_link_control' ),
								'class'             => 'customizer-repeater-link-control',
								'sanitize_callback' => 'esc_url_raw',
								'type'              => apply_filters( 'hestia_repeater_input_types_filter', '', $this->id, 'customizer_repeater_link_control' ),
							),
							$link
						);
					}
					if ( $this->customizer_repeater_text2_control === true ) {
						$this->input_control(
							array(
								'label' => apply_filters( 'repeater_input_labels_filter', esc_html__( 'Text', 'hestia-pro' ), $this->id, 'customizer_repeater_text2_control' ),
								'class' => 'customizer-repeater-text2-control',
								'type'  => apply_filters( 'hestia_repeater_input_types_filter', 'textarea', $this->id, 'customizer_repeater_text2_control' ),
							),
							$text2
						);
					}
					if ( $this->customizer_repeater_link2_control ) {
						$this->input_control(
							array(
								'label'             => apply_filters( 'repeater_input_labels_filter', esc_html__( 'Link', 'hestia-pro' ), $this->id, 'customizer_repeater_link2_control' ),
								'class'             => 'customizer-repeater-link2-control',
								'sanitize_callback' => 'esc_url_raw',
								'type'              => apply_filters( 'hestia_repeater_input_types_filter', '', $this->id, 'customizer_repeater_link2_control' ),
							),
							$link2
						);
					}
					if ( $this->customizer_repeater_shortcode_control === true ) {
						$this->input_control(
							array(
								'label' => apply_filters( 'repeater_input_labels_filter', esc_html__( 'Shortcode', 'hestia-pro' ), $this->id, 'customizer_repeater_shortcode_control' ),
								'class' => 'customizer-repeater-shortcode-control',
								'type'  => apply_filters( 'hestia_repeater_input_types_filter', '', $this->id, 'customizer_repeater_shortcode_control' ),
							),
							$shortcode
						);
					}
					if ( $this->customizer_repeater_repeater_control === true ) {
						$this->repeater_control( $repeater );
					}
					echo '<input type="hidden" class="social-repeater-box-id" value="';
					if ( ! empty( $id ) ) {
						echo esc_attr( $id );
					}
					echo '">';
					echo '<button type="button" class="social-repeater-general-control-remove-field"';
					if ( $it === 0 ) {
						echo 'style="display:none;"';
					}
					echo '>';
					esc_html_e( 'Delete field', 'hestia-pro' );
					?>
					</button>

				</div>
			</div>

			<?php
			$it++;
		}
	}

	/**
	 * Input control
	 *
	 * @param array  $options Options.
	 * @param string $value Value.
	 */
	private function input_control( $options, $value = '' ) {
		if ( ! empty( $options['type'] ) ) {
			switch ( $options['type'] ) {
				case 'textarea':
					?>
					<span class="customize-control-title"><?php echo esc_html( $options['label'] ); ?></span>
					<textarea class="<?php echo esc_attr( $options['class'] ); ?>" placeholder="<?php echo esc_attr( $options['label'] ); ?>"><?php echo ( ! empty( $options['sanitize_callback'] ) ? call_user_func_array( $options['sanitize_callback'], array( $value ) ) : esc_attr( $value ) ); ?></textarea>
					<?php
					break;
				case 'color':
					$style_to_add = '';
					if ( $this->id === 'hestia_features_content' && $options['choice'] !== 'customizer_repeater_icon' ) {
						$style_to_add = 'display:none';
					}
					?>
					<span class="customize-control-title"
					<?php
					if ( ! empty( $style_to_add ) ) {
						echo 'style="' . esc_attr( $style_to_add ) . '"';}
					?>
						><?php echo esc_html( $options['label'] ); ?></span>
					<div class="<?php echo esc_attr( $options['class'] ); ?>"
						<?php
						if ( ! empty( $style_to_add ) ) {
							echo 'style="' . esc_attr( $style_to_add ) . '"';}
						?>
					>
						<input type="text" value="<?php echo ( ! empty( $options['sanitize_callback'] ) ? call_user_func_array( $options['sanitize_callback'], array( $value ) ) : esc_attr( $value ) ); ?>" class="<?php echo esc_attr( $options['class'] ); ?>" />
					</div>
					<?php
					break;
			}
		} else {
			?>
			<span class="customize-control-title"><?php echo esc_html( $options['label'] ); ?></span>
			<input type="text" value="<?php echo ( ! empty( $options['sanitize_callback'] ) ? call_user_func_array( $options['sanitize_callback'], array( $value ) ) : esc_attr( $value ) ); ?>" class="<?php echo esc_attr( $options['class'] ); ?>" placeholder="<?php echo esc_attr( $options['label'] ); ?>"/>
			<?php
		}
	}

	/**
	 * Icon picker control
	 *
	 * @param string $value Value.
	 * @param string $show Show or not.
	 */
	private function icon_picker_control( $value = '', $show = '' ) {
		$dropdown_icons = $this->dropdown_icons;

		add_filter(
			'hestia_repeater_icons',
			function( $icons ) use ( $dropdown_icons ) {
				if ( ! empty( $dropdown_icons ) ) {
					return $dropdown_icons;
				}
				return $icons;
			}
		);
		?>
		<div class="social-repeater-general-control-icon"
			<?php
			if ( $show === 'customizer_repeater_image' || $show === 'customizer_repeater_none' ) {
				echo 'style="display:none;"'; }
			?>
		>
			<span class="customize-control-title">
				<?php esc_html_e( 'Icon', 'hestia-pro' ); ?>
			</span>
			<span class="description customize-control-description">
				<?php
				echo sprintf(
					/* translators: Fontawesome link with full list of icons available */
					esc_html__( 'Note: Some icons may not be displayed here. You can see the full list of icons at %1$s.', 'hestia-pro' ),
					/* translators: Fontawesome link with full list of icons available */
					sprintf( '<a href="http://fontawesome.io/icons/" rel="nofollow">%s</a>', esc_html__( 'http://fontawesome.io/icons/', 'hestia-pro' ) )
				);
				?>
			</span>
			<div class="input-group icp-container">
				<?php
				echo '<input data-placement="bottomRight" class="icp icp-auto" value="';
				if ( ! empty( $value ) ) {
					echo esc_attr( $value );
				}
				echo '" type="text">';
				?>
				<span class="input-group-addon">
					<i class="<?php echo esc_attr( hestia_display_fa_icon( $value ) ); ?>"></i>
				</span>
			</div>
			<?php get_template_part( $this->customizer_icon_container ); ?>
		</div>
		<?php
		remove_all_filters( 'hestia_repeater_icons' );
	}

	/**
	 * Image control
	 *
	 * @param string $value Value.
	 * @param string $show Show or not.
	 */
	private function image_control( $value = '', $show = '' ) {
		?>
		<div class="customizer-repeater-image-control"
			<?php
			if ( $show === 'customizer_repeater_icon' || $show === 'customizer_repeater_none' || ( $this->id === 'hestia_features_content' && empty( $show ) ) ) {
				echo 'style="display:none;"'; }
			?>
		>
			<span class="customize-control-title">
				<?php esc_html_e( 'Image', 'hestia-pro' ); ?>
			</span>
			<input type="text" class="widefat custom-media-url" value="<?php echo esc_attr( $value ); ?>">
			<input type="button" class="button button-secondary customizer-repeater-custom-media-button" value="<?php esc_attr_e( 'Upload Image', 'hestia-pro' ); ?>" />
		</div>
		<?php
	}

	/**
	 * Icon/Image/None select control
	 *
	 * @param string $value Select control type.
	 */
	private function icon_type_choice( $value = 'customizer_repeater_icon' ) {
		?>
		<span class="customize-control-title">
			<?php echo apply_filters( 'repeater_input_labels_filter', esc_html__( 'Image type', 'hestia-pro' ), $this->id, 'customizer_repeater_choice_control' ); ?>
		</span>
		<select class="customizer-repeater-image-choice">
			<option value="customizer_repeater_icon" <?php selected( $value, 'customizer_repeater_icon' ); ?>><?php esc_html_e( 'Icon', 'hestia-pro' ); ?></option>
			<option value="customizer_repeater_image" <?php selected( $value, 'customizer_repeater_image' ); ?>><?php esc_html_e( 'Image', 'hestia-pro' ); ?></option>
			<option value="customizer_repeater_none" <?php selected( $value, 'customizer_repeater_none' ); ?>><?php esc_html_e( 'None', 'hestia-pro' ); ?></option>
		</select>
		<?php
	}

	/**
	 * Repeater control
	 *
	 * @param string $value Value.
	 */
	private function repeater_control( $value = '' ) {
		$social_repeater = array();
		$show_del        = 0;
		?>
		<span class="customize-control-title"><?php esc_html_e( 'Social icons', 'hestia-pro' ); ?></span>
		<?php
		echo '<span class="description customize-control-description">';
		echo sprintf(
			/* translators: Fontawesome link with full list of icons available */
			esc_html__( 'Note: Some icons may not be displayed here. You can see the full list of icons at %1$s.', 'hestia-pro' ),
			/* translators: Fontawesome link with full list of icons available */
			sprintf( '<a href="http://fontawesome.io/icons/" rel="nofollow">%s</a>', esc_html__( 'http://fontawesome.io/icons/', 'hestia-pro' ) )
		);
		echo '</span>';
		if ( ! empty( $value ) ) {
			$social_repeater = json_decode( html_entity_decode( $value ), true );
		}
		if ( ( count( $social_repeater ) === 1 && '' === $social_repeater[0] ) || empty( $social_repeater ) ) {
			?>
			<div class="customizer-repeater-social-repeater">
				<div class="customizer-repeater-social-repeater-container">
					<div class="customizer-repeater-rc input-group icp-container">
						<?php
						echo '<input data-placement="bottomRight" class="icp icp-auto" value="';
						if ( ! empty( $value ) ) {
							echo esc_attr( $value );
						}
						echo '" type="text">';
						?>
						<span class="input-group-addon"></span>
					</div>
					<?php get_template_part( $this->customizer_icon_container ); ?>
					<input type="text" class="customizer-repeater-social-repeater-link" placeholder="<?php esc_attr_e( 'Link', 'hestia-pro' ); ?>">
					<input type="hidden" class="customizer-repeater-social-repeater-id" value="">
					<button class="social-repeater-remove-social-item" style="display:none">
						<?php esc_html_e( 'Remove Icon', 'hestia-pro' ); ?>
					</button>
				</div>
				<input type="hidden" class="social-repeater-socials-repeater-colector" value=""/>
			</div>
			<button class="social-repeater-add-social-item button-secondary"><?php esc_html_e( 'Add Icon', 'hestia-pro' ); ?></button>
			<?php
		} else {
			?>
			<div class="customizer-repeater-social-repeater">
				<?php
				foreach ( $social_repeater as $social_icon ) {
					$show_del ++;
					echo '<div class="customizer-repeater-social-repeater-container">';
					echo '<div class="customizer-repeater-rc input-group icp-container">';
					echo '<input data-placement="bottomRight" class="icp icp-auto" value="';
					if ( ! empty( $social_icon['icon'] ) ) {
						echo esc_attr( $social_icon['icon'] );
					}
					echo '" type="text">';
					echo '<span class="input-group-addon"><i class="' . esc_attr( hestia_display_fa_icon( $social_icon['icon'] ) ) . '"></i></span>';
					echo '</div>';
					get_template_part( $this->customizer_icon_container );
					echo '<input type="text" class="customizer-repeater-social-repeater-link" placeholder="' . esc_attr__( 'Link', 'hestia-pro' ) . '" value="';
					if ( ! empty( $social_icon['link'] ) ) {
						echo esc_url( $social_icon['link'] );
					}
					echo '">';
					echo '<input type="hidden" class="customizer-repeater-social-repeater-id" value="';
					if ( ! empty( $social_icon['id'] ) ) {
						echo esc_attr( $social_icon['id'] );
					}
					echo '">';
					echo '<button class="social-repeater-remove-social-item" style="';
					if ( $show_del === 1 ) {
						echo 'display:none';
					}
					echo '">' . esc_html__( 'Remove Icon', 'hestia-pro' ) . '</button>';
					echo '</div>';
				}
				?>
				<input type="hidden" class="social-repeater-socials-repeater-colector" value="<?php echo esc_textarea( html_entity_decode( $value ) ); ?>" />
			</div>
			<button class="social-repeater-add-social-item button-secondary"><?php esc_html_e( 'Add Icon', 'hestia-pro' ); ?></button>
			<?php
		}
	}

	/**
	 * Recursive wp_parse_args.
	 * Extends parse args for nested arrays.
	 *
	 * @param array $target  The target array.
	 * @param array $default The defaults array.
	 *
	 * @return array
	 */
	private function rec_wp_parse_args( &$target, $default ) {
		$target  = (array) $target;
		$default = (array) $default;
		$result  = $default;
		foreach ( $target as $key => &$value ) {
			if ( is_array( $value ) && isset( $result[ $key ] ) ) {
				$result[ $key ] = $this->rec_wp_parse_args( $value, $result[ $key ] );
			} else {
				$result[ $key ] = $value;
			}
		}

		return $result;
	}
}
