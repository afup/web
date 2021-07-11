<?php
/**
 * WordPress eXtended RSS file parser implementations
 *
 * @package    WordPress
 * @subpackage Importer
 */

/**
 * WordPress Importer class for managing parsing of WXR files.
 */
class Themeisle_OB_WXR_Parser {
	/**
	 * Logger instance.
	 *
	 * @var Themeisle_OB_WP_Import_Logger
	 */
	private $logger;

	/**
	 * Used page builder.
	 *
	 * @var string
	 */
	private $page_builder;

	/**
	 * Themeisle_OB_WXR_Parser constructor.
	 *
	 * @param string $page_builder the page builder used.
	 */
	public function __construct( $page_builder = '' ) {
		$this->page_builder = $page_builder;
		$this->logger       = Themeisle_OB_WP_Import_Logger::get_instance();
	}

	/**
	 * Parse XML file.
	 *
	 * @param string $file file path.
	 *
	 * @return array|WP_Error
	 */
	public function parse( $file ) {
		$this->logger->log( "Starting parsing file:{$file}", 'progress' );
		$result = null;

		if ( $this->page_builder === 'beaver-builder' || $this->page_builder === 'beaver builder' ) {
			if ( extension_loaded( 'xml' ) ) {
				$parser = new Themeisle_OB_Beaver_ParserXML();

				$result = $parser->parse( $file );
			} else {
				$this->logger->log( 'xml not active.' );
			}
			if ( is_wp_error( $result ) ) {
				$this->logger->log( "Parse failed with message: {$result->get_error_message()}" );
			}

			if ( $result === null ) {
				$this->logger->log( 'Nothing got parsed from XML.' );
			}

			return $result;
		}

		// Attempt to use proper XML parsers first
		if ( extension_loaded( 'simplexml' ) ) {
			$parser = new Themeisle_OB_WXR_Parser_SimpleXML();
			$this->logger->log( 'Using Themeisle_OB_WXR_Parser_SimpleXML...', 'progress' );
			$result = $parser->parse( $file );
			// If SimpleXML succeeds or this is an invalid WXR file then return the results
			if ( ! is_wp_error( $result ) || 'SimpleXML_parse_error' != $result->get_error_code() ) {
				$this->logger->log( 'Parsed XML', 'success' );

				return $result;
			}
		} elseif ( extension_loaded( 'xml' ) ) {
			$this->logger->log( 'Using Themeisle_OB_WXR_Parser_XML...', 'progress' );
			$parser = new Themeisle_OB_WXR_Parser_XML;
			$result = $parser->parse( $file );
			// If XMLParser succeeds or this is an invalid WXR file then return the results
			if ( ! is_wp_error( $result ) || 'XML_parse_error' != $result->get_error_code() ) {
				$this->logger->log( 'Parsed XML', 'success' );

				return $result;
			}
		}

		if ( is_wp_error( $result ) ) {
			$this->logger->log( 'simplexml and xml not active.' );
			$this->logger->log( "Parse failed with message: {$result->get_error_message()}" );
		}

		return $result;
	}
}

/**
 * WXR Parser that makes use of the SimpleXML PHP extension.
 */
