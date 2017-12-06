<?php
/**
 * @package Basis
 */

if ( ! function_exists( 'Basis_HTML_Builder' ) ) :
/**
 * Defines the functionality for the HTML Builder.
 *
 * @since 1.0.
 */
class Basis_HTML_Builder {
	/**
	 * The one instance of Basis_HTML_Builder.
	 *
	 * @since 1.0.
	 *
	 * @var   Basis_HTML_Builder
	 */
	private static $instance;

	/**
	 * Holds the menu item information
	 *
	 * @since 1.0.
	 *
	 * @var   array    Contains a multidimensional array with menu item information.
	 */
	private $_product_menu_items = array();

	/**
	 * Holds the iterator for managing sections
	 *
	 * @since 1.0.
	 *
	 * @var   int    Current section number.
	 */
	private $_iterator = 0;

	/**
	 * A variable for tracking the current section being processed.
	 *
	 * @since 1.0.
	 *
	 * @var   int
	 */
	private $_current_section_number = 0;

	/**
	 * Instantiate or return the one Basis_HTML_Builder instance.
	 *
	 * @since  1.0.
	 *
	 * @return Basis_HTML_Builder
	 */
	public static function instance() {
		if ( is_null( self::$instance ) )
			self::$instance = new self();

		return self::$instance;
	}

	/**
	 * Initiate actions.
	 *
	 * @since  1.0.
	 *
	 * @return Basis_HTML_Builder
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 1 ); // Bias toward top of stack
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_print_styles-post.php', array( $this, 'admin_print_styles' ) );
		add_action( 'admin_print_styles-post-new.php', array( $this, 'admin_print_styles' ) );
		add_action( 'admin_footer', array( $this, 'print_templates' ) );
		add_action( 'admin_footer', array( $this, 'add_js_data' ) );
		add_action( 'tiny_mce_before_init', array( $this, 'tiny_mce_before_init' ), 15, 2 );
		add_action( 'after_wp_tiny_mce', array( $this, 'after_wp_tiny_mce' ) );

		// Combine the input into the post's content
		add_filter( 'wp_insert_post_data', array( $this, 'wp_insert_post_data' ), 30, 2 );

		// Filter the content displayed in templates
		global $wp_embed;
		add_filter( 'basis_the_builder_content', array( $wp_embed, 'autoembed' ), 8 );
		add_filter( 'basis_the_builder_content', 'wpautop' );

		// Setup config
		$this->_set_product_menu_items();
	}

	/**
	 * Set the menu items.
	 *
	 * @since  1.0.
	 *
	 * @return array    The menu items array.
	 */
	private function _set_product_menu_items() {
		// Setup the default menu items
		$menu_items = array(
			'banner'  => array(
				'id'          => 'banner',
				'label'       => __( 'Banner', 'basis' ),
				'description' => __( 'A full-width background image with custom text and an optional button.', 'basis' ),
			),
			'feature' => array(
				'id'          => 'feature',
				'label'       => __( 'Feature', 'basis' ),
				'description' => __( 'A featured image accompanied on the left or right side by a narrow column of text.', 'basis' ),
			),
			'profile' => array(
				'id'          => 'profile',
				'label'       => __( 'Profile', 'basis' ),
				'description' => __( 'Three sortable columns, each featuring an image, title and text.', 'basis' ),
			),
			'text'    => array(
				'id'          => 'text',
				'label'       => __( 'Text', 'basis' ),
				'description' => __( 'A blank canvas for standard content or HTML code.', 'basis' ),
			)
		);

		// Set the instance var
		$this->_product_menu_items = $menu_items;
		return $menu_items;
	}

	/**
	 * Add the meta box.
	 *
	 * @since  1.0.
	 *
	 * @return void
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'basis_builder_product',
			__( 'Page Builder', 'basis' ),
			array( $this, 'display_builder_product' ),
			'page',
			'normal',
			'high'
		);

		add_meta_box(
			'basis_builder_slideshow',
			__( 'Slideshow Builder', 'basis' ),
			array( $this, 'basis_builder_slideshow' ),
			'page',
			'normal',
			'high'
		);
	}

	/**
	 * Display the meta box.
	 *
	 * @since  1.0.
	 *
	 * @param  WP_Post    $post_local    The current post object.
	 * @return void
	 */
	public function display_builder_product( $post_local ) {
		wp_nonce_field( 'save', 'basis-builder-product-nonce' );

		// Get the current sections
		global $basis_sections, $basis_builder;
		$basis_sections = get_post_meta( $post_local->ID, '_basis-sections-product', true );
		$basis_sections = ( is_array( $basis_sections ) ) ? $basis_sections : array();
		$basis_builder  = 'product';

		// Load the boilerplate templates
		get_template_part( 'includes/html-builder/templates/menu-product' );
		get_template_part( 'includes/html-builder/templates/stage-header' );

		$section_ids = array();

		// Print the current sections
		foreach ( $basis_sections as $section ) {
			if ( isset( $section['section-type'] ) ) {
				$this->_load_section( $this->get_menu_item( $section['section-type'] ), $section, 'product' );
				$section_ids[] = $this->get_iterator();
				$this->increment_iterator();
			}
		}

		get_template_part( 'includes/html-builder/templates/stage-footer' );

		// Generate initial section order input
		$section_order = '';
		foreach ( $section_ids as $number ) {
			$section_order .= 'basis-section-' . $number . ',';
		}

		$section_order = substr( $section_order, 0, -1 );

		// Add the sort input
		echo '<input type="hidden" value="' . esc_attr( $section_order ) . '" name="basis-section-order-product" id="basis-section-order-product" />';
	}

