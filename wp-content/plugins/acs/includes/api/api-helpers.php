<?php 

/*
*  acs_is_array
*
*  This function will return true for a non empty array
*
*  @type	function
*  @date	6/07/2016
*  @since	5.4.0
*
*  @param	$array (array)
*  @return	(boolean)
*/

function acs_is_array( $array ) {
	
	return ( is_array($array) && !empty($array) );
	
}

/**
*  acs_has_setting
*
*  alias of acs()->has_setting()
*
*  @date	2/2/18
*  @since	5.6.5
*
*  @param	n/a
*  @return	n/a
*/

function acs_has_setting( $name = '' ) {
	return acs()->has_setting( $name );
}


/**
*  acs_raw_setting
*
*  alias of acs()->get_setting()
*
*  @date	2/2/18
*  @since	5.6.5
*
*  @param	n/a
*  @return	n/a
*/

function acs_raw_setting( $name = '' ) {
	return acs()->get_setting( $name );
}


/*
*  acs_update_setting
*
*  alias of acs()->update_setting()
*
*  @type	function
*  @date	28/09/13
*  @since	5.0.0
*
*  @param	$name (string)
*  @param	$value (mixed)
*  @return	n/a
*/

function acs_update_setting( $name, $value ) {
	
	// validate name
	$name = acs_validate_setting( $name );
	
	// update
	return acs()->update_setting( $name, $value );
}


/**
*  acs_validate_setting
*
*  Returns the changed setting name if available.
*
*  @date	2/2/18
*  @since	5.6.5
*
*  @param	n/a
*  @return	n/a
*/

function acs_validate_setting( $name = '' ) {
	return apply_filters( "acs/validate_setting", $name );
}


/*
*  acs_get_setting
*
*  alias of acs()->get_setting()
*
*  @type	function
*  @date	28/09/13
*  @since	5.0.0
*
*  @param	n/a
*  @return	n/a
*/

function acs_get_setting( $name, $value = null ) {
	
	// validate name
	$name = acs_validate_setting( $name );
	
	// check settings
	if( acs_has_setting($name) ) {
		$value = acs_raw_setting( $name );
	}
	
	// filter
	$value = apply_filters( "acs/settings/{$name}", $value );
	
	// return
	return $value;
}


/*
*  acs_append_setting
*
*  This function will add a value into the settings array found in the acs object
*
*  @type	function
*  @date	28/09/13
*  @since	5.0.0
*
*  @param	$name (string)
*  @param	$value (mixed)
*  @return	n/a
*/

function acs_append_setting( $name, $value ) {
	
	// vars
	$setting = acs_raw_setting( $name );
	
	// bail ealry if not array
	if( !is_array($setting) ) {
		$setting = array();
	}
	
	// append
	$setting[] = $value;
	
	// update
	return acs_update_setting( $name, $setting );
}


/**
*  acs_get_data
*
*  Returns data.
*
*  @date	28/09/13
*  @since	5.0.0
*
*  @param	string $name
*  @return	mixed
*/

function acs_get_data( $name ) {
	return acs()->get_data( $name );
}


/**
*  acs_set_data
*
*  Sets data.
*
*  @date	28/09/13
*  @since	5.0.0
*
*  @param	string $name
*  @param	mixed $value
*  @return	n/a
*/

function acs_set_data( $name, $value ) {
	return acs()->set_data( $name, $value );
}

/**
 * Appends data to an existing key.
 *
 * @date	11/06/2020
 * @since	5.9.0
 *
 * @param	string $name The data name.
 * @return	array $data The data array.
 */
function acs_append_data( $name, $data ) {
	$prev_data = acs()->get_data( $name );
	if( is_array($prev_data) ) {
		$data = array_merge( $prev_data, $data );
	}
	acs()->set_data( $name, $data );
}

/*
*  acs_init
*
*  alias of acs()->init()
*
*  @type	function
*  @date	28/09/13
*  @since	5.0.0
*
*  @param	n/a
*  @return	n/a
*/

function acs_init() {
	
	acs()->init();
	
}


/*
*  acs_has_done
*
*  This function will return true if this action has already been done
*
*  @type	function
*  @date	16/12/2015
*  @since	5.3.2
*
*  @param	$name (string)
*  @return	(boolean)
*/

function acs_has_done( $name ) {
	
	// return true if already done
	if( acs_raw_setting("has_done_{$name}") ) {
		return true;
	}
	
	// update setting and return
	acs_update_setting("has_done_{$name}", true);
	return false;
}




/*
*  acs_get_external_path
*
*  This function will return the path to a file within an external folder
*
*  @type	function
*  @date	22/2/17
*  @since	5.5.8
*
*  @param	$file (string)
*  @param	$path (string)
*  @return	(string)
*/

function acs_get_external_path( $file, $path = '' ) {
    
    return plugin_dir_path( $file ) . $path;
    
}


/*
*  acs_get_external_dir
*
*  This function will return the url to a file within an external folder
*
*  @type	function
*  @date	22/2/17
*  @since	5.5.8
*
*  @param	$file (string)
*  @param	$path (string)
*  @return	(string)
*/

function acs_get_external_dir( $file, $path = '' ) {
    
    return acs_plugin_dir_url( $file ) . $path;
	
}


/**
*  acs_plugin_dir_url
*
*  This function will calculate the url to a plugin folder.
*  Different to the WP plugin_dir_url(), this function can calculate for urls outside of the plugins folder (theme include).
*
*  @date	13/12/17
*  @since	5.6.8
*
*  @param	type $var Description. Default.
*  @return	type Description.
*/

function acs_plugin_dir_url( $file ) {
	
	// vars
	$path = plugin_dir_path( $file );
	$path = wp_normalize_path( $path );
	
	
	// check plugins
	$check_path = wp_normalize_path( realpath(WP_PLUGIN_DIR) );
	if( strpos($path, $check_path) === 0 ) {
		return str_replace( $check_path, plugins_url(), $path );
	}
	
	// check wp-content
	$check_path = wp_normalize_path( realpath(WP_CONTENT_DIR) );
	if( strpos($path, $check_path) === 0 ) {
		return str_replace( $check_path, content_url(), $path );
	}
	
	// check root
	$check_path = wp_normalize_path( realpath(ABSPATH) );
	if( strpos($path, $check_path) === 0 ) {
		return str_replace( $check_path, site_url('/'), $path );
	}
	                
    
    // return
    return plugin_dir_url( $file );
    
}


/*
*  acs_parse_args
*
*  This function will merge together 2 arrays and also convert any numeric values to ints
*
*  @type	function
*  @date	18/10/13
*  @since	5.0.0
*
*  @param	$args (array)
*  @param	$defaults (array)
*  @return	$args (array)
*/

function acs_parse_args( $args, $defaults = array() ) {
	
	// parse args
	$args = wp_parse_args( $args, $defaults );
	
	
	// parse types
	$args = acs_parse_types( $args );
	
	
	// return
	return $args;
	
}


/*
*  acs_parse_types
*
*  This function will convert any numeric values to int and trim strings
*
*  @type	function
*  @date	18/10/13
*  @since	5.0.0
*
*  @param	$var (mixed)
*  @return	$var (mixed)
*/

function acs_parse_types( $array ) {
	return array_map( 'acs_parse_type', $array );
}


/*
*  acs_parse_type
*
*  description
*
*  @type	function
*  @date	11/11/2014
*  @since	5.0.9
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function acs_parse_type( $v ) {
	
	// Check if is string.
	if( is_string($v) ) {
		
		// Trim ("Word " = "Word").
		$v = trim( $v );
		
		// Convert int strings to int ("123" = 123).
		if( is_numeric($v) && strval(intval($v)) === $v ) {
			$v = intval( $v );
		}
	}
	
	// return.
	return $v;
}


/*
*  acs_get_view
*
*  This function will load in a file from the 'admin/views' folder and allow variables to be passed through
*
*  @type	function
*  @date	28/09/13
*  @since	5.0.0
*
*  @param	$view_name (string)
*  @param	$args (array)
*  @return	n/a
*/

function acs_get_view( $path = '', $args = array() ) {
	
	// allow view file name shortcut
	if( substr($path, -4) !== '.php' ) {
		
		$path = acs_get_path("includes/admin/views/{$path}.php");
		
	}
	
	
	// include
	if( file_exists($path) ) {
		
		extract( $args );
		include( $path );
		
	}
	
}


/*
*  acs_merge_atts
*
*  description
*
*  @type	function
*  @date	2/11/2014
*  @since	5.0.9
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function acs_merge_atts( $atts, $extra = array() ) {
	
	// bail ealry if no $extra
	if( empty($extra) ) return $atts;
	
	
	// trim
	$extra = array_map('trim', $extra);
	$extra = array_filter($extra);
	
	
	// merge in new atts
	foreach( $extra as $k => $v ) {
		
		// append
		if( $k == 'class' || $k == 'style' ) {
			
			$atts[ $k ] .= ' ' . $v;
		
		// merge	
		} else {
			
			$atts[ $k ] = $v;
			
		}
		
	}
	
	
	// return
	return $atts;
	
}


/*
*  acs_nonce_input
*
*  This function will create a basic nonce input
*
*  @type	function
*  @date	24/5/17
*  @since	5.6.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function acs_nonce_input( $nonce = '' ) {
	
	echo '<input type="hidden" name="_acs_nonce" value="' . wp_create_nonce( $nonce ) . '" />';
	
}


/*
*  acs_extract_var
*
*  This function will remove the var from the array, and return the var
*
*  @type	function
*  @date	2/10/13
*  @since	5.0.0
*
*  @param	$array (array)
*  @param	$key (string)
*  @return	(mixed)
*/

function acs_extract_var( &$array, $key, $default = null ) {
	
	// check if exists
	// - uses array_key_exists to extract NULL values (isset will fail)
	if( is_array($array) && array_key_exists($key, $array) ) {
		
		// store value
		$v = $array[ $key ];
		
		
		// unset
		unset( $array[ $key ] );
		
		
		// return
		return $v;
		
	}
	
	
	// return
	return $default;
}


/*
*  acs_extract_vars
*
*  This function will remove the vars from the array, and return the vars
*
*  @type	function
*  @date	8/10/13
*  @since	5.0.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function acs_extract_vars( &$array, $keys ) {
	
	$r = array();
	
	foreach( $keys as $key ) {
		
		$r[ $key ] = acs_extract_var( $array, $key );
		
	}
	
	return $r;
}


/*
*  acs_get_sub_array
*
*  This function will return a sub array of data
*
*  @type	function
*  @date	15/03/2016
*  @since	5.3.2
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function acs_get_sub_array( $array, $keys ) {
	
	$r = array();
	
	foreach( $keys as $key ) {
		
		$r[ $key ] = $array[ $key ];
		
	}
	
	return $r;
	
}


/**
*  acs_get_post_types
*
*  Returns an array of post type names.
*
*  @date	7/10/13
*  @since	5.0.0
*
*  @param	array $args Optional. An array of key => value arguments to match against the post type objects. Default empty array.
*  @return	array A list of post type names.
*/

function acs_get_post_types( $args = array() ) {
	
	// vars
	$post_types = array();
	
	// extract special arg
	$exclude = acs_extract_var( $args, 'exclude', array() );
	$exclude[] = 'acs-field';
	$exclude[] = 'acs-field-group';
	
	// get post type objects
	$objects = get_post_types( $args, 'objects' );
	
	// loop
	foreach( $objects as $i => $object ) {
		
		// bail early if is exclude
		if( in_array($i, $exclude) ) continue;
		
		// bail early if is builtin (WP) private post type
		// - nav_menu_item, revision, customize_changeset, etc
		if( $object->_builtin && !$object->public ) continue;
		
		// append
		$post_types[] = $i;
	}
	
	// filter
	$post_types = apply_filters('acs/get_post_types', $post_types, $args);
	
	// return
	return $post_types;
}

