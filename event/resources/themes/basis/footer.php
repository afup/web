<?php
/**
 * @package Basis
 */
?>
<footer id="footer">
	<div class="footer-wrapper">
		<?php basis_maybe_show_footer_widgets(); ?>

		<section class="fine-print">
			<?php $footer_text = get_theme_mod( 'footer-text' ); ?>
			<?php if ( ! empty( $footer_text ) ) : ?>
			<p class="footer-text">
				<?php echo basis_allowed_tags( $footer_text ); ?>
			</p>
			<?php endif; ?>
			<p class="theme-byline">
				<?php if ( basis_is_wpcom() ) : ?>
				<a href="http://wordpress.org/"><?php printf( __( 'Proudly powered by %s', 'collections' ), 'WordPress' ); ?></a><br />
				<?php printf( __( 'Theme: %1$s by %2$s.', 'basis' ), 'Basis', '<a href="https://thethemefoundry.com/" rel="designer">The Theme Foundry</a>' ); ?>
				<?php else : ?>
				<a title="<?php esc_attr_e( 'Theme info', 'basis' ); ?>" href="https://thethemefoundry.com/wordpress-themes/basis/">Basis theme</a> <span class="by"><?php _e( 'by', 'basis' ); ?></span> <a title="<?php esc_attr_e( 'The Theme Foundry home page', 'basis' ); ?>" href="https://thethemefoundry.com/">The Theme Foundry</a>
				<?php endif; ?>
			</p>
		</section>

		<?php $social_links = basis_get_social_links(); ?>
		<?php if ( ! empty( $social_links ) ) : ?>
		<ul id="social" class="icons">
			<?php foreach ( $social_links as $service_name => $details ) : ?>
			<li>
				<a class="<?php echo esc_attr( $service_name ); ?>" href="<?php echo esc_url( $details['url'] ); ?>" title="<?php echo esc_attr( $details['title'] ); ?>"></a>
			</li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>
	
	</div>
</footer>

</div>
<?php wp_footer(); ?>
</body>
</html>