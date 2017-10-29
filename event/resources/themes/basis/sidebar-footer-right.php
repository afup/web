<?php
/**
 * @package Basis
 */
?>

<?php if ( is_active_sidebar( 'footer-right' ) ) : ?>
	<div class="footer-widgets footer-widgets-right">
		<?php dynamic_sidebar( 'footer-right' ) ?>
	</div>
<?php endif; ?>