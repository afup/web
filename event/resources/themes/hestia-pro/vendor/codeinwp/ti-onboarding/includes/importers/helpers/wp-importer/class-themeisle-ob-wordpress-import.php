<?php
/**
 * WordPress WXR Importer.
 */

if ( ! class_exists( 'WP_Importer' ) ) {
	$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
	if ( file_exists( $class_wp_importer ) && is_readable( $class_wp_importer ) ) {
		require $class_wp_importer;
	}
}

/**
 * WordPress Importer class for managing the import process of a WXR file
 *
 * @package    WordPress
 * @subpackage Importer
 */
if ( ! class_exists( 'WP_Importer' ) ) {
	return;
}

class Themeisle_OB_WP_Import extends WP_Importer {
	use Themeisle_OB;
	/**
	 * @var Themeisle_OB_WP_Import_Logger
	 */
	private $logger;

	public $max_wxr_version = 1.2; // max. supported WXR version
	public $id; // WXR attachment ID
	// information to import from WXR file
	public $version;
	public $posts         = array();
	public $terms         = array();
	public $categories    = array();
	public $tags          = array();
	public $base_url      = '';
	public $base_blog_url = '';
	// mappings from old information to new
	public $processed_posts      = array();
	public $processed_terms      = array();
	public $post_orphans         = array();
	public $processed_menu_items = array();
	public $menu_item_orphans    = array();
	public $missing_menu_items   = array();
	public $fetch_attachments    = true;
	public $url_remap            = array();
	public $featured_images      = array();
	public $page_builder         = null;

	/**
	 * Themeisle_OB_WP_Import constructor.
	 *
	 * @param string $page_builder the page builder used.
	 */
	public function __construct( $page_builder = '' ) {
		$this->page_builder = $page_builder;
		require_once ABSPATH . 'wp-admin/includes/import.php';
		require_once ABSPATH . 'wp-admin/includes/post.php';
		require_once ABSPATH . 'wp-admin/includes/taxonomy.php';
	}


	/**
	 * The main controller for the actual import stage.
	 *
	 * @param string $file Path to the WXR file for importing
	 */
	function import( $file ) {
		$this->set_logger();
		add_filter( 'import_post_meta_key', array( $this, 'is_valid_meta_key' ) );
		add_filter( 'http_request_timeout', array( &$this, 'bump_request_timeout' ) );
		$this->import_start( $file );
		wp_suspend_cache_invalidation( true );
		$this->process_categories();
		$this->process_tags();
		$this->process_terms();
		$this->process_posts();
		wp_suspend_cache_invalidation( false );
		// update incorrect/missing information in the DB
		$this->backfill_parents();
		$this->backfill_attachment_urls();
		$this->remap_featured_images();
		$this->import_end();
	}

	/**
	 * Logger initialized.
	 */
	private function set_logger() {
		$this->logger = Themeisle_OB_WP_Import_Logger::get_instance();
	}

	/**
	 * Parses the WXR file and prepares us for the task of processing parsed data
	 *
	 * @param string $file Path to the WXR file for importing
	 */
	private function import_start( $file ) {
		$this->logger->log( 'Import started.', 'success' );
		if ( ! is_file( $file ) ) {
			$this->logger->log( 'No file to import.' );
			die();
		}
		$import_data = $this->parse( $file );
		if ( is_wp_error( $import_data ) ) {
			$this->logger->log( 'Error parsing WXR file.' );
			die();
		}
		$this->version       = $import_data['version'];
		$this->posts         = $import_data['posts'];
		$this->terms         = $import_data['terms'];
		$this->categories    = $import_data['categories'];
		$this->tags          = $import_data['tags'];
		$this->base_url      = esc_url( $import_data['base_url'] );
		$this->base_blog_url = esc_url( $import_data['base_blog_url'] );
		wp_defer_term_counting( true );
		wp_defer_comment_counting( true );
		do_action( 'import_start' );
	}

	/**
	 * Performs post-import cleanup of files and the cache
	 */
	private function import_end() {
		$this->logger->log( "Cleaning up import with id {$this->id}...", 'progress' );
		wp_import_cleanup( $this->id );
		$this->logger->log( 'Done cleanup. Flushing cache...', 'progress' );
		wp_cache_flush();
		$this->logger->log( 'Flushed cache. Removing temporary data..', 'progress' );
		foreach ( get_taxonomies() as $tax ) {
			delete_option( "{$tax}_children" );
			_get_term_hierarchy( $tax );
		}
		wp_defer_term_counting( false );
		wp_defer_comment_counting( false );
		$this->logger->log( 'Done importing', 'success' );
		do_action( 'import_end' );
	}

