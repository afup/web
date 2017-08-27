<?php
/**
 * @package Basis
 */
?>

<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
	<aside role="complementary" id="sidebar" class="sidebar-widgets">
		<?php dynamic_sidebar( 'sidebar-1' ) ?>
	</aside>
<?php endif; ?>