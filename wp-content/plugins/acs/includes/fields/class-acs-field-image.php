<?php

if( ! class_exists('acs_field_image') ) :

class acs_field_image extends acs_field {
	
	
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
		$this->name = 'image';
		$this->label = __("Image",'acs');
		$this->category = 'content';
		$this->defaults = array(
			'return_format'	=> 'array',
			'preview_size'	=> 'medium',
			'library'		=> 'all',
			'min_width'		=> 0,
			'min_height'	=> 0,
			'min_size'		=> 0,
			'max_width'		=> 0,
			'max_height'	=> 0,
			'max_size'		=> 0,
			'mime_types'	=> ''
		);
		
		// filters
		add_filter('get_media_item_args',				array($this, 'get_media_item_args'));
    
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
		
		// localize
		acs_localize_text(array(
		   	'Select Image'	=> __('Select Image', 'acs'),
			'Edit Image'	=> __('Edit Image', 'acs'),
			'Update Image'	=> __('Update Image', 'acs'),
			'All images'	=> __('All images', 'acs'),
	   	));
	}
	
	/**
	 * Renders the field HTML.
	 *
	 * @date	23/01/13
	 * @since	3.6.0
	 *
	 * @param	array $field The field settings.
	 * @return	void
	 */
	function render_field( $field ) {
		$uploader = acs_get_setting('uploader');
		
		// Enqueue uploader scripts
		if( $uploader === 'wp' ) {
			acs_enqueue_uploader();
		}

		// Elements and attributes.
		$value = '';
		$div_attrs = array(
			'class'				=> 'acs-image-uploader',
			'data-preview_size'	=> $field['preview_size'],
			'data-library'		=> $field['library'],
			'data-mime_types'	=> $field['mime_types'],
			'data-uploader'		=> $uploader
		);
		$img_attrs = array(
			'src'		=> '',
			'alt'		=> '',
			'data-name'	=> 'image'
		);
		
		// Detect value.
		if( $field['value'] && is_numeric($field['value']) ) {
			$image = wp_get_attachment_image_src( $field['value'], $field['preview_size'] );
			if( $image ) {
				$value = $field['value'];
				$img_attrs['src'] = $image[0];
				$img_attrs['alt'] = get_post_meta( $field['value'], '_wp_attachment_image_alt', true );
				$div_attrs['class'] .= ' has-value';
			}			
		}
		
		// Add "preview size" max width and height style.
		// Apply max-width to wrap, and max-height to img for max compatibility with field widths.
		$size = acs_get_image_size( $field['preview_size'] );
		$size_w = $size['width'] ? $size['width'] . 'px' : '100%';
		$size_h = $size['height'] ? $size['height'] . 'px' : '100%';
		$img_attrs['style'] = sprintf( 'max-height: %s;', $size_h );

		// Render HTML.
		?>
<div <?php echo acs_esc_attrs( $div_attrs ); ?>>
	<?php acs_hidden_input(array( 
		'name' => $field['name'],
		'value' => $value
	)); ?>
	<div class="show-if-value image-wrap" style="max-width: <?php echo esc_attr( $size_w ); ?>">
		<img <?php echo acs_esc_attrs( $img_attrs ); ?> />
		<div class="acs-actions -hover">
			<?php if( $uploader !== 'basic' ): ?>
			<a class="acs-icon -pencil dark" data-name="edit" href="#" title="<?php _e( 'Edit', 'acs' ); ?>"></a>
			<?php endif; ?>
			<a class="acs-icon -cancel dark" data-name="remove" href="#" title="<?php _e( 'Remove', 'acs' ); ?>"></a>
		</div>
	</div>
	<div class="hide-if-value">
		<?php if( $uploader === 'basic' ): ?>
			<?php if( $field['value'] && !is_numeric($field['value']) ): ?>
				<div class="acs-error-message"><p><?php echo acs_esc_html( $field['value'] ); ?></p></div>
			<?php endif; ?>
			<label class="acs-basic-uploader">
				<?php acs_file_input(array(
					'name' => $field['name'], 
					'id' => $field['id']
				)); ?>
			</label>
		<?php else: ?>
			<p><?php _e( 'No image selected', 'acs' ); ?> <a data-name="add" class="acs-button button" href="#"><?php _e( 'Add Image', 'acs' ); ?></a></p>
		<?php endif; ?>
	</div>
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
		
		// clear numeric settings
		$clear = array(
			'min_width',
			'min_height',
			'min_size',
			'max_width',
			'max_height',
			'max_size'
		);
		
		foreach( $clear as $k ) {
			
			if( empty($field[$k]) ) {
				
				$field[$k] = '';
				
			}
			
		}
		
		
		// return_format
		acs_render_field_setting( $field, array(
			'label'			=> __('Return Format','acs'),
			'instructions'	=> '',
			'type'			=> 'radio',
			'name'			=> 'return_format',
			'layout'		=> 'horizontal',
			'choices'		=> array(
				'array'			=> __("Image Array",'acs'),
				'url'			=> __("Image URL",'acs'),
				'id'			=> __("Image ID",'acs')
			)
		));
		
		
		// preview_size
		acs_render_field_setting( $field, array(
			'label'			=> __('Preview Size','acs'),
			'instructions'	=> '',
			'type'			=> 'select',
			'name'			=> 'preview_size',
			'choices'		=> acs_get_image_sizes()
		));
		
		
		// library
		acs_render_field_setting( $field, array(
			'label'			=> __('Library','acs'),
			'instructions'	=> __('Limit the media library choice','acs'),
			'type'			=> 'radio',
			'name'			=> 'library',
			'layout'		=> 'horizontal',
			'choices' 		=> array(
				'all'			=> __('All', 'acs'),
				'uploadedTo'	=> __('Uploaded to post', 'acs')
			)
		));
		
		
		// min
		acs_render_field_setting( $field, array(
			'label'			=> __('Minimum','acs'),
			'instructions'	=> __('Restrict which images can be uploaded','acs'),
			'type'			=> 'text',
			'name'			=> 'min_width',
			'prepend'		=> __('Width', 'acs'),
			'append'		=> 'px',
		));
		
		acs_render_field_setting( $field, array(
			'label'			=> '',
			'type'			=> 'text',
			'name'			=> 'min_height',
			'prepend'		=> __('Height', 'acs'),
			'append'		=> 'px',
			'_append' 		=> 'min_width'
		));
		
		acs_render_field_setting( $field, array(
			'label'			=> '',
			'type'			=> 'text',
			'name'			=> 'min_size',
			'prepend'		=> __('File size', 'acs'),
			'append'		=> 'MB',
			'_append' 		=> 'min_width'
		));	
		
		
		// max
		acs_render_field_setting( $field, array(
			'label'			=> __('Maximum','acs'),
			'instructions'	=> __('Restrict which images can be uploaded','acs'),
			'type'			=> 'text',
			'name'			=> 'max_width',
			'prepend'		=> __('Width', 'acs'),
			'append'		=> 'px',
		));
		
		acs_render_field_setting( $field, array(
			'label'			=> '',
			'type'			=> 'text',
			'name'			=> 'max_height',
			'prepend'		=> __('Height', 'acs'),
			'append'		=> 'px',
			'_append' 		=> 'max_width'
		));
		
		acs_render_field_setting( $field, array(
			'label'			=> '',
			'type'			=> 'text',
			'name'			=> 'max_size',
			'prepend'		=> __('File size', 'acs'),
			'append'		=> 'MB',
			'_append' 		=> 'max_width'
		));	
		
		
		// allowed type
		acs_render_field_setting( $field, array(
			'label'			=> __('Allowed file types','acs'),
			'instructions'	=> __('Comma separated list. Leave blank for all types','acs'),
			'type'			=> 'text',
			'name'			=> 'mime_types',
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
		
		// bail early if no value
		if( empty($value) ) return false;
		
		
		// bail early if not numeric (error message)
		if( !is_numeric($value) ) return false;
		
		
		// convert to int
		$value = intval($value);
		
		
		// format
		if( $field['return_format'] == 'url' ) {
		
			return wp_get_attachment_url( $value );
			
		} elseif( $field['return_format'] == 'array' ) {
			
			return acs_get_attachment( $value );
			
		}
		
		
		// return
		return $value;
		
	}
	
	
	/*
	*  get_media_item_args
	*
	*  description
	*
	*  @type	function
	*  @date	27/01/13
	*  @since	3.6.0
	*
	*  @param	$vars (array)
	*  @return	$vars
	*/
	
	function get_media_item_args( $vars ) {
	
	    $vars['send'] = true;
	    return($vars);
	    
	}
	
	
	/*
	*  update_value()
	*
	*  This filter is appied to the $value before it is updated in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value which will be saved in the database
	*  @param	$post_id - the $post_id of which the value will be saved
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the modified value
	*/
	
	function update_value( $value, $post_id, $field ) {
		
		return acs_get_field_type('file')->update_value( $value, $post_id, $field );
		
	}
	
	
	/*
	*  validate_value
	*
	*  This function will validate a basic file input
	*
	*  @type	function
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function validate_value( $valid, $value, $field, $input ){
		
		return acs_get_field_type('file')->validate_value( $valid, $value, $field, $input );
		
	}
	
}


// initialize
acs_register_field_type( 'acs_field_image' );

endif; // class_exists check

?>