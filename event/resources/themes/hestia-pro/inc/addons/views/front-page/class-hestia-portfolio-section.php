<?php
/**
 * The Portfolio Section
 *
 * @package Hestia
 */

/**
 * Class Hestia_Portfolio_Section
 */
class Hestia_Portfolio_Section extends Hestia_Abstract_Main {
	/**
	 * Initialize
	 */
	public function init() {
		$this->hook_section();
	}

	/**
	 * Hook the section.
	 */
	private function hook_section() {
		if ( ! class_exists( 'Jetpack', false ) ) {
			return;
		}

		if ( ! Jetpack::is_module_active( 'custom-content-types' ) ) {
			return;
		}

		$section_priority = apply_filters( 'hestia_section_priority', 25, 'hestia_portfolio' );
		add_action( 'hestia_sections', array( $this, 'do_section' ), absint( $section_priority ) );
		add_action( 'hestia_do_portfolio_section', array( $this, 'render_section' ) );

		add_action( 'wp_ajax_nopriv_hestia_get_portfolio_item_data', array( $this, 'hestia_get_portfolio_item_data' ) );
		add_action( 'wp_ajax_hestia_get_portfolio_item_data', array( $this, 'hestia_get_portfolio_item_data' ) );
	}

	/**
	 * Executes the hook on which the content is rendered.
	 */
	public function do_section() {
		do_action( 'hestia_do_portfolio_section', false );
	}

	/**
	 * Portfolio section content.
	 *
	 * @since Hestia 1.0
	 * @modified 1.1.34
	 */
	public function render_section( $is_shortcode = false ) {
		/**
		 * Don't show section if Disable section is checked.
		 * Show it if it's called as a shortcode.
		 */
		$section_style = '';
		$hide_section  = get_theme_mod( 'hestia_portfolio_hide', false );
		if ( $is_shortcode === false && (bool) $hide_section === true ) {
			if ( is_customize_preview() ) {
				$section_style .= 'style="display: none;"';
			} else {
				return;
			}
		}

		/**
		 * Gather data to display the section.
		 */
		$hestia_portfolio_title    = get_theme_mod( 'hestia_portfolio_title', esc_html__( 'Portfolio', 'hestia-pro' ) );
		$hestia_portfolio_subtitle = get_theme_mod( 'hestia_portfolio_subtitle', esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'hestia-pro' ) );
		if ( $is_shortcode ) {
			$hestia_portfolio_title    = '';
			$hestia_portfolio_subtitle = '';
		}

		$hestia_portfolio_items = get_theme_mod( 'hestia_portfolio_items', 3 );

		/**
		 * In case this function is called as shortcode, we remove the container and we add 'is-shortcode' class.
		 */
		$wrapper_class   = $is_shortcode === true ? 'is-shortcode' : '';
		$container_class = $is_shortcode === true ? '' : 'container';

		$html_allowed_strings = array(
			$hestia_portfolio_title,
			$hestia_portfolio_subtitle,
		);
		maybe_trigger_fa_loading( $html_allowed_strings );

		hestia_before_portfolio_section_trigger();
		?>
		<section class="hestia-work <?php echo esc_attr( $wrapper_class ); ?>" id="portfolio"
				data-sorder="hestia_portfolio" <?php echo wp_kses_post( $section_style ); ?>>
			<?php
			hestia_before_portfolio_section_content_trigger();
			if ( $is_shortcode === false ) {
				hestia_display_customizer_shortcut( 'hestia_portfolio_hide', true );
			}
			?>
			<div class="<?php echo esc_attr( $container_class ); ?>">
				<?php
				hestia_top_portfolio_section_content_trigger();
				if ( $is_shortcode === false ) {
					?>
					<div class="row">
						<div class="col-md-8 col-md-offset-2 text-center hestia-portfolio-title-area">
							<?php
							if ( ! empty( $hestia_portfolio_title ) && ! empty( $hestia_portfolio_subtitle ) ) {
								hestia_display_customizer_shortcut( 'hestia_portfolio_title' );
							}
							if ( ! empty( $hestia_portfolio_title ) || is_customize_preview() ) {
								echo '<h2 class="hestia-title">' . wp_kses_post( $hestia_portfolio_title ) . '</h2>';
							}
							if ( ! empty( $hestia_portfolio_subtitle ) || is_customize_preview() ) {
								echo '<h5 class="description">' . wp_kses_post( $hestia_portfolio_subtitle ) . '</h5>';
							}
							?>
						</div>
					</div>
					<?php
				}

