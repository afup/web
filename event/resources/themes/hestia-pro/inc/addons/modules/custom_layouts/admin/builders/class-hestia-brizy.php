<?php
/**
 * Replace header, footer or hooks for Brizy page builder.
 *
 * @package Inc/Addons/Modules/Custom_Layouts/Admin/Builders
 */
/**
 * Class Hestia_Brizy
 */
class Hestia_Brizy extends Hestia_Abstract_Builders {

	/**
	 * Check if class should load or not.
	 *
	 * @return bool
	 */
	function should_load() {
		return class_exists( 'Brizy_Editor_Post' );
	}

	/**
	 * Function that enqueues styles if needed.
	 */
	public function add_styles() {
		$posts = Hestia_Custom_Layouts_Module::$post_map;
		foreach ( $posts as $hook => $value ) {
			foreach ( $value as $pid => $priority ) {
				try {
					$post = \Brizy_Editor_Post::get( $pid );
					if ( ! $post ) {
						continue;
					}
					$needs_compile = ! $post->isCompiledWithCurrentVersion() || $post->get_needs_compile();
					if ( $needs_compile ) {
						$post->compile_page();
						$post->saveStorage();
						$post->savePost();
					}
					$main = version_compare( BRIZY_VERSION, '1.0.126', '<=' ) ? new \Brizy_Public_Main( $post ) : \Brizy_Public_Main::get( $post );
					add_filter( 'body_class', array( $main, 'body_class_frontend' ) );
					add_action( 'wp_enqueue_scripts', array( $main, '_action_enqueue_preview_assets' ), 9999 );
					add_action(
						'wp_head',
						function () use ( $post ) {
							$html = new \Brizy_Editor_CompiledHtml( $post->get_compiled_html() );
							echo $html->get_head();
						}
					);
				} catch ( \Exception $exception ) {
					// The post type is not supported by Brizy hence Brizy should not be used render the post.
				}
			}
		}
	}

	/**
	 * Builder id.
	 *
	 * @return string
	 */
	function get_builder_id() {
		return 'brizy';
	}

	/**
	 * Load markup for current hook.
	 *
	 * @param int $post_id Layout id.
	 *
	 * @return mixed|void
	 */
	function render( $post_id ) {
		$post_id = Hestia_Abstract_Builders::maybe_get_translated_layout( $post_id );
		try {
			$post = \Brizy_Editor_Post::get( $post_id );
			if ( $post ) {
				$html    = new \Brizy_Editor_CompiledHtml( $post->get_compiled_html() );
				$content = apply_filters( 'hestia_post_content', $html->get_body() );
				echo apply_filters( 'hestia_custom_layout_magic_tags', $content, $post_id );
			}
		} catch ( \Exception $exception ) {
			// The post type is not supported by Brizy hence Brizy should not be used render the post.
		}

		return true;
	}

}