function acs_get_pretty_post_types( $post_types = array() ) {
	
	// get post types
	if( empty($post_types) ) {
		
		// get all custom post types
		$post_types = acs_get_post_types();
		
	}
	
	
	// get labels
	$ref = array();
	$r = array();
	
	foreach( $post_types as $post_type ) {
		
		// vars
		$label = acs_get_post_type_label($post_type);
		
		
		// append to r
		$r[ $post_type ] = $label;
		
		
		// increase counter
		if( !isset($ref[ $label ]) ) {
			
			$ref[ $label ] = 0;
			
		}
		
		$ref[ $label ]++;
	}
	
	
	// get slugs
	foreach( array_keys($r) as $i ) {
		
		// vars
		$post_type = $r[ $i ];
		
		if( $ref[ $post_type ] > 1 ) {
			
			$r[ $i ] .= ' (' . $i . ')';
			
		}
		
	}
	
	
	// return
	return $r;
	
}



/*
*  acs_get_post_type_label
*
*  This function will return a pretty label for a specific post_type
*
*  @type	function
*  @date	5/07/2016
*  @since	5.4.0
*
*  @param	$post_type (string)
*  @return	(string)
*/

function acs_get_post_type_label( $post_type ) {
	
	// vars
	$label = $post_type;
	
		
	// check that object exists
	// - case exists when importing field group from another install and post type does not exist
	if( post_type_exists($post_type) ) {
		
		$obj = get_post_type_object($post_type);
		$label = $obj->labels->singular_name;
		
	}
	
	
	// return
	return $label;
	
}


/*
*  acs_verify_nonce
*
*  This function will look at the $_POST['_acs_nonce'] value and return true or false
*
*  @type	function
*  @date	15/10/13
*  @since	5.0.0
*
*  @param	$nonce (string)
*  @return	(boolean)
*/

function acs_verify_nonce( $value) {
	
	// vars
	$nonce = acs_maybe_get_POST('_acs_nonce');
	
	
	// bail early nonce does not match (post|user|comment|term)
	if( !$nonce || !wp_verify_nonce($nonce, $value) ) return false;
	
	
	// reset nonce (only allow 1 save)
	$_POST['_acs_nonce'] = false;
	
	
	// return
	return true;
		
}


/*
*  acs_verify_ajax
*
*  This function will return true if the current AJAX request is valid
*  It's action will also allow WPML to set the lang and avoid AJAX get_posts issues
*
*  @type	function
*  @date	7/08/2015
*  @since	5.2.3
*
*  @param	n/a
*  @return	(boolean)
*/

function acs_verify_ajax() {
	
	// vars
	$nonce = isset($_REQUEST['nonce']) ? $_REQUEST['nonce'] : '';
	
	// bail early if not acs nonce
	if( !$nonce || !wp_verify_nonce($nonce, 'acs_nonce') ) {
		return false;
	}
	
	// action for 3rd party customization
	do_action('acs/verify_ajax');
	
	// return
	return true;
}


/*
*  acs_get_image_sizes
*
*  This function will return an array of available image sizes
*
*  @type	function
*  @date	23/10/13
*  @since	5.0.0
*
*  @param	n/a
*  @return	(array)
*/

function acs_get_image_sizes() {
	
	// vars
	$sizes = array(
		'thumbnail'	=>	__("Thumbnail",'acs'),
		'medium'	=>	__("Medium",'acs'),
		'large'		=>	__("Large",'acs')
	);
	
	
	// find all sizes
	$all_sizes = get_intermediate_image_sizes();
	
	
	// add extra registered sizes
	if( !empty($all_sizes) ) {
		
		foreach( $all_sizes as $size ) {
			
			// bail early if already in array
			if( isset($sizes[ $size ]) ) {
			
				continue;
				
			}
			
			
			// append to array
			$label = str_replace('-', ' ', $size);
			$label = ucwords( $label );
			$sizes[ $size ] = $label;
			
		}
		
	}
	
	
	// add sizes
	foreach( array_keys($sizes) as $s ) {
		
		// vars
		$data = acs_get_image_size($s);
		
		
		// append
		if( $data['width'] && $data['height'] ) {
			
			$sizes[ $s ] .= ' (' . $data['width'] . ' x ' . $data['height'] . ')';
			
		}
		
	}
	
	
	// add full end
	$sizes['full'] = __("Full Size",'acs');
	
	
	// filter for 3rd party customization
	$sizes = apply_filters( 'acs/get_image_sizes', $sizes );
	
	
	// return
	return $sizes;
	
}

function acs_get_image_size( $s = '' ) {
	
	// global
	global $_wp_additional_image_sizes;
	
	
	// rename for nicer code
	$_sizes = $_wp_additional_image_sizes;
	
	
	// vars
	$data = array(
		'width' 	=> isset($_sizes[$s]['width']) ? $_sizes[$s]['width'] : get_option("{$s}_size_w"),
		'height'	=> isset($_sizes[$s]['height']) ? $_sizes[$s]['height'] : get_option("{$s}_size_h")
	);
	
	
	// return
	return $data;
		
}

/**
 * acs_version_compare
 *
 * Similar to the version_compare() function but with extra functionality.
 *
 * @date	21/11/16
 * @since	5.5.0
 *
 * @param	string $left The left version number.
 * @param	string $compare The compare operator.
 * @param	string $right The right version number.
 * @return	bool
 */
function acs_version_compare( $left = '', $compare = '>', $right = '' ) {
	
	// Detect 'wp' placeholder.
	if( $left === 'wp' ) {
		global $wp_version;
		$left = $wp_version;
	}
	
	// Return result.
	return version_compare( $left, $right, $compare );
}


/*
*  acs_get_full_version
*
*  This function will remove any '-beta1' or '-RC1' strings from a version
*
*  @type	function
*  @date	24/11/16
*  @since	5.5.0
*
*  @param	$version (string)
*  @return	(string)
*/

function acs_get_full_version( $version = '1' ) {
	
	// remove '-beta1' or '-RC1'
	if( $pos = strpos($version, '-') ) {
		
		$version = substr($version, 0, $pos);
		
	}
	
	
	// return
	return $version;
	
}


/*
*  acs_get_terms
*
*  This function is a wrapper for the get_terms() function
*
*  @type	function
*  @date	28/09/2016
*  @since	5.4.0
*
*  @param	$args (array)
*  @return	(array)
*/

function acs_get_terms( $args ) {
	
	// defaults
	$args = wp_parse_args($args, array(
		'taxonomy'					=> null,
		'hide_empty'				=> false,
		'update_term_meta_cache'	=> false,
	));
	
	// parameters changed in version 4.5
	if( acs_version_compare('wp', '<', '4.5') ) {
		return get_terms( $args['taxonomy'], $args );
	}
	
	// return
	return get_terms( $args );
}


/*
*  acs_get_taxonomy_terms
*
*  This function will return an array of available taxonomy terms
*
*  @type	function
*  @date	7/10/13
*  @since	5.0.0
*
*  @param	$taxonomies (array)
*  @return	(array)
*/

function acs_get_taxonomy_terms( $taxonomies = array() ) {
	
	// force array
	$taxonomies = acs_get_array( $taxonomies );
	
	
	// get pretty taxonomy names
	$taxonomies = acs_get_pretty_taxonomies( $taxonomies );
	
	
	// vars
	$r = array();
	
	
	// populate $r
	foreach( array_keys($taxonomies) as $taxonomy ) {
		
		// vars
		$label = $taxonomies[ $taxonomy ];
		$is_hierarchical = is_taxonomy_hierarchical( $taxonomy );
		$terms = acs_get_terms(array(
			'taxonomy'		=> $taxonomy,
			'hide_empty' 	=> false
		));
		
		
		// bail early i no terms
		if( empty($terms) ) continue;
		
		
		// sort into hierachial order!
		if( $is_hierarchical ) {
			
			$terms = _get_term_children( 0, $terms, $taxonomy );
			
		}
		
		
		// add placeholder		
		$r[ $label ] = array();
		
		
		// add choices
		foreach( $terms as $term ) {
		
			$k = "{$taxonomy}:{$term->slug}"; 
			$r[ $label ][ $k ] = acs_get_term_title( $term );
			
		}
		
	}
		
	
	// return
	return $r;
	
}


/*
*  acs_decode_taxonomy_terms
*
*  This function decodes the $taxonomy:$term strings into a nested array
*
*  @type	function
*  @date	27/02/2014
*  @since	5.0.0
*
*  @param	$terms (array)
*  @return	(array)
*/

function acs_decode_taxonomy_terms( $strings = false ) {
	
	// bail early if no terms
	if( empty($strings) ) return false;
	
	
	// vars
	$terms = array();
	
	
	// loop
	foreach( $strings as $string ) {
		
		// vars
		$data = acs_decode_taxonomy_term( $string );
		$taxonomy = $data['taxonomy'];
		$term = $data['term'];
		
		
		// create empty array
		if( !isset($terms[ $taxonomy ]) ) {
			
			$terms[ $taxonomy ] = array();
			
		}
				
		
		// append
		$terms[ $taxonomy ][] = $term;
		
	}
	
	
	// return
	return $terms;
	
}


/*
*  acs_decode_taxonomy_term
*
*  This function will return the taxonomy and term slug for a given value
*
*  @type	function
*  @date	31/03/2014
*  @since	5.0.0
*
*  @param	$string (string)
*  @return	(array)
*/

function acs_decode_taxonomy_term( $value ) {
	
	// vars
	$data = array(
		'taxonomy'	=> '',
		'term'		=> ''
	);
	
	
	// int
	if( is_numeric($value) ) {
		
		$data['term'] = $value;
			
	// string
	} elseif( is_string($value) ) {
		
		$value = explode(':', $value);
		$data['taxonomy'] = isset($value[0]) ? $value[0] : '';
		$data['term'] = isset($value[1]) ? $value[1] : '';
		
	// error	
	} else {
		
		return false;
		
	}
	
	
	// allow for term_id (Used by ACS v4)
	if( is_numeric($data['term']) ) {
		
		// global
		global $wpdb;
		
		
		// find taxonomy
		if( !$data['taxonomy'] ) {
			
			$data['taxonomy'] = $wpdb->get_var( $wpdb->prepare("SELECT taxonomy FROM $wpdb->term_taxonomy WHERE term_id = %d LIMIT 1", $data['term']) );
			
		}
		
		
		// find term (may have numeric slug '123')
		$term = get_term_by( 'slug', $data['term'], $data['taxonomy'] );
		
		
		// attempt get term via ID (ACS4 uses ID)
		if( !$term ) $term = get_term( $data['term'], $data['taxonomy'] );
		
		
		// bail early if no term
		if( !$term ) return false;
		
		
		// update
		$data['taxonomy'] = $term->taxonomy;
		$data['term'] = $term->slug;
		
	}
	
	
	// return
	return $data;
	
}

/**
 * acs_array
 *
 * Casts the value into an array.
 *
 * @date	9/1/19
 * @since	5.7.10
 *
 * @param	mixed $val The value to cast.
 * @return	array
 */
function acs_array( $val = array() ) {
	return (array) $val;
}

/**
 * Returns a non-array value.
 *
 * @date	11/05/2020
 * @since	5.8.10
 *
 * @param	mixed $val The value to review.
 * @return	mixed
 */
function acs_unarray( $val ) {
	if( is_array( $val ) ) {
		return reset( $val );
	}
	return $val;
}

/*
*  acs_get_array
*
*  This function will force a variable to become an array
*
*  @type	function
*  @date	4/02/2014
*  @since	5.0.0
*
*  @param	$var (mixed)
*  @return	(array)
*/

function acs_get_array( $var = false, $delimiter = '' ) {
	
	// array
	if( is_array($var) ) {
		return $var;
	}
	
	
	// bail early if empty
	if( acs_is_empty($var) ) {
		return array();
	}
	
	
	// string 
	if( is_string($var) && $delimiter ) {
		return explode($delimiter, $var);
	}
	
	
	// place in array
	return (array) $var;
	
}


/*
*  acs_get_numeric
*
*  This function will return numeric values
*
*  @type	function
*  @date	15/07/2016
*  @since	5.4.0
*
*  @param	$value (mixed)
*  @return	(mixed)
*/

