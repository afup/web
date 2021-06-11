<?php
/**
 * Page editor helper class.
 *
 * @package Hestia
 * @since   Hestia 1.1.3
 */

/**
 * Class Hestia_Customizer_Page_Editor_Helper
 */
class Hestia_Customizer_Page_Editor_Helper extends Hestia_Abstract_Main {
	/**
	 * Initialize Customizer Page Editor Helper.
	 */
	public function init() {
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'customize_editor' ), 1 );
		add_filter( 'tiny_mce_before_init', array( $this, 'override_tinymce_options' ) );
		add_filter( 'wp_default_editor', array( $this, 'change_editor_mode_to_html' ) );
		$this->filter_content();
	}

	/**
	 * Display editor for page editor control.
	 *
	 * @since 1.1.51
	 */
	public function customize_editor() {
		?>
		<div id="wp-editor-widget-container" style="display: none;">
			<a class="close" href="javascript:WPEditorWidget.hideEditor();"><span class="icon"></span></a>
			<div class="editor">
				<?php
				$settings = array(
					'tinymce' => array(
						'content_style' => $this->get_editor_style(),
						'rows'          => 55,
						'setup'         => "function (editor) {
                 			editor.onInit.add(function(){
                 			var iframe = document.getElementById('wpeditorwidget_ifr');
                 			iframe.style.height = '260px';
                 			});
                		}",
					),
				);
				wp_editor( '', 'wpeditorwidget', $settings );
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Add custom inline style for editor.
	 *
	 * @return string
	 */
	public function get_editor_style() {
		$accent_color  = get_theme_mod( 'accent_color', apply_filters( 'hestia_accent_color_default', '#e91e63' ) );
		$headings_font = get_theme_mod( 'hestia_headings_font' );
		$body_font     = get_theme_mod( 'hestia_body_font' );
		$custom_css    = '';
		// Load google font.
		if ( ! empty( $body_font ) ) {
			$custom_css .= '@import url(\'https://fonts.googleapis.com/css?family=' . esc_attr( $body_font ) . '\');';
		}
		if ( ! empty( $headings_font ) ) {
			$custom_css .= '@import url(\'https://fonts.googleapis.com/css?family=' . esc_attr( $headings_font ) . '\');';
		}
		// Check if accent color is exists.
		if ( ! empty( $accent_color ) ) {
			$custom_css .= 'body.wpeditorwidget.mce-content-body a { color: ' . esc_attr( $accent_color ) . '; }';
		}
		// Check if font family for body exists.
		if ( ! empty( $body_font ) ) {
			$custom_css .= 'body.wpeditorwidget, body.wpeditorwidget p { font-family: ' . esc_attr( $body_font ) . ' !important; }';
		}
		// Check if font family for headings exists.
		if ( ! empty( $headings_font ) ) {
			$custom_css .= 'body.wpeditorwidget h1, body.wpeditorwidget h2, body.wpeditorwidget h3, body.wpeditorwidget h4, body.wpeditorwidget h5, body.wpeditorwidget h6 { font-family: ' . esc_attr( $headings_font ) . ' !important; }';
		}

		return $custom_css;
	}

	/**
	 * When the frontpage is edited, we set a flag with 'sync_customizer' value to know that we should update
	 * hestia_feature_thumbnail control.
	 *
	 * @param int $post_id ID of the post that we need to update.
	 *
	 * @deprecated 2.0.9
	 * @since 1.1.60
	 */
	public function trigger_sync_from_page( $post_id ) {
		$frontpage_id = get_option( 'page_on_front' );
		if ( empty( $frontpage_id ) ) {
			return;
		}
		if ( intval( $post_id ) === intval( $frontpage_id ) ) {
			update_option( 'hestia_sync_needed', 'sync_customizer' );
		};
	}

	/**
	 * When customizer is saved, we set the flag to 'sync_page' value to know that we should update the frontpage
	 * content and feature image.
	 *
	 * @deprecated 2.0.9
	 * @since 1.1.60
	 */
	function trigger_sync_from_customizer() {
		$frontpage_id = get_option( 'page_on_front' );
		if ( ! empty( $frontpage_id ) ) {
			update_option( 'hestia_sync_needed', 'sync_page' );
		}
	}

	/**
	 * Based on 'hestia_sync_needed' option value, update either page or customizer controls and then we update
	 * the flag as false to know that we don't need to update anything.
	 *
	 * @deprecated 2.0.9
	 * @since 1.1.60
	 */
	function sync_controls() {
		$should_sync = get_option( 'hestia_sync_needed' );
		if ( $should_sync === false ) {
			return;
		}
		$frontpage_id = get_option( 'page_on_front' );
		if ( empty( $frontpage_id ) ) {
			return;
		}
		switch ( $should_sync ) {
			// Synchronize customizer controls with page content
			case 'sync_customizer':
				$content = get_post_field( 'post_content', $frontpage_id );
				set_theme_mod( 'hestia_page_editor', $content );
				$featured_image = '';
				if ( has_post_thumbnail( $frontpage_id ) ) {
					$featured_image = get_the_post_thumbnail_url( $frontpage_id );
				} else {
					$thumbnail = get_theme_mod( 'hestia_feature_thumbnail', get_template_directory_uri() . '/assets/img/contact.jpg' );
					if ( $thumbnail === get_template_directory_uri() . '/assets/img/contact.jpg' ) {
						$featured_image = get_template_directory_uri() . '/assets/img/contact.jpg';
					}
				}
				set_theme_mod( 'hestia_feature_thumbnail', $featured_image );
				break;
			// Synchronize frontpage content with customizer values.
			case 'sync_page':
				$content = get_theme_mod( 'hestia_page_editor' );
				if ( ! empty( $frontpage_id ) ) {
					if ( ! wp_is_post_revision( $frontpage_id ) ) {
						// update the post, which calls save_post again
						$post = array(
							'ID'           => $frontpage_id,
							'post_content' => wp_kses_post( $content ),
						);
						wp_update_post( $post );
					}
				}
				$thumbnail    = get_theme_mod( 'hestia_feature_thumbnail', get_template_directory_uri() . '/assets/img/contact.jpg' );
				$thumbnail_id = attachment_url_to_postid( $thumbnail );
				update_post_meta( $frontpage_id, '_thumbnail_id', $thumbnail_id );
				break;
		}
		update_option( 'hestia_sync_needed', false );
	}

	/**
	 * This function updates controls from customizer (about content and featured background) when you change your
	 * frontpage.
	 *
	 * @deprecated 2.0.9
	 */
	public function update_frontpage_change() {
		$pid          = $_POST['pid'];
		$return_value = array();
		$content      = get_post_field( 'post_content', $pid );
		set_theme_mod( 'hestia_page_editor', $content );
		$featured_image = '';
		if ( has_post_thumbnail( $pid ) ) {
			$featured_image = get_the_post_thumbnail_url( $pid );
		} else {
			$thumbnail = get_theme_mod( 'hestia_feature_thumbnail', get_template_directory_uri() . '/assets/img/contact.jpg' );
			if ( $thumbnail === get_template_directory_uri() . '/assets/img/contact.jpg' ) {
				$featured_image = get_template_directory_uri() . '/assets/img/contact.jpg';
			}
		}
		set_theme_mod( 'hestia_feature_thumbnail', $featured_image );
		$return_value['post_content']   = $content;
		$return_value['post_thumbnail'] = $featured_image;
		echo json_encode( $return_value );
		die();
	}

	/**
	 * Hestia allow all HTML tags in TinyMce editor.
	 *
	 * @param array $init_array TinyMce settings.
	 *
	 * @return array
	 */
	public function override_tinymce_options( $init_array ) {
		$opts                                  = '*[*]';
		$init_array['valid_elements']          = $opts;
		$init_array['extended_valid_elements'] = $opts;

		return $init_array;
	}

	/**
	 * Change the default mode of the editor to html when using the tinyMce editor in customizer.
	 *
	 * @param string $editor_mode The current mode of the default editor.
	 *
	 * @return string The new mode (visual or html) of the editor, if we are in the customizer page.
	 */
	public function change_editor_mode_to_html( $editor_mode ) {
		if ( is_customize_preview() && function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( ! isset( $screen->id ) ) {
				return $editor_mode;
			}
			if ( $screen->id === 'customize' ) {
				return 'tmce';
			}
		}

		return $editor_mode;
	}

	/**
	 * This filter is used to filter the content of the post after it is retrieved from the database and before it is
	 * printed to the screen.
	 * Initial we've applied 'the_content' filter but that was wrong because it relies on the global $post being set.
	 * Otherwise, it can break plugins. See https://github.com/Codeinwp/hestia-pro/issues/309 for the issue.
	 * For more explanations check this link https://themehybrid.com/weblog/how-to-apply-content-filters
	 */
	private function filter_content() {
		global $wp_embed;
		add_filter( 'hestia_text', 'wptexturize' );
		add_filter( 'hestia_text', 'convert_smilies' );
		add_filter( 'hestia_text', 'convert_chars' );
		add_filter( 'hestia_text', 'wpautop' );
		add_filter( 'hestia_text', 'shortcode_unautop' );
		add_filter( 'hestia_text', 'do_shortcode' );
		add_filter( 'hestia_text', array( $wp_embed, 'run_shortcode' ) );
		add_filter( 'hestia_text', array( $wp_embed, 'autoembed' ) );
	}
}
