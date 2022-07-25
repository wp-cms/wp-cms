<?php

if( ! class_exists('acs_field_time_picker') ) :

class acs_field_time_picker extends acs_field {
	
	
	/*
	*  __construct
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
		$this->name = 'time_picker';
		$this->label = __("Time Picker",'acs');
		$this->category = 'jquery';
		$this->defaults = array(
			'display_format'		=> 'g:i a',
			'return_format'			=> 'g:i a'
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
		
		// format value
		$display_value = '';
		
		if( $field['value'] ) {
			$display_value = acs_format_date( $field['value'], $field['display_format'] );
		}
		
		
		// vars
		$div = array(
			'class'					=> 'acs-time-picker acs-input-wrap',
			'data-time_format'		=> acs_convert_time_to_js($field['display_format'])
		);
		$hidden_input = array(
			'id'					=> $field['id'],
			'class' 				=> 'input-alt',
			'type'					=> 'hidden',
			'name'					=> $field['name'],
			'value'					=> $field['value'],
		);
		$text_input = array(
			'class' 				=> 'input',
			'type'					=> 'text',
			'value'					=> $display_value,
		);
		
		
		// html
		?>
		<div <?php acs_esc_attr_e( $div ); ?>>
			<?php acs_hidden_input( $hidden_input ); ?>
			<?php acs_text_input( $text_input ); ?>
		</div>
		<?php
		
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
		
		// vars
		$g_i_a = date_i18n('g:i a');
		$H_i_s = date_i18n('H:i:s');
		
		
		// display_format
		acs_render_field_setting( $field, array(
			'label'			=> __('Display Format','acs'),
			'instructions'	=> __('The format displayed when editing a post','acs'),
			'type'			=> 'radio',
			'name'			=> 'display_format',
			'other_choice'	=> 1,
			'choices'		=> array(
				'g:i a'	=> '<span>' . $g_i_a . '</span><code>g:i a</code>',
				'H:i:s'	=> '<span>' . $H_i_s . '</span><code>H:i:s</code>',
				'other'	=> '<span>' . __('Custom:','acs') . '</span>'
			)
		));
				
		
		// return_format
		acs_render_field_setting( $field, array(
			'label'			=> __('Return Format','acs'),
			'instructions'	=> __('The format returned via template functions','acs'),
			'type'			=> 'radio',
			'name'			=> 'return_format',
			'other_choice'	=> 1,
			'choices'		=> array(
				'g:i a'	=> '<span>' . $g_i_a . '</span><code>g:i a</code>',
				'H:i:s'	=> '<span>' . $H_i_s . '</span><code>H:i:s</code>',
				'other'	=> '<span>' . __('Custom:','acs') . '</span>'
			)
		));
		
	}
	
	
	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
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
		
		return acs_format_date( $value, $field['return_format'] );
		
	}
	
}


// initialize
acs_register_field_type( 'acs_field_time_picker' );

endif; // class_exists check

?>