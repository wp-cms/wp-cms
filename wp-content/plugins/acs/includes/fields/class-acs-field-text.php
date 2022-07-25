<?php

if( ! class_exists('acs_field_text') ) :

class acs_field_text extends acs_field {
	
	
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
		$this->name = 'text';
		$this->label = __("Text",'acs');
		$this->defaults = array(
			'default_value'	=> '',
			'maxlength'		=> '',
			'placeholder'	=> '',
			'prepend'		=> '',
			'append'		=> ''
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
		$html = '';
		
		// Prepend text.
		if( $field['prepend'] !== '' ) {
			$field['class'] .= ' acs-is-prepended';
			$html .= '<div class="acs-input-prepend">' . acs_esc_html($field['prepend']) . '</div>';
		}
		
		// Append text.
		if( $field['append'] !== '' ) {
			$field['class'] .= ' acs-is-appended';
			$html .= '<div class="acs-input-append">' . acs_esc_html($field['append']) . '</div>';
		}
		
		// Input.
		$input_attrs = array();
		foreach( array( 'type', 'id', 'class', 'name', 'value', 'placeholder', 'maxlength', 'pattern', 'readonly', 'disabled', 'required' ) as $k ) {
			if( isset($field[ $k ]) ) {
				$input_attrs[ $k ] = $field[ $k ];
			}
		}
		$html .= '<div class="acs-input-wrap">' . acs_get_text_input( acs_filter_attrs($input_attrs) ) . '</div>';
		
		// Display.
		echo $html;
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
			'type'			=> 'text',
			'name'			=> 'default_value',
		));
		
		
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
		
		
		// maxlength
		acs_render_field_setting( $field, array(
			'label'			=> __('Character Limit','acs'),
			'instructions'	=> __('Leave blank for no limit','acs'),
			'type'			=> 'number',
			'name'			=> 'maxlength',
		));
		
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
		
		// Check maxlength
		if( $field['maxlength'] && (acs_strlen($value) > $field['maxlength']) ) {
			return sprintf( __('Value must not exceed %d characters', 'acs'), $field['maxlength'] );
		}
		
		// Return.
		return $valid;
	}
}


// initialize
acs_register_field_type( 'acs_field_text' );

endif; // class_exists check

?>