class Themeisle_OB_WXR_Parser_SimpleXML {
	/**
	 * @param string $file file path.
	 *
	 * @return array | WP_Error
	 */
	public function parse( $file ) {
		global $wp_filesystem;
		WP_Filesystem();
		$authors         = $posts = $categories = $tags = $terms = array();
		$internal_errors = libxml_use_internal_errors( true );
		$dom             = new DOMDocument;
		$old_value       = null;
		if ( function_exists( 'libxml_disable_entity_loader' ) ) {
			$old_value = libxml_disable_entity_loader( true );
		}
		$success = $dom->loadXML( $wp_filesystem->get_contents( $file ) );
		if ( ! is_null( $old_value ) ) {
			libxml_disable_entity_loader( $old_value );
		}
		if ( ! $success || isset( $dom->doctype ) ) {
			return new WP_Error( 'SimpleXML_parse_error', 'There was an error when reading this WXR file', libxml_get_errors() );
		}
		$xml = simplexml_import_dom( $dom );
		unset( $dom );
		// halt if loading produces an error
		if ( ! $xml ) {
			return new WP_Error( 'SimpleXML_parse_error', 'There was an error when reading this WXR file', libxml_get_errors() );
		}
		$wxr_version = $xml->xpath( '/rss/channel/wp:wxr_version' );
		if ( ! $wxr_version ) {
			return new WP_Error( 'WXR_parse_error', 'This does not appear to be a WXR file, missing/invalid WXR version number' );
		}
		$wxr_version = (string) trim( $wxr_version[0] );
		// confirm that we are dealing with the correct file format
		if ( ! preg_match( '/^\d+\.\d+$/', $wxr_version ) ) {
			return new WP_Error( 'WXR_parse_error', 'This does not appear to be a WXR file, missing/invalid WXR version number' );
		}
		$base_url      = $xml->xpath( '/rss/channel/wp:base_site_url' );
		$base_url      = (string) trim( $base_url[0] );
		$base_blog_url = $xml->xpath( '/rss/channel/wp:base_blog_url' );
		$base_blog_url = (string) trim( $base_blog_url[0] );
		$namespaces    = $xml->getDocNamespaces();
		if ( ! isset( $namespaces['wp'] ) ) {
			$namespaces['wp'] = 'http://wordpress.org/export/1.1/';
		}
		if ( ! isset( $namespaces['excerpt'] ) ) {
			$namespaces['excerpt'] = 'http://wordpress.org/export/1.1/excerpt/';
		}
		// grab authors
		foreach ( $xml->xpath( '/rss/channel/wp:author' ) as $author_arr ) {
			$a                 = $author_arr->children( $namespaces['wp'] );
			$login             = (string) $a->author_login;
			$authors[ $login ] = array(
				'author_id'           => (int) $a->author_id,
				'author_login'        => $login,
				'author_email'        => (string) $a->author_email,
				'author_display_name' => (string) $a->author_display_name,
				'author_first_name'   => (string) $a->author_first_name,
				'author_last_name'    => (string) $a->author_last_name,
			);
		}
		// grab cats, tags and terms
		foreach ( $xml->xpath( '/rss/channel/wp:category' ) as $term_arr ) {
			$t        = $term_arr->children( $namespaces['wp'] );
			$category = array(
				'term_id'              => (int) $t->term_id,
				'category_nicename'    => (string) $t->category_nicename,
				'category_parent'      => (string) $t->category_parent,
				'cat_name'             => (string) $t->cat_name,
				'category_description' => (string) $t->category_description,
			);
			foreach ( $t->termmeta as $meta ) {
				$category['termmeta'][] = array(
					'key'   => (string) $meta->meta_key,
					'value' => (string) $meta->meta_value,
				);
			}
			$categories[] = $category;
		}
		foreach ( $xml->xpath( '/rss/channel/wp:tag' ) as $term_arr ) {
			$t   = $term_arr->children( $namespaces['wp'] );
			$tag = array(
				'term_id'         => (int) $t->term_id,
				'tag_slug'        => (string) $t->tag_slug,
				'tag_name'        => (string) $t->tag_name,
				'tag_description' => (string) $t->tag_description,
			);
			foreach ( $t->termmeta as $meta ) {
				$tag['termmeta'][] = array(
					'key'   => (string) $meta->meta_key,
					'value' => (string) $meta->meta_value,
				);
			}
			$tags[] = $tag;
		}
		foreach ( $xml->xpath( '/rss/channel/wp:term' ) as $term_arr ) {
			$t    = $term_arr->children( $namespaces['wp'] );
			$term = array(
				'term_id'          => (int) $t->term_id,
				'term_taxonomy'    => (string) $t->term_taxonomy,
				'slug'             => (string) $t->term_slug,
				'term_parent'      => (string) $t->term_parent,
				'term_name'        => (string) $t->term_name,
				'term_description' => (string) $t->term_description,
			);
			foreach ( $t->termmeta as $meta ) {
				$term['termmeta'][] = array(
					'key'   => (string) $meta->meta_key,
					'value' => (string) $meta->meta_value,
				);
			}
			$terms[] = $term;
		}
		// grab posts
		foreach ( $xml->channel->item as $item ) {
			$post                   = array(
				'post_title' => (string) $item->title,
				'guid'       => (string) $item->guid,
			);
			$dc                     = $item->children( 'http://purl.org/dc/elements/1.1/' );
			$post['post_author']    = (string) $dc->creator;
			$content                = $item->children( 'http://purl.org/rss/1.0/modules/content/' );
			$excerpt                = $item->children( $namespaces['excerpt'] );
			$post['post_content']   = (string) $content->encoded;
			$post['post_excerpt']   = (string) $excerpt->encoded;
			$wp                     = $item->children( $namespaces['wp'] );
			$post['post_id']        = (int) $wp->post_id;
			$post['post_date']      = (string) $wp->post_date;
			$post['post_date_gmt']  = (string) $wp->post_date_gmt;
			$post['comment_status'] = (string) $wp->comment_status;
			$post['ping_status']    = (string) $wp->ping_status;
			$post['post_name']      = (string) $wp->post_name;
			$post['status']         = (string) $wp->status;
			$post['post_parent']    = (int) $wp->post_parent;
			$post['menu_order']     = (int) $wp->menu_order;
			$post['post_type']      = (string) $wp->post_type;
			$post['post_password']  = (string) $wp->post_password;
			$post['is_sticky']      = (int) $wp->is_sticky;
			if ( isset( $wp->attachment_url ) ) {
				$post['attachment_url'] = (string) $wp->attachment_url;
			}
			foreach ( $item->category as $c ) {
				$att = $c->attributes();
				if ( isset( $att['nicename'] ) ) {
					$post['terms'][] = array(
						'name'   => (string) $c,
						'slug'   => (string) $att['nicename'],
						'domain' => (string) $att['domain'],
					);
				}
			}
			foreach ( $wp->postmeta as $meta ) {
				$post['postmeta'][] = array(
					'key'   => (string) $meta->meta_key,
					'value' => (string) $meta->meta_value,
				);
			}
			foreach ( $wp->comment as $comment ) {
				$meta = array();
				if ( isset( $comment->commentmeta ) ) {
					foreach ( $comment->commentmeta as $m ) {
						$meta[] = array(
							'key'   => (string) $m->meta_key,
							'value' => (string) $m->meta_value,
						);
					}
				}
				$post['comments'][] = array(
					'comment_id'           => (int) $comment->comment_id,
					'comment_author'       => (string) $comment->comment_author,
					'comment_author_email' => (string) $comment->comment_author_email,
					'comment_author_IP'    => (string) $comment->comment_author_IP,
					'comment_author_url'   => (string) $comment->comment_author_url,
					'comment_date'         => (string) $comment->comment_date,
					'comment_date_gmt'     => (string) $comment->comment_date_gmt,
					'comment_content'      => (string) $comment->comment_content,
					'comment_approved'     => (string) $comment->comment_approved,
					'comment_type'         => (string) $comment->comment_type,
					'comment_parent'       => (string) $comment->comment_parent,
					'comment_user_id'      => (int) $comment->comment_user_id,
					'commentmeta'          => $meta,
				);
			}
			$posts[] = $post;
		}

		return array(
			'authors'       => $authors,
			'posts'         => $posts,
			'categories'    => $categories,
			'tags'          => $tags,
			'terms'         => $terms,
			'base_url'      => $base_url,
			'base_blog_url' => $base_blog_url,
			'version'       => $wxr_version,
		);
	}
}

