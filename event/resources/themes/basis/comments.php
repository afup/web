<?php
/**
 * @package Basis
 */
?>

<?php if ( post_password_required() ) return; ?>

<?php if ( comments_open() || have_comments() ) : ?>
<div id="comments-wrapper">
	<div class="post-content">
		<div class="entry">
<?php endif; ?>

<?php if ( have_comments() ) :
	$comment_count = number_format_i18n( get_comments_number() );
	?>
			<section id="comments" class="thecomments">

				<h3 id="comment-headline"><?php printf( _n( 'One response to <span>%2$s</span>', '%1$s responses to <span>%2$s</span>', $comment_count, 'basis' ), $comment_count, get_the_title() ); ?></h3>

				<ol class="commentlist">
					<?php wp_list_comments( array( 'callback' => 'basis_comment' ) ); ?>
				</ol>

				<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
				<nav id="comments-nav" class="pagination">
					<div class="comments-previous alignright"><?php previous_comments_link( __( 'Older comments', 'basis' ) ); ?></div>
					<div class="comments-next alignleft"><?php next_comments_link( __( 'Newer comments', 'basis' ) ); ?></div>
				</nav>
				<?php endif; ?>

				<?php if ( ! comments_open() && ! is_page() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
				<p class="comments-closed"><em><?php _e( 'Comments are closed.', 'basis' ); ?></em></p>
				<?php endif; ?>
			</section>
<?php endif; ?>

<?php comment_form(); ?>

<?php if ( comments_open() || have_comments() ) : ?>
		</div>
	</div>
</div>
<?php endif; ?>