<?php

if( ! class_exists('acs_field_true_false') ) :

class acs_field_true_false extends acs_field {
	
	
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
		$this->name = 'true_false';
		$this->label = __('True / False','acs');
		$this->category = 'choice';
		$this->defaults = array(
			'default_value'	=> 0,
			'message'		=> '',
			'ui'			=> 0,
			'ui_on_text'	=> '',
			'ui_off_text'	=> '',
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
		$input = array(
			'type'		=> 'checkbox',
			'id'		=> $field['id'],
			'name'		=> $field['name'],
			'value'		=> '1',
			'class'		=> $field['class'],
			'autocomplete'	=> 'off'
		);
		
		$hidden = array(
			'name' 		=> $field['name'],
			'value'		=> 0
		);
		
		$active = $field['value'] ? true : false;
		$switch = '';
		
		
		// checked
		if( $active ) $input['checked'] = 'checked';
		
		
		// ui
		if( $field['ui'] ) {
			
			// vars
			if( $field['ui_on_text'] === '' ) $field['ui_on_text'] = __('Yes', 'acs');
			if( $field['ui_off_text'] === '' ) $field['ui_off_text'] = __('No', 'acs');
			
			
			// update input
			$input['class'] .= ' acs-switch-input';
			//$input['style'] = 'display:none;';
			
			$switch .= '<div class="acs-switch' . ($active ? ' -on' : '') . '">';
				$switch .= '<span class="acs-switch-on">'.$field['ui_on_text'].'</span>';
				$switch .= '<span class="acs-switch-off">'.$field['ui_off_text'].'</span>';
				$switch .= '<div class="acs-switch-slider"></div>';
			$switch .= '</div>';
			
		}
		
?>
<div class="acs-true-false">
	<?php acs_hidden_input($hidden); ?>
	<label>
		<input <?php echo acs_esc_attr($input); ?>/>
		<?php if( $switch ) echo acs_esc_html($switch); ?>
		<?php if( $field['message'] ): ?><span class="message"><?php echo acs_esc_html($field['message']); ?></span><?php endif; ?>
	</label>
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
		
		// message
		acs_render_field_setting( $field, array(
			'label'			=> __('Message','acs'),
			'instructions'	=> __('Displays text alongside the checkbox','acs'),
			'type'			=> 'text',
			'name'			=> 'message',
		));
		
		
		// default_value
		acs_render_field_setting( $field, array(
			'label'			=> __('Default Value','acs'),
			'instructions'	=> '',
			'type'			=> 'true_false',
			'name'			=> 'default_value',
		));
		
		
		// ui
		acs_render_field_setting( $field, array(
			'label'			=> __('Stylised UI','acs'),
			'instructions'	=> '',
			'type'			=> 'true_false',
			'name'			=> 'ui',
			'ui'			=> 1,
			'class'			=> 'acs-field-object-true-false-ui'
		));
		
		
		// on_text
		acs_render_field_setting( $field, array(
			'label'			=> __('On Text','acs'),
			'instructions'	=> __('Text shown when active','acs'),
			'type'			=> 'text',
			'name'			=> 'ui_on_text',
			'placeholder'	=> __('Yes', 'acs'),
			'conditions'	=> array(
				'field'		=> 'ui',
				'operator'	=> '==',
				'value'		=> 1
			)
		));
		
		
		// on_text
		acs_render_field_setting( $field, array(
			'label'			=> __('Off Text','acs'),
			'instructions'	=> __('Text shown when inactive','acs'),
			'type'			=> 'text',
			'name'			=> 'ui_off_text',
			'placeholder'	=> __('No', 'acs'),
			'conditions'	=> array(
				'field'		=> 'ui',
				'operator'	=> '==',
				'value'		=> 1
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
		
		return empty($value) ? false : true;
		
	}
	
	
	/*
	*  validate_value
	*
	*  description
	*
	*  @type	function
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function validate_value( $valid, $value, $field, $input ){
		
		// bail early if not required
		if( ! $field['required'] ) {
			
			return $valid;
			
		}
		
		
		// value may be '0'
		if( !$value ) {
			
			return false;
			
		}
		
		
		// return
		return $valid;
				
	}
	
	
	/*
	*  translate_field
	*
	*  This function will translate field settings
	*
	*  @type	function
	*  @date	8/03/2016
	*  @since	5.3.2
	*
	*  @param	$field (array)
	*  @return	$field
	*/
	
	function translate_field( $field ) {
		
		// translate
		$field['message'] = acs_translate( $field['message'] );
		$field['ui_on_text'] = acs_translate( $field['ui_on_text'] );
		$field['ui_off_text'] = acs_translate( $field['ui_off_text'] );
		
		
		// return
		return $field;
		
	}
	
}


// initialize
acs_register_field_type( 'acs_field_true_false' );

endif; // class_exists check

?>