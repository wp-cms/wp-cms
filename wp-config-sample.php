<?php
/**
 * The base configuration for WP
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the website, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * - MySQL settings
 * - Secret keys
 * - Database table prefix
 * - ABSPATH
 *
 */

// Database information
const DB_NAME     = 'database_name_here';
const DB_USER     = 'username_here';
const DB_PASSWORD = 'password_here';
const DB_HOST     = 'localhost';
const DB_CHARSET  = 'utf8';
const DB_COLLATE  = '';

// Authentication Unique Keys and Salts
const AUTH_KEY         = 'put your unique phrase here';
const SECURE_AUTH_KEY  = 'put your unique phrase here';
const LOGGED_IN_KEY    = 'put your unique phrase here';
const NONCE_KEY        = 'put your unique phrase here';
const AUTH_SALT        = 'put your unique phrase here';
const SECURE_AUTH_SALT = 'put your unique phrase here';
const LOGGED_IN_SALT   = 'put your unique phrase here';
const NONCE_SALT       = 'put your unique phrase here';

/**
 * WP Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WP debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
const WP_DEBUG = false;

/* That's all, stop editing! Happy crafting! */

/** Absolute path to the WP directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WP vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