	/**
	 * Create new categories based on import information
	 *
	 * Doesn't create a new category if its slug already exists
	 */
	private function process_categories() {
		$this->logger->log( 'Processing categories...', 'progress' );
		$this->categories = apply_filters( 'wp_import_categories', $this->categories );
		if ( empty( $this->categories ) ) {
			$this->logger->log( 'No categories to process.', 'warning' );

			return;
		}
		foreach ( $this->categories as $cat ) {
			// if the category already exists leave it alone
			$term_id = term_exists( $cat['category_nicename'], 'category' );
			if ( $term_id ) {
				if ( is_array( $term_id ) ) {
					$term_id = $term_id['term_id'];
				}
				if ( isset( $cat['term_id'] ) ) {
					$this->processed_terms[ intval( $cat['term_id'] ) ] = (int) $term_id;
				}
				continue;
			}
			$category_parent      = empty( $cat['category_parent'] ) ? 0 : category_exists( $cat['category_parent'] );
			$category_description = isset( $cat['category_description'] ) ? $cat['category_description'] : '';
			$catarr               = array(
				'category_nicename'    => $cat['category_nicename'],
				'category_parent'      => $category_parent,
				'cat_name'             => $cat['cat_name'],
				'category_description' => $category_description,
			);
			$catarr               = wp_slash( $catarr );
			$id                   = wp_insert_category( $catarr );
			if ( ! is_wp_error( $id ) ) {
				if ( isset( $cat['term_id'] ) ) {
					$this->processed_terms[ intval( $cat['term_id'] ) ] = $id;
				}
			} else {
				$this->logger->log( $id->get_error_message() );
				continue;
			}
			$this->process_termmeta( $cat, $id['term_id'] );
		}
		unset( $this->categories );
		$this->logger->log( 'Processed categories.', 'success' );
	}

	/**
	 * Create new post tags based on import information
	 *
	 * Doesn't create a tag if its slug already exists
	 */
	private function process_tags() {
		$this->logger->log( 'Processing tags...', 'progress' );
		$this->tags = apply_filters( 'wp_import_tags', $this->tags );
		if ( empty( $this->tags ) ) {
			$this->logger->log( 'No tags to process.', 'success' );

			return;
		}
		foreach ( $this->tags as $tag ) {
			// if the tag already exists leave it alone
			$term_id = term_exists( $tag['tag_slug'], 'post_tag' );
			if ( $term_id ) {
				if ( is_array( $term_id ) ) {
					$term_id = $term_id['term_id'];
				}
				if ( isset( $tag['term_id'] ) ) {
					$this->processed_terms[ intval( $tag['term_id'] ) ] = (int) $term_id;
				}
				continue;
			}
			$tag      = wp_slash( $tag );
			$tag_desc = isset( $tag['tag_description'] ) ? $tag['tag_description'] : '';
			$tagarr   = array(
				'slug'        => $tag['tag_slug'],
				'description' => $tag_desc,
			);
			$id       = wp_insert_term( $tag['tag_name'], 'post_tag', $tagarr );
			if ( ! is_wp_error( $id ) ) {
				if ( isset( $tag['term_id'] ) ) {
					$this->processed_terms[ intval( $tag['term_id'] ) ] = $id['term_id'];
				}
			} else {
				$this->logger->log( "Failed to import post tag {$tag['tag_name']}" );
				$this->logger->log( $id->get_error_message() );
				continue;
			}
			$this->process_termmeta( $tag, $id['term_id'] );
		}
		$this->logger->log( 'Processed tags.', 'success' );
		unset( $this->tags );
	}

