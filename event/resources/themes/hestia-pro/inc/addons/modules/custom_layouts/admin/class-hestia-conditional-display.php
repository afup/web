<?php
/**
 * Conditional display for custom layouts.
 *
 * @package Inc/Addons/Modules/Custom_Layouts/Admin
 */

/**
 * Class Hestia_Conditional_Display
 */
class Hestia_Conditional_Display {

	/**
	 * Root Ruleset
	 *
	 * @var array
	 */
	private $root_ruleset;
	/**
	 * End Ruleset
	 *
	 * @var array
	 */
	private $end_ruleset = array(
		'post_types'     => array(),
		'posts'          => array(),
		'page_templates' => array(),
		'pages'          => array(),
		'page_type'      => array(),
		'terms'          => array(),
		'taxonomies'     => array(),
		'archive_types'  => array(),
		'users'          => array(),
		'user_status'    => array(),
		'user_roles'     => array(),
	);
	/**
	 * Ruleset Map.
	 *
	 * @var array
	 */
	private $ruleset_map;
	/**
	 * Post types.
	 *
	 * @var array
	 */
	private $post_types;

	/**
	 * Conditional_Display constructor.
	 */
	public function __construct() {
		$this->post_types   = $this->get_post_types();
		$this->root_ruleset = array(
			'post'    => array(
				'label'   => __( 'Post', 'hestia-pro' ),
				'choices' => array(
					'post_type'     => __( 'Post Type', 'hestia-pro' ),
					'post_taxonomy' => __( 'Post Taxonomy', 'hestia-pro' ),
					'post_author'   => __( 'Post Author', 'hestia-pro' ),
					'post'          => __( 'Post', 'hestia-pro' ),
				),
			),
			'page'    => array(
				'label'   => __( 'Page', 'hestia-pro' ),
				'choices' => array(
					'page_type'     => __( 'Page Type', 'hestia-pro' ),
					'page_template' => __( 'Page Template', 'hestia-pro' ),
					'page'          => __( 'Page', 'hestia-pro' ),
				),
			),
			'archive' => array(
				'label'   => __( 'Archive', 'hestia-pro' ),
				'choices' => array(
					'archive_type'     => __( 'Archive Type', 'hestia-pro' ),
					'archive_taxonomy' => __( 'Archive Taxonomy', 'hestia-pro' ),
					'archive_term'     => __( 'Archive Term', 'hestia-pro' ),
					'archive_author'   => __( 'Archive Author', 'hestia-pro' ),
				),
			),
			'user'    => array(
				'label'   => __( 'User', 'hestia-pro' ),
				'choices' => array(
					'user_status' => __( 'User Status', 'hestia-pro' ),
					'user_role'   => __( 'User Role', 'hestia-pro' ),
					'user'        => __( 'User', 'hestia-pro' ),
				),
			),
		);

		$this->end_ruleset['post_types']     = $this->get_post_types();
		$this->end_ruleset['posts']          = $this->get_page_post_list();
		$this->end_ruleset['page_templates'] = $this->get_templates();
		$this->end_ruleset['pages']          = $this->get_page_post_list( 'page' );
		$this->end_ruleset['page_type']      = array(
			'front_page' => __( 'Front Page', 'hestia-pro' ),
			'posts_page' => __( 'Posts Page', 'hestia-pro' ),
			'not_found'  => __( '404', 'hestia-pro' ),
		);
		$this->end_ruleset['terms']          = $this->get_all_taxonomies();
		$this->end_ruleset['taxonomies']     = $this->get_all_taxonomies();
		$this->end_ruleset['archive_types']  = $this->get_archive_types();
		$this->end_ruleset['users']          = $this->get_users();
		$this->end_ruleset['user_status']    = array(
			'logged_in'  => __( 'Logged In', 'hestia-pro' ),
			'logged_out' => __( 'Logged Out', 'hestia-pro' ),
		);
		$this->end_ruleset['user_roles']     = $this->get_user_roles();

		$this->ruleset_map = array(
			'post_types'     => array( 'post_type' ),
			'posts'          => array( 'post' ),
			'page_templates' => array( 'page_template' ),
			'pages'          => array( 'page' ),
			'page_type'      => array( 'page_type' ),
			'terms'          => array( 'post_taxonomy', 'archive_term' ),
			'taxonomies'     => array( 'archive_taxonomy' ),
			'archive_types'  => array( 'archive_type' ),
			'users'          => array( 'user', 'post_author', 'archive_author' ),
			'user_status'    => array( 'user_status' ),
			'user_roles'     => array( 'user_role' ),
		);
	}

