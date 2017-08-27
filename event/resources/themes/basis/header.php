<?php
/**
 * @package Basis
 */
?>

<!DOCTYPE html>
<!--[if IE 7]>    <html class="no-js IE7 IE" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>    <html class="no-js IE8 IE" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 9]>    <html class="no-js IE9 IE" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
	<title><?php wp_title( ' | ', true, 'right' ); ?></title>

	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php
/**
 * Allow the header to be hidden. The HTML Builder allows the user to hide the header in order to make a microsite or
 * landing page. It uses this filter to control that behavior. Additionally, plugins or child themes can further
 * customize this behavior via the filter.
 */
?>
<?php if ( true === basis_show_header() ) : ?>
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
<?php endif; ?>