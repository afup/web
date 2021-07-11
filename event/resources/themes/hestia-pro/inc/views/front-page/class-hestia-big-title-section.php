<?php
/**
 * The Big title section handler.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Big_Title_Section
 */
class Hestia_Big_Title_Section extends Hestia_First_Front_Page_Section {

	/**
	 * Initialize the big title content.
	 */
	public function init() {
		parent::init();
		add_action( 'hestia_first_front_page_section_content', array( $this, 'render_big_title_content' ) );
	}

	/**
	 * Utility to check if we should display parallax.
	 * In hestia lite, hestia_slider_type control does not exist before refactor so we must check if both layers are not empty.
	 */
	public static function should_display_parallax() {
		$hestia_big_title_type = get_theme_mod( 'hestia_slider_type' );
		/**
		 * In hestia lite, hestia_slider_type control does not exist so we must check if both layers are not empty
		 */
		$parallax_layer1 = get_theme_mod( 'hestia_parallax_layer1', apply_filters( 'hestia_parallax_layer1_default', false ) );
		$parallax_layer2 = get_theme_mod( 'hestia_parallax_layer2', apply_filters( 'hestia_parallax_layer2_default', false ) );
		if ( empty( $hestia_big_title_type ) ) {
			if ( empty( $parallax_layer1 ) ) {
				return false;
			}
			if ( empty( $parallax_layer2 ) ) {
				return false;
			}
			/**
			 * Update slider type if hestia_slider_type in lite
			 */
			$should_update = get_option( 'update_slider_type' );
			if ( $should_update !== true ) {
				set_theme_mod( 'hestia_slider_type', 'parallax' );
				update_option( 'update_slider_type', true );
			}
		} else {
			if ( $hestia_big_title_type !== 'parallax' ) {
				return false;
			}
			if ( empty( $parallax_layer1 ) ) {
				return false;
			}
			if ( empty( $parallax_layer2 ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * The main render function for this section.
	 */
	public function render_big_title_content() {
		$this->maybe_render_parallax();
		$this->render_content();
	}

	/**
	 * Render the big title content.
	 */
	public function render_content() {
		$section_content      = $this->get_big_title_content();
		$big_title_background = $this->get_big_title_background();

		if ( empty( $big_title_background ) && empty( $section_content ) ) {
			return;
		} ?>

		<div class="item active">
			<div class="page-header">
				<?php
				if ( is_customize_preview() ) {
					echo '<div class="big-title-image"></div>';
				}
				hestia_before_big_title_section_content_trigger();
				?>

				<div class="container">
					<?php hestia_top_big_title_section_content_trigger(); ?>
					<div class="row hestia-big-title-content">
						<?php $this->show_big_title_content( $section_content ); ?>
					</div>
					<?php hestia_bottom_big_title_section_content_trigger(); ?>
				</div><!-- /.container -->

				<div class="header-filter"
					<?php
					if ( ! empty( $big_title_background ) ) {
						echo 'style="background-image: url(' . esc_url( $big_title_background ) . ')"';
					}
					?>
				></div><!-- /.header-filter -->
				<?php hestia_after_big_title_section_content_trigger(); ?>
			</div><!-- /.page-header -->
		</div>
		<?php
	}

	/**
	 * Get the big title background.
	 *
	 * @return string
	 */
	protected function get_big_title_background() {
		$background = '';
		if ( ! $this->should_display_parallax() ) {
			$background = get_theme_mod( 'hestia_big_title_background', apply_filters( 'hestia_big_title_background_default', get_template_directory_uri() . '/assets/img/slider1.jpg' ) );
		}

		return $background;

	}

	/**
	 * Display big title section content.
	 *
	 * @param array $content Section settings.
	 *
	 * @since 1.1.41
	 */
	public function show_big_title_content( $content ) {
		$alignment               = get_theme_mod( 'hestia_slider_alignment', 'center' );
		$slider_elements_classes = $this->get_big_title_elements_class( $alignment );
		$html_allowed_strings    = array();

		$this->maybe_render_widgets_area( $alignment, 'right', 1 );
		if ( array_key_exists( 'title', $content ) ) {
			$html_allowed_strings[] = $content['title'];
		}
		if ( array_key_exists( 'text', $content ) ) {
			$html_allowed_strings[] = $content['text'];
		}
		maybe_trigger_fa_loading( $html_allowed_strings );
		?>
		<div class="
		<?php
		if ( ! empty( $slider_elements_classes['slide'] ) ) {
			echo esc_attr( $slider_elements_classes['slide'] );
		}
		?>
		">
			<?php if ( ! empty( $content['title'] ) ) { ?>
				<h1 class="hestia-title"><?php echo wp_kses_post( $content['title'] ); ?></h1>
			<?php } ?>
			<?php if ( ! empty( $content['text'] ) ) { ?>
				<span class="sub-title"><?php echo wp_kses_post( $content['text'] ); ?></span>
			<?php } ?>
			<?php if ( ! empty( $content['button_link'] ) && ! empty( $content['button_text'] ) ) { ?>
				<div class="buttons">
					<a href="<?php echo esc_url( $content['button_link'] ); ?>"
							title="<?php echo esc_html( $content['button_text'] ); ?>"
							class="btn btn-primary" <?php echo hestia_is_external_url( $content['button_link'] ); ?>><?php echo esc_html( $content['button_text'] ); ?></a>
					<?php hestia_big_title_section_buttons_trigger(); ?>
				</div>
			<?php } ?>
		</div>
		<?php
		$this->maybe_render_widgets_area( $alignment, 'left', 1 );
	}

	/**
	 * Get Big Title section content.
	 *
	 * @since 1.1.41
	 */
	public function get_big_title_content() {
		$section_content = array();

		$hestia_slider_alignment = get_theme_mod( 'hestia_slider_alignment', 'center' );
		$class_to_add            = ( ! empty( $hestia_slider_alignment ) ? 'text-' . $hestia_slider_alignment : 'text-center' );
		if ( ! empty( $class_to_add ) ) {
			$section_content['class_to_add'] = $class_to_add;
		}

		/* translators: 1 - link to customizer setting. 2 - 'customizer' */
		$title_default          = current_user_can( 'edit_theme_options' ) ? sprintf( esc_html__( 'Change in the %s', 'hestia-pro' ), sprintf( '<a href="%1$s" class="default-link">%2$s</a>', esc_url( admin_url( 'customize.php?autofocus&#91;control&#93;=hestia_big_title_title' ) ), __( 'Customizer', 'hestia-pro' ) ) ) : false;
		$hestia_big_title_title = get_theme_mod( 'hestia_big_title_title', $title_default );
		if ( ! empty( $hestia_big_title_title ) ) {
			$section_content['title'] = $hestia_big_title_title;
		}

		/* translators: 1 - link to customizer setting. 2 - 'customizer' */
		$text_default          = current_user_can( 'edit_theme_options' ) ? sprintf( esc_html__( 'Change in the %s', 'hestia-pro' ), sprintf( '<a href="%1$s" class="default-link">%2$s</a>', esc_url( admin_url( 'customize.php?autofocus&#91;control&#93;=hestia_big_title_text' ) ), __( 'Customizer', 'hestia-pro' ) ) ) : false;
		$hestia_big_title_text = get_theme_mod( 'hestia_big_title_text', $text_default );
		if ( ! empty( $hestia_big_title_text ) ) {
			$section_content['text'] = $hestia_big_title_text;
		}

		$button_text_default          = current_user_can( 'edit_theme_options' ) ? esc_html__( 'Change in the Customizer', 'hestia-pro' ) : false;
		$hestia_big_title_button_text = get_theme_mod( 'hestia_big_title_button_text', $button_text_default );
		if ( ! empty( $hestia_big_title_button_text ) ) {
			$section_content['button_text'] = $hestia_big_title_button_text;
		}

		$button_link_default          = current_user_can( 'edit_theme_options' ) ? esc_url( admin_url( 'customize.php?autofocus&#91;control&#93;=hestia_big_title_button_text' ) ) : false;
		$hestia_big_title_button_link = get_theme_mod( 'hestia_big_title_button_link', $button_link_default );
		if ( ! empty( $hestia_big_title_button_link ) ) {
			$section_content['button_link'] = $hestia_big_title_button_link;
		}

		return $section_content;
	}
}
