<?php
/**
 * Abstract class for builders compatibility.
 *
 * @package Inc/Addons/Modules/Custom_Layouts/Admin/Builders
 */

/**
 * Class Hestia_Abstract_Builders
 */
abstract class Hestia_Abstract_Builders {

	/**
	 * Id of the current builder
	 *
	 * @var string
	 */
	protected $builder_id;


	/**
	 * The static rules array.
	 *
	 * @var array
	 */
	private $static_rules = array();

	/**
	 * Check if class should load or not.
	 *
	 * @return bool
	 */
	abstract function should_load();

	/**
	 * Get builder id.
	 *
	 * @return string
	 */
	abstract function get_builder_id();

	/**
	 * Add actions to hooks.
	 */
	public function register_hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'add_styles' ), 9 );
		add_filter( 'hestia_custom_layout_magic_tags', array( $this, 'replace_magic_tags' ), 10, 2 );
	}

	/**
	 * Replace magic tags from post content.
	 *
	 * @param string $post_content Current post content.
	 * @param int    $post_id Post id.
	 * @return string
	 */
	public function replace_magic_tags( $post_content, $post_id ) {
		$condition_groups = json_decode( get_post_meta( $post_id, 'custom-layout-conditional-logic', true ), true );
		if ( empty( $condition_groups ) ) {
			return $post_content;
		}

		$archive_taxonomy = array( 'category', 'product_cat', 'post_tag', 'product_tag' );

		foreach ( $archive_taxonomy as $type ) {
			if ( $this->layout_has_condition( 'archive_taxonomy', $type, $condition_groups[0] ) ) {
				$category     = get_queried_object();
				$title        = $category->name;
				$description  = $category->description;
				$post_content = str_replace( '{title}', $title, $post_content );
				$post_content = str_replace( '{description}', $description, $post_content );
			}
		}

		if ( $this->layout_has_condition( 'archive_type', 'author', $condition_groups[0] ) ) {
			$author_id         = get_the_author_meta( 'ID' );
			$author_name       = get_the_author_meta( 'display_name' );
			$author_decription = get_the_author_meta( 'description' );
			$author_avatar     = get_avatar( $author_id, 32 );
			$post_content      = str_replace( '{author}', $author_name, $post_content );
			$post_content      = str_replace( '{author_description}', $author_decription, $post_content );
			$post_content      = str_replace( '{author_avatar}', $author_avatar, $post_content );
		}

		if ( $this->layout_has_condition( 'archive_type', 'date', $condition_groups[0] ) ) {
			$date         = get_the_archive_title();
			$post_content = str_replace( '{date}', $date, $post_content );
		}

		return $post_content;
	}

	/**
	 * Check if current custom layout has a specific condition.
	 *
	 * @param string $root Page category.
	 * @param string $end  Page type.
	 * @param array  $condition_groups List of conditions.
	 *
	 * @return bool
	 */
	private function layout_has_condition( $root, $end, $condition_groups ) {
		foreach ( $condition_groups as $index => $conditions ) {
			if ( $conditions['root'] === $root && $conditions['end'] === $end && $conditions['condition'] === '===' ) {
				return true;
			}
		}
		return false;
	}


	/**
	 * Get the builder that you used to edit a post.
	 *
	 * @param int $post_id Post id.
	 *
	 * @return string
	 */
	public static function get_post_builder( $post_id ) {
		if ( get_post_meta( $post_id, 'hestia_editor_mode', true ) === '1' ) {
			return 'custom';
		}

		if ( class_exists( '\Elementor\Plugin', false ) && \Elementor\Plugin::$instance->db->is_built_with_elementor( $post_id ) ) {
			return 'elementor';
		}

		if ( class_exists( 'FLBuilderModel', false ) && get_post_meta( $post_id, '_fl_builder_enabled', true ) ) {
			return 'beaver';
		}

		if ( class_exists( 'Brizy_Editor_Post', false ) ) {
			try {
				$post = \Brizy_Editor_Post::get( $post_id );
				if ( $post->uses_editor() ) {
					return 'brizy';
				}
			} catch ( \Exception $exception ) {
				// The post type is not supported by Brizy hence Brizy should not be used render the post.
			}
		}

		return 'default';
	}

	/**
	 * Abstract function that needs to be implemented in Builders classes.
	 * It loads the markup based on current hook.
	 *
	 * @param int $id Layout id.
	 *
	 * @return mixed
	 */
	abstract function render( $id );

	/**
	 * Function that enqueues styles if needed.
	 */
	abstract function add_styles();

	/**
	 * Check the display conditions.
	 *
	 * @param int $custom_layout_id the custom layout post ID.
	 *
	 * @return bool
	 */
	public function check_conditions( $custom_layout_id ) {
		$this->setup_static_rules();
		$condition_groups = json_decode( get_post_meta( $custom_layout_id, 'custom-layout-conditional-logic', true ), true );

		if ( ! is_array( $condition_groups ) || empty( $condition_groups ) ) {
			return true;
		}
		$evaluated_groups = array();
		foreach ( $condition_groups as $index => $conditions ) {
			$individual_rules = array();
			foreach ( $conditions as $condition ) {
				$individual_rules[ $index ][] = $this->evaluate_condition( $condition );
			}
			$evaluated_groups[ $index ] = ! in_array( false, $individual_rules[ $index ], true );
		}

		return in_array( true, $evaluated_groups, true );
	}

	/**
	 * Setup static rules.
	 */
	private function setup_static_rules() {
		$this->static_rules = array(
			'page_type'   => array(
				'front_page' => get_option( 'show_on_front' ) === 'page' && is_front_page(),
				'not_found'  => is_404(),
				'posts_page' => is_home(),
			),
			'user_status' => array(
				'logged_in'  => is_user_logged_in(),
				'logged_out' => ! is_user_logged_in(),
			),
		);

		$this->static_rules['archive_type'] = array(
			'date'   => is_date(),
			'author' => is_author(),
			'search' => is_search(),
		);

		$post_types = get_post_types( array( 'public' => true ) );

		foreach ( $post_types as $post_type ) {
			if ( $post_type === 'post' ) {
				$this->static_rules['archive_type'][ $post_type ] = is_home();
				continue;
			}
			$this->static_rules['archive_type'][ $post_type ] = is_post_type_archive( $post_type );
		}
	}

	/**
	 * Evaluate single condition
	 *
	 * @param array $condition condition.
	 *
	 * @return bool
	 */
	private function evaluate_condition( $condition ) {
		$post_id = null;
		global $post;
		if ( isset( $post->ID ) ) {
			$post_id = (string) $post->ID;
		}
		if ( ! is_array( $condition ) || empty( $condition ) ) {
			return true;
		}
		$evaluated = false;
		switch ( $condition['root'] ) {
			case 'post_type':
				$evaluated = is_singular( $condition['end'] );
				break;
			case 'post':
				$evaluated = is_single() && $post_id === $condition['end'];
				break;
			case 'page':
				$evaluated = is_page() && $post_id === $condition['end'];
				break;
			case 'page_template':
				$evaluated = get_page_template_slug() === $condition['end'];
				break;
			case 'page_type':
				$evaluated = $this->static_rules['page_type'][ $condition['end'] ];
				break;
			case 'post_taxonomy':
				$parts = preg_split( '/\|/', $condition['end'] );
				if ( is_array( $parts ) && sizeof( $parts ) === 2 ) {
					$evaluated = is_singular() && has_term( $parts[1], $parts[0], get_the_ID() );
				}
				break;
			case 'archive_term':
				$parts  = preg_split( '/\|/', $condition['end'] );
				$object = get_queried_object();
				if ( is_array( $parts ) && sizeof( $parts ) === 2 && $object instanceof \WP_Term && isset( $object->slug ) ) {
					$evaluated = $object->slug === $parts[1] && $object->taxonomy === $parts[0];
				}
				break;
			case 'archive_taxonomy':
				$object = get_queried_object();
				if ( $object instanceof \WP_Term && isset( $object->taxonomy ) ) {
					$evaluated = $object->taxonomy === $condition['end'];
				}
				break;
			case 'archive_type':
				$evaluated = $this->static_rules['archive_type'][ $condition['end'] ];
				break;
			case 'user':
				$evaluated = (string) get_current_user_id() === $condition['end'];
				break;
			case 'post_author':
				$evaluated = is_singular() && (string) $post->post_author === $condition['end'];
				break;
			case 'archive_author':
				$evaluated = is_author( $condition['end'] );
				break;
			case 'user_status':
				$evaluated = $this->static_rules['user_status'][ $condition['end'] ];
				break;
			case 'user_role':
				$user      = wp_get_current_user();
				$evaluated = in_array( $condition['end'], $user->roles, true );
				break;
		}
		if ( $condition['condition'] === '===' ) {
			return $evaluated;
		}

		return ! $evaluated;
	}

	/**
	 * Get the translated layout in Polylang.
	 *
	 * @param int $post_id Post id.
	 * @return int
	 */
	public static function maybe_get_translated_layout( $post_id ) {
		if ( function_exists( 'pll_current_language' ) && function_exists( 'pll_get_post' ) ) {
			$lang               = pll_current_language();
			$translated_post_id = pll_get_post( $post_id, $lang );
			return is_int( $translated_post_id ) ? $translated_post_id : $post_id;
		}

		// https://wpml.org/documentation/support/wpml-coding-api/wpml-hooks-reference/#hook-605256
		return apply_filters( 'wpml_object_id', $post_id, 'neve_custom_layouts', true );
	}
}