function acs_get_numeric( $value = '' ) {
	
	// vars
	$numbers = array();
	$is_array = is_array($value);
	
	
	// loop
	foreach( (array) $value as $v ) {
		
		if( is_numeric($v) ) $numbers[] = (int) $v;
		
	}
	
	
	// bail early if is empty
	if( empty($numbers) ) return false;
	
	
	// convert array
	if( !$is_array ) $numbers = $numbers[0];
	
	
	// return
	return $numbers;
	
}


/**
 * acs_get_posts
 *
 * Similar to the get_posts() function but with extra functionality.
 *
 * @date	3/03/15
 * @since	5.1.5
 *
 * @param	array $args The query args.
 * @return	array
 */
function acs_get_posts( $args = array() ) {
	
	// Vars.
	$posts = array();
	
	// Apply default args.
	$args = wp_parse_args($args, array(
		'posts_per_page'			=> -1,
		'post_type'					=> '',
		'post_status'				=> 'any',
		'update_post_meta_cache'	=> false,
		'update_post_term_cache' 	=> false
	));
	
	// Avoid default 'post' post_type by providing all public types.
	if( !$args['post_type'] ) {
		$args['post_type'] = acs_get_post_types();
	}
	
	// Check if specifc post ID's have been provided.
	if( $args['post__in'] ) {
		
		// Clean value into an array of IDs.
		$args['post__in'] = array_map('intval', acs_array($args['post__in']));
	}
	
	// Query posts.
	$posts = get_posts( $args );
	
	// Remove any potential empty results.
	$posts = array_filter( $posts );
	
	// Manually order results.
	if( $posts && $args['post__in'] ) {
		$order = array();
		foreach( $posts as $i => $post ) {
			$order[ $i ] = array_search( $post->ID, $args['post__in'] );
		}
		array_multisort($order, $posts);
	}
	
	// Return posts.
	return $posts;
}


/*
*  _acs_query_remove_post_type
*
*  This function will remove the 'wp_posts.post_type' WHERE clause completely
*  When using 'post__in', this clause is unneccessary and slow.
*
*  @type	function
*  @date	4/03/2015
*  @since	5.1.5
*
*  @param	$sql (string)
*  @return	$sql
*/

function _acs_query_remove_post_type( $sql ) {
	
	// global
	global $wpdb;
	
	
	// bail ealry if no 'wp_posts.ID IN'
	if( strpos($sql, "$wpdb->posts.ID IN") === false ) {
		
		return $sql;
		
	}
	
    
    // get bits
	$glue = 'AND';
	$bits = explode($glue, $sql);
	
    
	// loop through $where and remove any post_type queries
	foreach( $bits as $i => $bit ) {
		
		if( strpos($bit, "$wpdb->posts.post_type") !== false ) {
			
			unset( $bits[ $i ] );
			
		}
		
	}
	
	
	// join $where back together
	$sql = implode($glue, $bits);
    
    
    // return
    return $sql;
    
}


/*
*  acs_get_grouped_posts
*
*  This function will return all posts grouped by post_type
*  This is handy for select settings
*
*  @type	function
*  @date	27/02/2014
*  @since	5.0.0
*
*  @param	$args (array)
*  @return	(array)
*/

function acs_get_grouped_posts( $args ) {
	
	// vars
	$data = array();
	
	
	// defaults
	$args = wp_parse_args( $args, array(
		'posts_per_page'			=> -1,
		'paged'						=> 0,
		'post_type'					=> 'post',
		'orderby'					=> 'menu_order title',
		'order'						=> 'ASC',
		'post_status'				=> 'any',
		'suppress_filters'			=> false,
		'update_post_meta_cache'	=> false,
	));

	
	// find array of post_type
	$post_types = acs_get_array( $args['post_type'] );
	$post_types_labels = acs_get_pretty_post_types($post_types);
	$is_single_post_type = ( count($post_types) == 1 );
	
	
	// attachment doesn't work if it is the only item in an array
	if( $is_single_post_type ) {
		$args['post_type'] = reset($post_types);
	}
	
	
	// add filter to orderby post type
	if( !$is_single_post_type ) {
		add_filter('posts_orderby', '_acs_orderby_post_type', 10, 2);
	}
	
	
	// get posts
	$posts = get_posts( $args );
	
	
	// remove this filter (only once)
	if( !$is_single_post_type ) {
		remove_filter('posts_orderby', '_acs_orderby_post_type', 10, 2);
	}
	
	
	// loop
	foreach( $post_types as $post_type ) {
		
		// vars
		$this_posts = array();
		$this_group = array();
		
		
		// populate $this_posts
		foreach( $posts as $post ) {
			if( $post->post_type == $post_type ) {
				$this_posts[] = $post;
			}
		}
		
		
		// bail early if no posts for this post type
		if( empty($this_posts) ) continue;
		
		
		// sort into hierachial order!
		// this will fail if a search has taken place because parents wont exist
		if( is_post_type_hierarchical($post_type) && empty($args['s'])) {
			
			// vars
			$post_id = $this_posts[0]->ID;
			$parent_id = acs_maybe_get($args, 'post_parent', 0);
			$offset = 0;
			$length = count($this_posts);
			
			
			// get all posts from this post type
			$all_posts = get_posts(array_merge($args, array(
				'posts_per_page'	=> -1,
				'paged'				=> 0,
				'post_type'			=> $post_type
			)));
			
			
			// find starting point (offset)
			foreach( $all_posts as $i => $post ) {
				if( $post->ID == $post_id ) {
					$offset = $i;
					break;
				}
			}
			
			
			// order posts
			$ordered_posts = get_page_children($parent_id, $all_posts);
			
			
			// compare aray lengths
			// if $ordered_posts is smaller than $all_posts, WP has lost posts during the get_page_children() function
			// this is possible when get_post( $args ) filter out parents (via taxonomy, meta and other search parameters) 
			if( count($ordered_posts) == count($all_posts) ) {
				$this_posts = array_slice($ordered_posts, $offset, $length);
			}
			
		}
		
		
		// populate $this_posts
		foreach( $this_posts as $post ) {
			$this_group[ $post->ID ] = $post;
		}
		
		
		// group by post type
		$label = $post_types_labels[ $post_type ];
		$data[ $label ] = $this_group;
					
	}
	
	
	// return
	return $data;
	
}


function _acs_orderby_post_type( $ordeby, $wp_query ) {
	
	// global
	global $wpdb;
	
	
	// get post types
	$post_types = $wp_query->get('post_type');
	

	// prepend SQL
	if( is_array($post_types) ) {
		
		$post_types = implode("','", $post_types);
		$ordeby = "FIELD({$wpdb->posts}.post_type,'$post_types')," . $ordeby;
		
	}
	
	
	// return
	return $ordeby;
	
}


function acs_get_post_title( $post = 0, $is_search = false ) {
	
	// vars
	$post = get_post($post);
	$title = '';
	$prepend = '';
	$append = '';
	
    
	// bail early if no post
	if( !$post ) return '';
	
	
	// title
	$title = get_the_title( $post->ID );
	
	
	// empty
	if( $title === '' ) {
		
		$title = __('(no title)', 'acs');
		
	}
	
	
	// status
	if( get_post_status( $post->ID ) != "publish" ) {
		
		$append .= ' (' . get_post_status( $post->ID ) . ')';
		
	}
	
	
	// ancestors
	if( $post->post_type !== 'attachment' ) {
		
		// get ancestors
		$ancestors = get_ancestors( $post->ID, $post->post_type );
		$prepend .= str_repeat('- ', count($ancestors));
		
		
		// add parent
/*
		removed in 5.6.5 as not used by the UI
		if( $is_search && !empty($ancestors) ) {
			
			// reverse
			$ancestors = array_reverse($ancestors);
			
			
			// convert id's into titles
			foreach( $ancestors as $i => $id ) {
				
				$ancestors[ $i ] = get_the_title( $id );
				
			}
			
			
			// append
			$append .= ' | ' . __('Parent', 'acs') . ': ' . implode(' / ', $ancestors);
			
		}
*/
		
	}
	
	
	// merge
	$title = $prepend . $title . $append;
	
	
	// return
	return $title;
	
}


function acs_order_by_search( $array, $search ) {
	
	// vars
	$weights = array();
	$needle = strtolower( $search );
	
	
	// add key prefix
	foreach( array_keys($array) as $k ) {
		
		$array[ '_' . $k ] = acs_extract_var( $array, $k );
		
	}


	// add search weight
	foreach( $array as $k => $v ) {
	
		// vars
		$weight = 0;
		$haystack = strtolower( $v );
		$strpos = strpos( $haystack, $needle );
		
		
		// detect search match
		if( $strpos !== false ) {
			
			// set eright to length of match
			$weight = strlen( $search );
			
			
			// increase weight if match starts at begining of string
			if( $strpos == 0 ) {
				
				$weight++;
				
			}
			
		}
		
		
		// append to wights
		$weights[ $k ] = $weight;
		
	}
	
	
	// sort the array with menu_order ascending
	array_multisort( $weights, SORT_DESC, $array );
	
	
	// remove key prefix
	foreach( array_keys($array) as $k ) {
		
		$array[ substr($k,1) ] = acs_extract_var( $array, $k );
		
	}
		
	
	// return
	return $array;
}


/*
*  acs_get_pretty_user_roles
*
*  description
*
*  @type	function
*  @date	23/02/2016
*  @since	5.3.2
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function acs_get_pretty_user_roles( $allowed = false ) {
	
	// vars
	$editable_roles = get_editable_roles();
	$allowed = acs_get_array($allowed);
	$roles = array();
	
	
	// loop
	foreach( $editable_roles as $role_name => $role_details ) {	
		
		// bail early if not allowed
		if( !empty($allowed) && !in_array($role_name, $allowed) ) continue;
		
		
		// append
		$roles[ $role_name ] = translate_user_role( $role_details['name'] );
		
	}
	
	
	// return
	return $roles;
		
}


/*
*  acs_get_grouped_users
*
*  This function will return all users grouped by role
*  This is handy for select settings
*
*  @type	function
*  @date	27/02/2014
*  @since	5.0.0
*
*  @param	$args (array)
*  @return	(array)
*/

function acs_get_grouped_users( $args = array() ) {
	
	// vars
	$r = array();
	
	
	// defaults
	$args = wp_parse_args( $args, array(
		'users_per_page'			=> -1,
		'paged'						=> 0,
		'role'         				=> '',
		'orderby'					=> 'login',
		'order'						=> 'ASC',
	));
	
	
	// offset
	$i = 0;
	$min = 0;
	$max = 0;
	$users_per_page = acs_extract_var($args, 'users_per_page');
	$paged = acs_extract_var($args, 'paged');
	
	if( $users_per_page > 0 ) {
		
		// prevent paged from being -1
		$paged = max(0, $paged);
		
		
		// set min / max
		$min = (($paged-1) * $users_per_page) + 1; // 	1, 	11
		$max = ($paged * $users_per_page); // 			10,	20
		
	}
	
	
	// find array of post_type
	$user_roles = acs_get_pretty_user_roles($args['role']);
	
	
	// fix role
	if( is_array($args['role']) ) {
		
		// global
   		global $wp_version, $wpdb;
   		
   		
		// vars
		$roles = acs_extract_var($args, 'role');
		
		
		// new WP has role__in
		if( version_compare($wp_version, '4.4', '>=' ) ) {
			
			$args['role__in'] = $roles;
				
		// old WP doesn't have role__in
		} else {
			
			// vars
			$blog_id = get_current_blog_id();
			$meta_query = array( 'relation' => 'OR' );
			
			
			// loop
			foreach( $roles as $role ) {
				
				$meta_query[] = array(
					'key'     => $wpdb->get_blog_prefix( $blog_id ) . 'capabilities',
					'value'   => '"' . $role . '"',
					'compare' => 'LIKE',
				);
				
			}
			
			
			// append
			$args['meta_query'] = $meta_query;
			
		}
		
	}
	
	
	// get posts
	$users = get_users( $args );
	
	
	// loop
	foreach( $user_roles as $user_role_name => $user_role_label ) {
		
		// vars
		$this_users = array();
		$this_group = array();
		
		
		// populate $this_posts
		foreach( array_keys($users) as $key ) {
			
			// bail ealry if not correct role
			if( !in_array($user_role_name, $users[ $key ]->roles) ) continue;
		
			
			// extract user
			$user = acs_extract_var( $users, $key );
			
			
			// increase
			$i++;
			
			
			// bail ealry if too low
			if( $min && $i < $min ) continue;
			
			
			// bail early if too high (don't bother looking at any more users)
			if( $max && $i > $max ) break;
			
			
			// group by post type
			$this_users[ $user->ID ] = $user;
			
			
		}
		
		
		// bail early if no posts for this post type
		if( empty($this_users) ) continue;
		
		
		// append
		$r[ $user_role_label ] = $this_users;
					
	}
	
	
	// return
	return $r;
	
}

