<?php
/**
 * @package Basis
 */

$heading_level = ( is_singular() ) ? '1' : '2';
?>

<h<?php echo $heading_level; ?> class="<?php echo get_post_type(); ?>-title">
	<?php if ( ! is_singular() ) : ?>
	<a href="<?php the_permalink(); ?>" title="<?php esc_attr_e( 'Link to the full post', 'basis' ); ?>" rel="bookmark">
	<?php endif; ?>

		<?php the_title(); ?>

	<?php if ( ! is_singular() ) : ?>
	</a>
	<?php endif; ?>
</h<?php echo $heading_level; ?>>