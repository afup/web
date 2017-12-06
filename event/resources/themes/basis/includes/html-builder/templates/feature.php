<?php require( get_template_directory() . '/includes/html-builder/templates/section-header.php' ); ?>
<?php global $basis_section, $basis_section_data, $basis_section_id, $basis_section_name, $basis_is_js_template; ?>
<?php $order = ( isset( $basis_section_data['order'] ) ) ? implode( ',', $basis_section_data['order'] ) : 'basis-section-feature-image,basis-section-feature-text'; ?>

	<div class="basis-section-sortable-stage basis-section-sortable-stage-feature">
		<?php $columns = array(); ?>
		<?php ob_start(); ?>
		<div class="basis-feature-column basis-sortable" id="basis-section-feature-image">
			<div title="<?php esc_attr_e( 'Drag-and-drop this column into place', 'basis' ); ?>" class="basis-sortable-handle">
				<div class="sortable-background"></div>
			</div>

			<div class="basis-titlediv">
				<input placeholder="<?php esc_attr_e( 'Enter link here' ); ?>" type="text" name="<?php echo esc_attr( $basis_section_name ); ?>[image-link]" class="basis-link widefat" value="<?php if ( isset( $basis_section_data['image-link'] ) ) echo esc_url( $basis_section_data['image-link'] ); ?>" autocomplete="off" />
			</div>

			<?php
				$id = ( isset( $basis_section_data['image-id'] ) ) ? $basis_section_data['image-id'] : 0;
				basis_get_html_builder()->add_uploader( $basis_section_name, $id );
			?>
		</div>
		<?php $columns['image'] = ob_get_clean(); ?>

		<?php ob_start(); ?>
		<div class="basis-feature-column basis-sortable" id="basis-section-feature-text">
			<div title="<?php esc_attr_e( 'Drag-and-drop this column into place', 'basis' ); ?>" class="basis-sortable-handle">
				<div class="sortable-background"></div>
			</div>

			<div class="basis-titlediv">
				<input placeholder="<?php esc_attr_e( 'Enter link here' ); ?>" type="text" name="<?php echo esc_attr( $basis_section_name ); ?>[title-link]" class="basis-link widefat" value="<?php if ( isset( $basis_section_data['title-link'] ) ) echo esc_url( $basis_section_data['title-link'] ); ?>" autocomplete="off" />
			</div>

			<div class="basis-titlediv">
				<div class="basis-titlewrap">
					<input placeholder="<?php esc_attr_e( 'Enter title here' ); ?>" type="text" name="<?php echo esc_attr( $basis_section_name ); ?>[title]" class="basis-title basis-section-header-title-input" value="<?php if ( isset( $basis_section_data['title'] ) ) echo esc_attr( htmlspecialchars( $basis_section_data['title'] ) ); ?>" autocomplete="off" />
				</div>
			</div>

			<?php if ( true === $basis_is_js_template ) : ?>
				<?php basis_get_html_builder()->wp_editor( '', 'basiseditortempfeature', array( 'editor_height' => 125 ) ); ?>
			<?php else : ?>
				<?php $content = ( isset( $basis_section_data['content'] ) ) ? $basis_section_data['content'] : ''; ?>
				<?php basis_get_html_builder()->wp_editor( $content, $basis_section_id, array( 'editor_height' => 125, 'textarea_name' => $basis_section_name . '[content]' ) ); ?>
			<?php endif; ?>
		</div>
		<?php $columns['text'] = ob_get_clean(); ?>
		<?php
			foreach ( explode( ',', $order ) as $column ) {
				if ( 'image' === str_replace( 'basis-section-feature-', '', $column ) ) {
					echo $columns['image'];
				} else {
					echo $columns['text'];
				}
			}
		?>
	</div>

	<input type="hidden" class="basis-section-state" name="<?php echo esc_attr( $basis_section_name ); ?>[state]" value="<?php if ( isset( $basis_section_data['state'] ) ) echo esc_attr( $basis_section_data['state'] ); else echo 'open'; ?>" />
	<input type="hidden" class="basis-section-order" name="<?php echo esc_attr( $basis_section_name ); ?>[order]" value="<?php echo esc_attr( $order ); ?>" />

<?php require( get_template_directory() . '/includes/html-builder/templates/section-footer.php' ); ?>