/**
 * acs_json_encode
 *
 * Returns json_encode() ready for file / database use.
 *
 * @date	29/4/19
 * @since	5.0.0
 *
 * @param	array $json The array of data to encode.
 * @return	string
 */
function acs_json_encode( $json ) {
	return json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}


/*
*  acs_str_exists
*
*  This function will return true if a sub string is found
*
*  @type	function
*  @date	1/05/2014
*  @since	5.0.0
*
*  @param	$needle (string)
*  @param	$haystack (string)
*  @return	(boolean)
*/

function acs_str_exists( $needle, $haystack ) {
	
	// return true if $haystack contains the $needle
	if( is_string($haystack) && strpos($haystack, $needle) !== false ) {
		
		return true;
		
	}
	
	
	// return
	return false;
}


/*
*  acs_debug
*
*  description
*
*  @type	function
*  @date	2/05/2014
*  @since	5.0.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function acs_debug() {
	
	// vars
	$args = func_get_args();
	$s = array_shift($args);
	$o = '';
	$nl = "\r\n";
	
	
	// start script
	$o .= '<script type="text/javascript">' . $nl;
	
	$o .= 'console.log("' . $s . '"';
	
	if( !empty($args) ) {
		
		foreach( $args as $arg ) {
			
			if( is_object($arg) || is_array($arg) ) {
				
				$arg = json_encode($arg);
				
			} elseif( is_bool($arg) ) {
				
				$arg = $arg ? 'true' : 'false';
				
			}elseif( is_string($arg) ) {
				
				$arg = '"' . $arg . '"';
				
			}
			
			$o .= ', ' . $arg;
			
		}
	}
	
	$o .= ');' . $nl;
	
	
	// end script
	$o .= '</script>' . $nl;
	
	
	// echo
	echo $o;
}

function acs_debug_start() {
	
	acs_update_setting( 'debug_start', memory_get_usage());
	
}

function acs_debug_end() {
	
	$start = acs_get_setting( 'debug_start' );
	$end = memory_get_usage();
	
	return $end - $start;
	
}


/*
*  acs_encode_choices
*
*  description
*
*  @type	function
*  @date	4/06/2014
*  @since	5.0.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function acs_encode_choices( $array = array(), $show_keys = true ) {
	
	// bail early if not array (maybe a single string)
	if( !is_array($array) ) return $array;
	
	
	// bail early if empty array
	if( empty($array) ) return '';
	
	
	// vars
	$string = '';
	
	
	// if allowed to show keys (good for choices, not for default values)
	if( $show_keys ) {
		
		// loop
		foreach( $array as $k => $v ) { 
			
			// ignore if key and value are the same
			if( strval($k) == strval($v) )  continue;
			
			
			// show key in the value
			$array[ $k ] = $k . ' : ' . $v;
			
		}
	
	}
	
	
	// implode
	$string = implode("\n", $array);

	
	// return
	return $string;
	
}

function acs_decode_choices( $string = '', $array_keys = false ) {
	
	// bail early if already array
	if( is_array($string) ) {
		
		return $string;
	
	// allow numeric values (same as string)
	} elseif( is_numeric($string) ) {
		
		// do nothing
	
	// bail early if not a string
	} elseif( !is_string($string) ) {
		
		return array();
	
	// bail early if is empty string 
	} elseif( $string === '' ) {
		
		return array();
		
	}
	
	
	// vars
	$array = array();
	
	
	// explode
	$lines = explode("\n", $string);
	
	
	// key => value
	foreach( $lines as $line ) {
		
		// vars
		$k = trim($line);
		$v = trim($line);
		
		
		// look for ' : '
		if( acs_str_exists(' : ', $line) ) {
		
			$line = explode(' : ', $line);
			
			$k = trim($line[0]);
			$v = trim($line[1]);
			
		}
		
		
		// append
		$array[ $k ] = $v;
		
	}
	
	
	// return only array keys? (good for checkbox default_value)
	if( $array_keys ) {
		
		return array_keys($array);
		
	}
	
	
	// return
	return $array;
	
}


/*
*  acs_str_replace
*
*  This function will replace an array of strings much like str_replace
*  The difference is the extra logic to avoid replacing a string that has alread been replaced
*  This is very useful for replacing date characters as they overlap with eachother
*
*  @type	function
*  @date	21/06/2016
*  @since	5.3.8
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function acs_str_replace( $string = '', $search_replace = array() ) {
	
	// vars
	$ignore = array();
	
	
	// remove potential empty search to avoid PHP error
	unset($search_replace['']);
	
		
	// loop over conversions
	foreach( $search_replace as $search => $replace ) {
		
		// ignore this search, it was a previous replace
		if( in_array($search, $ignore) ) continue;
		
		
		// bail early if subsctring not found
		if( strpos($string, $search) === false ) continue;
		
		
		// replace
		$string = str_replace($search, $replace, $string);
		
		
		// append to ignore
		$ignore[] = $replace;
		
	}
	
	
	// return
	return $string;
	
}


/*
*  date & time formats
*
*  These settings contain an association of format strings from PHP => JS
*
*  @type	function
*  @date	21/06/2016
*  @since	5.3.8
*
*  @param	n/a
*  @return	n/a
*/

acs_update_setting('php_to_js_date_formats', array(

	// Year
	'Y'	=> 'yy',	// Numeric, 4 digits 								1999, 2003
	'y'	=> 'y',		// Numeric, 2 digits 								99, 03
	
	
	// Month
	'm'	=> 'mm',	// Numeric, with leading zeros  					01–12
	'n'	=> 'm',		// Numeric, without leading zeros  					1–12
	'F'	=> 'MM',	// Textual full   									January – December
	'M'	=> 'M',		// Textual three letters    						Jan - Dec 
	
	
	// Weekday
	'l'	=> 'DD',	// Full name  (lowercase 'L') 						Sunday – Saturday
	'D'	=> 'D',		// Three letter name 	 							Mon – Sun 
	
	
	// Day of Month
	'd'	=> 'dd',	// Numeric, with leading zeros						01–31
	'j'	=> 'd',		// Numeric, without leading zeros 					1–31
	'S'	=> '',		// The English suffix for the day of the month  	st, nd or th in the 1st, 2nd or 15th. 
	
));

acs_update_setting('php_to_js_time_formats', array(
	
	'a' => 'tt',	// Lowercase Ante meridiem and Post meridiem 		am or pm
	'A' => 'TT',	// Uppercase Ante meridiem and Post meridiem 		AM or PM
	'h' => 'hh',	// 12-hour format of an hour with leading zeros 	01 through 12
	'g' => 'h',		// 12-hour format of an hour without leading zeros 	1 through 12
	'H' => 'HH',	// 24-hour format of an hour with leading zeros 	00 through 23
	'G' => 'H',		// 24-hour format of an hour without leading zeros 	0 through 23
	'i' => 'mm',	// Minutes with leading zeros 						00 to 59
	's' => 'ss',	// Seconds, with leading zeros 						00 through 59
	
));


/*
*  acs_split_date_time
*
*  This function will split a format string into seperate date and time
*
*  @type	function
*  @date	26/05/2016
*  @since	5.3.8
*
*  @param	$date_time (string)
*  @return	$formats (array)
*/

function acs_split_date_time( $date_time = '' ) {
	
	// vars
	$php_date = acs_get_setting('php_to_js_date_formats');
	$php_time = acs_get_setting('php_to_js_time_formats');
	$chars = str_split($date_time);
	$type = 'date';
	
	
	// default
	$data = array(
		'date' => '',
		'time' => ''
	);
	
	
	// loop
	foreach( $chars as $i => $c ) {
		
		// find type
		// - allow misc characters to append to previous type
		if( isset($php_date[ $c ]) ) {
			
			$type = 'date';
			
		} elseif( isset($php_time[ $c ]) ) {
			
			$type = 'time';
			
		}
		
		
		// append char
		$data[ $type ] .= $c;
		
	}
	
	
	// trim
	$data['date'] = trim($data['date']);
	$data['time'] = trim($data['time']);
	
	
	// return
	return $data;	
	
}


/*
*  acs_convert_date_to_php
*
*  This fucntion converts a date format string from JS to PHP
*
*  @type	function
*  @date	20/06/2014
*  @since	5.0.0
*
*  @param	$date (string)
*  @return	(string)
*/

function acs_convert_date_to_php( $date = '' ) {
	
	// vars
	$php_to_js = acs_get_setting('php_to_js_date_formats');
	$js_to_php = array_flip($php_to_js);
		
	
	// return
	return acs_str_replace( $date, $js_to_php );
	
}

/*
*  acs_convert_date_to_js
*
*  This fucntion converts a date format string from PHP to JS
*
*  @type	function
*  @date	20/06/2014
*  @since	5.0.0
*
*  @param	$date (string)
*  @return	(string)
*/

function acs_convert_date_to_js( $date = '' ) {
	
	// vars
	$php_to_js = acs_get_setting('php_to_js_date_formats');
		
	
	// return
	return acs_str_replace( $date, $php_to_js );
	
}


/*
*  acs_convert_time_to_php
*
*  This fucntion converts a time format string from JS to PHP
*
*  @type	function
*  @date	20/06/2014
*  @since	5.0.0
*
*  @param	$time (string)
*  @return	(string)
*/

function acs_convert_time_to_php( $time = '' ) {
	
	// vars
	$php_to_js = acs_get_setting('php_to_js_time_formats');
	$js_to_php = array_flip($php_to_js);
		
	
	// return
	return acs_str_replace( $time, $js_to_php );
	
}


/*
*  acs_convert_time_to_js
*
*  This fucntion converts a date format string from PHP to JS
*
*  @type	function
*  @date	20/06/2014
*  @since	5.0.0
*
*  @param	$time (string)
*  @return	(string)
*/

function acs_convert_time_to_js( $time = '' ) {
	
	// vars
	$php_to_js = acs_get_setting('php_to_js_time_formats');
		
	
	// return
	return acs_str_replace( $time, $php_to_js );
	
}


/*
*  acs_update_user_setting
*
*  description
*
*  @type	function
*  @date	15/07/2014
*  @since	5.0.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function acs_update_user_setting( $name, $value ) {
	
	// get current user id
	$user_id = get_current_user_id();
	
	
	// get user settings
	$settings = get_user_meta( $user_id, 'acs_user_settings', true );
	
	
	// ensure array
	$settings = acs_get_array($settings);
	
	
	// delete setting (allow 0 to save)
	if( acs_is_empty($value) ) {
		
		unset($settings[ $name ]);
	
	// append setting	
	} else {
		
		$settings[ $name ] = $value;
		
	}
	
	
	// update user data
	return update_metadata('user', $user_id, 'acs_user_settings', $settings);
	
}


/*
*  acs_get_user_setting
*
*  description
*
*  @type	function
*  @date	15/07/2014
*  @since	5.0.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function acs_get_user_setting( $name = '', $default = false ) {
	
	// get current user id
	$user_id = get_current_user_id();
	
	
	// get user settings
	$settings = get_user_meta( $user_id, 'acs_user_settings', true );
	
	
	// ensure array
	$settings = acs_get_array($settings);
	
	
	// bail arly if no settings
	if( !isset($settings[$name]) ) return $default;
	
	
	// return
	return $settings[$name];
	
}


/*
*  acs_in_array
*
*  description
*
*  @type	function
*  @date	22/07/2014
*  @since	5.0.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function acs_in_array( $value = '', $array = false ) {
	
	// bail early if not array
	if( !is_array($array) ) return false;
	
	
	// find value in array
	return in_array($value, $array);
	
}


/*
*  acs_get_valid_post_id
*
*  This function will return a valid post_id based on the current screen / parameter
*
*  @type	function
*  @date	8/12/2013
*  @since	5.0.0
*
*  @param	$post_id (mixed)
*  @return	$post_id (mixed)
*/

