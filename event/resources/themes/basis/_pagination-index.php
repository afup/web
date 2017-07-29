<?php
/**
 * @package Basis
 */
?>

<?php if ( false === basis_is_active_infinite_scroll() && ( get_previous_posts_link() || get_next_posts_link() ) ) : ?>
<footer class="pagination index">
	<div class="alignleft">
		<?php previous_posts_link( __( 'Newer posts', 'basis' )); ?>
	</div>
	<div class="alignright">
		<?php next_posts_link( __( 'Older posts', 'basis' )); ?>
	</div>
</footer>
<?php endif; ?>