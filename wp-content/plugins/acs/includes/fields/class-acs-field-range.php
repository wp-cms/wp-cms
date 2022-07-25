<?php

if( ! class_exists('acs_field_range') ) :

class acs_field_range extends acs_field_number {
	
	
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
		$this->name = 'range';
		$this->label = __("Range",'acs');
		$this->defaults = array(
			'default_value'	=> '',
			'min'			=> '',
			'max'			=> '',
			'step'			=> '',
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
		$keys = array( 'type', 'id', 'class', 'name', 'value', 'min', 'max', 'step' );
		$keys2 = array( 'readonly', 'disabled', 'required' );
		$html = '';
		
		// step
		if( !$field['step'] ) {
			$field['step'] = 1;
		}
		
		// min / max
		if( !$field['min'] ) {
			$field['min'] = 0;
		}
		if( !$field['max'] ) {
			$field['max'] = 100;
		}
		
		// allow for prev 'non numeric' value
		if( !is_numeric($field['value']) ) {
			$field['value'] = 0;
		}
		
		// constrain within max and min
		$field['value'] = max($field['value'], $field['min']);
		$field['value'] = min($field['value'], $field['max']);
		
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
		
		// open
		$html .= '<div class="acs-range-wrap">';
			
			// prepend
			if( $field['prepend'] !== '' ) {
				$html .= '<div class="acs-prepend">' . acs_esc_html($field['prepend']) . '</div>';
			}
			
			// range
			$html .= acs_get_text_input( $atts );

			// Calculate input width based on the largest possible input character length.
			// Also take into account the step size for decimal steps minus - 1.5 chars for leading "0.".
			$len = max(
				strlen( strval($field['min']) ),
				strlen( strval($field['max']) )
			);
			if( floatval($atts['step']) < 1 ) {
				$len += strlen( strval($field['step']) ) - 1.5;
			}
			
			// input
			$html .= acs_get_text_input(array(
				'type'	=> 'number', 
				'id'	=> $atts['id'] . '-alt',
				'value'	=> $atts['value'],
				'step'	=> $atts['step'],
				//'min'	=> $atts['min'], // removed to avoid browser validation errors
				//'max'	=> $atts['max'],
				'style'	=> 'width: ' . (1.8 + $len*0.7) . 'em;'
			));
			
			// append
			if( $field['append'] !== '' ) {
				$html .= '<div class="acs-append">' . acs_esc_html($field['append']) . '</div>';
			}
		
		// close
		$html .= '</div>';
		
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
			'type'			=> 'number',
			'name'			=> 'default_value',
		));
		
		
		// min
		acs_render_field_setting( $field, array(
			'label'			=> __('Minimum Value','acs'),
			'instructions'	=> '',
			'type'			=> 'number',
			'name'			=> 'min',
			'placeholder'	=> '0'
		));
		
		
		// max
		acs_render_field_setting( $field, array(
			'label'			=> __('Maximum Value','acs'),
			'instructions'	=> '',
			'type'			=> 'number',
			'name'			=> 'max',
			'placeholder'	=> '100'
		));
		
		
		// step
		acs_render_field_setting( $field, array(
			'label'			=> __('Step Size','acs'),
			'instructions'	=> '',
			'type'			=> 'number',
			'name'			=> 'step',
			'placeholder'	=> '1'
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
acs_register_field_type( 'acs_field_range' );

endif; // class_exists check

?>