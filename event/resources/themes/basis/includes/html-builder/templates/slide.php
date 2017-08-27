<?php require( get_template_directory() . '/includes/html-builder/templates/section-header.php' ); ?>
<?php global $basis_section, $basis_section_data, $basis_section_id, $basis_section_name, $basis_is_js_template; ?>

	<div class="basis-banner-column basis-banner-column-left">
		<?php
			$id = ( isset( $basis_section_data['image-id'] ) ) ? $basis_section_data['image-id'] : 0;
			basis_get_html_builder()->add_uploader(
				$basis_section_name,
				$id,
				array(
					'add'    => __( 'Set background image', 'basis' ),
					'remove' => __( 'Remove background image', 'basis' ),
					'title'  => __( 'Background image', 'basis' ),
					'button' => __( 'Use as Background Image', 'basis' ),
				)
			);
		?>
	</div>

	<div class="basis-banner-column basis-banner-column-right">
		<h4><?php _e( 'Button', 'basis' ); ?></h4>
		<p>
			<input type="text" class="widefat" placeholder="<?php esc_attr_e( 'Enter URL here', 'basis' ); ?>" name="<?php echo esc_attr( $basis_section_name ); ?>[button-url]" value="<?php if ( isset( $basis_section_data['button-url'] ) ) echo esc_url( $basis_section_data['button-url'] ); ?>" />
		</p>
		<p>
			<input type="text" class="widefat" placeholder="<?php esc_attr_e( 'Enter label here', 'basis' ); ?>" name="<?php echo esc_attr( $basis_section_name ); ?>[button-text]" value="<?php if ( isset( $basis_section_data['button-text'] ) ) echo sanitize_text_field( $basis_section_data['button-text'] ); ?>" />
		</p>

		<h4><?php _e( 'Background', 'basis' ); ?></h4>
		<p>
			<input id="<?php echo esc_attr( $basis_section_name ); ?>[darken-image]" type="checkbox" name="<?php echo esc_attr( $basis_section_name ); ?>[darken-image]" value="1" <?php checked( ( isset( $basis_section_data['darken-image'] ) && 1 === (int) $basis_section_data['darken-image'] ) || true === $basis_is_js_template ); ?> />
			<label for="<?php echo esc_attr( $basis_section_name ); ?>[darken-image]">
				<?php _e( 'Darken to improve readability', 'basis' ); ?>
			</label>
		</p>
	</div>

	<div class="clear"></div>

	<div class="basis-titlediv">
		<div class="basis-titlewrap">
			<input placeholder="<?php esc_attr_e( 'Enter title here' ); ?>" type="text" name="<?php echo esc_attr( $basis_section_name ); ?>[title]" value="<?php if ( isset( $basis_section_data['title'] ) ) echo esc_attr( htmlspecialchars( $basis_section_data['title'] ) ); ?>" class="basis-title basis-section-header-title-input" autocomplete="off" />
		</div>
	</div>

	<?php if ( true === $basis_is_js_template ) : ?>
		<?php basis_get_html_builder()->wp_editor( '', 'basiseditortempslide', array( 'editor_height' => 245 ) ); ?>
	<?php else : ?>
		<?php $content = ( isset( $basis_section_data['content'] ) ) ? $basis_section_data['content'] : ''; ?>
		<?php basis_get_html_builder()->wp_editor( $content, $basis_section_id, array( 'editor_height' => 245, 'textarea_name' => $basis_section_name . '[content]' ) ); ?>
	<?php endif; ?>

	<input type="hidden" class="basis-section-state" name="<?php echo esc_attr( $basis_section_name ); ?>[state]" value="<?php if ( isset( $basis_section_data['state'] ) ) echo esc_attr( $basis_section_data['state'] ); else echo 'open'; ?>" />

<?php require( get_template_directory() . '/includes/html-builder/templates/section-footer.php' ); ?>