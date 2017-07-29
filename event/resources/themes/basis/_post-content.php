<?php
/**
 * @package Basis
 */
?>

<?php
// Allow site-wide customization of the 'Read more' link text
$read_more = apply_filters( 'basis_read_more_text', __( 'Read more', 'basis' ) );

// Single posts and pages always use the_content()
if ( is_singular() ) : ?>
	<?php the_content( $read_more ); ?>
<?php elseif ( is_search() ) : ?>
	<?php the_excerpt(); ?>
<?php
// Archive views only show content if the theme option is checked.
// When showing content, the_excerpt gets precedence.
else : ?>
	<?php if ( ! get_theme_mod( 'archive-content' ) ) : ?>
		<?php
		// The post has an excerpt.
		if ( has_excerpt() ) : ?>
			<?php the_excerpt(); ?>
		<?php else : ?>
			<?php the_content( $read_more ); ?>
		<?php endif; ?>
	<?php endif; ?>
<?php endif; ?>
