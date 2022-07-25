<?php

namespace theme\act_child;

function enqueue_theme_styles() {
	$theme = wp_get_theme();

	wp_enqueue_style(
		'base-style',
		get_template_directory_uri() . '/dist/style.css',
		array(), // Has no dependencies
		$theme->parent()->get( 'Version' )
	);

	wp_enqueue_style(
		'child-style',
		get_stylesheet_uri(),
		array( 'base-style' ),
		$theme->get( 'Version' )
	);
}

add_action( 'wp_enqueue_scripts', 'theme\act_child\enqueue_theme_styles' );
