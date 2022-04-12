<?php

if(!defined('WP_UNINSTALL_PLUGIN')) {
  die('You are not allowed to call this page directly.');
}

$users = get_users();

foreach($users as $user) {
    delete_user_meta($user->ID, 'user_avatar');
}

delete_option('wpua_default_avatar');

// Reset all default avatars to Mystery Man
update_option('avatar_default', 'mystery');
