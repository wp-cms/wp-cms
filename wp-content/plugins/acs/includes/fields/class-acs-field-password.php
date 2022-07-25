<?php

if( ! class_exists('acs_field_password') ) :

class acs_field_password extends acs_field {
	
	
	/*
	*  initialize
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function initialize() {
		
		// vars
		$this->name = 'password';
		$this->label = __("Password",'acs');
		$this->defaults = array(
			'placeholder'	=> '',
			'prepend'		=> '',
			'append'		=> '',
		);
		
	}
		
	
	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function render_field( $field ) {
		
		acs_get_field_type('text')->render_field( $field );
		
	}
	
	
	/*
	*  render_field_settings()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function render_field_settings( $field ) {
		
		// placeholder
		acs_render_field_setting( $field, array(
			'label'			=> __('Placeholder Text','acs'),
			'instructions'	=> __('Appears within the input','acs'),
			'type'			=> 'text',
			'name'			=> 'placeholder',
		));
		
		
		// prepend
		acs_render_field_setting( $field, array(
			'label'			=> __('Prepend','acs'),
			'instructions'	=> __('Appears before the input','acs'),
			'type'			=> 'text',
			'name'			=> 'prepend',
		));
		
		
		// append
		acs_render_field_setting( $field, array(
			'label'			=> __('Append','acs'),
			'instructions'	=> __('Appears after the input','acs'),
			'type'			=> 'text',
			'name'			=> 'append',
		));
	}
	
}


// initialize
acs_register_field_type( 'acs_field_password' );

endif; // class_exists check

?>