				$this->portfolio_content( $hestia_portfolio_items );
				hestia_bottom_portfolio_section_content_trigger();
				?>
			</div>
			<?php hestia_after_portfolio_section_content_trigger(); ?>
		</section>
		<?php
		hestia_after_portfolio_section_trigger();
	}

	/**
	 * Get content for portfolio section.
	 *
	 * @since 1.1.31
	 * @access public
	 *
	 * @param string $hestia_portfolio_items Number of items.
	 */
	function portfolio_content( $hestia_portfolio_items ) {
		$post = new WP_Query(
			array(
				'post_type'      => 'jetpack-portfolio',
				'posts_per_page' => ! empty( $hestia_portfolio_items ) ? absint( $hestia_portfolio_items ) : 3,
			)
		);

		if ( ! $post->have_posts() ) {
			return;
		}

		/**
		 * Flag to know portfolio section layout. If true, the every % 4 and % 5 photo will be 50 width.
		 */
		$hestia_portfolio_boxes_type = get_theme_mod( 'hestia_portfolio_boxes_type', false );

		/**
		 * Index of the current element that is shown from 1 to 5. The sixth emelent will be 1 and so on.
		 * Used in hestia_get_portfolio_item_class to determine if the box should be large or small
		 */
		$portfolio_counter = 1;

		echo '<div class="hestia-portfolio-content">';

		/**
		 * This index is for counting post items so we know when to close .row and start a new one.
		 */
		$i = 1;
		echo '<div class="row">';

		while ( $post->have_posts() ) :
			$post->the_post();

			/**
			 * Post id
			 */
			$pid = get_the_ID();

			/**
			 * Get portfolio item class and update the counter.
			 */
			$item_class             = $this->get_portfolio_item_class( $portfolio_counter );
			$portfolio_class_to_add = $hestia_portfolio_boxes_type === false ? 'col-md-4' : $item_class['class'];
			$portfolio_counter      = $item_class['counter'];
			$animation_attribute    = $item_class['animation_attr'];

			/**
			 * Get portfolio thumbnail.
			 */
			$portfolio_thumbnail = '';
			if ( has_post_thumbnail( $pid ) ) {
				$thumbnail_url       = get_the_post_thumbnail_url( $pid, 'hestia-portfolio' );
				$portfolio_thumbnail = 'style="background-image: url(' . esc_url( $thumbnail_url ) . ')"';
			}
			echo '<div class="col-xs-12 col-ms-10 col-ms-offset-1 col-sm-8 col-sm-offset-2 ' . esc_attr( $portfolio_class_to_add ) . ' portfolio-item" ' . $animation_attribute . '>';
			echo '<div class="card card-background card-raised" ' . wp_kses_post( $portfolio_thumbnail ) . '>';

			$link_attributes   = $this->get_portfolio_link_attributes();
			$lightbox_attrutes = '';

			echo '<a ' . wp_kses_post( $link_attributes ) . '>';

			echo '<div class="content" ' . wp_kses_post( $lightbox_attrutes ) . '>';

			$hestia_categories = get_the_terms( $post->ID, 'jetpack-portfolio-type' );
			if ( ! empty( $hestia_categories ) ) {
				echo '<span class="label label-primary">' . esc_html( $hestia_categories[0]->name ) . '</span>';
			}

			the_title( '<h4 class="card-title">', '</h4>' );

			echo '</div>'; /* END .content */

			echo '</a>';
			echo '</div>';
			echo '</div>';

			if ( ! empty( $hestia_portfolio_boxes_type ) && ( (bool) $hestia_portfolio_boxes_type === true ) ) {
				if ( $i % 3 === 0 ) {
					echo '</div><!-- /.row -->';
					echo '<div class="row">';
				} elseif ( ( $i % 5 === 0 ) ) {
					echo '</div><!-- /.row -->';
					echo '<div class="row">';
					$i = $i - 5;
				}
			} else {
				if ( $i % 3 === 0 ) {
					echo '</div><!-- /.row -->';
					echo '<div class="row">';
				}
			}
			$i ++;

		endwhile;
		wp_reset_postdata();
		echo '</div>';
		$this->show_portfolio_modal();
		echo '</div>';

	}

	/**
	 * Get items link attributes.
	 *
	 * @return string
	 */
	private function get_portfolio_link_attributes() {
		$enable_portfolio_lightbox = get_theme_mod( 'hestia_enable_portfolio_lightbox', false );

		if ( $enable_portfolio_lightbox !== true ) {
			$permalink = get_permalink();
			$tag       = 'href="' . esc_url( $permalink ) . '" title="' . the_title_attribute( 'echo=0' ) . '"';
		} else {
			$tag = 'data-toggle="modal" data-target=".hestia-portfolio-modal" data-pid="' . esc_attr( get_the_ID() ) . '"';
		}

		return $tag;
	}

	/**
	 * Ajax function used for grabbing post content and add it to modal.
	 *
	 * @since 1.1.63
	 */
	public function hestia_get_portfolio_item_data() {
		$pid = $_POST['pid'];

		$title        = get_the_title( $pid );
		$header_image = '';
		if ( has_post_thumbnail( $pid ) ) {
			$image        = get_the_post_thumbnail_url( $pid );
			$header_image = 'style="background-image:url(' . esc_url( $image ) . ')"';
		}
		$content      = apply_filters( 'hestia_text', get_post_field( 'post_content', $pid ) );
		$link_to_post = get_permalink( $pid );

		/**
		 * If we use wp_kses_post, the style tag will be escaped and we need it to display a gallery (for example) in
		 * post content. So we need to do this:
		 *
		 * Get default html tags that are allowed in a post.
		 */
		$allowed_tags = wp_kses_allowed_html( 'post' );
		/**
		 * Add style tag to allowed html.
		 */
		$allowed_tags['style'] = array();

		/**
		 * These tags are used when a user uploads a video or when an oembed shortcode is in action.
		 */
		$allowed_tags['video'] = array(
			'width'    => true,
			'height'   => true,
			'class'    => true,
			'id'       => true,
			'preload'  => true,
			'controls' => true,
		);

		$allowed_tags['source'] = array(
			'src'  => true,
			'type' => true,
		);

		$allowed_tags['iframe'] = array(
			'width'           => true,
			'height'          => true,
			'frameborder'     => true,
			'allow'           => true,
			'src'             => true,
			'allowfullscreen' => true,
		);

		$result = '
	<div class="modal-header section-image">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		<div class="row portfolio-title-container">
			<div class="col-md-12 text-center">
				<h3 class="hestia-title"><a href="' . esc_url( $link_to_post ) . '">' . wp_kses_post( $title ) . '</a></h3>
			</div>
		</div>
		
		<div class="header-filter header-filter-gradient" ' . wp_kses_post( $header_image ) . '></div>
	</div>
	<div class="modal-body">
		' . wp_kses( $content, $allowed_tags ) . '
	</div>';

		echo $result;
		die();
	}

	/**
	 * Display the modal for a portfolio item.
	 *
	 * @since 1.1.63
	 */
	private function show_portfolio_modal() {
		$enable_portfolio_lightbox = get_theme_mod( 'hestia_enable_portfolio_lightbox', false );

		if ( $enable_portfolio_lightbox !== true ) {
			return;
		}
		?>
		<div class="modal fade hestia-portfolio-modal" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="portfolio-loading text-center">
						<i class="fas fa-circle-notch fa-3x fa-spin"></i>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Get portfolio item class based on section layout and item position.
	 *
	 * @param int $portfolio_counter Index of item.
	 *
	 * @return array Array containing class / post ID / animation attribute.
	 *
	 * The returned array contains the following keys:
	 * -----------------------------------------------
	 * ['class'] => Useful for the default and alternative layout of the Portfolio section.
	 * It adds the 'col-md-6' class for big portfolio items and the 'col-md-4'
	 * class for the small portfolio items.
	 *
	 * ['animation_attr'] => Useful for the animation attribute added to the portfolio item
	 * to be used with Animate On Scroll JS.
	 *
	 * ['counter'] => Returns the portfolio counter to be used in the view template.
	 */
	private function get_portfolio_item_class( $portfolio_counter ) {

		if ( ( $portfolio_counter % 4 === 0 ) || ( $portfolio_counter % 5 === 0 ) ) {
			// Add bootstrap class.
			$portfolio_class_to_add = 'col-md-6';
			// Add animation attribute.
			if ( $portfolio_counter % 2 === 0 ) {
				$animation_attr = hestia_add_animationation( 'fade-right' );
			} else {
				$animation_attr = hestia_add_animationation( 'fade-left' );
			}
		} elseif ( $portfolio_counter > 5 ) {

			$portfolio_counter = 1;
			// Add bootstrap class.
			$portfolio_class_to_add = 'col-md-4';
			// Add animation attribute.
			$animation_attr = hestia_add_animationation( 'fade-right' );
		} else {
			// Add bootstrap class.
			$portfolio_class_to_add = 'col-md-4';
			// Add animation attribute.
			if ( $portfolio_counter === 1 ) {
				$animation_attr = hestia_add_animationation( 'fade-right' );
			} elseif ( $portfolio_counter % 2 === 0 ) {
				$animation_attr = hestia_add_animationation( 'fade-up' );
			} else {
				$animation_attr = hestia_add_animationation( 'fade-left' );
			}
		}
		$portfolio_counter ++;

		return array(
			'animation_attr' => $animation_attr,
			'class'          => $portfolio_class_to_add,
			'counter'        => $portfolio_counter,
		);
	}
}
