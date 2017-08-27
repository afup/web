<?php
/**
 * @package Basis
 */
?>

<?php if ( has_tag() ) : ?>
	<div class="post-tags">
		<?php the_tags( __( 'Tagged: ', 'basis' ), ', ', '' ); ?>
	</div>
<?php endif; ?>