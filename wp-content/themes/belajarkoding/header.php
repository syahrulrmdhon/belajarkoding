<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package belajarkoding
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">

  <link rel="stylesheet" href="<?php echo esc_url( get_stylesheet_directory_uri() );?>/assets/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="<?php echo esc_url( get_stylesheet_directory_uri() );?>/assets/css/style.css"/>
  <link rel="stylesheet" href="<?php echo esc_url( get_stylesheet_directory_uri() );?>/assets/css/font-awesome/css/font-awesome.min.css"/>
  <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet"/>
	<title><?php wp_title(); ?></title>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'belajarkoding' ); ?></a>

		<header class="site-header" role="banner">
      <!-- nav bar -->
      <div class="navbar-wrapper">
        <div class="navbar navbar-custom navbar-fixed-top" role="navigation">
          <div class="container">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse" name="button">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand"  href="<?php echo get_site_url();?>"> <img src="<?php header_image(); ?>" alt=""></a>
            </div>
						<div class="navbar-collapse collapse">
								<?php
										wp_nav_menu(
											array(
												'theme_location' => 'menu-1' ,
												'container' => 'ul',
												'container_class' => 'navbar-collapse collapse',
												'menu_class' => 'nav navbar-nav navbar-right'
											)
										);
								?>
						</div>
          </div>
        </div>
      </div>
    </header>


	<div id="content" class="site-content">
