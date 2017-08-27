<?php
/**
 * @package Basis
 */

global $basis_section_data;
$order = basis_get_html_builder()->get_featured_section_order( $basis_section_data ); ?>
<section class="product-section basis-list <?php echo esc_attr( basis_get_html_builder()->section_classes() ); ?>">
	<div class="feature-image <?php echo esc_attr( $order['image'] ); ?>">
		<?php $link = ( isset( $basis_section_data['image-link'] ) ) ? $basis_section_data['image-link'] : '' ; ?>
		<?php if ( ! empty( $link ) ) : ?>
		<a class="product-section-custom-link" href="<?php echo esc_url( $link ); ?>">
		<?php endif; ?>
			<figure><?php echo wp_get_attachment_image( $basis_section_data['image-id'], 'basis-featured-page' ); ?></figure>
		<?php if ( ! empty( $link ) ) : ?>
		</a>
		<?php endif; ?>
	</div>
	<div class="feature-content <?php echo esc_attr( $order['text'] ); ?>">
		<?php $link = ( isset( $basis_section_data['title-link'] ) ) ? $basis_section_data['title-link'] : '' ; ?>
		<?php if ( ! empty( $link ) ) : ?>
		<a class="product-section-custom-link" href="<?php echo esc_url( $link ); ?>">
		<?php endif; ?>
			<?php if ( ! empty( $basis_section_data['title'] ) ) : ?>
			<h3 class="feature-section-title"><?php echo apply_filters( 'the_title', $basis_section_data['title'] ); ?></h3>
			<?php endif; ?>
		<?php if ( ! empty( $link ) ) : ?>
		</a>
		<?php endif; ?>

		<?php if ( ! empty( $basis_section_data['content'] ) ) : ?>
		<?php basis_get_html_builder()->the_builder_content( $basis_section_data['content'] ); ?>
		<?php endif; ?>

	</div>
</section>
