<?php

if( ! class_exists('acs_field__accordion') ) :

class acs_field__accordion extends acs_field {
	
	
	/**
	*  initialize
	*
	*  This function will setup the field type data
	*
	*  @date	30/10/17
	*  @since	5.6.3
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function initialize() {
		
		// vars
		$this->name = 'accordion';
		$this->label = __("Accordion",'acs');
		$this->category = 'layout';
		$this->defaults = array(
			'open'			=> 0,
			'multi_expand'	=> 0,
			'endpoint'		=> 0
		);
		
	}
	
	
	/**
	*  render_field
	*
	*  Create the HTML interface for your field
	*
	*  @date	30/10/17
	*  @since	5.6.3
	*
	*  @param	array $field
	*  @return	n/a
	*/
	
	function render_field( $field ) {
		
		// vars
		$atts = array(
			'class'				=> 'acs-fields',
			'data-open'			=> $field['open'],
			'data-multi_expand'	=> $field['multi_expand'],
			'data-endpoint'		=> $field['endpoint']
		);
		
		?>
		<div <?php acs_esc_attr_e($atts); ?>></div>
		<?php
		
	}
	
	
	
	/*
	*  render_field_settings()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @param	$field	- an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function render_field_settings( $field ) {
		
/*
		// message
		$message = '';
		$message .= '<p>' . __( 'Accordions help you organize fields into panels that open and close.', 'acs') . '</p>';
		$message .= '<p>' . __( 'All fields following this accordion (or until another accordion is defined) will be grouped together.','acs') . '</p>';
		
		
		// default_value
		acs_render_field_setting( $field, array(
			'label'			=> __('Instructions','acs'),
			'instructions'	=> '',
			'name'			=> 'notes',
			'type'			=> 'message',
			'message'		=> $message,
		));
*/
		
		// active
		acs_render_field_setting( $field, array(
			'label'			=> __('Open','acs'),
			'instructions'	=> __('Display this accordion as open on page load.','acs'),
			'name'			=> 'open',
			'type'			=> 'true_false',
			'ui'			=> 1,
		));
		
		
		// multi_expand
		acs_render_field_setting( $field, array(
			'label'			=> __('Multi-expand','acs'),
			'instructions'	=> __('Allow this accordion to open without closing others.','acs'),
			'name'			=> 'multi_expand',
			'type'			=> 'true_false',
			'ui'			=> 1,
		));
		
		
		// endpoint
		acs_render_field_setting( $field, array(
			'label'			=> __('Endpoint','acs'),
			'instructions'	=> __('Define an endpoint for the previous accordion to stop. This accordion will not be visible.','acs'),
			'name'			=> 'endpoint',
			'type'			=> 'true_false',
			'ui'			=> 1,
		));
					
	}
	
	
	/*
	*  load_field()
	*
	*  This filter is appied to the $field after it is loaded from the database
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$field - the field array holding all the field options
	*/
	
	function load_field( $field ) {
		
		// remove name to avoid caching issue
		$field['name'] = '';
		
		// remove required to avoid JS issues
		$field['required'] = 0;
		
		// set value other than 'null' to avoid ACS loading / caching issue
		$field['value'] = false;
		
		// return
		return $field;
		
	}
	
}


// initialize
acs_register_field_type( 'acs_field__accordion' );

endif; // class_exists check

?>