	/**
	 * Create new terms based on import information
	 *
	 * Doesn't create a term its slug already exists
	 */
	private function process_terms() {
		$this->logger->log( 'Processing terms...', 'progress' );
		$this->terms = apply_filters( 'wp_import_terms', $this->terms );
		if ( empty( $this->terms ) ) {
			$this->logger->log( 'No terms to process.', 'success' );

			return;
		}
		foreach ( $this->terms as $term ) {
			// if the term already exists in the correct taxonomy leave it alone
			$term_id = term_exists( $term['slug'], $term['term_taxonomy'] );
			if ( $term_id ) {
				if ( is_array( $term_id ) ) {
					$term_id = $term_id['term_id'];
				}
				if ( isset( $term['term_id'] ) ) {
					$this->processed_terms[ intval( $term['term_id'] ) ] = (int) $term_id;
				}
				continue;
			}
			if ( empty( $term['term_parent'] ) ) {
				$parent = 0;
			} else {
				$parent = term_exists( $term['term_parent'], $term['term_taxonomy'] );
				if ( is_array( $parent ) ) {
					$parent = $parent['term_id'];
				}
			}
			$term        = wp_slash( $term );
			$description = isset( $term['term_description'] ) ? $term['term_description'] : '';
			$termarr     = array(
				'slug'        => $term['slug'],
				'description' => $description,
				'parent'      => intval( $parent ),
			);
			$id          = wp_insert_term( $term['term_name'], $term['term_taxonomy'], $termarr );
			if ( ! is_wp_error( $id ) ) {
				if ( isset( $term['term_id'] ) ) {
					$this->processed_terms[ intval( $term['term_id'] ) ] = $id['term_id'];
				}
			} else {
				$this->logger->log( "Failed to import {$term['term_taxonomy']} {$term['term_name']}", 'warning' );
				$this->logger->log( $id->get_error_message() );
				continue;
			}
			$this->process_termmeta( $term, $id['term_id'] );
		}
		unset( $this->terms );
		$this->logger->log( 'Processed terms.', 'success' );
	}

	/**
	 * Add metadata to imported term.
	 *
	 * @since 0.6.2
	 *
	 * @param array $term    Term data from WXR import.
	 * @param int   $term_id ID of the newly created term.
	 */
	private function process_termmeta( $term, $term_id ) {
		if ( ! isset( $term['termmeta'] ) ) {
			$term['termmeta'] = array();
		}
		/**
		 * Filters the metadata attached to an imported term.
		 *
		 * @since 0.6.2
		 *
		 * @param array $termmeta Array of term meta.
		 * @param int   $term_id  ID of the newly created term.
		 * @param array $term     Term data from the WXR import.
		 */
		$term['termmeta'] = apply_filters( 'wp_import_term_meta', $term['termmeta'], $term_id, $term );
		if ( empty( $term['termmeta'] ) ) {
			return;
		}
		foreach ( $term['termmeta'] as $meta ) {
			/**
			 * Filters the meta key for an imported piece of term meta.
			 *
			 * @since 0.6.2
			 *
			 * @param string $meta_key Meta key.
			 * @param int    $term_id  ID of the newly created term.
			 * @param array  $term     Term data from the WXR import.
			 */
			$key = apply_filters( 'import_term_meta_key', $meta['key'], $term_id, $term );
			if ( ! $key ) {
				continue;
			}
			// Export gets meta straight from the DB so could have a serialized string
			$value = maybe_unserialize( $meta['value'] );
			add_term_meta( $term_id, $key, $value );
			/**
			 * Fires after term meta is imported.
			 *
			 * @since 0.6.2
			 *
			 * @param int    $term_id ID of the newly created term.
			 * @param string $key     Meta key.
			 * @param mixed  $value   Meta value.
			 */
			do_action( 'import_term_meta', $term_id, $key, $value );
		}
	}

