<?php
/**
 * @package Basis
 */
?>

<?php if ( 'slideshow.php' !== get_page_template_slug() ) : ?>
	<a href="<?php comments_link(); ?>" class="<?php echo basis_comments_link_class(); ?>" title="<?php echo esc_attr( basis_comments_link_label() ); ?>"></a>
<?php endif; ?>