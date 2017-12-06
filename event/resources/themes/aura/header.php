<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"  "http://www.w3.org/TR/html4/strict.dtd">
<!--[if IE 6]>
<html id="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head profile="http://gmpg.org/xfn/11">
<link rel="profile" href="http://gmpg.org/xfn/11" />
<meta http-equiv="content-type" content="<?php bloginfo('html_type') ?>;chartset=<?php bloginfo('charset'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<link href='http://fonts.googleapis.com/css?family=PT+Sans' rel='stylesheet' type='text/css' media="screen">
<link href='http://fonts.googleapis.com/css?family=Doppio+One' rel='stylesheet' type='text/css'>

<meta charset="<?php bloginfo('charset'); ?>">
<title><?php wp_title(); ?></title>

<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<div id="head">
  <div id="head-container">
     <div id="logo"><a class="blogname" href="<?php echo esc_url( home_url() ); ?>"><?php echo bloginfo('name'); ?></a>
     <div id="slogan"><?php echo bloginfo('description'); ?></div></div>
     <div id="nav">
	     <ul>
	     <?php wp_nav_menu( array('theme_location' => 'header-nav', 'depth' => -1, 'menu_class' => 'nav' )); ?>
	     </ul>
     </div>
     <div class="clear"></div>
  </div>
</div>