	/**
	 * Create new posts based on import information
	 *
	 * Posts marked as having a parent which doesn't exist will become top level items.
	 * Doesn't create a new post if: the post type doesn't exist, the given post ID
	 * is already noted as imported or a post with the same title and date already exists.
	 * Note that new/updated terms, comments and meta are imported for the last of the above.
	 */
	private function process_posts() {
		$this->logger->log( 'Processing posts...', 'progress' );
		$this->posts = apply_filters( 'wp_import_posts', $this->posts );
		foreach ( $this->posts as $post ) {
			$post = apply_filters( 'wp_import_post_data_raw', $post );
			if ( ! post_type_exists( $post['post_type'] ) ) {
				$this->logger->log( "Failed to import {$post['post_title']}. Invalid post type {$post['post_type']}" );
				do_action( 'wp_import_post_exists', $post );
				continue;
			}
			if ( isset( $this->processed_posts[ $post['post_id'] ] ) && ! empty( $post['post_id'] ) ) {
				continue;
			}
			if ( $post['status'] == 'auto-draft' ) {
				continue;
			}
			if ( 'nav_menu_item' == $post['post_type'] ) {
				$this->process_menu_item( $post );
				continue;
			}
			$post_type_object = get_post_type_object( $post['post_type'] );
			$post_exists      = post_exists( $post['post_title'], '', $post['post_date'] );
			/**
			 * Filter ID of the existing post corresponding to post currently importing.
			 *
			 * Return 0 to force the post to be imported. Filter the ID to be something else
			 * to override which existing post is mapped to the imported post.
			 *
			 * @see   post_exists()
			 * @since 0.6.2
			 *
			 * @param int   $post_exists Post ID, or 0 if post did not exist.
			 * @param array $post        The post array to be inserted.
			 */
			$post_exists = apply_filters( 'wp_import_existing_post', $post_exists, $post );
			if ( $post_exists && get_post_type( $post_exists ) == $post['post_type'] ) {
				$this->logger->log( $post_type_object->labels->singular_name . ' ' . esc_html( $post['post_title'] ) . ' already exists.', 'success' );
				$comment_post_id                                     = $post_id = $post_exists;
				$this->processed_posts[ intval( $post['post_id'] ) ] = intval( $post_exists );
			} else {
				$post_parent = (int) $post['post_parent'];
				if ( $post_parent ) {
					// if we already know the parent, map it to the new local ID
					if ( isset( $this->processed_posts[ $post_parent ] ) ) {
						$post_parent = $this->processed_posts[ $post_parent ];
						// otherwise record the parent for later
					} else {
						$this->post_orphans[ intval( $post['post_id'] ) ] = $post_parent;
						$post_parent                                      = 0;
					}
				}
				$author           = (int) get_current_user_id();
				$postdata         = array(
					'import_id'      => $post['post_id'],
					'post_author'    => $author,
					'post_date'      => $post['post_date'],
					'post_date_gmt'  => $post['post_date_gmt'],
					'post_content'   => $post['post_content'],
					'post_excerpt'   => $post['post_excerpt'],
					'post_title'     => $post['post_title'],
					'post_status'    => $post['status'],
					'post_name'      => $post['post_name'],
					'comment_status' => $post['comment_status'],
					'ping_status'    => $post['ping_status'],
					'guid'           => $post['guid'],
					'post_parent'    => $post_parent,
					'menu_order'     => $post['menu_order'],
					'post_type'      => $post['post_type'],
					'post_password'  => $post['post_password'],
				);
				$original_post_id = $post['post_id'];
				$postdata         = apply_filters( 'wp_import_post_data_processed', $postdata, $post );
				$postdata         = wp_slash( $postdata );
				if ( 'attachment' == $postdata['post_type'] ) {
					$remote_url = ! empty( $post['attachment_url'] ) ? $post['attachment_url'] : $post['guid'];
					// try to use _wp_attached file for upload folder placement to ensure the same location as the export site
					// e.g. location is 2003/05/image.jpg but the attachment post_date is 2010/09, see media_handle_upload()
					$postdata['upload_date'] = $post['post_date'];
					if ( isset( $post['postmeta'] ) ) {
						foreach ( $post['postmeta'] as $meta ) {
							if ( $meta['key'] == '_wp_attached_file' ) {
								if ( preg_match( '%^[0-9]{4}/[0-9]{2}%', $meta['value'], $matches ) ) {
									$postdata['upload_date'] = $matches[0];
								}
								break;
							}
						}
					}
					$comment_post_id = $post_id = $this->process_attachment( $postdata, $remote_url );
				} else {
					$this->logger->log( "Inserting {$postdata['post_type']}: {$postdata['post_title']}.", 'progress' );
					$comment_post_id = $post_id = wp_insert_post( $postdata, true );
					$this->logger->log( "Done inserting {$postdata['post_type']}: {$postdata['post_title']}.", 'success' );
					do_action( 'wp_import_insert_post', $post_id, $original_post_id, $postdata, $post );
				}
				if ( is_wp_error( $post_id ) ) {
					$this->logger->log( "Failed to import {$post_type_object->labels->singular_name} {$post['post_title']}. \n {$post_id->get_error_message()}" );
					continue;
				}
				if ( $post['is_sticky'] == 1 ) {
					stick_post( $post_id );
				}
			}
			// map pre-import ID to local ID
			$this->processed_posts[ intval( $post['post_id'] ) ] = (int) $post_id;
			if ( ! isset( $post['terms'] ) ) {
				$post['terms'] = array();
			}
			$post['terms'] = apply_filters( 'wp_import_post_terms', $post['terms'], $post_id, $post );
			// add categories, tags and other terms
			if ( ! empty( $post['terms'] ) ) {
				$terms_to_set = array();
				foreach ( $post['terms'] as $term ) {
					// back compat with WXR 1.0 map 'tag' to 'post_tag'
					$taxonomy    = ( 'tag' == $term['domain'] ) ? 'post_tag' : $term['domain'];
					$term_exists = term_exists( $term['slug'], $taxonomy );
					$term_id     = is_array( $term_exists ) ? $term_exists['term_id'] : $term_exists;
					if ( ! $term_id ) {
						$t = wp_insert_term( $term['name'], $taxonomy, array( 'slug' => $term['slug'] ) );
						if ( ! is_wp_error( $t ) ) {
							$term_id = $t['term_id'];
							do_action( 'wp_import_insert_term', $t, $term, $post_id, $post );
						} else {
							$this->logger->log( "Failed to import {$taxonomy} {$term['name']}", 'success' );
							$this->logger->log( $t->get_error_message() );
							do_action( 'wp_import_insert_term_failed', $t, $term, $post_id, $post );
							continue;
						}
					}
					$terms_to_set[ $taxonomy ][] = intval( $term_id );
				}
				foreach ( $terms_to_set as $tax => $ids ) {
					$tt_ids = wp_set_post_terms( $post_id, $ids, $tax );
					do_action( 'wp_import_set_post_terms', $tt_ids, $ids, $tax, $post_id, $post );
				}
				unset( $post['terms'], $terms_to_set );
			}
			if ( ! isset( $post['comments'] ) ) {
				$post['comments'] = array();
			}
			$post['comments'] = apply_filters( 'wp_import_post_comments', $post['comments'], $post_id, $post );

			if ( ! isset( $post['postmeta'] ) ) {
				$post['postmeta'] = array();
			}
			$post['postmeta'] = apply_filters( 'wp_import_post_meta', $post['postmeta'], $post_id, $post );
			// add/update post meta
			if ( ! empty( $post['postmeta'] ) ) {
				foreach ( $post['postmeta'] as $meta ) {
					$key   = apply_filters( 'import_post_meta_key', $meta['key'], $post_id, $post );
					$value = false;
					if ( '_edit_last' == $key ) {
						$key = false;
					}
					if ( $key ) {
						// export gets meta straight from the DB so could have a serialized string
						if ( ! $value ) {
							$value = maybe_unserialize( $meta['value'] );
						}
						if ( $key === '_elementor_data' ) {
							$this->logger->log( 'Filtering elementor meta...', 'progress' );
							require_once 'class-themeisle-ob-elementor-meta-handler.php';
							$meta_handler = new Themeisle_OB_Elementor_Meta_Handler( $value, $this->base_blog_url );
							$meta_handler->filter_meta();
							$this->logger->log( 'Filtered elementor meta.', 'success' );
						}
						if ( in_array(
							$key,
							array(
								'tve_custom_css',
								'tve_content_before_more',
								'tve_updated_post',
							)
						) ) {
							$value = $this->replace_image_urls( $value );
						}
						add_post_meta( $post_id, $key, $value );
						do_action( 'import_post_meta', $post_id, $key, $value );
						// if the post has a featured image, take note of this in case of remap
						if ( '_thumbnail_id' == $key ) {
							$this->featured_images[ $post_id ] = (int) $value;
						}
					}
				}
			}
		}
		unset( $this->posts );
		$this->logger->log( 'Processed posts.', 'success' );
	}