function acs_get_valid_post_id( $post_id = 0 ) {
	
	// allow filter to short-circuit load_value logic
	$preload = apply_filters( "acs/pre_load_post_id", null, $post_id );
    if( $preload !== null ) {
	    return $preload;
    }
    
	// vars
	$_post_id = $post_id;
	
	
	// if not $post_id, load queried object
	if( !$post_id ) {
		
		// try for global post (needed for setup_postdata)
		$post_id = (int) get_the_ID();
		
		
		// try for current screen
		if( !$post_id ) {
			
			$post_id = get_queried_object();
				
		}
		
	}
	
	
	// $post_id may be an object.
	// todo: Compare class types instead.
	if( is_object($post_id) ) {
		
		// post
		if( isset($post_id->post_type, $post_id->ID) ) {
		
			$post_id = $post_id->ID;
			
		// user
		} elseif( isset($post_id->roles, $post_id->ID) ) {
		
			$post_id = 'user_' . $post_id->ID;
		
		// term
		} elseif( isset($post_id->taxonomy, $post_id->term_id) ) {
			
			$post_id = 'term_' . $post_id->term_id;
		
		// comment
		} elseif( isset($post_id->comment_ID) ) {
		
			$post_id = 'comment_' . $post_id->comment_ID;
		
		// default
		} else {
			
			$post_id = 0;
			
		}
		
	}
	
	
	// allow for option == options
	if( $post_id === 'option' ) {
	
		$post_id = 'options';
		
	}
	
	
	// append language code
	if( $post_id == 'options' ) {
		
		$dl = acs_get_setting('default_language');
		$cl = acs_get_setting('current_language');
		
		if( $cl && $cl !== $dl ) {
			
			$post_id .= '_' . $cl;
			
		}
			
	}
	
	
	
	// filter for 3rd party
	$post_id = apply_filters('acs/validate_post_id', $post_id, $_post_id);
	
	
	// return
	return $post_id;
	
}



/*
*  acs_get_post_id_info
*
*  This function will return the type and id for a given $post_id string
*
*  @type	function
*  @date	2/07/2016
*  @since	5.4.0
*
*  @param	$post_id (mixed)
*  @return	$info (array)
*/

function acs_get_post_id_info( $post_id = 0 ) {
	
	// vars
	$info = array(
		'type'	=> 'post',
		'id'	=> 0
	);
	
	// bail early if no $post_id
	if( !$post_id ) return $info;
	
	
	// check cache
	// - this function will most likely be called multiple times (saving loading fields from post)
	//$cache_key = "get_post_id_info/post_id={$post_id}";
	
	//if( acs_isset_cache($cache_key) ) return acs_get_cache($cache_key);
	
	
	// numeric
	if( is_numeric($post_id) ) {
		
		$info['id'] = (int) $post_id;
	
	// string
	} elseif( is_string($post_id) ) {
		
		// vars
		$glue = '_';
		$type = explode($glue, $post_id);
		$id = array_pop($type);
		$type = implode($glue, $type);
		$meta = array('post', 'user', 'comment', 'term');
		
		
		// check if is taxonomy (ACS < 5.5)
		// - avoid scenario where taxonomy exists with name of meta type
		if( !in_array($type, $meta) && acs_isset_termmeta($type) ) $type = 'term';
		
		
		// meta
		if( is_numeric($id) && in_array($type, $meta) ) {
			
			$info['type'] = $type;
			$info['id'] = (int) $id;
		
		// option	
		} else {
			
			$info['type'] = 'option';
			$info['id'] = $post_id;
			
		}
		
	}
	
	
	// update cache
	//acs_set_cache($cache_key, $info);
	
	
	// filter
	$info = apply_filters("acs/get_post_id_info", $info, $post_id);
	
	// return
	return $info;
	
}


/*

acs_log( acs_get_post_id_info(4) );

acs_log( acs_get_post_id_info('post_4') );

acs_log( acs_get_post_id_info('user_123') );

acs_log( acs_get_post_id_info('term_567') );

acs_log( acs_get_post_id_info('category_204') );

acs_log( acs_get_post_id_info('comment_6') );

acs_log( acs_get_post_id_info('options_lol!') );

acs_log( acs_get_post_id_info('option') );

acs_log( acs_get_post_id_info('options') );

*/


/*
*  acs_isset_termmeta
*
*  This function will return true if the termmeta table exists
*  https://developer.wordpress.org/reference/functions/get_term_meta/
*
*  @type	function
*  @date	3/09/2016
*  @since	5.4.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function acs_isset_termmeta( $taxonomy = '' ) {
	
	// bail ealry if no table
	if( get_option('db_version') < 34370 ) return false;
	
	
	// check taxonomy
	if( $taxonomy && !taxonomy_exists($taxonomy) ) return false;
	
	
	// return
	return true;
		
}


/*
*  acs_upload_files
*
*  This function will walk througfh the $_FILES data and upload each found
*
*  @type	function
*  @date	25/10/2014
*  @since	5.0.9
*
*  @param	$ancestors (array) an internal parameter, not required
*  @return	n/a
*/
	
function acs_upload_files( $ancestors = array() ) {
	
	// vars
	$file = array(
		'name'		=> '',
		'type'		=> '',
		'tmp_name'	=> '',
		'error'		=> '',
		'size' 		=> ''
	);
	
	
	// populate with $_FILES data
	foreach( array_keys($file) as $k ) {
		
		$file[ $k ] = $_FILES['acs'][ $k ];
		
	}
	
	
	// walk through ancestors
	if( !empty($ancestors) ) {
		
		foreach( $ancestors as $a ) {
			
			foreach( array_keys($file) as $k ) {
				
				$file[ $k ] = $file[ $k ][ $a ];
				
			}
			
		}
		
	}
	
	
	// is array?
	if( is_array($file['name']) ) {
		
		foreach( array_keys($file['name']) as $k ) {
				
			$_ancestors = array_merge($ancestors, array($k));
			
			acs_upload_files( $_ancestors );
			
		}
		
		return;
		
	}
	
	
	// bail ealry if file has error (no file uploaded)
	if( $file['error'] ) {
		
		return;
		
	}
	
	
	// assign global _acsuploader for media validation
	$_POST['_acsuploader'] = end($ancestors);
	
	
	// file found!
	$attachment_id = acs_upload_file( $file );
	
	
	// update $_POST
	array_unshift($ancestors, 'acs');
	acs_update_nested_array( $_POST, $ancestors, $attachment_id );
	
}


/*
*  acs_upload_file
*
*  This function will uploade a $_FILE
*
*  @type	function
*  @date	27/10/2014
*  @since	5.0.9
*
*  @param	$uploaded_file (array) array found from $_FILE data
*  @return	$id (int) new attachment ID
*/

function acs_upload_file( $uploaded_file ) {
	
	// required
	//require_once( ABSPATH . "/wp-load.php" ); // WP should already be loaded
	require_once( ABSPATH . "/wp-admin/includes/media.php" ); // video functions
	require_once( ABSPATH . "/wp-admin/includes/file.php" );
	require_once( ABSPATH . "/wp-admin/includes/image.php" );
	 
	 
	// required for wp_handle_upload() to upload the file
	$upload_overrides = array( 'test_form' => false );
	
	
	// upload
	$file = wp_handle_upload( $uploaded_file, $upload_overrides );
	
	
	// bail ealry if upload failed
	if( isset($file['error']) ) {
		
		return $file['error'];
		
	}
	
	
	// vars
	$url = $file['url'];
	$type = $file['type'];
	$file = $file['file'];
	$filename = basename($file);
	

	// Construct the object array
	$object = array(
		'post_title' => $filename,
		'post_mime_type' => $type,
		'guid' => $url
	);

	// Save the data
	$id = wp_insert_attachment($object, $file);

	// Add the meta-data
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );
	
	/** This action is documented in wp-admin/custom-header.php */
	do_action( 'wp_create_file_in_uploads', $file, $id ); // For replication
	
	// return new ID
	return $id;
	
}


/*
*  acs_update_nested_array
*
*  This function will update a nested array value. Useful for modifying the $_POST array
*
*  @type	function
*  @date	27/10/2014
*  @since	5.0.9
*
*  @param	$array (array) target array to be updated
*  @param	$ancestors (array) array of keys to navigate through to find the child
*  @param	$value (mixed) The new value
*  @return	(boolean)
*/

function acs_update_nested_array( &$array, $ancestors, $value ) {
	
	// if no more ancestors, update the current var
	if( empty($ancestors) ) {
		
		$array = $value;
		
		// return
		return true;
		
	}
	
	
	// shift the next ancestor from the array
	$k = array_shift( $ancestors );
	
	
	// if exists
	if( isset($array[ $k ]) ) {
		
		return acs_update_nested_array( $array[ $k ], $ancestors, $value );
		
	}
		
	
	// return 
	return false;
}


/*
*  acs_is_screen
*
*  This function will return true if all args are matched for the current screen
*
*  @type	function
*  @date	9/12/2014
*  @since	5.1.5
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function acs_is_screen( $id = '' ) {
	
	// bail early if not defined
	if( !function_exists('get_current_screen') ) {
		return false;
	}
	
	// vars
	$current_screen = get_current_screen();
	
	// no screen
	if( !$current_screen ) {
		return false;
	
	// array
	} elseif( is_array($id) ) {
		return in_array($current_screen->id, $id);
	
	// string
	} else {
		return ($id === $current_screen->id);
	}
}


/*
*  acs_maybe_get
*
*  This function will return a var if it exists in an array
*
*  @type	function
*  @date	9/12/2014
*  @since	5.1.5
*
*  @param	$array (array) the array to look within
*  @param	$key (key) the array key to look for. Nested values may be found using '/'
*  @param	$default (mixed) the value returned if not found
*  @return	$post_id (int)
*/

function acs_maybe_get( $array = array(), $key = 0, $default = null ) {
	
	return isset( $array[$key] ) ? $array[$key] : $default;
		
}

function acs_maybe_get_POST( $key = '', $default = null ) {
	
	return isset( $_POST[$key] ) ? $_POST[$key] : $default;
	
}

function acs_maybe_get_GET( $key = '', $default = null ) {
	
	return isset( $_GET[$key] ) ? $_GET[$key] : $default;
	
}

/**
 * Returns an array of attachment data.
 *
 * @date	05/01/2015
 * @since	5.1.5
 *
 * @param	int|WP_Post The attachment ID or object.
 * @return	array|false
 */