/**
 * WXR Parser that makes use of the XML Parser PHP extension.
 */
class Themeisle_OB_WXR_Parser_XML {
	/**
	 * XML Tags.
	 *
	 * @var array
	 */
	public $wp_tags     = array(
		'wp:post_id',
		'wp:post_date',
		'wp:post_date_gmt',
		'wp:comment_status',
		'wp:ping_status',
		'wp:attachment_url',
		'wp:status',
		'wp:post_name',
		'wp:post_parent',
		'wp:menu_order',
		'wp:post_type',
		'wp:post_password',
		'wp:is_sticky',
		'wp:term_id',
		'wp:category_nicename',
		'wp:category_parent',
		'wp:cat_name',
		'wp:category_description',
		'wp:tag_slug',
		'wp:tag_name',
		'wp:tag_description',
		'wp:term_taxonomy',
		'wp:term_parent',
		'wp:term_name',
		'wp:term_description',
		'wp:author_id',
		'wp:author_login',
		'wp:author_email',
		'wp:author_display_name',
		'wp:author_first_name',
		'wp:author_last_name',
	);
	public $wp_sub_tags = array(
		'wp:comment_id',
		'wp:comment_author',
		'wp:comment_author_email',
		'wp:comment_author_url',
		'wp:comment_author_IP',
		'wp:comment_date',
		'wp:comment_date_gmt',
		'wp:comment_content',
		'wp:comment_approved',
		'wp:comment_type',
		'wp:comment_parent',
		'wp:comment_user_id',
	);

