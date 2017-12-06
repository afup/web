<?php
/**
 * @package Basis
 */

$thumbnail_image = get_the_post_thumbnail( null, 'basis-featured-archive' );
if ( '' !== $thumbnail_image &&
	(
		// Not single and not simple archive view
		( ! is_singular() && ! get_theme_mod( 'archive-content' ) ) ||
		// Is single, but thumbnail isn't wide enough
		( is_singular() && false === basis_is_thumbnail_wide_enough( get_post_thumbnail_id(), 800, 'basis-featured-single' ) )
	)
) : ?>
	<?php if ( ! is_singular() ) : ?>
	<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark">
	<?php endif; ?>
		<?php echo $thumbnail_image; ?>
	<?php if ( ! is_singular() ) : ?>
	</a>
	<?php endif; ?>
<?php endif; ?>