	/**
	 * Attempt to create a new menu item from import data
	 *
	 * Fails for draft, orphaned menu items and those without an associated nav_menu
	 * or an invalid nav_menu term. If the post type or term object which the menu item
	 * represents doesn't exist then the menu item will not be imported (waits until the
	 * end of the import to retry again before discarding).
	 *
	 * @param array $item Menu item details from WXR file
	 */
	private function process_menu_item( $item ) {
		$this->logger->log( "Processing single menu item '{$item['post_title']}'...", 'progress' );
		// skip draft, orphaned menu items
		if ( 'draft' == $item['status'] ) {
			return;
		}
		$menu_slug = false;
		if ( isset( $item['terms'] ) ) {
			// loop through terms, assume first nav_menu term is correct menu
			foreach ( $item['terms'] as $term ) {
				if ( 'nav_menu' == $term['domain'] ) {
					$menu_slug = $term['slug'];
					break;
				}
			}
		}
		// no nav_menu term associated with this menu item
		if ( ! $menu_slug ) {
			$this->logger->log( 'Menu item skipped. Missing slug.', 'error' );

			return;
		}
		$menu_id = term_exists( $menu_slug, 'nav_menu' );
		if ( ! $menu_id ) {
			$this->logger->log( "Menu item invalid slug: {$menu_slug}", 'success' );

			return;
		} else {
			$menu_id = is_array( $menu_id ) ? $menu_id['term_id'] : $menu_id;
		}
		foreach ( $item['postmeta'] as $meta ) {
			${$meta['key']} = $meta['value'];
		}
		if ( 'taxonomy' == $_menu_item_type && isset( $this->processed_terms[ intval( $_menu_item_object_id ) ] ) ) {
			$_menu_item_object_id = $this->processed_terms[ intval( $_menu_item_object_id ) ];
		} elseif ( 'post_type' == $_menu_item_type && isset( $this->processed_posts[ intval( $_menu_item_object_id ) ] ) ) {
			$_menu_item_object_id = $this->processed_posts[ intval( $_menu_item_object_id ) ];
		} elseif ( 'custom' != $_menu_item_type ) {
			// associated object is missing or not imported yet, we'll retry later
			$this->missing_menu_items[] = $item;

			return;
		}
		if ( isset( $this->processed_menu_items[ intval( $_menu_item_menu_item_parent ) ] ) ) {
			$_menu_item_menu_item_parent = $this->processed_menu_items[ intval( $_menu_item_menu_item_parent ) ];
		} elseif ( $_menu_item_menu_item_parent ) {
			$this->menu_item_orphans[ intval( $item['post_id'] ) ] = (int) $_menu_item_menu_item_parent;
			$_menu_item_menu_item_parent                           = 0;
		}
		// wp_update_nav_menu_item expects CSS classes as a space separated string
		$_menu_item_classes = maybe_unserialize( $_menu_item_classes );
		if ( is_array( $_menu_item_classes ) ) {
			$_menu_item_classes = implode( ' ', $_menu_item_classes );
		}
		$args     = array(
			'menu-item-object-id'   => $_menu_item_object_id,
			'menu-item-object'      => $_menu_item_object,
			'menu-item-parent-id'   => $_menu_item_menu_item_parent,
			'menu-item-position'    => intval( $item['menu_order'] ),
			'menu-item-type'        => $_menu_item_type,
			'menu-item-title'       => $item['post_title'],
			'menu-item-url'         => $_menu_item_url,
			'menu-item-description' => $item['post_content'],
			'menu-item-attr-title'  => $item['post_excerpt'],
			'menu-item-target'      => $_menu_item_target,
			'menu-item-classes'     => $_menu_item_classes,
			'menu-item-xfn'         => $_menu_item_xfn,
			'menu-item-status'      => $item['status'],
		);
		$args     = apply_filters( 'wp_import_nav_menu_item_args', $args, $this->base_blog_url );
		$existing = wp_get_nav_menu_items( $menu_id );
		foreach ( $existing as $existing_item ) {
			if ( $args['menu-item-url'] === $existing_item->url ) {
				$this->logger->log( 'Menu item already exists.', 'success' );

				return;
			}
		}

		$id = wp_update_nav_menu_item( $menu_id, 0, $args );
		if ( $id && ! is_wp_error( $id ) ) {
			$this->processed_menu_items[ intval( $item['post_id'] ) ] = (int) $id;
		}
		$this->logger->log( 'Processed single menu item.', 'success' );
	}

