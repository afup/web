<?php global $basis_section, $basis_section_name, $basis_section_data, $basis_is_js_template; ?>
<?php $id = ( isset( $basis_is_js_template ) && true === $basis_is_js_template ) ? 'basis-section-{{{ iterator }}}' : 'basis-section-' . absint( basis_get_html_builder()->get_iterator() ); ?>

<?php if ( ! isset( $basis_is_js_template ) || true !== $basis_is_js_template ) : ?>
<div class="basis-section <?php if ( isset( $basis_section_data['state'] ) && 'open' === $basis_section_data['state'] ) echo 'basis-section-open'; ?> basis-section-<?php echo esc_attr( $basis_section['id'] ); ?>" id="<?php echo esc_attr( $id ); ?>" data-iterator="<?php echo absint( basis_get_html_builder()->get_iterator() ); ?>" data-section-type="<?php echo esc_attr( $basis_section['id'] ); ?>" data-iterator="<?php echo absint( basis_get_html_builder()->get_iterator() ); ?>" data-section-type="<?php echo esc_attr( $basis_section['id'] ); ?>">
<?php endif; ?>
	<div class="basis-section-header">
		<?php
			$header_title = '';
			if ( isset( $basis_section_data['title'] ) ) {
				$header_title = $basis_section_data['title'];
			} elseif ( isset( $basis_section_data['left']['title'] ) ) {
				$header_title = $basis_section_data['left']['title'];
			}

			$pipe_extra_class = ( empty( $header_title ) ) ? ' basis-section-header-pipe-hidden' : '';
		?>
		<h3>
			<span class="basis-section-header-title"><?php echo esc_html( $header_title ); ?></span><em><?php echo esc_html( $basis_section['label'] ); ?></em>
		</h3>
		<a href="#" class="basis-section-toggle" title="<?php esc_attr_e( 'Click to toggle', 'basis' ); ?>">
			<div class="handlediv"></div>
		</a>
	</div>
	<div class="clear"></div>
	<div class="basis-section-body">
		<input type="hidden" value="<?php echo esc_attr( $basis_section['id'] ); ?>" name="<?php echo esc_attr( $basis_section_name ); ?>[section-type]" />
