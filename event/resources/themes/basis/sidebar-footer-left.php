<?php
/**
 * @package Basis
 */
?>

<?php if ( is_active_sidebar( 'footer-left' ) ) : ?>
	<div class="footer-widgets footer-widgets-left">
		<?php dynamic_sidebar( 'footer-left' ) ?>
	</div>
<?php endif; ?>