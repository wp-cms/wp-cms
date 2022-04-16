<?php

namespace Core\ClonePosts;

/**
 * This will run during plugin activation
 */
function activate_clone_posts() {
    update_option('clone_posts_post_status', 'draft');
    update_option('clone_posts_post_date', 'current');
    update_option('clone_posts_post_type', ['post']);
}

register_activation_hook( __FILE__, 'activate_clone_posts' );