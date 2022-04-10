<?php
/*
Plugin Name: WP User Avatar
Description: Use any image from your WP Media Library as a custom user avatar. Add your own Default Avatar.
Version: 2.0.2
Text Domain: wp-user-avatar
Domain Path: /lang/
*/

if(!defined('ABSPATH')) {
  die('You are not allowed to call this page directly.');
}

/**
 * Let's get started!
 */
class WP_User_Avatar_Setup {
  /**
   * Constructor
   * @since 1.9.2
   */
  public function __construct() {
    $this->_define_constants();
    $this->_load_wpua();
  }

  /**
   * Define paths
   * @since 1.9.2
   */
  private function _define_constants() {
    define('WPUA_VERSION', '2.0.2');
    define('WPUA_FOLDER', basename(dirname(__FILE__)));
    define('WPUA_DIR', plugin_dir_path(__FILE__));
    define('WPUA_INC', WPUA_DIR.'includes'.'/');
    define('WPUA_URL', plugin_dir_url(WPUA_FOLDER).WPUA_FOLDER.'/');
    define('WPUA_INC_URL', WPUA_URL.'includes'.'/');
  }

  /**
   * Load WP User Avatar
   * @since 1.9.2
   * @uses is_admin()
   */
  private function _load_wpua() {
    require_once(WPUA_INC.'wpua-globals.php');
    require_once(WPUA_INC.'wpua-functions.php');
    require_once(WPUA_INC.'class-wp-user-avatar-admin.php');
    require_once(WPUA_INC.'class-wp-user-avatar.php');
    require_once(WPUA_INC.'class-wp-user-avatar-functions.php');
    require_once(WPUA_INC.'class-wp-user-avatar-shortcode.php');
    require_once(WPUA_INC.'class-wp-user-avatar-subscriber.php');
    require_once(WPUA_INC.'class-wp-user-avatar-update.php');
    require_once(WPUA_INC.'class-wp-user-avatar-widget.php');
  }
}

/**
 * Initialize
 */
new WP_User_Avatar_Setup();
