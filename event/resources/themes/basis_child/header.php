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

<style>
    @import url(https://fonts.googleapis.com/css?family=Sansita+One|Roboto:400,100,700);

    #afup-global-menu {
        text-align: center;
        background-color: #1d2241;
        height: 30px;
        color: #fff
    }
    #afup-global-menu .lien-entete {
        box-sizing: border-box;
        font-family: Roboto, Helvetica, sans-serif;
        letter-spacing: 0.08em;
        font-size: 13px;
        color: #fff;
        padding: 4px 15px;
        text-decoration: none;
        height: 30px;
        display: inline-block;
        line-height: 1.55;
    }
    #afup-global-menu .lien-entete:hover {
        background-color: #36a7df;
    }

    #afup-global-menu .lien-entete__active {
        background-color: #36a7df;
    }
</style>
<div id="afup-global-menu">
    <a href="https://afup.org" class="lien-entete">AFUP</a>
    <a href="https://event.afup.org" class="lien-entete lien-entete__active">PHPTour 2018</a>
    <a href="https://barometre.afup.org" class="lien-entete">Baromètre</a>
    <a href="http://www.planete-php.fr" class="lien-entete">Planète PHP</a>
</div>

<?php
/**
 * Allow the heade
 * r to be hidden. The HTML Builder allows the user to hide the header in order to make a microsite or
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
