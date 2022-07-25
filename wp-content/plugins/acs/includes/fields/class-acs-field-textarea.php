<?php

if( ! class_exists('acs_field_textarea') ) :

class acs_field_textarea extends acs_field {
	
	
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
		$this->name = 'textarea';
		$this->label = __("Text Area",'acs');
		$this->defaults = array(
			'default_value'	=> '',
			'new_lines'		=> '',
			'maxlength'		=> '',
			'placeholder'	=> '',
			'rows'			=> ''
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
		
		// vars
		$atts = array();
		$keys = array( 'id', 'class', 'name', 'value', 'placeholder', 'rows', 'maxlength' );
		$keys2 = array( 'readonly', 'disabled', 'required' );
		
		
		// rows
		if( !$field['rows'] ) {
			$field['rows'] = 8;
		}
		
		
		// atts (value="123")
		foreach( $keys as $k ) {
			if( isset($field[ $k ]) ) $atts[ $k ] = $field[ $k ];
		}
		
		
		// atts2 (disabled="disabled")
		foreach( $keys2 as $k ) {
			if( !empty($field[ $k ]) ) $atts[ $k ] = $k;
		}
		
		
		// remove empty atts
		$atts = acs_clean_atts( $atts );
		
		
		// return
		acs_textarea_input( $atts );
		
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
		
		// default_value
		acs_render_field_setting( $field, array(
			'label'			=> __('Default Value','acs'),
			'instructions'	=> __('Appears when creating a new post','acs'),
			'type'			=> 'textarea',
			'name'			=> 'default_value',
		));
		
		
		// placeholder
		acs_render_field_setting( $field, array(
			'label'			=> __('Placeholder Text','acs'),
			'instructions'	=> __('Appears within the input','acs'),
			'type'			=> 'text',
			'name'			=> 'placeholder',
		));
		
		
		// maxlength
		acs_render_field_setting( $field, array(
			'label'			=> __('Character Limit','acs'),
			'instructions'	=> __('Leave blank for no limit','acs'),
			'type'			=> 'number',
			'name'			=> 'maxlength',
		));
		
		
		// rows
		acs_render_field_setting( $field, array(
			'label'			=> __('Rows','acs'),
			'instructions'	=> __('Sets the textarea height','acs'),
			'type'			=> 'number',
			'name'			=> 'rows',
			'placeholder'	=> 8
		));
		
		
		// formatting
		acs_render_field_setting( $field, array(
			'label'			=> __('New Lines','acs'),
			'instructions'	=> __('Controls how new lines are rendered','acs'),
			'type'			=> 'select',
			'name'			=> 'new_lines',
			'choices'		=> array(
				'wpautop'		=> __("Automatically add paragraphs",'acs'),
				'br'			=> __("Automatically add &lt;br&gt;",'acs'),
				''				=> __("No Formatting",'acs')
			)
		));
		
	}
	
	
	/*
	*  format_value()
	*
	*  This filter is applied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the modified value
	*/
	
	function format_value( $value, $post_id, $field ) {
		
		// bail early if no value or not for template
		if( empty($value) || !is_string($value) ) {
			
			return $value;
		
		}
				
		
		// new lines
		if( $field['new_lines'] == 'wpautop' ) {
			
			$value = wpautop($value);
			
		} elseif( $field['new_lines'] == 'br' ) {
			
			$value = nl2br($value);
			
		}
		
		
		// return
		return $value;
	}
	
	/**
	 * validate_value
	 *
	 * Validates a field's value.
	 *
	 * @date	29/1/19
	 * @since	5.7.11
	 *
	 * @param	(bool|string) Whether the value is vaid or not.
	 * @param	mixed $value The field value.
	 * @param	array $field The field array.
	 * @param	string $input The HTML input name.
	 * @return	(bool|string)
	 */
	function validate_value( $valid, $value, $field, $input ){
		
		// Check maxlength.
		if( $field['maxlength'] && (acs_strlen($value) > $field['maxlength']) ) {
			return sprintf( __('Value must not exceed %d characters', 'acs'), $field['maxlength'] );
		}
		
		// Return.
		return $valid;
	}
}


// initialize
acs_register_field_type( 'acs_field_textarea' );

endif; // class_exists check

?>