<?php

if( ! class_exists('acs_field_date_picker') ) :

class acs_field_date_picker extends acs_field {
	
	
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
		$this->name = 'date_picker';
		$this->label = __("Date Picker",'acs');
		$this->category = 'jquery';
		$this->defaults = array(
			'display_format'	=> 'd/m/Y',
			'return_format'		=> 'd/m/Y',
			'first_day'			=> 1
		);
	}
	
	
	/*
	*  input_admin_enqueue_scripts
	*
	*  description
	*
	*  @type	function
	*  @date	16/12/2015
	*  @since	5.3.2
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function input_admin_enqueue_scripts() {
		
		// bail ealry if no enqueue
	   	if( !acs_get_setting('enqueue_datepicker') ) {
		   	return;
	   	}
	   	
	   	// localize
	   	global $wp_locale;
	   	acs_localize_data(array(
		   	'datePickerL10n'	=> array(
				'closeText'			=> _x('Done',	'Date Picker JS closeText',		'acs'),
				'currentText'		=> _x('Today',	'Date Picker JS currentText',	'acs'),
				'nextText'			=> _x('Next',	'Date Picker JS nextText',		'acs'),
				'prevText'			=> _x('Prev',	'Date Picker JS prevText',		'acs'),
				'weekHeader'		=> _x('Wk',		'Date Picker JS weekHeader',	'acs'),
				'monthNames'        => array_values( $wp_locale->month ),
				'monthNamesShort'   => array_values( $wp_locale->month_abbrev ),
				'dayNames'          => array_values( $wp_locale->weekday ),
				'dayNamesMin'       => array_values( $wp_locale->weekday_initial ),
				'dayNamesShort'     => array_values( $wp_locale->weekday_abbrev )
			)
	   	));
	   	
		// script
		wp_enqueue_script('jquery-ui-datepicker');
		
		// style
		wp_enqueue_style('acs-datepicker', acs_get_url('assets/inc/datepicker/jquery-ui.min.css'), array(), '1.11.4' );
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
		$hidden_value = '';
		$display_value = '';
		
		// format value
		if( $field['value'] ) {
			$hidden_value = acs_format_date( $field['value'], 'Ymd' );
			$display_value = acs_format_date( $field['value'], $field['display_format'] );
		}
		
		// elements
		$div = array(
			'class'					=> 'acs-date-picker acs-input-wrap',
			'data-date_format'		=> acs_convert_date_to_js($field['display_format']),
			'data-first_day'		=> $field['first_day'],
		);
		$hidden_input = array(
			'id'					=> $field['id'],
			'name'					=> $field['name'],
			'value'					=> $hidden_value,
		);
		$text_input = array(
			'class' 				=> 'input',
			'value'					=> $display_value,
		);
		
		// special attributes
		foreach( array( 'readonly', 'disabled' ) as $k ) {
			if( !empty($field[ $k ]) ) {
				$text_input[ $k ] = $k;
			}
		}
		
		// save_format - compatibility with ACS < 5.0.0
		if( !empty($field['save_format']) ) {
			
			// add custom JS save format
			$div['data-save_format'] = $field['save_format'];
			
			// revert hidden input value to raw DB value
			$hidden_input['value'] = $field['value'];
			
			// remove formatted value (will do this via JS)
			$text_input['value'] = '';
		}
		
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
		
		// global
		global $wp_locale;
		
		
		// vars
		$d_m_Y = date_i18n('d/m/Y');
		$m_d_Y = date_i18n('m/d/Y');
		$F_j_Y = date_i18n('F j, Y');
		$Ymd = date_i18n('Ymd');
		
		
		// display_format
		acs_render_field_setting( $field, array(
			'label'			=> __('Display Format','acs'),
			'instructions'	=> __('The format displayed when editing a post','acs'),
			'type'			=> 'radio',
			'name'			=> 'display_format',
			'other_choice'	=> 1,
			'choices'		=> array(
				'd/m/Y'			=> '<span>' . $d_m_Y . '</span><code>d/m/Y</code>',
				'm/d/Y'			=> '<span>' . $m_d_Y . '</span><code>m/d/Y</code>',
				'F j, Y'		=> '<span>' . $F_j_Y . '</span><code>F j, Y</code>',
				'other'			=> '<span>' . __('Custom:','acs') . '</span>'
			)
		));
				
		
		// save_format - compatibility with ACS < 5.0.0
		if( !empty($field['save_format']) ) {
			
			// save_format
			acs_render_field_setting( $field, array(
				'label'			=> __('Save Format','acs'),
				'instructions'	=> __('The format used when saving a value','acs'),
				'type'			=> 'text',
				'name'			=> 'save_format',
				//'readonly'		=> 1 // this setting was not readonly in v4
			));
			
		} else {
			
			// return_format
			acs_render_field_setting( $field, array(
				'label'			=> __('Return Format','acs'),
				'instructions'	=> __('The format returned via template functions','acs'),
				'type'			=> 'radio',
				'name'			=> 'return_format',
				'other_choice'	=> 1,
				'choices'		=> array(
					'd/m/Y'			=> '<span>' . $d_m_Y . '</span><code>d/m/Y</code>',
					'm/d/Y'			=> '<span>' . $m_d_Y . '</span><code>m/d/Y</code>',
					'F j, Y'		=> '<span>' . $F_j_Y . '</span><code>F j, Y</code>',
					'Ymd'			=> '<span>' . $Ymd . '</span><code>Ymd</code>',
					'other'			=> '<span>' . __('Custom:','acs') . '</span>'
				)
			));
			
		}
		
		
		// first_day
		acs_render_field_setting( $field, array(
			'label'			=> __('Week Starts On','acs'),
			'instructions'	=> '',
			'type'			=> 'select',
			'name'			=> 'first_day',
			'choices'		=> array_values( $wp_locale->weekday )
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
		
		// save_format - compatibility with ACS < 5.0.0
		if( !empty($field['save_format']) ) {
			
			return $value;
			
		}
		
		
		// return
		return acs_format_date( $value, $field['return_format'] );
		
	}
	
}


// initialize
acs_register_field_type( 'acs_field_date_picker' );

endif; // class_exists check

?>