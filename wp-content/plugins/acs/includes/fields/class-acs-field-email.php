<?php

if( ! class_exists('acs_field_email') ) :

class acs_field_email extends acs_field {
	
	
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
		$this->name = 'email';
		$this->label = __("Email",'acs');
		$this->defaults = array(
			'default_value'	=> '',
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
		
		// vars
		$atts = array();
		$keys = array( 'type', 'id', 'class', 'name', 'value', 'placeholder', 'pattern' );
		$keys2 = array( 'readonly', 'disabled', 'required', 'multiple' );
		$html = '';
		
		
		// prepend
		if( $field['prepend'] !== '' ) {
		
			$field['class'] .= ' acs-is-prepended';
			$html .= '<div class="acs-input-prepend">' . acs_esc_html($field['prepend']) . '</div>';
			
		}
		
		
		// append
		if( $field['append'] !== '' ) {
		
			$field['class'] .= ' acs-is-appended';
			$html .= '<div class="acs-input-append">' . acs_esc_html($field['append']) . '</div>';
			
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
		
		
		// render
		$html .= '<div class="acs-input-wrap">' . acs_get_text_input( $atts ) . '</div>';
		
		
		// return
		echo $html;
		
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

	}

	/**
	 * Validate the email value. If this method returns TRUE, the input value is valid. If
	 * FALSE or a string is returned, the input value is invalid and the user is shown a
	 * notice. If a string is returned, the string is show as the message text.
	 *
	 * @param bool   $valid Whether the value is valid.
	 * @param mixed  $value The field value.
	 * @param array  $field The field array.
	 * @param string $input The request variable name for the inbound field.
	 *
	 * @return bool|string
	 */
	public function validate_value( $valid, $value, $field, $input ) {
		if ( $value && filter_var( $value, FILTER_VALIDATE_EMAIL ) === false ) {
			return sprintf( __( "'%s' is not a valid email address", 'acs' ), $value );
		}

		return $valid;
	}

}


// initialize
acs_register_field_type( 'acs_field_email' );

endif; // class_exists check

?>