	/**
	 * Display the meta box.
	 *
	 * @since  1.0.
	 *
	 * @param  WP_Post    $post_local    The current post object.
	 * @return void
	 */
	public function basis_builder_slideshow( $post_local ) {
		wp_nonce_field( 'save', 'basis-builder-slideshow-nonce' );

		// Get the current sections
		global $basis_sections, $basis_builder;
		$basis_sections = get_post_meta( $post_local->ID, '_basis-sections-slideshow', true );
		$basis_sections = ( is_array( $basis_sections ) ) ? $basis_sections : array();
		$basis_builder  = 'slideshow';

		// Load the boilerplate templates
		get_template_part( 'includes/html-builder/templates/menu-slideshow' );
		get_template_part( 'includes/html-builder/templates/stage-header' );

		$section_ids = array();

		// Print the current sections
		foreach ( $basis_sections as $section ) {
			if ( isset( $section['section-type'] ) ) {
				$this->_load_section( array( 'id' => 'slide', 'label' => __( 'Slide', 'basis' ) ), $section, 'slideshow' );
				$section_ids[] = $this->get_iterator();
				$this->increment_iterator();
			}
		}

		get_template_part( 'includes/html-builder/templates/stage-footer' );

		// Generate initial section order input
		$section_order = '';
		foreach ( $section_ids as $number ) {
			$section_order .= 'basis-section-' . $number . ',';
		}

		$section_order = substr( $section_order, 0, -1 );

		// Add the sort input
		echo '<input type="hidden" value="' . esc_attr( $section_order ) . '" name="basis-section-order-slideshow" id="basis-section-order-slideshow" />';
	}

	/**
	 * Save the gallery IDs and order.
	 *
	 * @since  1.0.
	 *
	 * @param  int        $post_id    The ID of the current post.
	 * @param  WP_Post    $post       The post object for the current post.
	 * @return void
	 */
	public function save_post( $post_id, $post ) {
		// Don't do anything during autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Only check permissions for pages since it can only run on pages
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

		// Verify that the page template param is set
		if ( ! isset( $_POST['page_template'] ) ) {
			return;
		}

		// Run the product builder routine maybe
		if ( 'product.php' === $_POST['page_template'] ) {
			if ( isset( $_POST[ 'basis-builder-product-nonce' ] ) && wp_verify_nonce( $_POST[ 'basis-builder-product-nonce' ], 'save' ) ) {
				// Process and save data
				$sanitized_sections = $this->prepare_data( 'product' );
				update_post_meta( $post_id, '_basis-sections-product', $sanitized_sections );
			}
		} elseif ( 'slideshow.php' === $_POST['page_template'] ) {
			if ( isset( $_POST[ 'basis-builder-slideshow-nonce' ] ) && wp_verify_nonce( $_POST[ 'basis-builder-slideshow-nonce' ], 'save' ) ) {
				// Process and save data
				$sanitized_sections = $this->prepare_data( 'slideshow' );
				update_post_meta( $post_id, '_basis-sections-slideshow', $sanitized_sections );
			}
		}

		// Save the value of the hide/show header variable
		if ( isset( $_POST[ 'basis-builder-product-nonce' ] ) && wp_verify_nonce( $_POST[ 'basis-builder-product-nonce' ], 'save' ) ) {
			if ( isset( $_POST['page_template'] ) && in_array( $_POST['page_template'], array( 'product.php', 'slideshow.php' ) ) && isset( $_POST['basis-hide-header'] ) ) {
				$value       = $_POST['basis-hide-header'];
				$clean_value = ( in_array( $value, array( 0, 1 ) ) ) ? (int) $value : 0;

				// Only save it if necessary
				if ( 1 === $clean_value ) {
					update_post_meta( $post_id, '_basis-hide-header', 1 );
				} else {
					delete_post_meta( $post_id, '_basis-hide-header' );
				}
			} else {
				delete_post_meta( $post_id, '_basis-hide-header' );
			}
		}
	}

	/**
	 * Validate and sanitize the builder section data.
	 *
	 * @since  1.0.
	 *
	 * @param  string    $builder    The builder type.
	 * @return array                 Array of cleaned section data.
	 */
	public function prepare_data( $builder ) {
		$sanitized_sections = array();

		if ( isset( $_POST['basis-section'][ $builder ] ) ) {
			// Get section order
			$order = array();
			if ( isset( $_POST['basis-section-order-' . $builder] ) ) {
				$order = $this->process_order( $_POST['basis-section-order-' . $builder] );
			}

			// Process and save data
			$sanitized_sections = $this->process_section_data( $_POST['basis-section'][ $builder ], $order );
		}

		return $sanitized_sections;
	}

