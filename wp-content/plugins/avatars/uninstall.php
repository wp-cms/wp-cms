<?php

// Die if WP_UNINSTALL_PLUGIN isn't defined
if(!defined('WP_UNINSTALL_PLUGIN')) {
  die('You are not allowed to call this page directly.');
}

// Delete user_avatar metadata for all users
$users = get_users();
foreach($users as $user) {
    delete_user_meta($user->ID, 'user_avatar');
}

// Delete the custom default avatar
delete_option('wpua_default_avatar');

// Reset all default avatars to Mystery Man
update_option('avatar_default', 'mystery');
