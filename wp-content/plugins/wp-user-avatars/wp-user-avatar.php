<?php
/*
Plugin Name: WP User Avatar
Description: Autogenerate letter avatar, upload a default custom image and allow users to upload their own.
Version: 2.0.2
Text Domain: wp-user-avatar
Domain Path: /languages/
*/

namespace Core\UserAvatar;

if(!defined('ABSPATH')) {
  die('You are not allowed to call this page directly.');
}

// Functions used by backend and frontend
require_once(plugin_dir_path( __FILE__ ).'includes/general-functions.php');

// Filter the native WP get_avatar() result
add_filter('get_avatar', 'Core\UserAvatar\get_avatar_filter', 10, 5);

// Setup Admin Area hooks and callbacks
if(is_admin()) {
    require_once(plugin_dir_path( __FILE__ ).'includes/admin-functions.php');
    add_action('init', 'Core\UserAvatar\setup_admin_area');
}