	/**
	 * If fetching attachments is enabled then attempt to create a new attachment
	 *
	 * @param array  $post Attachment post details from WXR
	 * @param string $url  URL to fetch attachment from
	 *
	 * @return int|WP_Error Post ID on success, WP_Error otherwise
	 */
	private function process_attachment( $post, $url ) {
		$this->logger->log( "Processing attachment: {$url}...", 'progress' );
		if ( ! $this->fetch_attachments ) {
			$this->logger->log( 'Fetching attachments is not enabled', 'error' );
		}
		// if the URL is absolute, but does not contain address, then upload it assuming base_site_url
		if ( preg_match( '|^/[\w\W]+$|', $url ) ) {
			$url = rtrim( $this->base_url, '/' ) . $url;
		}
		$upload = $this->fetch_remote_file( $url, $post );
		if ( is_wp_error( $upload ) ) {
			$this->logger->log( $upload );

			return $upload;
		}
		$info = wp_check_filetype( $upload['file'] );
		if ( $info ) {
			$post['post_mime_type'] = $info['type'];
		} else {
			$error = new WP_Error( 'attachment_processing_error', 'Invalid file type' );
			$this->logger->log( $error->get_error_message() );

			return $error;
		}
		$post['guid'] = $upload['url'];
		// as per wp-admin/includes/upload.php
		$this->logger->log( 'Inserting attachment...', 'progress' );
		$post_id = wp_insert_attachment( $post, $upload['file'] );
		$this->logger->log( 'Attachment inserted', 'success' );
		wp_update_attachment_metadata( $post_id, wp_generate_attachment_metadata( $post_id, $upload['file'] ) );
		// remap resized image URLs, works by stripping the extension and remapping the URL stub.
		if ( preg_match( '!^image/!', $info['type'] ) ) {
			$parts     = pathinfo( $url );
			$name      = basename( $parts['basename'], ".{$parts['extension']}" ); // PATHINFO_FILENAME in PHP 5.2
			$parts_new = pathinfo( $upload['url'] );
			$name_new  = basename( $parts_new['basename'], ".{$parts_new['extension']}" );
			$this->url_remap[ $parts['dirname'] . '/' . $name ] = $parts_new['dirname'] . '/' . $name_new;
		}
		$this->logger->log( 'Processed attachment.', 'success' );

		return $post_id;
	}

