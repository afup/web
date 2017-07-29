<?php require( get_template_directory() . '/includes/html-builder/templates/section-header.php' ); ?>
<?php global $basis_section, $basis_section_data, $basis_section_id, $basis_section_name, $basis_is_js_template; ?>

	<div class="basis-titlediv">
		<div class="basis-titlewrap">
			<input placeholder="<?php esc_attr_e( 'Enter title here' ); ?>" type="text" name="<?php echo esc_attr( $basis_section_name ); ?>[title]" class="basis-title basis-section-header-title-input" value="<?php if ( isset( $basis_section_data['title'] ) ) echo esc_attr( htmlspecialchars( $basis_section_data['title'] ) ); ?>" autocomplete="off" />
		</div>
	</div>

	<?php
	$editor_settings = array(
		'tinymce'   => array(
			'theme_advanced_buttons1' => 'bold,italic,link,unlink,justifyleft,justifycenter,justifyright',
			'theme_advanced_buttons2' => '',
			'theme_advanced_buttons3' => '',
			'theme_advanced_buttons4' => '',
			'toolbar1'                => 'bold,italic,link,unlink,alignleft,aligncenter,alignright',
			'toolbar2'                => '',
			'toolbar3'                => '',
			'toolbar4'                => '',
		),
		'quicktags' => array(
			'buttons' => 'strong,em,link',
		),
		'editor_height' => 345,
		'textarea_name' => $basis_section_name . '[content]'
	);

	if ( true === $basis_is_js_template ) : ?>
		<?php basis_get_html_builder()->wp_editor( '', 'basiseditortemptext', $editor_settings ); ?>
	<?php else : ?>
		<?php $content = ( isset( $basis_section_data['content'] ) ) ? $basis_section_data['content'] : ''; ?>
		<?php basis_get_html_builder()->wp_editor( $content, $basis_section_id, $editor_settings ); ?>
	<?php endif; ?>

	<input type="hidden" class="basis-section-state" name="<?php echo esc_attr( $basis_section_name ); ?>[state]" value="<?php if ( isset( $basis_section_data['state'] ) ) echo esc_attr( $basis_section_data['state'] ); else echo 'open'; ?>" />

<?php require( get_template_directory() . '/includes/html-builder/templates/section-footer.php' ); ?>