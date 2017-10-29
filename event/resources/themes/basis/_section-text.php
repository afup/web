<?php
/**
 * @package Basis
 */

global $basis_section_data; ?>
<section class="product-section basis-list <?php echo esc_attr( basis_get_html_builder()->section_classes() ); ?>">
	<?php if ( ! empty( $basis_section_data['title'] ) ) : ?>
	<h3 class="text-section-title"><?php echo apply_filters( 'the_title', $basis_section_data['title'] ); ?></h3>
	<?php endif; ?>

	<?php if ( ! empty( $basis_section_data['content'] ) ) : ?>
	<?php basis_get_html_builder()->the_builder_content( $basis_section_data['content'] ); ?>
	<?php endif; ?>

</section>