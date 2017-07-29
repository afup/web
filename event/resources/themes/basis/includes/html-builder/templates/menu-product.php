<?php $class = ( 'c' === get_user_setting( 'basismt' . get_the_ID() ) ) ? 'closed' : 'opened'; ?>
<div class="basis-menu-product basis-menu-product-<?php echo esc_attr( $class ); ?>" id="basis-menu-product">
	<div class="basis-menu-product-pane">
		<ul class="basis-menu-product-list">
			<?php foreach ( basis_get_html_builder()->get_product_menu_items() as $id => $item ) : ?>
			<li class="basis-menu-product-list-item">
				<a href="#" title="<?php esc_attr_e( 'Add', 'basis' ); ?>" class="basis-menu-product-list-item-link" id="basis-menu-product-list-item-link-<?php echo esc_attr( $id ); ?>" data-section="<?php echo esc_attr( $id ); ?>">
					<div class="basis-menu-product-list-item-link-icon-wrapper clear">
						<span class="basis-menu-product-list-item-link-icon"></span>
					</div>
					<div class="section-type-description">
					<h4>
						<?php echo esc_html( $item['label'] ); ?>
					</h4>
					<p>
						<?php echo esc_html( $item['description'] ); ?>
					</p>
					</div>
				</a>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<div class="basis-menu-product-tab">
		<a href="#" class="basis-menu-product-tab-link">
			<span><?php _e( 'Add New Section', 'basis' ); ?></span>
		</a>
	</div>
</div>