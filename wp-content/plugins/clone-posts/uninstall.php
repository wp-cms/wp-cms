<?php

namespace Core\ClonePosts;

// Die if WP_UNINSTALL_PLUGIN isn't defined
if(!defined('WP_UNINSTALL_PLUGIN')) {
    die('You are not allowed to call this page directly.');
}

// If we are on a multisite installation clean up all sub-sites
if ( is_multisite() ) {

	foreach (get_sites(['fields'=>'ids']) as $blog_id) {
		switch_to_blog($blog_id);
		cleanup_settings_data();
		restore_current_blog();
	}

} else {
	cleanup_settings_data();
}

function cleanup_settings_data(){

	// Plugin options
	$options = array(
		'clone_posts_post_status',
		'clone_posts_post_date',
		'clone_posts_post_type',
	);

	// Loop through each option
	foreach ( $options as $option ) {
		delete_option( $option );
	}

}
