<?php
/**
 * The base configuration for ClassicPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package ClassicPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for ClassicPress */
define('DB_NAME', 'classicpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.classicpress.net/secret-key/1.0/salt/ ClassicPress.net secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since WP-2.6.0
 */
define('AUTH_KEY',         '/(Jd)q_]GP2d5laTX~_/G8+r+f:E^;~<Bg(m^gg(EoHs^^LY22WMEjlT~Hg=`@+7');
define('SECURE_AUTH_KEY',  'rf&W)4Cx+Z2c*:xpG&QLZrrQ]TjPHy,olDG[OV7H:+N/t@XdpqG^`D5x{9N6iFj4');
define('LOGGED_IN_KEY',    '??h-6@aSV?ejMzPF5I~t >6ZH$Lu1!FfeLI(R]olm$CtFkU2b;]9}MQ-Y$`y@khf');
define('NONCE_KEY',        '#z8p3o2WX}%&Ylrl5Y)`Mb>y=0JJd*0Dvv=D;>l,BE:(#Ne%d 8p}+K:4iOo/++`');
define('AUTH_SALT',        'pyA_:7>7VhZ&R95?x@ylv5Bb35`gd.hGD^F%Uq4nMgQuP8MM:c*2tbR1|,^2o]4O');
define('SECURE_AUTH_SALT', '3KW-}4+sev5-q4s{F=_ynXf(o=vSr/h{2wC$O.sCt1&}@IuKS5e?@8qQJB@utu]b');
define('LOGGED_IN_SALT',   'Ha8lB,tm&-JK9nLT(qZp6Upsl[98I2pw]`YxQQ<cLs)$U!CW%MDYKIuG[PNsajD*');
define('NONCE_SALT',       'Uce}WY)HsVwBlaSo||W[)AsybsrGaMX}l1LHIzU,8m7-.m4K(=[^G-Mb6zk%x%Cm');

/**#@-*/

/**
 * ClassicPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'cp_';

/**
 * For developers: ClassicPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the ClassicPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up ClassicPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
