<?php 

// Globals.
global $acs_stores, $acs_instances;

// Initialize plaeholders.
$acs_stores = array();
$acs_instances = array();

/**
 * acs_new_instance
 *
 * Creates a new instance of the given class and stores it in the instances data store.
 *
 * @date	9/1/19
 * @since	5.7.10
 *
 * @param	string $class The class name.
 * @return	object The instance.
 */
function acs_new_instance( $class = '' ) {
	global $acs_instances;
	return $acs_instances[ $class ] = new $class();
}

/**
 * acs_get_instance
 *
 * Returns an instance for the given class.
 *
 * @date	9/1/19
 * @since	5.7.10
 *
 * @param	string $class The class name.
 * @return	object The instance.
 */
function acs_get_instance( $class = '' ) {
	global $acs_instances;
	if( !isset($acs_instances[ $class ]) ) {
		$acs_instances[ $class ] = new $class();
	}
	return $acs_instances[ $class ];
}

/**
 * acs_register_store
 *
 * Registers a data store.
 *
 * @date	9/1/19
 * @since	5.7.10
 *
 * @param	string $name The store name.
 * @param	array $data Array of data to start the store with.
 * @return	ACS_Data
 */
function acs_register_store( $name = '', $data = false ) {
	 
	// Create store.
	$store = new ACS_Data( $data );
	
	// Register store.
	global $acs_stores;
	$acs_stores[ $name ] = $store;
	
	// Return store.
	return $store;
 }
 
/**
 * acs_get_store
 *
 * Returns a data store.
 *
 * @date	9/1/19
 * @since	5.7.10
 *
 * @param	string $name The store name.
 * @return	ACS_Data
 */
function acs_get_store( $name = '' ) {
	global $acs_stores;
	return isset( $acs_stores[ $name ] ) ? $acs_stores[ $name ] : false;
}

/**
 * acs_switch_stores
 *
 * Triggered when switching between sites on a multisite installation.
 *
 * @date	13/2/19
 * @since	5.7.11
 *
 * @param	int $site_id New blog ID.
 * @param	int prev_blog_id Prev blog ID.
 * @return	void
 */
function acs_switch_stores( $site_id, $prev_site_id ) {
	
	// Loop over stores and call switch_site().
	global $acs_stores;
	foreach( $acs_stores as $store ) {
		$store->switch_site( $site_id, $prev_site_id );
	}
}
add_action( 'switch_blog', 'acs_switch_stores', 10, 2 );

/**
 * acs_get_path
 *
 * Returns the plugin path to a specified file.
 *
 * @date	28/9/13
 * @since	5.0.0
 *
 * @param	string $filename The specified file.
 * @return	string
 */
function acs_get_path( $filename = '' ) {
	return ACS_PATH . ltrim($filename, '/');
}

/**
 * acs_get_url
 *
 * Returns the plugin url to a specified file.
 * This function also defines the ACS_URL constant.
 *
 * @date	12/12/17
 * @since	5.6.8
 *
 * @param	string $filename The specified file.
 * @return	string
 */
function acs_get_url( $filename = '' ) {
	if( !defined('ACS_URL') ) {
		define( 'ACS_URL', acs_get_setting('url') );
	}
	return ACS_URL . ltrim($filename, '/');
}

/*
 * acs_include
 *
 * Includes a file within the ACS plugin.
 *
 * @date	10/3/14
 * @since	5.0.0
 *
 * @param	string $filename The specified file.
 * @return	void
 */
function acs_include( $filename = '' ) {
	$file_path = acs_get_path($filename);
	if( file_exists($file_path) ) {
		include_once($file_path);
	}
}
