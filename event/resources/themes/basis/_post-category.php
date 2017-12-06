<?php
/**
 * @package Basis
 */
?>

<?php if ( has_category() ) : ?>
	<div class="post-categories">
		<?php printf( __( 'From: %s', 'basis' ), get_the_category_list( ', ' ) ); ?>
	</div>
<?php endif; ?>