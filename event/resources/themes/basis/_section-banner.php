<?php
/**
 * @package Basis
 *
 * Banner parameters in $basis_section_data:
 * - image-id
 * - darken-image
 * - title
 * - content
 * - button-text
 * - button-url
 */

global $basis_section_data;

// Background image minimum width
$minimum_width        = 800;
$section_banner_style = '';

// If the background image is large enough, show it as a full-width background
$background_image = ( ! empty( $basis_section_data['image-id'] ) ) ? basis_is_thumbnail_wide_enough( $basis_section_data['image-id'], $minimum_width, 'basis-homepage' ) : '';
if ( ! empty( $background_image ) ) :
	$section_banner_style = sprintf(
		' style="background-image: url(%s);"',
		"'" . addcslashes( esc_url_raw( $background_image[0] ), "'" ) . "'"
	);
endif;
?>
<section class="basis-list <?php echo esc_attr( basis_get_html_builder()->section_classes() ); ?>"<?php echo $section_banner_style; ?>>
	<div class="product-section">
		<?php if ( ! empty( $basis_section_data['title'] ) ) : ?>
		<div class="banner-title">
			<h2><?php echo apply_filters( 'the_title', $basis_section_data['title'] ); ?></h2>
		</div>
		<?php endif; ?>

		<div class="banner-content">

			<?php if ( ! empty( $basis_section_data['content'] ) ) : ?>
			<?php basis_get_html_builder()->the_builder_content( $basis_section_data['content'] ); ?>
			<?php endif; ?>

		</div>

		<?php if ( ! empty( $basis_section_data['button-url'] ) && ! empty( $basis_section_data['button-text'] ) ) : ?>
		<div class="banner-button-container">
			<a class="banner-button basis-primary-background" href="<?php echo esc_url( $basis_section_data['button-url'] ); ?>"><?php echo esc_html( $basis_section_data['button-text'] ); ?></a>
		</div>
		<?php endif; ?>
	</div>

	<?php if ( $background_image && ! empty( $basis_section_data['darken-image'] ) && 1 === (int) $basis_section_data['darken-image'] ) : ?>
	<div class="banner-overlay"></div>
	<?php endif; ?>
</section>