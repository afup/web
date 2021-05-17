<?php
/**
 * This class allows developers to display a button in customizer that links to Elementor live edit if the page
 * that is set as frontpage was previously edited with Elementor. This control replace the text editor control
 * if the page was edited with Elementor.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Elementor_Edit
 *
 * @since  1.1.60
 * @access public
 */
class Hestia_PageBuilder_Button extends WP_Customize_Control {

	/**
	 * The type of customize control being rendered.
	 *
	 * @since  1.1.60
	 * @access public
	 * @var    string
	 */
	public $type = 'page-builder-button';

	/**
	 * The post id of the page that is set as frontpage.
	 *
	 * @since  1.1.60
	 * @access public
	 * @var    string
	 */
	public $pid = '';

	/**
	 * Page Builder pugin
	 *
	 * @since  1.1.63
	 * @access public
	 * @var    string
	 */
	public $page_builder = array();

	/**
	 * Hestia_Elementor_Edit constructor.
	 *
	 * @param WP_Customize_Manager $manager Customize manager object.
	 * @param string               $id Control id.
	 * @param array                $args Control arguments.
	 */
	public function __construct( WP_Customize_Manager $manager, $id, array $args = array() ) {
		parent::__construct( $manager, $id, $args );

		$frontpage_id = get_option( 'page_on_front' );
		if ( ! empty( $frontpage_id ) ) {

			if ( ! empty( $frontpage_id ) ) {
				$this->pid = $frontpage_id;
			}

			$page_edited_with_elementor = false;
			$page_edited_with_beaver    = false;
			$page_edited_with_wpbakery  = false;
			$page_edited_with_so        = false;
			$page_edited_with_divi      = false;

			/**
			 * Elementor and Beaver builder mark if the page was edited with its editors in post meta so we'll have to
			 * check if plugins exists and the page was edited with page builder.
			 */
			$post_meta = ! empty( $frontpage_id ) ? get_post_meta( $frontpage_id ) : '';
			if ( ! empty( $post_meta ) ) {
				$page_edited_with_elementor = ! empty( $post_meta['_elementor_edit_mode'] ) && $post_meta['_elementor_edit_mode'][0] === 'builder' && class_exists( 'Elementor\Plugin', false );
				$page_edited_with_beaver    = ! empty( $post_meta['_fl_builder_enabled'] ) && $post_meta['_fl_builder_enabled'][0] === '1' && class_exists( 'FLBuilder', false );
				$page_edited_with_so        = ! empty( $post_meta['panels_data'] ) && class_exists( 'SiteOrigin_Panels', false );
				$page_edited_with_divi      = ! empty( $post_meta['_et_pb_use_builder'] ) && $post_meta['_et_pb_use_builder'][0] === 'on' && class_exists( 'ET_Builder_Plugin', false );
			}

			/**
			 * WP Bakery (former Visual Composer) doesn't store a flag in meta data to say whether or not the page
			 * is edited with it so we have to check post content if it contains shortcodes from plugin.
			 */
			$post_content = get_post_field( 'post_content', $frontpage_id );
			if ( ! empty( $post_content ) ) {
				$page_edited_with_wpbakery = class_exists( 'Vc_Manager', false ) && strpos( $post_content, '[vc_' ) !== false;
			}

			$this->page_builder = array(
				'elementor'  => (bool) $page_edited_with_elementor,
				'beaver'     => (bool) $page_edited_with_beaver,
				'wpbakery'   => (bool) $page_edited_with_wpbakery,
				'siteorigin' => (bool) $page_edited_with_so,
				'divi'       => (bool) $page_edited_with_divi,
			);
		}
	}

	/**
	 * Enqueue scripts/styles.
	 *
	 * @since  1.1.60
	 * @access public
	 * @return void
	 */
	public function enqueue() {
	}

	/**
	 * Add custom parameters to pass to the JS via JSON.
	 *
	 * @since  1.1.60
	 * @access public
	 * @return array
	 */
	public function json() {
		$json = parent::json();
		foreach ( $this->page_builder as $builder_name => $builder_value ) {
			if ( $builder_value === true ) {
				switch ( $builder_name ) {
					case 'elementor':
						$json['edit_link']['elementor'] = \Elementor\Plugin::$instance->documents->get( $this->pid )->get_edit_url();
						break;
					case 'beaver':
						$json['edit_link']['beaver'] = FLBuilderModel::get_edit_url( $this->pid );
						break;
					case 'wpbakery':
						$json['edit_link']['wpbakery'] = Vc_Frontend_Editor::getInlineUrl( '', $this->pid );
						break;
					case 'siteorigin':
						$json['edit_link']['siteorigin'] = add_query_arg( 'so_live_editor', 1, get_edit_post_link( $this->pid ) );
						break;
					case 'divi':
						$json['edit_link']['divi'] = add_query_arg( 'et_fb', 1, esc_url( get_permalink( $this->pid ) ) );
						break;
				}
			}
		}
		return $json;
	}

	/**
	 * Don't render the content via PHP.  This control is handled with a JS template.
	 *
	 * @since  1.1.60
	 * @access public
	 * @return void
	 */
	protected function render_content() {}

	/**
	 * Underscore JS template to handle the control's output.
	 *
	 * @since  1.1.60
	 * @access public
	 * @return void
	 */
	public function content_template() {
		?>

		<label>
			<# if ( data.label ) { #>
				<span class="customize-control-title">{{ data.label }}</span>
				<# } #>

					<# if ( data.description ) { #>
						<span class="description customize-control-description">{{ data.description }}</span>
						<# } #>

							<# if( data.edit_link ){ #>
								<# _.each(data.edit_link, function(v, k) { #>
									<!-- wp-playlist-caption class is added to not add customize changeset to this link -->
									<a href="{{{v}}}" class="wp-playlist-caption"><div id="{{k}}-editor-button" class="button button-primary">
											<# if( k === 'elementor') {#>
												<i class="eicon-elementor" aria-hidden="true"></i>
												<# } #>
													Edit with {{k}}
										</div></a>
									<# }) #>
										<# } #>

		</label>
		<?php
	}
}