function acs_get_attachment( $attachment ) {
	
	// Allow filter to short-circuit load attachment logic.
	// Alternatively, this filter may be used to switch blogs for multisite media functionality. 
	$response = apply_filters( "acs/pre_load_attachment", null, $attachment );
	if( $response !== null ) {
		return $response;
	}

	// Get the attachment post object.
	$attachment = get_post( $attachment );
	if( !$attachment ) {
		return false;
	}
	if( $attachment->post_type !== 'attachment' ) {
		return false;
	}
	
	// Load various attachment details.
	$meta = wp_get_attachment_metadata( $attachment->ID );
	$attached_file = get_attached_file( $attachment->ID );
	if( strpos( $attachment->post_mime_type, '/' ) !== false ) {
		list( $type, $subtype ) = explode( '/', $attachment->post_mime_type );
	} else {
		list( $type, $subtype ) = array( $attachment->post_mime_type, '' );
	}

	// Generate response.
	$response = array(
		'ID'			=> $attachment->ID,
		'id'			=> $attachment->ID,
		'title'       	=> $attachment->post_title,
		'filename'		=> wp_basename( $attached_file ),
		'filesize'		=> 0,
		'url'			=> wp_get_attachment_url( $attachment->ID ),
		'link'			=> get_attachment_link( $attachment->ID ),
		'alt'			=> get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
		'author'		=> $attachment->post_author,
		'description'	=> $attachment->post_content,
		'caption'		=> $attachment->post_excerpt,
		'name'			=> $attachment->post_name,
        'status'		=> $attachment->post_status,
        'uploaded_to'	=> $attachment->post_parent,
        'date'			=> $attachment->post_date_gmt,
		'modified'		=> $attachment->post_modified_gmt,
		'menu_order'	=> $attachment->menu_order,
		'mime_type'		=> $attachment->post_mime_type,
        'type'			=> $type,
        'subtype'		=> $subtype,
        'icon'			=> wp_mime_type_icon( $attachment->ID )
	);
	
	// Append filesize data.
	if( isset($meta['filesize']) ) {
		$response['filesize'] = $meta['filesize'];
	} elseif( file_exists($attached_file) ) {
		$response['filesize'] = filesize( $attached_file );
	}
	
	// Restrict the loading of image "sizes".
	$sizes_id = 0;

	// Type specific logic.
	switch( $type ) {
		case 'image':
			$sizes_id = $attachment->ID;
			$src = wp_get_attachment_image_src( $attachment->ID, 'full' );
			if ( $src ) {
				$response['url'] = $src[0];
				$response['width'] = $src[1];
				$response['height'] = $src[2];
			}
			break;
		case 'video':
			$response['width'] = acs_maybe_get( $meta, 'width', 0 );
			$response['height'] = acs_maybe_get( $meta, 'height', 0 );
			if( $featured_id = get_post_thumbnail_id( $attachment->ID ) ) {
				$sizes_id = $featured_id;
			}
			break;
		case 'audio':
			if( $featured_id = get_post_thumbnail_id( $attachment->ID ) ) {
				$sizes_id = $featured_id;
			}	
			break;
	}

	// Load array of image sizes.
	if( $sizes_id ) {
		$sizes = get_intermediate_image_sizes();
		$sizes_data = array();
		foreach( $sizes as $size ) {
			$src = wp_get_attachment_image_src( $sizes_id, $size );
			if ( $src ) {
				$sizes_data[ $size ] = $src[0];
				$sizes_data[ $size . '-width' ] = $src[1];
				$sizes_data[ $size . '-height' ] = $src[2];
			}
		}
		$response['sizes'] = $sizes_data;
	}
	
	/**
	 * Filters the attachment $response after it has been loaded.
	 *
	 * @date	16/06/2020
 	 * @since	5.9.0
	 *
	 * @param	array $response Array of loaded attachment data.
     * @param	WP_Post $attachment Attachment object.
     * @param	array|false $meta Array of attachment meta data, or false if there is none.
	 */
	return apply_filters( "acs/load_attachment", $response, $attachment, $meta );
}


/*
*  acs_get_truncated
*
*  This function will truncate and return a string
*
*  @type	function
*  @date	8/08/2014
*  @since	5.0.0
*
*  @param	$text (string)
*  @param	$length (int)
*  @return	(string)
*/

function acs_get_truncated( $text, $length = 64 ) {
	
	// vars
	$text = trim($text);
	$the_length = strlen( $text );
	
	
	// cut
	$return = substr( $text, 0, ($length - 3) );
	
	
	// ...
	if( $the_length > ($length - 3) ) {
	
		$return .= '...';
		
	}
	
	
	// return
	return $return;
	
}


/*
*  acs_get_current_url
*
*  This function will return the current URL.
*
*  @date	23/01/2015
*  @since	5.1.5
*
*  @param	void
*  @return	string
*/

