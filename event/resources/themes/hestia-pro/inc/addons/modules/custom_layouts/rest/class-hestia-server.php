<?php
/**
 * Rest server for Hestia Custom Layouts
 *
 * @package Inc/Addons/Modules/Custom_Layouts/Rest
 */

/**
 * Class Hestia_Server
 */
class Hestia_Server {
	/**
	 * Initialize the rest functionality.
	 */
	public function init() {
		add_action( 'rest_api_init', array( $this, 'register_endpoints' ) );
	}

	/**
	 * Register endpoints.
	 */
	public function register_endpoints() {
		register_rest_field(
			'hestia_layouts',
			'hestia_editor_mode',
			array(
				'get_callback'    => array( $this, 'get_meta_field' ),
				'update_callback' => array( $this, 'update_meta_field' ),
			)
		);

		register_rest_field(
			'hestia_layouts',
			'hestia_editor_content',
			array(
				'get_callback'    => array( $this, 'get_meta_field' ),
				'update_callback' => array( $this, 'update_editor_content_field' ),
			)
		);
	}

	/**
	 * Editor mode endpoint get.
	 *
	 * @param array  $object Post.
	 * @param string $field_name Field name.
	 * @param Object $request Request object.
	 *
	 * @return mixed
	 */
	public function get_meta_field( $object, $field_name, $request ) {
		return get_post_meta( $object['id'], $field_name, true );
	}

	/**
	 * Editor mode endpoint update callback.
	 *
	 * @param array  $value Request data.
	 * @param Object $object Request object.
	 * @param string $field_name Field name.
	 *
	 * @return bool
	 */
	public function update_meta_field( $value, $object, $field_name ) {
		if ( $field_name !== 'hestia_editor_mode' ) {
			return false;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$meta_value    = get_post_meta( $object->ID, $field_name, true );
		$updated_value = '0';
		if ( empty( $meta_value ) || $meta_value === '0' ) {
			$updated_value = '1';
		}
		update_post_meta( $object->ID, $field_name, $updated_value );

		return true;
	}

	/**
	 * Editor content endpoint update callback.
	 *
	 * @param array  $value Request data.
	 * @param Object $object Request object.
	 * @param string $field_name Field name.
	 *
	 * @return mixed
	 */
	public function update_editor_content_field( $value, $object, $field_name ) {
		if ( $field_name !== 'hestia_editor_content' ) {
			return false;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$post_id       = $object->ID;
		$file_name     = 'hestia-custom-script-' . $post_id;
		$wp_upload_dir = wp_upload_dir( null, false );
		$upload_dir    = $wp_upload_dir['basedir'] . '/hestia-theme/';
		$file_path     = $upload_dir . $file_name . '.php';

		require_once( ABSPATH . '/wp-admin/includes/file.php' );
		global $wp_filesystem;
		WP_Filesystem();
		// Make sure the upload directory exists
		wp_mkdir_p( $upload_dir );
		$value = apply_filters( 'hestia_custom_layout_magic_tags', $value, $post_id );
		$wp_filesystem->put_contents( $file_path, $value );
		update_post_meta( $object->ID, $field_name, $file_name );

		return true;
	}
}