	/**
	 * Attempt to download a remote file attachment
	 *
	 * @param string $url  URL of item to fetch
	 * @param array  $post Attachment details
	 *
	 * @return array|WP_Error Local file location details on success, WP_Error otherwise
	 */
	private function fetch_remote_file( $url, $post ) {
		$this->logger->log( "Fetching attachment from url: {$url}...", 'progress' );
		// extract the file name and extension from the url
		$file_name = basename( $url );
		// get placeholder file in the upload dir with a unique, sanitized filename
		$upload = wp_upload_bits( $file_name, 0, '', $post['upload_date'] );
		if ( $upload['error'] ) {
			return new WP_Error( 'upload_dir_error', $upload['error'] );
		}
		// fetch the remote url and write it to the placeholder file
		$remote_response = wp_safe_remote_get(
			$url,
			array(
				'timeout'  => 300,
				'stream'   => true,
				'filename' => $upload['file'],
			)
		);
		$headers         = wp_remote_retrieve_headers( $remote_response );
		// request failed
		if ( ! $headers ) {
			@unlink( $upload['file'] );
			$error = new WP_Error( 'import_file_error', 'Remote server did not respond' );
			$this->logger->log( $error->get_error_message() );

			return $error;
		}
		$remote_response_code = wp_remote_retrieve_response_code( $remote_response );
		// make sure the fetch was successful
		if ( $remote_response_code != '200' ) {
			@unlink( $upload['file'] );
			$error = new WP_Error( 'import_file_error', sprintf( 'Remote server returned error response %1$d %2$s', esc_html( $remote_response_code ), get_status_header_desc( $remote_response_code ) ) );
			$this->logger->log( $error->get_error_message() );

			return $error;
		}
		$filesize = filesize( $upload['file'] );
		if ( isset( $headers['content-length'] ) && $filesize != $headers['content-length'] ) {
			@unlink( $upload['file'] );
			$error = new WP_Error( 'import_file_error', 'Remote file is incorrect size' );
			$this->logger->log( $error->get_error_message() );

			return $error;
		}
		if ( 0 == $filesize ) {
			@unlink( $upload['file'] );
			$error = new WP_Error( 'import_file_error', 'Zero size file downloaded' );
			$this->logger->log( $error->get_error_message() );

			return $error;
		}
		$max_size = (int) $this->max_attachment_size();
		if ( ! empty( $max_size ) && $filesize > $max_size ) {
			@unlink( $upload['file'] );
			$error = new WP_Error( 'import_file_error', sprintf( 'Remote file is too large, limit is %s', size_format( $max_size ) ) );
			$this->logger->log( $error->get_error_message() );

			return $error;
		}
		// keep track of the old and new urls so we can substitute them later
		$this->url_remap[ $url ]          = $upload['url'];
		$this->url_remap[ $post['guid'] ] = $upload['url']; // r13735, really needed?
		// keep track of the destination if the remote url is redirected somewhere else
		if ( isset( $headers['x-final-location'] ) && $headers['x-final-location'] != $url ) {
			$this->url_remap[ $headers['x-final-location'] ] = $upload['url'];
		}

		$this->logger->log( 'Fetched remote attachment.', 'success' );

		return $upload;
	}