	/**
	 * Parse the XML
	 *
	 * @param $file
	 *
	 * @return array|WP_Error
	 */
	public function parse( $file ) {
		global $wp_filesystem;
		WP_Filesystem();
		$this->wxr_version = $this->in_post = $this->cdata = $this->data = $this->sub_data = $this->in_tag = $this->in_sub_tag = false;
		$this->authors     = $this->posts = $this->term = $this->category = $this->tag = array();
		$xml               = xml_parser_create( 'UTF-8' );
		xml_parser_set_option( $xml, XML_OPTION_SKIP_WHITE, 1 );
		xml_parser_set_option( $xml, XML_OPTION_CASE_FOLDING, 0 );
		xml_set_object( $xml, $this );
		xml_set_character_data_handler( $xml, 'cdata' );
		xml_set_element_handler( $xml, 'tag_open', 'tag_close' );
		if ( ! xml_parse( $xml, $wp_filesystem->get_contents( $file ), true ) ) {
			$current_line   = xml_get_current_line_number( $xml );
			$current_column = xml_get_current_column_number( $xml );
			$error_code     = xml_get_error_code( $xml );
			$error_string   = xml_error_string( $error_code );

			return new WP_Error(
				'XML_parse_error',
				'There was an error when reading this WXR file',
				array(
					$current_line,
					$current_column,
					$error_string,
				)
			);
		}
		xml_parser_free( $xml );
		if ( ! preg_match( '/^\d+\.\d+$/', $this->wxr_version ) ) {
			return new WP_Error( 'WXR_parse_error', 'This does not appear to be a WXR file, missing/invalid WXR version number' );
		}

		return array(
			'authors'       => $this->authors,
			'posts'         => $this->posts,
			'categories'    => $this->category,
			'tags'          => $this->tag,
			'terms'         => $this->term,
			'base_url'      => $this->base_url,
			'base_blog_url' => $this->base_blog_url,
			'version'       => $this->wxr_version,
		);
	}

