<?php
/**
 * @package Basis
 */

global $basis_section_data; ?>

<section class="product-section basis-list <?php echo esc_attr( basis_get_html_builder()->section_classes() ); ?>">
	<?php $i = 1; foreach ( array(  'left' , 'middle', 'right' ) as $column ) : ?>
	<div class="profile-column profile-column-<?php echo $i; ?>">
		<?php $link = ( isset( $basis_section_data[ $column ]['link'] ) ) ? $basis_section_data[ $column ]['link'] : '' ; ?>
		<?php if ( ! empty( $link ) ) : ?>
		<a class="product-section-custom-link" href="<?php echo esc_url( $link ); ?>">
		<?php endif; ?>

			<?php $image = ( isset( $basis_section_data[ $column ]['image-id'] ) ) ? wp_get_attachment_image( $basis_section_data[ $column ]['image-id'], 'basis-featured-page' ) : ''; ?>
			<?php if ( ! empty( $image ) ) : ?>
			<figure class="profile-column-img"><?php echo $image; ?></figure>
			<?php endif; ?>
			
		<?php if ( ! empty( $link ) ) : ?>
		</a>
		<?php endif; ?>
		<?php if ( ! empty( $link ) ) : ?>
		<a class="product-section-custom-link" href="<?php echo esc_url( $link ); ?>">
		<?php endif; ?>

			<?php if ( ! empty( $basis_section_data[ $column ]['title'] ) ) : ?>
			<h3 class="profile-column-title"><?php echo apply_filters( 'the_title', $basis_section_data[ $column ]['title'] ); ?></h3>
			<?php endif; ?>

		<?php if ( ! empty( $link ) ) : ?>
		</a>
		<?php endif; ?>

		<?php
		/**
		 * The 'the_content' filters are not run on this content. This template will be combined with other templates to
		 * comprise the whole of a page's content. Once that content is combined, the resulting content will be passed
		 * through the 'the_content' filters which provides sanitization, runs shortcodes and allows for plugin compat.
		 */
		if ( ! empty( $basis_section_data[ $column ]['content'] ) ) : ?>
		<?php basis_get_html_builder()->the_builder_content( $basis_section_data[ $column ]['content'] ); ?>
		<?php endif; ?>

	</div>
	<?php $i++; endforeach; ?>
</section>