	/**
	 * Interpret the order input into meaningful order data.
	 *
	 * @since  1.0.
	 *
	 * @param  string    $input    The order string.
	 * @return array               Array of order values.
	 */
	public function process_order( $input ) {
		$input = str_replace( 'basis-section-', '', $input );
		return explode( ',', $input );
	}

	/**
	 * Process data for a single section.
	 *
	 * @since  1.0.
	 *
	 * @param  array    $data     The data for the section.
	 * @param  array    $order    The order for the section.
	 * @return array              Sanitized data.
	 */
	public function process_section_data( $data, $order ) {
		$sanitized_sections = array();

		foreach( $order as $section_number ) {
			if ( isset( $data[ $section_number ] ) && isset( $data[ $section_number ]['section-type'] ) ) {
				$section              = $data[ $section_number ];
				$section_type         = $section[ 'section-type' ];
				$sanitized_sections[] = call_user_func_array( array( $this, 'process_' . $section_type ), array( 'data' => $section ) );
			}
		}

		return $sanitized_sections;
	}

	/**
	 * Process the banner section data.
	 *
	 * @since  1.0.
	 *
	 * @param  array    $data    The section's data.
	 * @return array             Sanitized data.
	 */
	public function process_banner( $data ) {
		$clean_data = array(
			'section-type' => 'banner'
		);

		if ( isset( $data['image-id'] ) ) {
			$clean_data['image-id'] = absint( $data['image-id'] );
		}

		if ( isset( $data['button-url'] ) ) {
			$clean_data['button-url'] = esc_url_raw( $data['button-url'] );
		}

		if ( isset( $data['button-text'] ) ) {
			$clean_data['button-text'] = sanitize_text_field( $data['button-text'] );
		}

		$clean_data['background-image'] = ( isset( $data['background-image'] ) && 1 === (int) $data['background-image'] ) ? 1 : 0;
		$clean_data['darken-image']     = ( isset( $data['darken-image'] ) && 1 === (int) $data['darken-image'] ) ? 1 : 0;

		if ( isset( $data['title'] ) ) {
			$clean_data['title'] = apply_filters( 'title_save_pre', $data['title'] );
		}

		if ( isset( $data['content'] ) ) {
			$clean_data['content'] = wp_filter_post_kses( $data['content'] );
		}

		if ( isset( $data['state'] ) ) {
			$clean_data['state'] = sanitize_key( $data['state'] );
		}

		return $clean_data;
	}

	/**
	 * Process the slide section data.
	 *
	 * Slides are identical to banner sections; however, the type is different and there is no background image. Instead
	 * of rewriting the routine, the banner routine is used and the two differences are handled in this function.
	 *
	 * @since  1.0.
	 *
	 * @param  array    $data    The section's data.
	 * @return array             Sanitized data.
	 */
	public function process_slide( $data ) {
		$clean_data = $this->process_banner( $data );

		// There is no "background-image" option for slides
		unset( $clean_data['background-image'] );

		// Reset the section type value
		$clean_data['section-type'] = 'slide';

		return $clean_data;
	}

	/**
	 * Process the feature section data.
	 *
	 * @since  1.0.
	 *
	 * @param  array    $data    The section's data.
	 * @return array             Sanitized data.
	 */
	public function process_feature( $data ) {
		$clean_data = array(
			'section-type' => 'feature'
		);

		if ( isset( $data['image-link'] ) ) {
			$clean_data['image-link'] = esc_url_raw( $data['image-link'] );
		}

		if ( isset( $data['image-id'] ) ) {
			$clean_data['image-id'] = absint( $data['image-id'] );
		}

		if ( isset( $data['title-link'] ) ) {
			$clean_data['title-link'] = esc_url_raw( $data['title-link'] );
		}

		if ( isset( $data['title'] ) ) {
			$clean_data['title'] = apply_filters( 'title_save_pre', $data['title'] );
		}

		if ( isset( $data['content'] ) ) {
			$clean_data['content'] = wp_filter_post_kses( $data['content'] );
		}

		if ( isset( $data['order'] ) ) {
			$clean_data['order'] = explode( ',', $data['order'] );
			array_map( 'sanitize_key', $clean_data['order'] );
		}

		if ( isset( $data['state'] ) ) {
			$clean_data['state'] = sanitize_key( $data['state'] );
		}

		return $clean_data;
	}