	/**
	 * Attempt to associate posts and menu items with previously missing parents
	 *
	 * An imported post's parent may not have been imported when it was first created
	 * so try again. Similarly for child menu items and menu items which were missing
	 * the object (e.g. post) they represent in the menu
	 */
	function backfill_parents() {
		global $wpdb;
		// find parents for post orphans
		foreach ( $this->post_orphans as $child_id => $parent_id ) {
			$local_child_id = $local_parent_id = false;
			if ( isset( $this->processed_posts[ $child_id ] ) ) {
				$local_child_id = $this->processed_posts[ $child_id ];
			}
			if ( isset( $this->processed_posts[ $parent_id ] ) ) {
				$local_parent_id = $this->processed_posts[ $parent_id ];
			}
			if ( $local_child_id && $local_parent_id ) {
				$wpdb->update( $wpdb->posts, array( 'post_parent' => $local_parent_id ), array( 'ID' => $local_child_id ), '%d', '%d' );
				clean_post_cache( $local_child_id );
			}
		}
		// all other posts/terms are imported, retry menu items with missing associated object
		$missing_menu_items = $this->missing_menu_items;
		foreach ( $missing_menu_items as $item ) {
			$this->process_menu_item( $item );
		}
		// find parents for menu item orphans
		foreach ( $this->menu_item_orphans as $child_id => $parent_id ) {
			$local_child_id = $local_parent_id = 0;
			if ( isset( $this->processed_menu_items[ $child_id ] ) ) {
				$local_child_id = $this->processed_menu_items[ $child_id ];
			}
			if ( isset( $this->processed_menu_items[ $parent_id ] ) ) {
				$local_parent_id = $this->processed_menu_items[ $parent_id ];
			}
			if ( $local_child_id && $local_parent_id ) {
				update_post_meta( $local_child_id, '_menu_item_menu_item_parent', (int) $local_parent_id );
			}
		}
	}

	/**
	 * Use stored mapping information to update old attachment URLs
	 */
	function backfill_attachment_urls() {
		global $wpdb;
		// make sure we do the longest urls first, in case one is a substring of another
		uksort( $this->url_remap, array( &$this, 'cmpr_strlen' ) );
		foreach ( $this->url_remap as $from_url => $to_url ) {
			// remap urls in post_content
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)", $from_url, $to_url ) );
			// remap enclosure urls
			$result = $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_key='enclosure'", $from_url, $to_url ) );
		}
	}

	/**
	 * Update _thumbnail_id meta to new, imported attachment IDs
	 */
	function remap_featured_images() {
		// cycle through posts that have a featured image
		foreach ( $this->featured_images as $post_id => $value ) {
			if ( isset( $this->processed_posts[ $value ] ) ) {
				$new_id = $this->processed_posts[ $value ];
				// only update if there's a difference
				if ( $new_id != $value ) {
					update_post_meta( $post_id, '_thumbnail_id', $new_id );
				}
			}
		}
	}

	/**
	 * Parse a WXR file
	 *
	 * @param string $file Path to WXR file for parsing
	 *
	 * @return array Information gathered from the WXR file
	 */
	private function parse( $file ) {
		$this->logger->log( 'Parsing XML file.', 'success' );
		$parser = new Themeisle_OB_WXR_Parser( $this->page_builder );

		return $parser->parse( $file );
	}

	/**
	 * Decide if the given meta key maps to information we will want to import
	 *
	 * @param string $key The meta key to check
	 *
	 * @return string|bool The key if we do want to import, false if not
	 */
	public function is_valid_meta_key( $key ) {
		// skip attachment metadata since we'll regenerate it from scratch
		// skip _edit_lock as not relevant for import
		if ( in_array( $key, array( '_wp_attached_file', '_wp_attachment_metadata', '_edit_lock' ) ) ) {
			return false;
		}

		return $key;
	}

	/**
	 * Decide whether or not the importer should attempt to download attachment files.
	 * Default is true, can be filtered via import_allow_fetch_attachments. The choice
	 * made at the import options screen must also be true, false here hides that checkbox.
	 *
	 * @return bool True if downloading attachments is allowed
	 */
	function allow_fetch_attachments() {
		return apply_filters( 'import_allow_fetch_attachments', true );
	}

	/**
	 * Decide what the maximum file size for downloaded attachments is.
	 * Default is 0 (unlimited), can be filtered via import_attachment_size_limit
	 *
	 * @return int Maximum attachment file size to import
	 */
	private function max_attachment_size() {
		return apply_filters( 'import_attachment_size_limit', 0 );
	}

	/**
	 * Added to http_request_timeout filter to force timeout at 60 seconds during import
	 * @return int 60
	 */
	public function bump_request_timeout( $val ) {
		return 60;
	}

	// return the difference in length between two strings
	public function cmpr_strlen( $a, $b ) {
		return strlen( $b ) - strlen( $a );
	}
}
