<?php

namespace theme\starter;

function register_menus() {
	register_nav_menus(
		array(
			'main-menu' => __( 'Main Menu' ),
			'footer-menu' => __( 'Footer Menu' ),
		)
	);
}

add_action( 'init', 'theme\starter\register_menus' );
