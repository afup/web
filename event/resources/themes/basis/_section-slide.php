<?php
/**
 * @package Basis
 *
 * Slide parameters in $basis_section_data:
 * - image-id
 * - darken-image
 * - title
 * - content
 * - button-text
 * - button-url
 */

global $basis_section_data;

// Background image minimum width
$minimum_width = 800;
$slide_style   = '';

// If the background image is large enough, show it as a full-width background
$background_image = ( ! empty( $basis_section_data['image-id'] ) ) ? basis_is_thumbnail_wide_enough( $basis_section_data['image-id'], $minimum_width, 'basis-homepage' ) : '';
if ( ! empty( $background_image ) ) :
	$slide_style = sprintf(
		' style="background-image: url(%s);"',
		"'" . addcslashes( esc_url_raw( $background_image[0] ), "'" ) . "'"
	);
endif;
?>
<div class="slideshow-slide"<?php echo $slide_style; ?>>
	<div class="slide-content">
		<?php if ( ! empty( $basis_section_data['title'] ) ) : ?>
			<h2 class="slide-title"><?php echo apply_filters( 'the_title', $basis_section_data['title'] ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $basis_section_data['content'] ) ) : ?>
			<?php basis_get_html_builder()->the_builder_content( $basis_section_data['content'] ); ?>
		<?php endif; ?>

		<?php if ( ! empty( $basis_section_data['button-url'] ) && ! empty( $basis_section_data['button-text'] ) ) : ?>
			<div class="slide-button-container">
				<a class="slide-button basis-primary-background" href="<?php echo esc_url( $basis_section_data['button-url'] ); ?>">
					<?php echo esc_html( $basis_section_data['button-text'] ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( $background_image && ! empty( $basis_section_data['darken-image'] ) && 1 === (int) $basis_section_data['darken-image'] ) : ?>
		<div class="slide-overlay"></div>
	<?php endif; ?>
</div>