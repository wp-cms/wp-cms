<?php
/**
 * Loads the WP environment and expected template of the theme for this request.
 */

if ( ! isset( $wp_did_header ) ) {

	$wp_did_header = true;

	// Load the WP library
	require_once( dirname( __FILE__ ) . '/wp-load.php' );

	// Set up the WP query
	wp();

	// Load the theme template
	require_once( ABSPATH . WPINC . '/template-loader.php' );

}
