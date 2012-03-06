<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'lb');

/** MySQL database username */
define('DB_USER', 'lb');

/** MySQL database password */
define('DB_PASSWORD', '12qwaszx');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'C.#&kUab6SmVsF)okbi?az&O>Mqu=Ni!fgn3l5m8d=cMEoCGU+=4O,LUZ8[x:0_U');
define('SECURE_AUTH_KEY',  'tKA^i<>:Y2GcDbIxRzRt^k8&V,Dvu4o2[4&~$0h8-h;7MbEig]/:}DFg[2laq@ke');
define('LOGGED_IN_KEY',    'QD3eH/Gm;n=u[)qi +9pTg52rA$4|Q0+8oNd;TS8@Gl2{N|^YQAJ*}q`L6=61HsF');
define('NONCE_KEY',        'h-elg#B0Nx.hW>jzovD,Yx8!Ha!#I(V+~$C=L1(jg/py},@~?GKBrEwBf0i`toAx');
define('AUTH_SALT',        '9vP.A4-x35{Ir3ux)zjy/gqAyp8:kAMX5iX}xCt?|_T.Jpfp7C`6uk@`|O6gI[W]');
define('SECURE_AUTH_SALT', '|Se-05WZaO(dpFKPSrh(E$f/I-Q{B:5|[oubY+WxA.}D?]7w?{^N~7&_.70HuYJ<');
define('LOGGED_IN_SALT',   'bG*dwv]]-}dWu]ud`dy*!%$NPHDp=78oW$g,$[ou.XPc1h/&r75bvE-Z.1ZH:]gb');
define('NONCE_SALT',       'xzR%j<pGXj81, ]9[|Ju^nEL;*^RqjeU-Z$P}oxs{pjBJl4Z7jdwzf_^7i!^)0;&');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
