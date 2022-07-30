<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="full_site_wrapper">

<header class="site-header">

    <img class="logo" src="https://via.placeholder.com/100" alt="Logo">

    <input type="checkbox" id="checkbox-main-menu">

	<?php
	wp_nav_menu(
		array(
			'container'       => 'nav',
			'container_class' => 'main-menu',
			'theme_location'  => 'main-menu',
		)
	);
	?>

    <label for="checkbox-main-menu" class="toggle-main-menu">
        <span class="hamburger-open-menu">
            <span class="line-1"></span>
            <span class="line-2"></span>
            <span class="line-3"></span>
        </span>
        <span class="x-close-menu"></span>
    </label>

</header>

<main id="primary" class="site-main" role="main">