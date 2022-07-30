<?php

/**
 * The WP version string
 */
$wp_version = '0.0.2';

/**
 * Holds the ClassicPress DB revision, increments when changes are made to the ClassicPress DB schema.
 *
 * @global int $wp_db_version
 */
$wp_db_version = 38590;

/**
 * Holds the TinyMCE version
 *
 * @global string $tinymce_version
 */
$tinymce_version = '49110-20201110';

/**
 * Holds the required PHP version
 *
 * @global string $required_php_version
 */
$required_php_version = '7.0.0';

/**
 * Holds the required MySQL version
 *
 * @global string $required_mysql_version
 */
$required_mysql_version = '5.0';

/**
 * Return the ClassicPress version string.
 *
 * `function_exists( 'classicpress_version' )` is the recommended way for
 * plugins and themes to determine whether they are running under ClassicPress.
 *
 * @since 1.0.0
 *
 * @return string The ClassicPress version string.
 */
if ( ! function_exists( 'classicpress_version' ) ) {
	function classicpress_version() {
		global $wp_version;
		return $wp_version;
	}
}

/**
 * Return the ClassicPress version number without any alpha/beta/etc suffixes.
 *
 * @since 1.0.0
 *
 * @return string The ClassicPress version number with no suffix.
 */
if ( ! function_exists( 'classicpress_version_short' ) ) {
	function classicpress_version_short() {
		global $wp_version;
		return preg_replace( '#[+-].*$#', '', $wp_version );
	}
}

/**
 * Return whether ClassicPress is running as a source install (the result of
 * cloning the source repository rather than installing a built version).
 *
 * This is mostly supported, but there are a few things that need to work
 * slightly differently or need to be disabled.
 *
 * @since 1.0.0
 *
 * @return bool Whether ClassicPress is running as a source install.
 */
if ( ! function_exists( 'classicpress_is_dev_install' ) ) {
	function classicpress_is_dev_install() {
		return true;
	}
}
