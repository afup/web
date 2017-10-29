<header id="header">
    <div class="header-wrapper">
        <?php
        wp_nav_menu(
            array(
                'theme_location'  => 'header',
                'container_id'    => 'basis-header-nav',
                'container_class' => 'header-menu-container',
                'menu_class'      => 'header-menu',
                'depth'           => '2'
            )
        );
        ?>
        <div id="mobile-toggle">
            <span><?php echo wp_strip_all_tags( basis_get_responsive_nav_options( 'label' ) ); ?></span>
        </div>
        <div id="title">
            <?php if ( basis_get_logo()->has_logo() ) : ?>
                <a class="custom-logo" title="<?php esc_attr_e( 'Home', 'basis' ); ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>"></a>
            <?php else : ?>
                <h1>
                    <a href="<?php echo esc_url( home_url() ); ?>"><?php bloginfo( 'name' ); ?></a>
                </h1>
            <?php endif; ?>
        </div>
        <?php if ( get_bloginfo( 'description' ) ) : ?>
            <span class="basis-tagline">
				<?php bloginfo( 'description' ); ?>
			</span>
        <?php endif; ?>
    </div>
</header>
