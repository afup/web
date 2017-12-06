<?php
/**
 * @package Basis
 */
?>

<?php if ( is_sticky() && '' !== get_theme_mod( 'sticky-label' ) ) : ?>
	<span class="basis-sticky-post"><?php echo wp_strip_all_tags( get_theme_mod( 'sticky-label', __( 'Featured', 'basis' ) ) ); ?></span>
<?php endif; ?>