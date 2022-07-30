<?php

namespace theme\starter;

// Set up theme defaults and register support for various WordPress feaures
add_action( 'after_setup_theme', 'theme\starter\setup_theme_settings' );

// Enqueue scripts and styles
add_action( 'wp_enqueue_scripts', 'theme\starter\enqueue_css_and_js' );

function setup_theme_settings() {

	load_theme_textdomain( 'bathe', get_theme_file_uri( 'languages' ) );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'post-formats',
		array(
			'aside',
			'image',
			'video',
			'quote',
			'link',
		)
	);

	// Add support for core custom logo
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 200,
			'width'       => 50,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

}

function enqueue_css_and_js() {
	wp_enqueue_style( 'base-style', get_theme_file_uri( 'dist/style.css' ) );
	wp_enqueue_script( 'global-js', get_theme_file_uri( 'dist/globalScripts.js' ), array(), null, true );
}


require get_template_directory() . '/includes/register-menus-and-locations.php';
