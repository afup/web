<?php
/**
 * @package Basis
 */
?>

<form class="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">
	<input placeholder="<?php esc_attr_e( 'Search', 'basis' ); ?>" type="text" name="s" value="<?php the_search_query(); ?>">
</form>