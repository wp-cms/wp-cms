<?php

namespace theme\starter;

/**
 * Set up theme defaults and registers support for various WordPress feaures.
 */
add_action( 'after_setup_theme', function() {
	load_theme_textdomain( 'bathe', get_theme_file_uri( 'languages' ) );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
	) );


	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support( 'custom-logo', array(
		'height'      => 200,
		'width'       => 50,
		'flex-width'  => true,
		'flex-height' => true,
	) );

} );

/**
 * Enqueue scripts and styles.
 */
add_action( 'wp_enqueue_scripts', function() {

	wp_enqueue_style( 'main-css', get_theme_file_uri( 'dist/style.css' ) );

	wp_enqueue_script( 'global-js', get_theme_file_uri( 'dist/globalScripts.js' ), array(), null, true );

} );

require get_template_directory() . '/includes/register-menus-and-locations.php';