	/**
	 * Process the profile section data.
	 *
	 * @since  1.0.
	 *
	 * @param  array    $data    The section's data.
	 * @return array             Sanitized data.
	 */
	public function process_profile( $data ) {
		$clean_data = array(
			'section-type' => 'profile'
		);

		// Gather the order of the columns in an array
		if ( isset( $data['order'] ) && ! empty( $data['order'] ) ) {
			$order = str_replace(
				array(
					'basis-profile-',
					'-column'
				),
				array(
					'',
					''
				),
				$data['order']
			);

			// Turn into array and remove empty values
			$order = array_filter( explode( ',', $order ) );
		} else {
			$order = array(
				'left',
				'middle',
				'right'
			);
		}

		// Loop through the order array. Save each value from the corresponding column.
		$i = 0;
		foreach ( $order as $column ) {
			if ( 0 === $i ) {
				$which = 'left';
			} elseif ( 1 === $i ) {
				$which = 'middle';
			} else {
				$which = 'right';
			}

			if ( isset( $data[ $column ]['link'] ) ) {
				$clean_data[ $which ]['link'] = esc_url_raw( $data[ $column ]['link'] );
			}

			if ( isset( $data[ $column ]['image-id'] ) ) {
				$clean_data[ $which ]['image-id'] = absint( $data[ $column ]['image-id'] );
			}

			if ( isset( $data[ $column ]['title'] ) ) {
				$clean_data[ $which ]['title'] = apply_filters( 'title_save_pre', $data[ $column ]['title'] );
			}

			if ( isset( $data[ $column ]['content'] ) ) {
				$clean_data[ $which ]['content'] = wp_filter_post_kses( $data[ $column ]['content'] );
			}

			$i++;
		}

		if ( isset( $data['state'] ) ) {
			$clean_data['state'] = sanitize_key( $data['state'] );
		}

		return $clean_data;
	}

	/**
	 * Process the text section data.
	 *
	 * @since  1.0.
	 *
	 * @param  array    $data    The section's data.
	 * @return array             Sanitized data.
	 */
	public function process_text( $data ) {
		$clean_data = array(
			'section-type' => 'text'
		);

		if ( isset( $data['title'] ) ) {
			$clean_data['title'] = apply_filters( 'title_save_pre', $data['title'] );
		}

		if ( isset( $data['content'] ) ) {
			$clean_data['content'] = wp_filter_post_kses( $data['content'] );
		}

		if ( isset( $data['state'] ) ) {
			$clean_data['state'] = sanitize_key( $data['state'] );
		}

		return $clean_data;
	}

	/**
	 * On post save, use a theme template to generate content from metadata.
	 *
	 * @since  1.0.
	 *
	 * @param  array    $data       The processed post data.
	 * @param  array    $postarr    The raw post data.
	 * @return array                Modified post data.
	 */
	public function wp_insert_post_data( $data, $postarr ) {
		$product_submit   = ( isset( $_POST[ 'basis-builder-product-nonce' ] ) && wp_verify_nonce( $_POST[ 'basis-builder-product-nonce' ], 'save' ) );
		$slideshow_submit = ( isset( $_POST[ 'basis-builder-slideshow-nonce' ] ) && wp_verify_nonce( $_POST[ 'basis-builder-slideshow-nonce' ], 'save' ) );

		if ( ! $product_submit && ! $slideshow_submit ) {
			return $data;
		}

		// Don't do anything during autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $data;
		}

		// Only check permissions for pages since it can only run on pages
		if ( ! current_user_can( 'edit_page', get_the_ID() ) ) {
			return $data;
		}

		// Verify that the page template param is set
		if ( ! isset( $_POST['page_template'] ) || ! in_array( $_POST['page_template'], array( 'product.php', 'slideshow.php' ) ) ) {
			return $data;
		}

		// Run the product builder routine maybe
		$sanitized_sections = array();

		if ( 'product.php' === $_POST['page_template'] ) {
			if ( $product_submit ) {
				$sanitized_sections = $this->prepare_data( 'product' );
			}
		} elseif ( 'slideshow.php' == $_POST['page_template'] ) {
			if ( $slideshow_submit ) {
				$sanitized_sections = $this->prepare_data( 'slideshow' );
			}
		}

		// The data has been deleted and can be removed
		if ( empty( $sanitized_sections ) ) {
			$data['post_content'] = '';
			return $data;
		}

		// Remove editor image constraints while rendering section data.
		add_filter( 'editor_max_image_size', array( &$this, 'remove_image_constraints' ) );

		// Start the output buffer to collect the contents of the templates
		ob_start();

		global $basis_sanitized_sections;
		$basis_sanitized_sections = $sanitized_sections;

		// Verify that the section counter is reset
		$this->_current_section_number = 0;

		// For each sections, render it using the template
		foreach ( $sanitized_sections as $section ) {
			global $basis_section_data;
			$basis_section_data = $section;

			// Get the template for the section
			get_template_part( '_section', $section['section-type'] );

			// Note the change in section number
			$this->_current_section_number++;

			// Cleanup the global
			unset( $GLOBALS['basis_section_data'] );
		}

		// Cleanup the global
		unset( $GLOBALS['basis_sanitized_sections'] );

		// Reset the counter
		$this->_current_section_number = 0;

		// Get the rendered templates from the output buffer
		$post_content = ob_get_clean();

		// Allow constraints again after builder data processing is complete.
		remove_filter( 'editor_max_image_size', array( &$this, 'remove_image_constraints' ) );

		// Sanitize and set the content
		$data['post_content'] = sanitize_post_field( 'post_content', $post_content, get_the_ID(), 'db' );

