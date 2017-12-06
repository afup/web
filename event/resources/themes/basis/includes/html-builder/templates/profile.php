<?php require( get_template_directory() . '/includes/html-builder/templates/section-header.php' ); ?>
<?php global $basis_section, $basis_section_data, $basis_section_id, $basis_section_name, $basis_is_js_template; ?>

	<div class="basis-section-sortable-stage basis-section-sortable-stage-profile">

		<?php foreach ( array( 'left', 'middle', 'right' ) as $column ) : ?>
			<div class="basis-profile-column basis-sortable basis-profile-<?php echo $column; ?>-column" id="basis-profile-<?php echo $column; ?>-column">
				<div title="<?php esc_attr_e( 'Drag-and-drop this column into place', 'basis' ); ?>" class="basis-sortable-handle">
					<div class="sortable-background"></div>
				</div>

				<div class="basis-titlediv">
					<input placeholder="<?php esc_attr_e( 'Enter link here' ); ?>" type="text" name="<?php echo esc_attr( $basis_section_name ); ?>[<?php echo $column; ?>][link]" class="basis-link widefat" value="<?php if ( isset( $basis_section_data[ $column ]['link'] ) ) echo esc_url( $basis_section_data[ $column ]['link'] ); ?>" autocomplete="off" />
				</div>

				<?php
					$section_name = $basis_section_name . '[' . $column . ']';
					$id = ( isset( $basis_section_data[ $column ]['image-id'] ) ) ? $basis_section_data[ $column ]['image-id'] : 0;
					basis_get_html_builder()->add_uploader( $section_name, $id );
				?>

				<div class="basis-titlediv">
					<div class="basis-titlewrap">
						<input placeholder="<?php esc_attr_e( 'Enter title here' ); ?>" type="text" name="<?php echo esc_attr( $basis_section_name ); ?>[<?php echo $column; ?>][title]" class="basis-title basis-section-header-title-input" value="<?php if ( isset( $basis_section_data[ $column ]['title'] ) ) echo esc_attr( htmlspecialchars( $basis_section_data[ $column ]['title'] ) ); ?>" autocomplete="off" />
					</div>
				</div>

				<?php
					$mce_args = array(
						'editor_css'    => '<style type="text/css" scoped>.wp_themeSkin .mceStatusbar{height: 20px;overflow: hidden;}.mceEditor.wp_themeSkin{overflow:hidden;display:block;}</style>',
						'editor_height' => '345',
						'textarea_name' => $basis_section_name . '[' . $column . '][content]'
					);
				?>

				<?php if ( true === $basis_is_js_template ) : ?>
					<?php basis_get_html_builder()->wp_editor( '', 'basiseditortempprofile' . $column, $mce_args ); ?>
				<?php else : ?>
					<?php $content = ( isset( $basis_section_data[ $column ]['content'] ) ) ? $basis_section_data[ $column ]['content'] : ''; ?>
					<?php basis_get_html_builder()->wp_editor( $content, $basis_section_id . $column, $mce_args ); ?>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>

		<input type="hidden" class="basis-section-state" name="<?php echo esc_attr( $basis_section_name ); ?>[state]" value="<?php if ( isset( $basis_section_data['state'] ) ) echo esc_attr( $basis_section_data['state'] ); else echo 'open'; ?>" />
		<input type="hidden" class="basis-section-order" name="<?php echo esc_attr( $basis_section_name ); ?>[order]" value="" />
	</div>
<?php require( get_template_directory() . '/includes/html-builder/templates/section-footer.php' ); ?>