	public function tag_open( $parse, $tag, $attr ) {
		if ( in_array( $tag, $this->wp_tags ) ) {
			$this->in_tag = substr( $tag, 3 );

			return;
		}
		if ( in_array( $tag, $this->wp_sub_tags ) ) {
			$this->in_sub_tag = substr( $tag, 3 );

			return;
		}
		switch ( $tag ) {
			case 'category':
				if ( isset( $attr['domain'], $attr['nicename'] ) ) {
					$this->sub_data['domain'] = $attr['domain'];
					$this->sub_data['slug']   = $attr['nicename'];
				}
				break;
			case 'item':
				$this->in_post = true;
				break;
			case 'title':
				if ( $this->in_post ) {
					$this->in_tag = 'post_title';
				}
				break;
			case 'guid':
				$this->in_tag = 'guid';
				break;
			case 'dc:creator':
				$this->in_tag = 'post_author';
				break;
			case 'content:encoded':
				$this->in_tag = 'post_content';
				break;
			case 'excerpt:encoded':
				$this->in_tag = 'post_excerpt';
				break;
			case 'wp:term_slug':
				$this->in_tag = 'slug';
				break;
			case 'wp:meta_key':
				$this->in_sub_tag = 'key';
				break;
			case 'wp:meta_value':
				$this->in_sub_tag = 'value';
				break;
		}
	}

	public function cdata( $parser, $cdata ) {
		if ( ! trim( $cdata ) ) {
			return;
		}
		if ( false !== $this->in_tag || false !== $this->in_sub_tag ) {
			$this->cdata .= $cdata;
		} else {
			$this->cdata .= trim( $cdata );
		}
	}

	public function tag_close( $parser, $tag ) {
		switch ( $tag ) {
			case 'wp:comment':
				unset( $this->sub_data['key'], $this->sub_data['value'] ); // remove meta sub_data
				if ( ! empty( $this->sub_data ) ) {
					$this->data['comments'][] = $this->sub_data;
				}
				$this->sub_data = false;
				break;
			case 'wp:commentmeta':
				$this->sub_data['commentmeta'][] = array(
					'key'   => $this->sub_data['key'],
					'value' => $this->sub_data['value'],
				);
				break;
			case 'category':
				if ( ! empty( $this->sub_data ) ) {
					$this->sub_data['name'] = $this->cdata;
					$this->data['terms'][]  = $this->sub_data;
				}
				$this->sub_data = false;
				break;
			case 'wp:postmeta':
				if ( ! empty( $this->sub_data ) ) {
					$this->data['postmeta'][] = $this->sub_data;
				}
				$this->sub_data = false;
				break;
			case 'item':
				$this->posts[] = $this->data;
				$this->data    = false;
				break;
			case 'wp:category':
			case 'wp:tag':
			case 'wp:term':
				$n = substr( $tag, 3 );
				array_push( $this->$n, $this->data );
				$this->data = false;
				break;
			case 'wp:author':
				if ( ! empty( $this->data['author_login'] ) ) {
					$this->authors[ $this->data['author_login'] ] = $this->data;
				}
				$this->data = false;
				break;
			case 'wp:base_site_url':
				$this->base_url = $this->cdata;
				break;
			case 'wp:base_blog_url':
				$this->base_blog_url = $this->cdata;
				break;
			case 'wp:wxr_version':
				$this->wxr_version = $this->cdata;
				break;
			default:
				if ( $this->in_sub_tag ) {
					$this->sub_data[ $this->in_sub_tag ] = ! empty( $this->cdata ) ? $this->cdata : '';
					$this->in_sub_tag                    = false;
				} elseif ( $this->in_tag ) {
					$this->data[ $this->in_tag ] = ! empty( $this->cdata ) ? $this->cdata : '';
					$this->in_tag                = false;
				}
		}
		$this->cdata = false;
	}
}

/**
 * Class Themeisle_OB_Beaver_ParserXML
 *
 * Brought in from Beaver Builder Lite.
 *
 * Beaver XML Parser
 */
class Themeisle_OB_Beaver_ParserXML extends Themeisle_OB_WXR_Parser_XML {

