<?php
/**
 * Deprecated Multisite functions from past versions. You shouldn't use these
 * functions and look for the alternatives instead. The functions will be removed
 * in a later version.
 *
 * Deprecated functions come here to die.
 *
 * EXAMPLE:

    function get_dashboard_blog() {
    _deprecated_function( __FUNCTION__, 'WP-3.1.0', 'get_site()' );
    if ( $blog = get_site_option( 'dashboard_blog' ) ) {
    return get_site( $blog );
    }

    return get_site( get_network()->site_id );
    }

 */
