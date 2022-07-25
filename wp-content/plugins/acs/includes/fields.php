<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('acs_fields') ) :

class acs_fields {
	
	/** @var array Contains an array of field type instances */
	var $types = array();
	
	
	/*
	*  __construct
	*
	*  This function will setup the class functionality
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.4.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
		/* do nothing */
	}
	
	
	/*
	*  register_field_type
	*
	*  This function will register a field type instance
	*
	*  @type	function
	*  @date	6/07/2016
	*  @since	5.4.0
	*
	*  @param	$class (string)
	*  @return	n/a
	*/
	
	function register_field_type( $class ) {
		
		// allow instance
		if( $class instanceOf acs_field ) {
			$this->types[ $class->name ] = $class;
		
		// allow class name
		} else {
			$instance = new $class();
			$this->types[ $instance->name ] = $instance;
		}
	}
	
	
	/*
	*  get_field_type
	*
	*  This function will return a field type instance
	*
	*  @type	function
	*  @date	6/07/2016
	*  @since	5.4.0
	*
	*  @param	$name (string)
	*  @return	(mixed)
	*/
	
	function get_field_type( $name ) {
		return isset( $this->types[$name] ) ? $this->types[$name] : null;
	}
	
	
	/*
	*  is_field_type
	*
	*  This function will return true if a field type exists
	*
	*  @type	function
	*  @date	6/07/2016
	*  @since	5.4.0
	*
	*  @param	$name (string)
	*  @return	(mixed)
	*/
	
	function is_field_type( $name ) {
		return isset( $this->types[$name] );
	}
	
	
	/*
	*  register_field_type_info
	*
	*  This function will store a basic array of info about the field type
	*  to later be overriden by the above register_field_type function
	*
	*  @type	function
	*  @date	29/5/17
	*  @since	5.6.0
	*
	*  @param	$info (array)
	*  @return	n/a
	*/
	
	function register_field_type_info( $info ) {
		
		// convert to object
		$instance = (object) $info;
		$this->types[ $instance->name ] = $instance;
	}	
	
	
	/*
	*  get_field_types
	*
	*  This function will return an array of all field types
	*
	*  @type	function
	*  @date	6/07/2016
	*  @since	5.4.0
	*
	*  @param	$name (string)
	*  @return	(mixed)
	*/
	
	function get_field_types() {
		return $this->types;
	}
}


// initialize
acs()->fields = new acs_fields();

endif; // class_exists check


/*
*  acs_register_field_type
*
*  alias of acs()->fields->register_field_type()
*
*  @type	function
*  @date	31/5/17
*  @since	5.6.0
*
*  @param	n/a
*  @return	n/a
*/

function acs_register_field_type( $class ) {
	return acs()->fields->register_field_type( $class );
}


/*
*  acs_register_field_type_info
*
*  alias of acs()->fields->register_field_type_info()
*
*  @type	function
*  @date	31/5/17
*  @since	5.6.0
*
*  @param	n/a
*  @return	n/a
*/

function acs_register_field_type_info( $info ) {
	return acs()->fields->register_field_type_info( $info );
}


/*
*  acs_get_field_type
*
*  alias of acs()->fields->get_field_type()
*
*  @type	function
*  @date	31/5/17
*  @since	5.6.0
*
*  @param	n/a
*  @return	n/a
*/

function acs_get_field_type( $name ) {
	return acs()->fields->get_field_type( $name );
}


/*
*  acs_get_field_types
*
*  alias of acs()->fields->get_field_types()
*
*  @type	function
*  @date	31/5/17
*  @since	5.6.0
*
*  @param	n/a
*  @return	n/a
*/

function acs_get_field_types( $args = array() ) {
	
	// default
	$args = wp_parse_args($args, array(
		'public'	=> true,	// true, false
	));
	
	// get field types
	$field_types = acs()->fields->get_field_types();
	
	// filter
    return wp_filter_object_list( $field_types, $args );
}


/**
*  acs_get_field_types_info
*
*  Returns an array containing information about each field type
*
*  @date	18/6/18
*  @since	5.6.9
*
*  @param	type $var Description. Default.
*  @return	type Description.
*/

function acs_get_field_types_info( $args = array() ) {
	
	// vars
	$data = array();
	$field_types = acs_get_field_types();
	
	// loop
	foreach( $field_types as $type ) {
		$data[ $type->name ] = array(
			'label'		=> $type->label,
			'name'		=> $type->name,
			'category'	=> $type->category,
			'public'	=> $type->public
		);
	}
	
	// return
	return $data;
}


/*
*  acs_is_field_type
*
*  alias of acs()->fields->is_field_type()
*
*  @type	function
*  @date	31/5/17
*  @since	5.6.0
*
*  @param	n/a
*  @return	n/a
*/

function acs_is_field_type( $name = '' ) {
	return acs()->fields->is_field_type( $name );
}


/*
*  acs_get_field_type_prop
*
*  This function will return a field type's property
*
*  @type	function
*  @date	1/10/13
*  @since	5.0.0
*
*  @param	n/a
*  @return	(array)
*/

function acs_get_field_type_prop( $name = '', $prop = '' ) {
	$type = acs_get_field_type( $name );
	return ($type && isset($type->$prop)) ? $type->$prop : null;
}


/*
*  acs_get_field_type_label
*
*  This function will return the label of a field type
*
*  @type	function
*  @date	1/10/13
*  @since	5.0.0
*
*  @param	n/a
*  @return	(array)
*/

function acs_get_field_type_label( $name = '' ) {
	$label = acs_get_field_type_prop( $name, 'label' );
	return $label ? $label : '<span class="acs-tooltip-js" title="'.__('Field type does not exist', 'acs').'">'.__('Unknown', 'acs').'</span>';
}


/*
*  acs_field_type_exists (deprecated)
*
*  deprecated in favour of acs_is_field_type()
*
*  @type	function
*  @date	1/10/13
*  @since	5.0.0
*
*  @param	$type (string)
*  @return	(boolean)
*/

function acs_field_type_exists( $type = '' ) {
	return acs_is_field_type( $type );
}


/*
*  acs_get_grouped_field_types
*
*  Returns an multi-dimentional array of field types "name => label" grouped by category
*
*  @type	function
*  @date	1/10/13
*  @since	5.0.0
*
*  @param	n/a
*  @return	(array)
*/

function acs_get_grouped_field_types() {
	
	// vars
	$types = acs_get_field_types();
	$groups = array();
	$l10n = array(
		'basic'			=> __('Basic', 'acs'),
		'content'		=> __('Content', 'acs'),
		'choice'		=> __('Choice', 'acs'),
		'relational'	=> __('Relational', 'acs'),
		'jquery'		=> __('jQuery', 'acs'),
		'layout'		=> __('Layout', 'acs'),
	);
	
	
	// loop
	foreach( $types as $type ) {
		
		// translate
		$cat = $type->category;
		$cat = isset( $l10n[$cat] ) ? $l10n[$cat] : $cat;
		
		// append
		$groups[ $cat ][ $type->name ] = $type->label;
	}
	
	
	// filter
	$groups = apply_filters('acs/get_field_types', $groups);
	
	
	// return
	return $groups;
}

?>