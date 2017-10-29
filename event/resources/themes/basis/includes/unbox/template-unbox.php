<div class="unbox-overlay">
	<?php
	$messageL0 = __(
		'Thank you!',
		'unbox'
	);

	$messageL1 = __(
		'Thanks for choosing %1$s. It really means the world to us here at The Theme Foundry.',
		'unbox'
	);

	$messageL1 = sprintf(
		$messageL1,
		esc_html( unbox_init()->get_theme_name() )
	);

	$messageL2 = __(
		'To get started, be sure to read your <a target="_blank" href="%1$s">theme\'s documentation</a>. If you have any questions, please stop by our <a target="_blank" href="%2$s">support forums</a>.',
		'unbox'
	);

	$messageL2 = sprintf(
		$messageL2,
		esc_url( unbox_init()->get_documention_link() ),
		esc_url( unbox_init()->get_support_link() )
	);
	?>
	<div class="unbox-message">
		<a class="close" title="<?php esc_attr_e( 'Click to close this message', 'unbox' ); ?>" href="#"></a>
		<div class="wrapper">
			<h2><?php echo esc_html( $messageL0 ); ?></h2>
			<p><?php echo $messageL1; ?></p>
			<p><?php echo $messageL2; ?></p>
			<em class="authors"><?php _e( 'Corey, Drew, Jennifer, Melissa, and Scott', 'unbox' ); ?></em>
		</div>
	</div>
</div>
