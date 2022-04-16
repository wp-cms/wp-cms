<?php

namespace Core\ClonePosts;

/**
 * This will load the current plugin textdomain
 */
function load_textdomain() {
    load_plugin_textdomain('clone-posts', 'clone-posts/languages');
}

add_action( 'plugins_loaded', 'Core\ClonePosts\load_textdomain' );