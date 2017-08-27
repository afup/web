<?php
/**
 * @package Basis
 */
?>

<?php if ( is_active_sidebar( 'sidebar-page' ) ) : ?>
	<aside role="complementary" id="sidebar-page" class="sidebar-widgets">
		<?php dynamic_sidebar( 'sidebar-page' ) ?>
	</aside>
<?php endif; ?>