function acs_get_current_url() {
	return ( is_ssl() ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/*
*  acs_current_user_can_admin
*
*  This function will return true if the current user can administrate the ACS field groups
*
*  @type	function
*  @date	9/02/2015
*  @since	5.1.5
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function acs_current_user_can_admin() {
	
	if( acs_get_setting('show_admin') && current_user_can(acs_get_setting('capability')) ) {
		
		return true;
		
	}
	
	
	// return
	return false;
	
}


/*
*  acs_get_filesize
*
*  This function will return a numeric value of bytes for a given filesize string
*
*  @type	function
*  @date	18/02/2015
*  @since	5.1.5
*
*  @param	$size (mixed)
*  @return	(int)
*/

function acs_get_filesize( $size = 1 ) {
	
	// vars
	$unit = 'MB';
	$units = array(
		'TB' => 4,
		'GB' => 3,
		'MB' => 2,
		'KB' => 1,
	);
	
	
	// look for $unit within the $size parameter (123 KB)
	if( is_string($size) ) {
		
		// vars
		$custom = strtoupper( substr($size, -2) );
		
		foreach( $units as $k => $v ) {
			
			if( $custom === $k ) {
				
				$unit = $k;
				$size = substr($size, 0, -2);
					
			}
			
		}
		
	}
	
	
	// calc bytes
	$bytes = floatval($size) * pow(1024, $units[$unit]); 
	
	
	// return
	return $bytes;
	
}


/*
*  acs_format_filesize
*
*  This function will return a formatted string containing the filesize and unit
*
*  @type	function
*  @date	18/02/2015
*  @since	5.1.5
*
*  @param	$size (mixed)
*  @return	(int)
*/

function acs_format_filesize( $size = 1 ) {
	
	// convert
	$bytes = acs_get_filesize( $size );
	
	
	// vars
	$units = array(
		'TB' => 4,
		'GB' => 3,
		'MB' => 2,
		'KB' => 1,
	);
	
	
	// loop through units
	foreach( $units as $k => $v ) {
		
		$result = $bytes / pow(1024, $v);
		
		if( $result >= 1 ) {
			
			return $result . ' ' . $k;
			
		}
		
	}
	
	
	// return
	return $bytes . ' B';
		
}


/*
*  acs_get_valid_terms
*
*  This function will replace old terms with new split term ids
*
*  @type	function
*  @date	27/02/2015
*  @since	5.1.5
*
*  @param	$terms (int|array)
*  @param	$taxonomy (string)
*  @return	$terms
*/

function acs_get_valid_terms( $terms = false, $taxonomy = 'category' ) {
	
	// force into array
	$terms = acs_get_array($terms);
	
	
	// force ints
	$terms = array_map('intval', $terms);
	
	
	// bail early if function does not yet exist or
	if( !function_exists('wp_get_split_term') || empty($terms) ) {
		
		return $terms;
		
	}
	
	
	// attempt to find new terms
	foreach( $terms as $i => $term_id ) {
		
		$new_term_id = wp_get_split_term($term_id, $taxonomy);
		
		if( $new_term_id ) {
			
			$terms[ $i ] = $new_term_id;
			
		}
		
	}
	
	
	// return
	return $terms;
	
}


/*
*  acs_esc_html_deep
*
*  Navigates through an array and escapes html from the values.
*
*  @type	function
*  @date	10/06/2015
*  @since	5.2.7
*
*  @param	$value (mixed)
*  @return	$value
*/

/*
function acs_esc_html_deep( $value ) {
	
	// array
	if( is_array($value) ) {
		
		$value = array_map('acs_esc_html_deep', $value);
	
	// object
	} elseif( is_object($value) ) {
		
		$vars = get_object_vars( $value );
		
		foreach( $vars as $k => $v ) {
			
			$value->{$k} = acs_esc_html_deep( $v );
		
		}
		
	// string
	} elseif( is_string($value) ) {

		$value = esc_html($value);

	}
	
	
	// return
	return $value;

}
*/


/*
*  acs_validate_attachment
*
*  This function will validate an attachment based on a field's resrictions and return an array of errors
*
*  @type	function
*  @date	3/07/2015
*  @since	5.2.3
*
*  @param	$attachment (array) attachment data. Cahnges based on context
*  @param	$field (array) field settings containing restrictions
*  @param	$context (string) $file is different when uploading / preparing
*  @return	$errors (array)
*/

function acs_validate_attachment( $attachment, $field, $context = 'prepare' ) {
	
	// vars
	$errors = array();
	$file = array(
		'type'		=> '',
		'width'		=> 0,
		'height'	=> 0,
		'size'		=> 0
	);
	
	
	// upload
	if( $context == 'upload' ) {
		
		// vars
		$file['type'] = pathinfo($attachment['name'], PATHINFO_EXTENSION);
		$file['size'] = filesize($attachment['tmp_name']);
		
		if( strpos($attachment['type'], 'image') !== false ) {
			
			$size = getimagesize($attachment['tmp_name']);
			$file['width'] = acs_maybe_get($size, 0);
			$file['height'] = acs_maybe_get($size, 1);
				
		}
	
	// prepare
	} elseif( $context == 'prepare' ) {
		
		$file['type'] = pathinfo($attachment['url'], PATHINFO_EXTENSION);
		$file['size'] = acs_maybe_get($attachment, 'filesizeInBytes', 0);
		$file['width'] = acs_maybe_get($attachment, 'width', 0);
		$file['height'] = acs_maybe_get($attachment, 'height', 0);
	
	// custom
	} else {
		
		$file = array_merge($file, $attachment);
		$file['type'] = pathinfo($attachment['url'], PATHINFO_EXTENSION);
		
	}
	
	
	// image
	if( $file['width'] || $file['height'] ) {
		
		// width
		$min_width = (int) acs_maybe_get($field, 'min_width', 0);
		$max_width = (int) acs_maybe_get($field, 'max_width', 0);
		
		if( $file['width'] ) {
			
			if( $min_width && $file['width'] < $min_width ) {
				
				// min width
				$errors['min_width'] = sprintf(__('Image width must be at least %dpx.', 'acs'), $min_width );
				
			} elseif( $max_width && $file['width'] > $max_width ) {
				
				// min width
				$errors['max_width'] = sprintf(__('Image width must not exceed %dpx.', 'acs'), $max_width );
				
			}
			
		}
		
		
		// height
		$min_height = (int) acs_maybe_get($field, 'min_height', 0);
		$max_height = (int) acs_maybe_get($field, 'max_height', 0);
		
		if( $file['height'] ) {
			
			if( $min_height && $file['height'] < $min_height ) {
				
				// min height
				$errors['min_height'] = sprintf(__('Image height must be at least %dpx.', 'acs'), $min_height );
				
			}  elseif( $max_height && $file['height'] > $max_height ) {
				
				// min height
				$errors['max_height'] = sprintf(__('Image height must not exceed %dpx.', 'acs'), $max_height );
				
			}
			
		}
			
	}
	
	
	// file size
	if( $file['size'] ) {
		
		$min_size = acs_maybe_get($field, 'min_size', 0);
		$max_size = acs_maybe_get($field, 'max_size', 0);
		
		if( $min_size && $file['size'] < acs_get_filesize($min_size) ) {
				
			// min width
			$errors['min_size'] = sprintf(__('File size must be at least %s.', 'acs'), acs_format_filesize($min_size) );
			
		} elseif( $max_size && $file['size'] > acs_get_filesize($max_size) ) {
				
			// min width
			$errors['max_size'] = sprintf(__('File size must not exceed %s.', 'acs'), acs_format_filesize($max_size) );
			
		}
	
	}
	
	
	// file type
	if( $file['type'] ) {
		
		$mime_types = acs_maybe_get($field, 'mime_types', '');
		
		// lower case
		$file['type'] = strtolower($file['type']);
		$mime_types = strtolower($mime_types);
		
		
		// explode
		$mime_types = str_replace(array(' ', '.'), '', $mime_types);
		$mime_types = explode(',', $mime_types); // split pieces
		$mime_types = array_filter($mime_types); // remove empty pieces
		
		if( !empty($mime_types) && !in_array($file['type'], $mime_types) ) {
			
			// glue together last 2 types
			if( count($mime_types) > 1 ) {
				
				$last1 = array_pop($mime_types);
				$last2 = array_pop($mime_types);
				
				$mime_types[] = $last2 . ' ' . __('or', 'acs') . ' ' . $last1;
				
			}
			
			$errors['mime_types'] = sprintf(__('File type must be %s.', 'acs'), implode(', ', $mime_types) );
			
		}
				
	}
	
	
	/**
	*  Filters the errors for a file before it is uploaded or displayed in the media modal.
	*
	*  @date	3/07/2015
	*  @since	5.2.3
	*
	*  @param	array $errors An array of errors.
	*  @param	array $file An array of data for a single file.
	*  @param	array $attachment An array of attachment data which differs based on the context.
	*  @param	array $field The field array.
	*  @param	string $context The curent context (uploading, preparing)
	*/
	$errors = apply_filters( "acs/validate_attachment/type={$field['type']}",	$errors, $file, $attachment, $field, $context );
	$errors = apply_filters( "acs/validate_attachment/name={$field['_name']}", 	$errors, $file, $attachment, $field, $context );
	$errors = apply_filters( "acs/validate_attachment/key={$field['key']}", 	$errors, $file, $attachment, $field, $context );
	$errors = apply_filters( "acs/validate_attachment", 						$errors, $file, $attachment, $field, $context );
	
	
	// return
	return $errors;
	
}


/*
*  _acs_settings_uploader
*
*  Dynamic logic for uploader setting
*
*  @type	function
*  @date	7/05/2015
*  @since	5.2.3
*
*  @param	$uploader (string)
*  @return	$uploader
*/

add_filter('acs/settings/uploader', '_acs_settings_uploader');

function _acs_settings_uploader( $uploader ) {
	
	// if can't upload files
	if( !current_user_can('upload_files') ) {
		
		$uploader = 'basic';
		
	}
	
	
	// return
	return $uploader;
}


/*
*  acs_translate_keys
*
*  description
*
*  @type	function
*  @date	7/12/2015
*  @since	5.3.2
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

/*
function acs_translate_keys( $array, $keys ) {
	
	// bail early if no keys
	if( empty($keys) ) return $array;
	
	
	// translate
	foreach( $keys as $k ) {
		
		// bail ealry if not exists
		if( !isset($array[ $k ]) ) continue;
		
		
		// translate
		$array[ $k ] = acs_translate( $array[ $k ] );
		
	}
	
	
	// return
	return $array;
	
}
*/


/*
*  acs_translate
*
*  This function will translate a string using the new 'l10n_textdomain' setting
*  Also works for arrays which is great for fields - select -> choices
*
*  @type	function
*  @date	4/12/2015
*  @since	5.3.2
*
*  @param	$string (mixed) string or array containins strings to be translated
*  @return	$string
*/

function acs_translate( $string ) {
	
	// vars
	$l10n = acs_get_setting('l10n');
	$textdomain = acs_get_setting('l10n_textdomain');
	
	
	// bail early if not enabled
	if( !$l10n ) return $string;
	
	
	// bail early if no textdomain
	if( !$textdomain ) return $string;
	
	
	// is array
	if( is_array($string) ) {
		
		return array_map('acs_translate', $string);
		
	}
	
	
	// bail early if not string
	if( !is_string($string) ) return $string;
	
	
	// bail early if empty
	if( $string === '' ) return $string;
	
	
	// allow for var_export export
	if( acs_get_setting('l10n_var_export') ){
		
		// bail early if already translated
		if( substr($string, 0, 7) === '!!__(!!' ) return $string;
		
		
		// return
		return "!!__(!!'" .  $string . "!!', !!'" . $textdomain . "!!')!!";
			
	}
	
	
	// vars
	return __( $string, $textdomain );
	
}


/*
*  acs_maybe_add_action
*
*  This function will determine if the action has already run before adding / calling the function
*
*  @type	function
*  @date	13/01/2016
*  @since	5.3.2
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function acs_maybe_add_action( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
	
	// if action has already run, execute it
	// - if currently doing action, allow $tag to be added as per usual to allow $priority ordering needed for 3rd party asset compatibility
	if( did_action($tag) && !doing_action($tag) ) {
			
		call_user_func( $function_to_add );
	
	// if action has not yet run, add it
	} else {
		
		add_action( $tag, $function_to_add, $priority, $accepted_args );
		
	}
	
}


/*
*  acs_is_row_collapsed
*
*  This function will return true if the field's row is collapsed
*
*  @type	function
*  @date	2/03/2016
*  @since	5.3.2
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function acs_is_row_collapsed( $field_key = '', $row_index = 0 ) {
	
	// collapsed
	$collapsed = acs_get_user_setting('collapsed_' . $field_key, '');
	
	
	// cookie fallback ( version < 5.3.2 )
	if( $collapsed === '' ) {
		
		$collapsed = acs_extract_var($_COOKIE, "acs_collapsed_{$field_key}", '');
		$collapsed = str_replace('|', ',', $collapsed);
		
		
		// update
		acs_update_user_setting( 'collapsed_' . $field_key, $collapsed );
			
	}
	
	
	// explode
	$collapsed = explode(',', $collapsed);
	$collapsed = array_filter($collapsed, 'is_numeric');
	
	
	// collapsed class
	return in_array($row_index, $collapsed);
	
}


/*
*  acs_get_attachment_image
*
*  description
*
*  @type	function
*  @date	24/10/16
*  @since	5.5.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function acs_get_attachment_image( $attachment_id = 0, $size = 'thumbnail' ) {
	
	// vars
	$url = wp_get_attachment_image_src($attachment_id, 'thumbnail');
	$alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
	
	
	// bail early if no url
	if( !$url ) return '';
	
	
	// return
	$value = '<img src="' . $url . '" alt="' . $alt . '" />';
	
}


/*
*  acs_get_post_thumbnail
*
*  This function will return a thumbail image url for a given post
*
*  @type	function
*  @date	3/05/2016
*  @since	5.3.8
*
*  @param	$post (obj)
*  @param	$size (mixed)
*  @return	(string)
*/

function acs_get_post_thumbnail( $post = null, $size = 'thumbnail' ) {
	
	// vars
	$data = array(
		'url'	=> '',
		'type'	=> '',
		'html'	=> ''
	);
	
	
	// post
	$post = get_post($post);
	
    
	// bail early if no post
	if( !$post ) return $data;
	
	
	// vars
	$thumb_id = $post->ID;
	$mime_type = acs_maybe_get(explode('/', $post->post_mime_type), 0);
	
	
	// attachment
	if( $post->post_type === 'attachment' ) {
		
		// change $thumb_id
		if( $mime_type === 'audio' || $mime_type === 'video' ) {
			
			$thumb_id = get_post_thumbnail_id($post->ID);
			
		}
	
	// post
	} else {
		
		$thumb_id = get_post_thumbnail_id($post->ID);
			
	}
	
	
	// try url
	$data['url'] = wp_get_attachment_image_src($thumb_id, $size);
	$data['url'] = acs_maybe_get($data['url'], 0);
	
	
	// default icon
	if( !$data['url'] && $post->post_type === 'attachment' ) {
		
		$data['url'] = wp_mime_type_icon($post->ID);
		$data['type'] = 'icon';
		
	}
	
	
	// html
	$data['html'] = '<img src="' . $data['url'] . '" alt="" />';
	
	
	// return
	return $data;
	
}

/**
 * acs_get_browser
 *
 * Returns the name of the current browser.
 *
 * @date	17/01/2014
 * @since	5.0.0
 *
 * @param	void
 * @return	string
 */
function acs_get_browser() {
	
	// Check server var.
	if( isset($_SERVER['HTTP_USER_AGENT']) ) {
		$agent = $_SERVER['HTTP_USER_AGENT'];
		
		// Loop over search terms.
		$browsers = array(
			'Firefox'	=> 'firefox',
			'Trident'	=> 'msie',
			'MSIE'		=> 'msie',
			'Edge'		=> 'edge',
			'Chrome'	=> 'chrome',
			'Safari'	=> 'safari',
		);
		foreach( $browsers as $k => $v ) {
			if( strpos($agent, $k) !== false ) {
				return $v;
			}
		}
	}
	
	// Return default.
	return '';
}


/*
*  acs_is_ajax
*
*  This function will reutrn true if performing a wp ajax call
*
*  @type	function
*  @date	7/06/2016
*  @since	5.3.8
*
*  @param	n/a
*  @return	(boolean)
*/

function acs_is_ajax( $action = '' ) {
	
	// vars
	$is_ajax = false;
	
	
	// check if is doing ajax
	if( defined('DOING_AJAX') && DOING_AJAX ) {
		
		$is_ajax = true;
		
	}
	
	
	// check $action
	if( $action && acs_maybe_get($_POST, 'action') !== $action ) {
		
		$is_ajax = false;
		
	}
	
	
	// return
	return $is_ajax;
		
}




/*
*  acs_format_date
*
*  This function will accept a date value and return it in a formatted string
*
*  @type	function
*  @date	16/06/2016
*  @since	5.3.8
*
*  @param	$value (string)
*  @return	$format (string)
*/

function acs_format_date( $value, $format ) {
	
	// bail early if no value
	if( !$value ) return $value;
	
	
	// vars
	$unixtimestamp = 0;
	
	
	// numeric (either unix or YYYYMMDD)
	if( is_numeric($value) && strlen($value) !== 8 ) {
		
		$unixtimestamp = $value;
		
	} else {
		
		$unixtimestamp = strtotime($value);
		
	}
	
	
	// return
	return date_i18n($format, $unixtimestamp);
	
}

/**
 * acs_clear_log
 *
 * Deletes the debug.log file.
 *
 * @date	21/1/19
 * @since	5.7.10
 *
 * @param	type $var Description. Default.
 * @return	type Description.
 */
function acs_clear_log() {
	unlink( WP_CONTENT_DIR . '/debug.log' );
}

/*
*  acs_log
*
*  description
*
*  @type	function
*  @date	24/06/2016
*  @since	5.3.8
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function acs_log() {
	
	// vars
	$args = func_get_args();
	
	// loop
	foreach( $args as $i => $arg ) {
		
		// array | object
		if( is_array($arg) || is_object($arg) ) {
			$arg = print_r($arg, true);
		
		// bool	
		} elseif( is_bool($arg) ) {
			$arg = 'bool(' . ( $arg ? 'true' : 'false' ) . ')';
		}
		
		// update
		$args[ $i ] = $arg;
	}
	
	// log
	error_log( implode(' ', $args) );
}

/**
*  acs_dev_log
*
*  Used to log variables only if ACS_DEV is defined
*
*  @date	25/8/18
*  @since	5.7.4
*
*  @param	mixed
*  @return	void
*/
function acs_dev_log() {
	if( defined('ACS_DEV') && ACS_DEV ) {
		call_user_func_array('acs_log', func_get_args());
	}
}

/*
*  acs_doing
*
*  This function will tell ACS what task it is doing
*
*  @type	function
*  @date	28/06/2016
*  @since	5.3.8
*
*  @param	$event (string)
*  @param	context (string)
*  @return	n/a
*/

function acs_doing( $event = '', $context = '' ) {
	
	acs_update_setting( 'doing', $event );
	acs_update_setting( 'doing_context', $context );
	
}


/*
*  acs_is_doing
*
*  This function can be used to state what ACS is doing, or to check
*
*  @type	function
*  @date	28/06/2016
*  @since	5.3.8
*
*  @param	$event (string)
*  @param	context (string)
*  @return	(boolean)
*/

function acs_is_doing( $event = '', $context = '' ) {
	
	// vars
	$doing = false;
	
	
	// task
	if( acs_get_setting('doing') === $event ) {
		
		$doing = true;
		
	}
	
	
	// context
	if( $context && acs_get_setting('doing_context') !== $context ) {
		
		$doing = false;
		
	}
	
	
	// return
	return $doing;
		
}


/*
*  acs_is_plugin_active
*
*  This function will return true if the ACS plugin is active
*  - May be included within a theme or other plugin
*
*  @type	function
*  @date	13/07/2016
*  @since	5.4.0
*
*  @param	$basename (int)
*  @return	$post_id (int)
*/


function acs_is_plugin_active() {
	
	// vars
	$basename = acs_get_setting('basename');
	
	
	// ensure is_plugin_active() exists (not on frontend)
	if( !function_exists('is_plugin_active') ) {
		
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
	}
	
	
	// return
	return is_plugin_active($basename);
	
}

/*
*  acs_send_ajax_results
*
*  This function will print JSON data for a Select2 AJAX query
*
*  @type	function
*  @date	19/07/2016
*  @since	5.4.0
*
*  @param	$response (array)
*  @return	n/a
*/

function acs_send_ajax_results( $response ) {
	
	// validate
	$response = wp_parse_args($response, array(
		'results'	=> array(),
		'more'		=> false,
		'limit'		=> 0
	));
	
	
	// limit
	if( $response['limit'] && $response['results']) {
		
		// vars
		$total = 0;
		
		foreach( $response['results'] as $result ) {
			
			// parent
			$total++;
			
			
			// children
			if( !empty($result['children']) ) {
				
				$total += count( $result['children'] );
				
			}
			
		}
		
		
		// calc
		if( $total >= $response['limit'] ) {
			
			$response['more'] = true;
			
		}
		
	}
	
	
	// return
	wp_send_json( $response );
	
}


/*
*  acs_is_sequential_array
*
*  This function will return true if the array contains only numeric keys
*
*  @source	http://stackoverflow.com/questions/173400/how-to-check-if-php-array-is-associative-or-sequential
*  @type	function
*  @date	9/09/2016
*  @since	5.4.0
*
*  @param	$array (array)
*  @return	(boolean)
*/

function acs_is_sequential_array( $array ) {
	
	// bail ealry if not array
	if( !is_array($array) ) return false;
	
	
	// loop
	foreach( $array as $key => $value ) {
		
		// bail ealry if is string
		if( is_string($key) ) return false;
	
	}
	
	
	// return
	return true;
	
}


/*
*  acs_is_associative_array
*
*  This function will return true if the array contains one or more string keys
*
*  @source	http://stackoverflow.com/questions/173400/how-to-check-if-php-array-is-associative-or-sequential
*  @type	function
*  @date	9/09/2016
*  @since	5.4.0
*
*  @param	$array (array)
*  @return	(boolean)
*/

function acs_is_associative_array( $array ) {
	
	// bail ealry if not array
	if( !is_array($array) ) return false;
	
	
	// loop
	foreach( $array as $key => $value ) {
		
		// bail ealry if is string
		if( is_string($key) ) return true;
	
	}
	
	
	// return
	return false;
	
}


/*
*  acs_add_array_key_prefix
*
*  This function will add a prefix to all array keys
*  Useful to preserve numeric keys when performing array_multisort
*
*  @type	function
*  @date	15/09/2016
*  @since	5.4.0
*
*  @param	$array (array)
*  @param	$prefix (string)
*  @return	(array)
*/

function acs_add_array_key_prefix( $array, $prefix ) {
	
	// vars
	$array2 = array();
	
	
	// loop
	foreach( $array as $k => $v ) {
		
		$k2 = $prefix . $k;
	    $array2[ $k2 ] = $v;
	    
	}
	
	
	// return
	return $array2;
	
}


/*
*  acs_remove_array_key_prefix
*
*  This function will remove a prefix to all array keys
*  Useful to preserve numeric keys when performing array_multisort
*
*  @type	function
*  @date	15/09/2016
*  @since	5.4.0
*
*  @param	$array (array)
*  @param	$prefix (string)
*  @return	(array)
*/

function acs_remove_array_key_prefix( $array, $prefix ) {
	
	// vars
	$array2 = array();
	$l = strlen($prefix);
	
	
	// loop
	foreach( $array as $k => $v ) {
		
		$k2 = (substr($k, 0, $l) === $prefix) ? substr($k, $l) : $k;
	    $array2[ $k2 ] = $v;
	    
	}
	
	
	// return
	return $array2;
	
}


/*
*  acs_strip_protocol
*
*  This function will remove the proticol from a url 
*  Used to allow licences to remain active if a site is switched to https 
*
*  @type	function
*  @date	10/01/2017
*  @since	5.5.4
*  @author	Aaron 
*
*  @param	$url (string)
*  @return	(string) 
*/

function acs_strip_protocol( $url ) {
		
	// strip the protical 
	return str_replace(array('http://','https://'), '', $url);

}


/*
*  acs_connect_attachment_to_post
*
*  This function will connect an attacment (image etc) to the post 
*  Used to connect attachements uploaded directly to media that have not been attaced to a post
*
*  @type	function
*  @date	11/01/2017
*  @since	5.8.0 Added filter to prevent connection.
*  @since	5.5.4
*
*  @param	int $attachment_id The attachment ID.
*  @param	int $post_id The post ID.
*  @return	bool True if attachment was connected.
*/
function acs_connect_attachment_to_post( $attachment_id = 0, $post_id = 0 ) {
	
	// Bail ealry if $attachment_id is not valid.
	if( !$attachment_id || !is_numeric($attachment_id) ) {
		return false;
	}
	
	// Bail ealry if $post_id is not valid.
	if( !$post_id || !is_numeric($post_id) ) {
		return false;
	}
	
	/**
	*  Filters whether or not to connect the attachment.
	*
	*  @date	8/11/18
	*  @since	5.8.0
	*
	*  @param	bool $bool Returning false will prevent the connection. Default true.
	*  @param	int $attachment_id The attachment ID.
	*  @param	int $post_id The post ID.
	*/
	if( !apply_filters('acs/connect_attachment_to_post', true, $attachment_id, $post_id) ) {
		return false;
	}
	
	// vars 
	$post = get_post( $attachment_id );
	
	// Check if is valid post.
	if( $post && $post->post_type == 'attachment' && $post->post_parent == 0 ) {
		
		// update
		wp_update_post( array('ID' => $post->ID, 'post_parent' => $post_id) );
		
		// return
		return true;
	}
	
	// return
	return true;
}


/*
*  acs_encrypt
*
*  This function will encrypt a string using PHP
*  https://bhoover.com/using-php-openssl_encrypt-openssl_decrypt-encrypt-decrypt-data/
*
*  @type	function
*  @date	27/2/17
*  @since	5.5.8
*
*  @param	$data (string)
*  @return	(string)
*/


function acs_encrypt( $data = '' ) {
	
	// bail ealry if no encrypt function
	if( !function_exists('openssl_encrypt') ) return base64_encode($data);
	
	
	// generate a key
	$key = wp_hash('acs_encrypt');
	
	
    // Generate an initialization vector
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    
    
    // Encrypt the data using AES 256 encryption in CBC mode using our encryption key and initialization vector.
    $encrypted_data = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
    
    
    // The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (::)
    return base64_encode($encrypted_data . '::' . $iv);
	
}


/*
*  acs_decrypt
*
*  This function will decrypt an encrypted string using PHP
*  https://bhoover.com/using-php-openssl_encrypt-openssl_decrypt-encrypt-decrypt-data/
*
*  @type	function
*  @date	27/2/17
*  @since	5.5.8
*
*  @param	$data (string)
*  @return	(string)
*/

function acs_decrypt( $data = '' ) {
	
	// bail ealry if no decrypt function
	if( !function_exists('openssl_decrypt') ) return base64_decode($data);
	
	
	// generate a key
	$key = wp_hash('acs_encrypt');
	
	
    // To decrypt, split the encrypted data from our IV - our unique separator used was "::"
    list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
    
    
    // decrypt
    return openssl_decrypt($encrypted_data, 'aes-256-cbc', $key, 0, $iv);
	
}

/**
*  acs_parse_markdown
*
*  A very basic regex-based Markdown parser function based off [slimdown](https://gist.github.com/jbroadway/2836900).
*
*  @date	6/8/18
*  @since	5.7.2
*
*  @param	string $text The string to parse.
*  @return	string
*/

function acs_parse_markdown( $text = '' ) {
	
	// trim
	$text = trim($text);
	
	// rules
	$rules = array (
		'/=== (.+?) ===/'				=> '<h2>$1</h2>',					// headings
		'/== (.+?) ==/'					=> '<h3>$1</h3>',					// headings
		'/= (.+?) =/'					=> '<h4>$1</h4>',					// headings
		'/\[([^\[]+)\]\(([^\)]+)\)/' 	=> '<a href="$2">$1</a>',			// links
		'/(\*\*)(.*?)\1/' 				=> '<strong>$2</strong>',			// bold
		'/(\*)(.*?)\1/' 				=> '<em>$2</em>',					// intalic
		'/`(.*?)`/'						=> '<code>$1</code>',				// inline code
		'/\n\*(.*)/'					=> "\n<ul>\n\t<li>$1</li>\n</ul>",	// ul lists
		'/\n[0-9]+\.(.*)/'				=> "\n<ol>\n\t<li>$1</li>\n</ol>",	// ol lists
		'/<\/ul>\s?<ul>/'				=> '',								// fix extra ul
		'/<\/ol>\s?<ol>/'				=> '',								// fix extra ol
	);
	foreach( $rules as $k => $v ) {
		$text = preg_replace($k, $v, $text);
	}
		
	// autop
	$text = wpautop($text);
	
	// return
	return $text;
}

/**
*  acs_get_sites
*
*  Returns an array of sites for a network.
*
*  @date	29/08/2016
*  @since	5.4.0
*
*  @param	void
*  @return	array
*/
function acs_get_sites() {
	$results = array();
	$sites = get_sites( array( 'number' => 0 ) );
	if( $sites ) {
		foreach( $sites as $site ) {
	        $results[] = get_site( $site )->to_array();
	    }
	}
	return $results;
}

/**
*  acs_convert_rules_to_groups
*
*  Converts an array of rules from ACS4 to an array of groups for ACS5
*
*  @date	25/8/18
*  @since	5.7.4
*
*  @param	array $rules An array of rules.
*  @param	string $anyorall The anyorall setting used in ACS4. Defaults to 'any'.
*  @return	array
*/
function acs_convert_rules_to_groups( $rules, $anyorall = 'any' ) {
	
	// vars
	$groups = array();
	$index = 0;
	
	// loop
	foreach( $rules as $rule ) {
		
		// extract vars
		$group = acs_extract_var( $rule, 'group_no' );
		$order = acs_extract_var( $rule, 'order_no' );
		
		// calculate group if not defined
		if( $group === null ) {
			$group = $index;
			
			// use $anyorall to determine if a new group is needed
			if( $anyorall == 'any' ) {
				$index++;
			}
		}
		
		// calculate order if not defined
		if( $order === null ) {
			$order = isset($groups[ $group ]) ? count($groups[ $group ]) : 0;
		}
		
		// append to group
		$groups[ $group ][ $order ] = $rule;
		
		// sort groups
		ksort( $groups[ $group ] );
	}
	
	// sort groups
	ksort( $groups );
	
	// return
	return $groups;
}

/**
*  acs_register_ajax
*
*  Regsiters an ajax callback.
*
*  @date	5/10/18
*  @since	5.7.7
*
*  @param	string $name The ajax action name.
*  @param	array $callback The callback function or array.
*  @param	bool $public Whether to allow access to non logged in users.
*  @return	void
*/
function acs_register_ajax( $name = '', $callback = false, $public = false ) {
	
	// vars
	$action = "acs/ajax/$name";
	
	// add action for logged-in users
	add_action( "wp_ajax_$action", $callback );
	
	// add action for non logged-in users
	if( $public ) {
		add_action( "wp_ajax_nopriv_$action", $callback );
	}
}

/**
*  acs_str_camel_case
*
*  Converts a string into camelCase.
*  Thanks to https://stackoverflow.com/questions/31274782/convert-array-keys-from-underscore-case-to-camelcase-recursively
*
*  @date	24/10/18
*  @since	5.8.0
*
*  @param	string $string The string ot convert.
*  @return	string
*/
function acs_str_camel_case( $string = '' ) {
	return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
}

/**
*  acs_array_camel_case
*
*  Converts all aray keys to camelCase.
*
*  @date	24/10/18
*  @since	5.8.0
*
*  @param	array $array The array to convert.
*  @return	array
*/
function acs_array_camel_case( $array = array() ) {
	$array2 = array();
	foreach( $array as $k => $v ) {
		$array2[ acs_str_camel_case($k) ] = $v;
	}
	return $array2;
}

/**
 * Returns true if the current screen is using the block editor.
 *
 * @date 13/12/18
 * @since 5.8.0
 *
 * @return bool
 */
function acs_is_block_editor() {
	if ( function_exists( 'get_current_screen' ) ) {
		$screen = get_current_screen();
		if( $screen && method_exists( $screen, 'is_block_editor' ) ) {
			return $screen->is_block_editor();
		}
	}
	return false;
}
