<?php
/**
 * Template Name: Curl Template
 *
 * @package Basis
 */
?>
<?php
	function basis_child_body_classes() {return ['page-template-default']; }
	add_filter( 'body_class', 'basis_child_body_classes' );
?>

<?php get_header(); ?>

<?php while ( have_posts() ) : ?>
	<?php the_post(); ?>
	<div class="post-content">
		<article id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php
			// Show the featured image and its caption and description if they are available.
			if ( ! post_password_required() && '' !== $featured_image_id = get_post_thumbnail_id() ) :
				$attachment = get_post( $featured_image_id );
				?>
			<div class="page-header">
				<?php echo wp_get_attachment_image( $attachment->ID, 'basis-featured-page' ); ?>
				<?php if ( $attachment->post_content ) : ?>
				<div class="page-header-description">
					<?php echo wpautop( basis_allowed_tags( $attachment->post_content ) ); ?>
				</div>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			<div class="entry basis-list">
				<?php get_template_part( '_post', 'title' ); ?>
				<?php the_content(); ?>
				<?php
					$curl_url = get_post_meta(get_the_ID(), 'curl_url', true);
					$html_block = get_post_meta(get_the_ID(), 'html_block', true);
					if ($html_block !== '') {
						echo $html_block;
					}
					if ($curl_url !== '') {
						$curl_url = json_decode($curl_url, true);
						$lang = 'fr';
						if (isset($_GET['lang']) && isset($curl_url[$_GET['lang']])) {
							$lang = $_GET['lang'];
						}
						remove_filter('the_content', 'wpautop');
						$ch = curl_init();
						curl_setopt(
							$ch,
							CURLOPT_URL,
							$curl_url[$lang]
						);

                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
						curl_setopt(
							$ch,
							CURLOPT_REFERER,
							'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
						);
/*print_r(file_get_contents('php://input'));*/
						if (isset($_POST) && get_post_meta(get_the_ID(), 'curl_handle_post', true) === 'true') {
                                                    curl_setopt($ch, CURLOPT_POST, 1);
						    $post = $_POST;
                                                    if (isset($_FILES)) {
                                                        foreach ($_FILES as $name => $file) {
						            if ($file['size'] !== 0) {
						                $post[$name] = new \CURLFile($file['tmp_name'], $file['type'], $file['name']);
						            }
							}
                                                    }

							curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
						}
						
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                        if (defined('WP_DEBUG') && WP_DEBUG == true) {
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        }

                        $return = curl_exec($ch);
						if (curl_errno($ch) > 0) {
							echo 'Une erreur est survenue';
						} else {
							echo $return;
						}
	
					}
				?>
				<?php get_template_part( '_pagination', 'single' ); ?>
			</div>
		</article>
		<?php get_sidebar( 'page' ); ?>
	</div>

	<?php comments_template(); ?>
<?php endwhile; ?>

<?php get_footer(); ?>
