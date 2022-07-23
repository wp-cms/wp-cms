<?php
/**
 * Front to the WP application. This file doesn't do anything, but loads
 * wp-blog-header.php loads the theme.
 */

/**
 * Tells ClassicPress to load the ClassicPress theme and output it.
 */
const WP_USE_THEMES = true;

/** Loads the WP Environment and Template */
require( dirname( __FILE__ ) . '/wp-blog-header.php' );
