<?php
/**
 * @package Basis
 */

$protocol = ( is_ssl() ) ? 'https://' : 'http://';
$current_url = $protocol . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
?>

<?php get_header(); ?>
<div class="post-content">
	<h2 class="page-title">
		<?php _e( '404: Page not found', 'basis' ); ?>
	</h2>
	<p><?php _e( 'We are terribly sorry, but nothing exists at this URL:', 'basis' ); ?><br /><span class="unknown-url"><?php echo esc_url( $current_url ); ?></span></p>
	<p><?php _e( 'Try searching the site:', 'basis' ); ?>
	<?php get_search_form(); ?>
	</p>
</div>

<?php get_footer(); ?>