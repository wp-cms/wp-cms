<?php

/*
*  ACS Attachment Form Class
*
*  All the logic for adding fields to attachments
*
*  @class 		acs_form_attachment
*  @package		ACS
*  @subpackage	Forms
*/

if( ! class_exists('acs_form_attachment') ) :

class acs_form_attachment {
	
	/*
	*  __construct
	*
	*  This function will setup the class functionality
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
		
		// actions
		add_action('admin_enqueue_scripts',			array($this, 'admin_enqueue_scripts'));
		
		
		// render
		add_filter('attachment_fields_to_edit', 	array($this, 'edit_attachment'), 10, 2);
		
		
		// save
		add_filter('attachment_fields_to_save', 	array($this, 'save_attachment'), 10, 2);
		
	}
	
	
	/*
	*  admin_enqueue_scripts
	*
	*  This action is run after post query but before any admin script / head actions. 
	*  It is a good place to register all actions.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @date	26/01/13
	*  @since	3.6.0
	*
	*  @param	N/A
	*  @return	N/A
	*/
	
	function admin_enqueue_scripts() {
		
		// bail early if not valid screen
		if( !acs_is_screen(array('attachment', 'upload')) ) {
			return;
		}
				
		// load acs scripts
		acs_enqueue_scripts(array(
			'uploader'	=> true,
		));
		
		// actions
		if( acs_is_screen('upload') ) {
			add_action('admin_footer', array($this, 'admin_footer'), 0);
		}
	}
	
	
	/*
	*  admin_footer
	*
	*  This function will add acs_form_data to the WP 4.0 attachment grid
	*
	*  @type	action (admin_footer)
	*  @date	11/09/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function admin_footer() {
		
		// render post data
		acs_form_data(array(
			'screen'	=> 'attachment',
			'post_id'	=> 0,
		));
		
?>
<script type="text/javascript">
	
// WP saves attachment on any input change, so unload is not needed
acs.unload.active = 0;

</script>
<?php
		
	}
	
	
	/*
	*  edit_attachment
	*
	*  description
	*
	*  @type	function
	*  @date	8/10/13
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function edit_attachment( $form_fields, $post ) {
		
		// vars
		$is_page = acs_is_screen('attachment');
		$post_id = $post->ID;
		$el = 'tr';
		
		
		// get field groups
		$field_groups = acs_get_field_groups(array(
			'attachment_id' => $post_id,
			'attachment' => $post_id // Leave for backwards compatibility
		));
		
		
		// render
		if( !empty($field_groups) ) {
			
			// get acs_form_data
			ob_start();
			
			
			acs_form_data(array(
				'screen'	=> 'attachment',
				'post_id'	=> $post_id,
			));
			
			
			// open
			echo '</td></tr>';
			
			
			// loop
			foreach( $field_groups as $field_group ) {
				
				// load fields
				$fields = acs_get_fields( $field_group );
				
				
				// override instruction placement for modal
				if( !$is_page ) {
					
					$field_group['instruction_placement'] = 'field';
				}
				
				
				// render			
				acs_render_fields( $fields, $post_id, $el, $field_group['instruction_placement'] );
				
			}
			
			
			// close
			echo '<tr class="compat-field-acs-blank"><td>';
			
			
			
			$html = ob_get_contents();
			
			
			ob_end_clean();
			
			
			$form_fields[ 'acs-form-data' ] = array(
	       		'label' => '',
	   			'input' => 'html',
	   			'html' => $html
			);
						
		}
		
		
		// return
		return $form_fields;
		
	}
	
	
	/*
	*  save_attachment
	*
	*  description
	*
	*  @type	function
	*  @date	8/10/13
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function save_attachment( $post, $attachment ) {
		
		// bail early if not valid nonce
		if( !acs_verify_nonce('attachment') ) {
			return $post;
		}
		
		// bypass validation for ajax
		if( acs_is_ajax('save-attachment-compat') ) {
			acs_save_post( $post['ID'] );
		
		// validate and save
		} elseif( acs_validate_save_post(true) ) {
			acs_save_post( $post['ID'] );
		}
		
		// return
		return $post;	
	}
	
			
}

new acs_form_attachment();

endif;

?>