	function tag_close( $parser, $tag ) {
		switch ( $tag ) {
			case 'wp:comment':
				unset( $this->sub_data['key'], $this->sub_data['value'] ); // remove meta sub_data
				if ( ! empty( $this->sub_data ) ) {
					$this->data['comments'][] = $this->sub_data;
				}
				$this->sub_data = false;
				break;
			case 'wp:commentmeta':
				$this->sub_data['commentmeta'][] = array(
					'key'   => $this->sub_data['key'],
					'value' => $this->sub_data['value'],
				);
				break;
			case 'category':
				if ( ! empty( $this->sub_data ) ) {
					$this->sub_data['name'] = $this->cdata;
					$this->data['terms'][]  = $this->sub_data;
				}
				$this->sub_data = false;
				break;
			case 'wp:postmeta':
				if ( ! empty( $this->sub_data ) ) {
					if ( stristr( $this->sub_data['key'], '_fl_builder_' ) ) {
						$this->sub_data['value'] = Themeisle_OB_Beaver_Data_Fix::run( serialize( $this->sub_data['value'] ) );
					}
					$this->data['postmeta'][] = $this->sub_data;
				}
				$this->sub_data = false;
				break;
			case 'item':
				$this->posts[] = $this->data;
				$this->data    = false;
				break;
			case 'wp:category':
			case 'wp:tag':
			case 'wp:term':
				$n = substr( $tag, 3 );
				array_push( $this->$n, $this->data );
				$this->data = false;
				break;
			case 'wp:author':
				if ( ! empty( $this->data['author_login'] ) ) {
					$this->authors[ $this->data['author_login'] ] = $this->data;
				}
				$this->data = false;
				break;
			case 'wp:base_site_url':
				$this->base_url = $this->cdata;
				break;
			case 'wp:base_blog_url':
				$this->base_blog_url = $this->cdata;
				break;
			case 'wp:wxr_version':
				$this->wxr_version = $this->cdata;
				break;
			default:
				if ( $this->in_sub_tag ) {
					$this->sub_data[ $this->in_sub_tag ] = ! empty( $this->cdata ) ? $this->cdata : '';
					$this->in_sub_tag                    = false;
				} elseif ( $this->in_tag ) {
					$this->data[ $this->in_tag ] = ! empty( $this->cdata ) ? $this->cdata : '';
					$this->in_tag                = false;
				}
		}
		$this->cdata = false;
	}
}

/**
 * Brought in from Beaver Builder Lite
 *
 * Portions borrowed from https://github.com/Blogestudio/Fix-Serialization/blob/master/fix-serialization.php
 *
 * Attempts to fix broken serialized data.
 *
 * @since 1.8
 */
final class Themeisle_OB_Beaver_Data_Fix {

	/**
	 * @since 1.8
	 * @return string
	 */
	static public function run( $data ) {
		// return if empty
		if ( empty( $data ) ) {
			return $data;
		}

		$data = maybe_unserialize( $data );

		// return if maybe_unserialize() returns an object or array, this is good.
		if ( is_object( $data ) || is_array( $data ) ) {
			return $data;
		}

		return preg_replace_callback( '!s:(\d+):([\\\\]?"[\\\\]?"|[\\\\]?"((.*?)[^\\\\])[\\\\]?");!', 'Themeisle_OB_Beaver_Data_Fix::regex_callback', $data );
	}

	/**
	 * @since 1.8
	 * @return string
	 */
	static public function regex_callback( $matches ) {
		if ( ! isset( $matches[3] ) ) {
			return $matches[0];
		}

		return 's:' . strlen( self::unescape_mysql( $matches[3] ) ) . ':"' . self::unescape_quotes( $matches[3] ) . '";';
	}

	/**
	 * Unescape to avoid dump-text issues.
	 *
	 * @since 1.8
	 * @access private
	 * @return string
	 */
	static private function unescape_mysql( $value ) {
		return str_replace(
			array( '\\\\', "\\0", "\\n", "\\r", '\Z', "\'", '\"' ),
			array( '\\', "\0", "\n", "\r", "\x1a", "'", '"' ),
			$value
		);
	}

	/**
	 * Fix strange behaviour if you have escaped quotes in your replacement.
	 *
	 * @since 1.8
	 * @access private
	 * @return string
	 */
	static private function unescape_quotes( $value ) {
		return str_replace( '\"', '"', $value );
	}
}
