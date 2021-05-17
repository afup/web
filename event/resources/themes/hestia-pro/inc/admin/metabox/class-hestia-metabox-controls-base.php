<?php
/**
 * Page settings metabox.
 *
 * @package Hestia
 */


/**
 * Class Hestia_Metabox_Controls_Base
 *
 * @package Hestia
 */
abstract class Hestia_Metabox_Controls_Base {

	/**
	 * Controls.
	 *
	 * @var array
	 */
	private $controls = array();

	/**
	 * Post ID.
	 *
	 * @var int
	 */
	protected $post_id;

	/**
	 * Hestia_Metabox_Controls_Base constructor.
	 */
	public function __construct() {
		$this->set_post_id();
	}

	/**
	 * Init function
	 */
	public function init() {
		$this->add_controls();
		$this->order_by_priority();
		add_action( 'save_post', array( $this, 'save' ) );
		add_action( 'hestia_settings_render_metabox_controls', array( $this, 'render_controls' ) );
	}

	/**
	 * Set the post ID.
	 *
	 * @since  2.0.18
	 * @access private
	 * @return int|null
	 */
	private function set_post_id() {
		if ( ! isset( $_GET['post'] ) ) {
			return null;
		}
		$this->post_id = $_GET['post'];
	}

	/**
	 * Add controls.
	 */
	abstract protected function add_controls();

	/**
	 * Add the control.
	 *
	 * @param Hestia_Metabox_Control_Base $control the control object.
	 */
	public function add_control( Hestia_Metabox_Control_Base $control ) {
		array_push( $this->controls, $control );
	}

	/**
	 * Order the controls by given priority.
	 */
	private function order_by_priority() {
		$order = array();
		foreach ( $this->controls as $key => $control_object ) {
			$order[ $key ] = $control_object->priority;
		}
		array_multisort( $order, SORT_ASC, $this->controls );
	}

	/**
	 * The metabox content.
	 */
	public function render_controls() {

		global $post;
		foreach ( $this->controls as $control ) {
			$control->render( $post->ID );
		}
	}

	/**
	 * Save metabox content.
	 *
	 * @param int $post_id post id.
	 */
	public function save( $post_id ) {
		foreach ( $this->controls as $control ) {
			$control->save( $post_id );
		}
	}
}