	/**
	 * Get the end ruleset.
	 *
	 * @return array
	 */
	public function get_end_ruleset() {
		return $this->end_ruleset;
	}

	/**
	 * Get the root ruleset.
	 *
	 * @return array
	 */
	public function get_root_ruleset() {
		return $this->root_ruleset;
	}

	/**
	 * Get the ruleset map.
	 *
	 * @return array
	 */
	public function get_ruleset_map() {
		return $this->ruleset_map;
	}

	/**
	 * Get available archive types.
	 *
	 * @return array
	 */
	private function get_archive_types() {
		$archive_types = array(
			'date'   => __( 'Date', 'hestia-pro' ),
			'author' => __( 'Author', 'hestia-pro' ),
			'search' => __( 'Search', 'hestia-pro' ),
		);

		return array_merge( $archive_types, $this->get_post_types() );
	}

	/**
	 * Gets the page templates.
	 *
	 * @return array|null
	 */
	private function get_templates() {
		require_once ABSPATH . 'wp-admin/includes/theme.php';

		return array_flip( get_page_templates() );
	}

	/**
	 * Get the pages and posts.
	 *
	 * @param string $type [post/page].
	 *
	 * @return array
	 */
	private function get_page_post_list( $type = 'post' ) {
		if ( $type === 'post' ) {
			$posts = get_posts( array( 'numberposts' => 999 ) );
		}

		if ( $type === 'page' ) {
			$posts = array_filter(
				get_pages( array( 'number' => 999 ) ),
				function ( $item ) {
					if ( (string) $item->ID === get_option( 'page_for_posts' ) ) {
						return false;
					}
					if ( (string) $item->ID === get_option( 'woocommerce_shop_page_id' ) ) {
						return false;
					}
					return true;
				}
			);
		}
		$post_list = array();

		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$post_list[ $post->ID ] = $post->post_title;
			}
		}

		return $post_list;
	}

	/**
	 * Get the site users.
	 *
	 * @return array
	 */
	private function get_users() {
		$users    = array();
		$wp_users = get_users();

		foreach ( $wp_users as $user_data ) {
			$users[ $user_data->ID ] = $user_data->display_name;
		}

		return $users;
	}

	/**
	 * Get all user roles.
	 *
	 * @return array
	 */
	private function get_user_roles() {
		global $wp_roles;
		$roles              = $wp_roles->get_names();
		$user_roles_choices = array(
			'all' => esc_html__( 'All', 'hestia-pro' ),
		);
		foreach ( $roles as $role_slug => $role_name ) {
			$user_roles_choices[ $role_slug ] = $role_name;
		}

		return $user_roles_choices;
	}

	/**
	 * Get all the taxonomies.
	 *
	 * @return array
	 */
	private function get_all_taxonomies() {
		$taxonomies = array();
		foreach ( $this->post_types as $post_type => $label ) {
			$all_taxes = get_object_taxonomies( $post_type );
			foreach ( $all_taxes as $single_tax ) {
				$tax_obj   = get_taxonomy( $single_tax );
				$tax_terms = get_terms( array( 'taxonomy' => $single_tax ) );

				$taxonomies[ $post_type ][] = array(
					'nicename' => $tax_obj->label,
					'name'     => $tax_obj->name,
					'terms'    => $tax_terms,
				);
			}
		}

		return $taxonomies;
	}

	/**
	 * Get the post types.
	 *
	 * @return array
	 */
	private function get_post_types() {
		$post_types = array_filter(
			get_post_types( array( 'public' => true ) ),
			function ( $post_type ) {
				$excluded = array( 'attachment', 'hestia_layouts' );
				if ( in_array( $post_type, $excluded, true ) ) {
					return false;
				}

				return true;
			}
		);
		foreach ( $post_types as $post_type ) {
			$pt_object                = get_post_type_object( $post_type );
			$post_types[ $post_type ] = $pt_object->label;

			if ( ! in_array( $post_type, array( 'page', 'post' ), true ) ) {
				$posts = get_posts(
					array(
						'post_type'   => $post_type,
						'numberposts' => 999,
					)
				);
				if ( ! empty( $posts ) ) {
					$post_list = array();
					foreach ( $posts as $post ) {
						$post_list[ $post->ID ] = $post->post_title;
					}
					$this->end_ruleset['posts'] = array_replace( $this->end_ruleset['posts'], $post_list );
				}
			}
		}

		return $post_types;
	}
}