		return $data;
	}

	/**
	 * Allows image size to be saved regardless of the content width variable.
	 *
	 * @since  1.0.
	 *
	 * @param  array    $dimensions    The default dimensions.
	 * @return array                   The modified dimensions.
	 */
	public function remove_image_constraints( $dimensions ) {
		return array( 9999, 9999 );
	}

	/**
	 * Get the next section's data.
	 *
	 * @since  1.0.
	 *
	 * @return array    The next section's data.
	 */
	public function get_next_section_data() {
		global $basis_sanitized_sections;

		// Get the next section number
		$section_to_get = $this->_current_section_number + 1;

		// If the section does not exist, the current section is the last section
		if ( isset( $basis_sanitized_sections[ $section_to_get ] ) ) {
			return $basis_sanitized_sections[ $section_to_get ];
		} else {
			return array();
		}
	}

	/**
	 * Get the previous section's data.
	 *
	 * @since  1.0.
	 *
	 * @return array    The previous section's data.
	 */
	public function get_prev_section_data() {
		global $basis_sanitized_sections;

		// Get the next section number
		$section_to_get = $this->_current_section_number - 1;

		// If the section does not exist, the current section is the last section
		if ( isset( $basis_sanitized_sections[ $section_to_get ] ) ) {
			return $basis_sanitized_sections[ $section_to_get ];
		} else {
			return array();
		}
	}

	/**
	 * Prepare the classes need for a section.
	 *
	 * Includes the name of the current section type, the next section type and the previous section type. It will also
	 * denote if a section is the first or last section.
	 *
	 * @since  1.0.
	 *
	 * @return string
	 */
	public function section_classes() {
		global $basis_sanitized_sections;

		// Get the current section type
		$current = ( isset( $basis_sanitized_sections[ $this->_current_section_number ]['section-type'] ) ) ? $basis_sanitized_sections[ $this->_current_section_number ]['section-type'] : '';

		// Get the next section's type
		$next_data = $this->get_next_section_data();
		$next = ( ! empty( $next_data ) && isset( $next_data['section-type'] ) ) ? 'next-' . $next_data['section-type'] : 'last';

		// Get the previous section's type
		$prev_data = $this->get_prev_section_data();
		$prev = ( ! empty( $prev_data ) && isset( $prev_data['section-type'] ) ) ? 'prev-' . $prev_data['section-type'] : 'first';

		// Return the values as a single string
		return $prev . ' ' . $current . ' ' . $next;
	}

	/**
	 * Enqueue the JS and CSS for the admin.
	 *
	 * @param  string    $hook_suffix    The suffix for the screen.
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook_suffix ) {
		global $wp_version;

		// Only load resources if they are needed on the current page
		if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ) ) || 'page' !== get_post_type() ) {
			return;
		}

		// Enqueue the CSS
		wp_enqueue_style(
			'basis-html-builder',
			get_template_directory_uri() . '/includes/html-builder/css/html-builder.css',
			array(),
			BASIS_VERSION
		);

		// MP6 support
		if ( basis_is_wpcom() || ( defined( 'MP6' ) && MP6 ) ) {
			wp_enqueue_style(
				'basis-html-builder-mp6',
				get_template_directory_uri() . '/includes/html-builder/css/html-builder-mp6.css',
				array( 'basis-html-builder' ),
				BASIS_VERSION
			);
		} else if ( true === version_compare( $wp_version, '3.7.9', '>' ) ) {
			wp_enqueue_style(
				'basis-html-builder-wp38plus',
				get_template_directory_uri() . '/includes/html-builder/css/html-builder-wp38plus.css',
				array( 'basis-html-builder' ),
				BASIS_VERSION
			);
		}

		wp_enqueue_style( 'wp-color-picker' );

		// Dependencies regardless of min/full scripts
		$dependencies = array(
			'wplink',
			'utils',
			'wp-color-picker',
			'jquery-effects-core',
			'jquery-ui-sortable',
			'backbone',
		);

		// Only load full scripts for WordPress.com and those with SCRIPT_DEBUG set to true
		if ( basis_is_wpcom() || ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ) {
			wp_register_script(
				'basis-html-builder/js/app.js',
				get_template_directory_uri() . '/includes/html-builder/js/app.js',
				array(),
				BASIS_VERSION,
				true
			);

			wp_register_script(
				'basis-html-builder/js/models/section.js',
				get_template_directory_uri() . '/includes/html-builder/js/models/section.js',
				array(),
				BASIS_VERSION,
				true
			);

			wp_register_script(
				'basis-html-builder/js/collections/sections.js',
				get_template_directory_uri() . '/includes/html-builder/js/collections/sections.js',
				array(),
				BASIS_VERSION,
				true
			);

			wp_register_script(
				'basis-html-builder/js/views/menu-product.js',
				get_template_directory_uri() . '/includes/html-builder/js/views/menu-product.js',
				array(),
				BASIS_VERSION,
				true
			);

			wp_register_script(
				'basis-html-builder/js/views/menu-slideshow.js',
				get_template_directory_uri() . '/includes/html-builder/js/views/menu-slideshow.js',
				array(),
				BASIS_VERSION,
				true
			);

			wp_register_script(
				'basis-html-builder/js/views/section.js',
				get_template_directory_uri() . '/includes/html-builder/js/views/section.js',
				array(),
				BASIS_VERSION,
				true
			);

			wp_enqueue_script(
				'basis-html-builder',
				get_template_directory_uri() . '/includes/html-builder/js/html-builder.js',
				array_merge(
					$dependencies,
					array(
						'basis-html-builder/js/app.js',
						'basis-html-builder/js/models/section.js',
						'basis-html-builder/js/collections/sections.js',
						'basis-html-builder/js/views/menu-product.js',
						'basis-html-builder/js/views/menu-slideshow.js',
						'basis-html-builder/js/views/section.js',
					)
				),
				BASIS_VERSION,
				true
			);
		} else {
			wp_enqueue_script(
				'basis-html-builder',
				get_template_directory_uri() . '/includes/html-builder/js/html-builder.min.js',
				$dependencies,
				BASIS_VERSION,
				true
			);
		}
	}

	/**
	 * Hide the builder metabox and main editor if necessary.
	 *
	 * @since  1.0.
	 *
	 * @return void
	 */
	public function admin_print_styles() {
		// Do not complete the function if the product template is in use (i.e., the builder needs to be shown)
		if ( 'page' !== get_post_type() ) {
			return;
		}

		$template = get_page_template_slug();
	?>
		<style type="text/css">
		<?php if ( in_array( get_page_template_slug(), array( 'product.php', 'slideshow.php' ) ) ) : ?>
			#postdivrich,
			#postimagediv,
			.misc-pub-revisions {
				display: none;
			}
		<?php endif; ?>

		<?php if ( 'product.php' === $template ) : ?>
			#basis_builder_slideshow {
				display: none;
			}
			#basis_builder_product {
				display: block;
			}
		<?php elseif ( 'slideshow.php' === $template ) : ?>
			#basis_builder_product {
				display: none;
			}
			#basis_builder_slideshow {
				display: block;
			}
		<?php else : ?>
			#basis_builder_slideshow,
			#basis_builder_product {
				display: none;
			}
		<?php endif; ?>
		</style>
	<?php
	}

	/**
	 * Add data for the HTML Builder.
	 *
	 * Data needs to be added late so that the iterator value is properly set.
	 *
	 * @since  1.0.
	 *
	 * @return void
	 */
	public function add_js_data() {
		global $hook_suffix;

		// Only load resources if they are needed on the current page
		if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ) ) || 'page' !== get_post_type() ) {
			return;
		}

		// Add data needed for the JS
		$data = array(
			'iterator'          => $this->get_iterator(),
			'pageID'            => get_the_ID(),
			'hideHeaderChecked' => checked( get_post_meta( get_the_ID(), '_basis-hide-header', true ), '1', false ),
			'hideHeaderLabel'   => __( 'Hide the site header on this page', 'basis' ),
		);

		wp_localize_script(
			'basis-html-builder',
			'basisHTMLBuilderData',
			$data
		);
	}

	/**
	 * Reusable component for adding an image uploader.
	 *
	 * @since  1.0.
	 *
	 * @param  string    $section_name    Name of the current section.
	 * @param  int       $image_id        ID of the current image.
	 * @param  array     $messages        Message to show.
	 * @return void
	 */
	public function add_uploader( $section_name, $image_id = 0, $messages = array() ) {
		$image        = wp_get_attachment_image( $image_id, 'large' );
		$add_state    = ( '' === $image ) ? 'basis-show' : 'basis-hide';
		$remove_state = ( '' === $image ) ? 'basis-hide' : 'basis-show';

		// Set default messages. Note that 'basis' is not used in some cases the strings are core i18ns
		$messages['add']    = ( empty( $messages['add'] ) )    ? __( 'Set featured image' )            : $messages['add'];
		$messages['remove'] = ( empty( $messages['remove'] ) ) ? __( 'Remove featured image' )         : $messages['remove'];
		$messages['title']  = ( empty( $messages['title'] ) )  ? __( 'Featured Image', 'basis' )        : $messages['title'];
		$messages['button'] = ( empty( $messages['button'] ) ) ? __( 'Use as Featured Image', 'basis' ) : $messages['button'];
	?>
		<div class="basis-uploader">
			<div class="basis-media-uploader-placeholder basis-media-uploader-add">
				<?php if ( '' !== $image ) : ?>
					<?php echo $image; ?>
				<?php endif; ?>
			</div>
			<div class="basis-media-link-wrap">
				<a href="#" class="basis-media-uploader-add basis-media-uploader-set-link <?php echo $add_state; ?>" data-title="<?php echo esc_attr( $messages['title'] ); ?>" data-button-text="<?php echo esc_attr( $messages['button'] ); ?>">
					<?php echo $messages['add']; ?>
				</a>
				<a href="#" class="basis-media-uploader-remove <?php echo $remove_state; ?>">
					<?php echo $messages['remove']; ?>
				</a>
			</div>
			<input type="hidden" name="<?php echo esc_attr( $section_name ); ?>[image-id]" value="<?php echo absint( $image_id ); ?>" class="basis-media-uploader-value" />
		</div>
	<?php
	}

	/**
	 * Load a section template with an available data payload for use in the template.
	 *
	 * @since  1.0.
	 *
	 * @param  string    $section    The section data.
	 * @param  string    $builder    The builder that is being generated.
	 * @param  array     $data       The data payload to inject into the section.
	 * @return void
	 */
	private function _load_section( $section, $data = array(), $builder = '' ) {
		if ( ! isset( $section['id'] ) ) {
			return;
		}

		// Globalize the data to provide access within the template
		global $basis_section, $basis_builder, $basis_section_data, $basis_section_id, $basis_section_name, $basis_is_js_template;
		$basis_section      = $section;
		$basis_builder      = $builder;
		$basis_section_data = $data;

		// Change the template depending on JS or PHP context
		if ( true === $basis_is_js_template ) {
			$basis_section_name = 'basis-section['. $basis_builder . '][{{{ iterator }}}]';
			$basis_section_id   = 'basissection' . $basis_builder . '{{{ iterator }}}';
		} else {
			$basis_section_name = 'basis-section['. $basis_builder . '][' . absint( basis_get_html_builder()->get_iterator() ) . ']';
			$basis_section_id   = 'basissection'. $basis_builder . absint( basis_get_html_builder()->get_iterator() );
		}

		// Include the template
		get_template_part( 'includes/html-builder/templates/' . $section['id'] );

		// Destroy the variable as a good citizen does
		unset( $GLOBALS['basis_section'] );
		unset( $GLOBALS['basis_builder'] );
		unset( $GLOBALS['basis_section_data'] );
		unset( $GLOBALS['basis_section_id'] );
		unset( $GLOBALS['basis_section_name'] );
	}

	/**
	 * Print out the JS section templates
	 *
	 * @since  1.0.
	 *
	 * @return void
	 */
	public function print_templates() {
		global $hook_suffix, $typenow, $basis_is_js_template;
		$basis_is_js_template = true;

		// Only show when adding/editing pages
		if ( 'page' !== $typenow || ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ) )) {
			return;
		}

		// Combine the menu items for the product page with the additional slideshow template
		$templates = array_merge(
			$this->get_product_menu_items(),
			array(
				'slide' => array(
					'id'    => 'slide',
					'label' => __( 'Slide', 'basis' ),
				)
			)
		);

		// Print the templates
		foreach ( $templates as $key => $section ) : ?>
			<script type="text/html" id="tmpl-basis-<?php echo esc_attr( $section['id'] ); ?>">
				<?php
				ob_start();
				$builder = ( 'slide' === $section['id'] ) ? 'slideshow' : 'product';
				$this->_load_section( $section, array(), $builder );
				$html = ob_get_clean();

				$html = str_replace(
					array(
						'name="basiseditortemp' . $section['id'] . '"',
						'name="basiseditortemp' . $section['id'] . 'left"',
						'name="basiseditortemp' . $section['id'] . 'middle"',
						'name="basiseditortemp' . $section['id'] . 'right"',
						'basiseditortemp' . $section['id']
					),
					array(
						'name="basis-section[' . $builder . '][{{{ iterator }}}][content]"',
						'name="basis-section[' . $builder . '][{{{ iterator }}}][left][content]"',
						'name="basis-section[' . $builder . '][{{{ iterator }}}][middle][content]"',
						'name="basis-section[' . $builder . '][{{{ iterator }}}][right][content]"',
						'basiseditor' . $section['id'] . '{{{ iterator }}}',
					),
					$html
				);

				echo $html;
				?>
			</script>
		<?php endforeach;

		unset( $GLOBALS['basis_is_js_template'] );
	}

	/**
	 * Wrapper function to produce a WP Editor with special defaults.
	 *
	 * @since  1.0.
	 *
	 * @param  string    $content     The content to display in the editor.
	 * @param  string    $name        Name of the editor.
	 * @param  array     $settings    Setting to send to the editor.
	 * @return void
	 */
	public function wp_editor( $content, $name, $settings = array() ) {
		$settings = wp_parse_args( $settings, array(
			'tinymce'   => array(
				'theme_advanced_buttons1' => 'bold,italic,link,unlink',
				'theme_advanced_buttons2' => '',
				'theme_advanced_buttons3' => '',
				'theme_advanced_buttons4' => '',
				'toolbar1'                => 'bold,italic,link,unlink',
				'toolbar2'                => '',
				'toolbar3'                => '',
				'toolbar4'                => '',
			),
			'quicktags' => array(
				'buttons' => 'strong,em,link',
			),
			'editor_height' => 150,
		) );

		// Remove the default media buttons action and replace it with the custom one
		remove_action( 'media_buttons', 'media_buttons' );
		add_action( 'media_buttons', array( $this, 'media_buttons' ) );

		// Render the editor
		wp_editor( $content, $name, $settings );

		// Reinstate the original media buttons function
		remove_action( 'media_buttons', array( $this, 'media_buttons' ) );
		add_action( 'media_buttons', 'media_buttons' );
	}

	/**
	 * Add the media buttons to the text editor.
	 *
	 * This is a copy and modification of the core "media_buttons" function. In order to make the media editor work
	 * better for smaller width screens, we need to wrap the button text in a span tag. By doing so, we can hide the
	 * text in some situations.
	 *
	 * @since  1.0.
	 *
	 * @param  string    $editor_id    The value of the current editor ID.
	 * @return void
	 */
	public function media_buttons( $editor_id = 'content' ) {
		$post = get_post();
		if ( ! $post && ! empty( $GLOBALS['post_ID'] ) ) {
			$post = $GLOBALS['post_ID'];
		}

		wp_enqueue_media( array(
			'post' => $post
		) );

		$img = '<span class="wp-media-buttons-icon"></span>';

		echo '<a href="#" id="insert-media-button" class="button insert-media add_media" data-editor="' . esc_attr( $editor_id ) . '" title="' . esc_attr__( 'Add Media' ) . '">' . $img . ' <span class="basis-media-button-text">' . __( 'Add Media' ) . '</span></a>';
	}

	/**
	 * Append the editor styles to the section editors.
	 *
	 * Unfortunately, the `wp_editor()` function does not support a "content_css" argument. As a result, the stylesheet
	 * for the "content_css" parameter needs to be added via a filter.
	 *
	 * @since  1.0.4.
	 *
	 * @param  array     $mce_init     The array of tinyMCE settings.
	 * @param  string    $editor_id    The ID for the current editor.
	 * @return array                   The modified settings.
	 */
	function tiny_mce_before_init( $mce_init, $editor_id ) {
		// Only add stylesheet to a section editor
		if ( false === strpos( $editor_id, 'basis' ) ) {
			return $mce_init;
		}

		$content_css = get_template_directory_uri() . '/includes/stylesheets/editor-style.css';

		// If there is already a stylesheet being added, append and do not override
		if ( isset( $mce_init[ 'content_css' ] ) ) {
			$mce_init['content_css'] .= ',' . $content_css;
		} else {
			$mce_init['content_css'] = $content_css;
		}

		return $mce_init;
	}

	/**
	 * Denote the default editor for the user.
	 *
	 * Note that it would usually be ideal to expose this via a JS variable using wp_localize_script; however, it is
	 * being printed here in order to guarantee that nothing changes this value before it would otherwise be printed.
	 * The "after_wp_tiny_mce" action appears to be the most reliable place to print this variable.
	 *
	 * @since  1.0.
	 *
	 * @param  array    $settings   TinyMCE settings.
	 * @return void
	 */
	public function after_wp_tiny_mce( $settings ) {
	?>
		<script type="text/javascript">
			var basisMCE = '<?php echo esc_js( wp_default_editor() ); ?>';
		</script>
	<?php
	}

	/**
	 * Duplicate of "the_content" with custom filter name for generating content in builder templates.
	 *
	 * @since  1.0.
	 *
	 * @param  string    $content    The original content.
	 * @return void
	 */
	public function the_builder_content( $content ) {
		$content = apply_filters( 'basis_the_builder_content', $content );
		$content = str_replace( ']]>', ']]&gt;', $content );
		echo $content;
	}

	/**
	 * Get the order for a feature section.
	 *
	 * @since  1.0.
	 *
	 * @param  array    $data    The section data.
	 * @return array             The desired order.
	 */
	public function get_featured_section_order( $data ) {
		$order = array(
			'image' => 'left',
			'text'  => 'right',
		);

		if ( isset( $data['order'] ) ) {
			if ( isset( $data['order'][0] ) && false !== strpos( $data['order'][0], 'text' ) ) {
				$order = array(
					'image' => 'right',
					'text'  => 'left',
				);
			}
		}

		return $order;
	}

	/**
	 * Retrieve the menu item information.
	 *
	 * @since  1.0.
	 *
	 * @return array    The menu item information.
	 */
	public function get_product_menu_items() {
		return $this->_product_menu_items;
	}

	/**
	 * Retrieve an individual menu item's information.
	 *
	 * @since  1.0.
	 *
	 * @param  string    $id    The section name to return.
	 * @return array            The menu item information.
	 */
	public function get_menu_item( $id ) {
		$items = $this->_product_menu_items;

		// Only return an item if it exists
		if ( array_key_exists( $id, $items ) ) {
			return $items[ $id ];
		} else {
			return array();
		}
	}

	/**
	 * Get the value of the iterator.
	 *
	 * @since  1.0.
	 *
	 * @return int    The value of the iterator.
	 */
	public function get_iterator() {
		return $this->_iterator;
	}

	/**
	 * Set the iterator value.
	 *
	 * @since  1.0.
	 *
	 * @param  int    $value    The new iterator value.
	 * @return int              The iterator value.
	 */
	public function set_iterator( $value ) {
		$this->_iterator = $value;
		return $this->get_iterator();
	}

	/**
	 * Increase the interator value by 1.
	 *
	 * @since  1.0.
	 *
	 * @return int    The iterator value.
	 */
	public function increment_iterator() {
		$value = $this->get_iterator();
		$value++;

		$this->set_iterator( $value );
		return $this->get_iterator();
	}
}
endif;

/**
 * Instantiate or return the one Basis_HTML_Builder instance.
 *
 * @since  1.0.
 *
 * @return Basis_HTML_Builder
 */
function basis_get_html_builder() {
	return Basis_HTML_Builder::instance();
}

add_action( 'admin_init', 'basis_get_html_builder' );
