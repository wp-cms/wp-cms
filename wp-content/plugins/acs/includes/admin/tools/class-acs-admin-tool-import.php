<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('ACS_Admin_Tool_Import') ) :

class ACS_Admin_Tool_Import extends ACS_Admin_Tool {
	
	
	/**
	*  initialize
	*
	*  This function will initialize the admin tool
	*
	*  @date	10/10/17
	*  @since	5.6.3
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function initialize() {
		
		// vars
		$this->name = 'import';
		$this->title = __("Import Field Groups", 'acs');
    	$this->icon = 'dashicons-upload';
    	
	}
	
	
	/**
	*  html
	*
	*  This function will output the metabox HTML
	*
	*  @date	10/10/17
	*  @since	5.6.3
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function html() {
		
		?>
		<p><?php _e('Select the Amazing Custom Stuff JSON file you would like to import. When you click the import button below, ACS will import the field groups.', 'acs'); ?></p>
		<div class="acs-fields">
			<?php 
			
			acs_render_field_wrap(array(
				'label'		=> __('Select File', 'acs'),
				'type'		=> 'file',
				'name'		=> 'acs_import_file',
				'value'		=> false,
				'uploader'	=> 'basic',
			));
			
			?>
		</div>
		<p class="acs-submit">
			<input type="submit" class="button button-primary" value="<?php _e('Import File', 'acs'); ?>" />
		</p>
		<?php
		
	}
	
	
	/**
	*  submit
	*
	*  This function will run when the tool's form has been submit
	*
	*  @date	10/10/17
	*  @since	5.6.3
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function submit() {
		
		// Check file size.
		if( empty($_FILES['acs_import_file']['size']) ) {
			return acs_add_admin_notice( __("No file selected", 'acs'), 'warning' );
		}
		
		// Get file data.
		$file = $_FILES['acs_import_file'];
		
		// Check errors.
		if( $file['error'] ) {
			return acs_add_admin_notice( __("Error uploading file. Please try again", 'acs'), 'warning' );
		}
		
		// Check file type.
		if( pathinfo($file['name'], PATHINFO_EXTENSION) !== 'json' ) {
			return acs_add_admin_notice( __("Incorrect file type", 'acs'), 'warning' );
		}
		
		// Read JSON.
		$json = file_get_contents( $file['tmp_name'] );
		$json = json_decode($json, true);
		
		// Check if empty.
    	if( !$json || !is_array($json) ) {
    		return acs_add_admin_notice( __("Import file empty", 'acs'), 'warning' );
    	}
    	
    	// Ensure $json is an array of groups.
    	if( isset($json['key']) ) {
	    	$json = array( $json );	    	
    	}
    	
    	// Remeber imported field group ids.
    	$ids = array();
    	
    	// Loop over json
    	foreach( $json as $field_group ) {
	    	
	    	// Search database for existing field group.
	    	$post = acs_get_field_group_post( $field_group['key'] );
	    	if( $post ) {
		    	$field_group['ID'] = $post->ID;
	    	}
	    	
	    	// Import field group.
	    	$field_group = acs_import_field_group( $field_group );
	    	
	    	// append message
	    	$ids[] = $field_group['ID'];
    	}
    	
    	// Count number of imported field groups.
		$total = count($ids);
		
		// Generate text.
		$text = sprintf( _n( 'Imported 1 field group', 'Imported %s field groups', $total, 'acs' ), $total );		
		
		// Add links to text.
		$links = array();
		foreach( $ids as $id ) {
			$links[] = '<a href="' . get_edit_post_link( $id ) . '">' . get_the_title( $id ) . '</a>';
		}
		$text .= ' ' . implode( ', ', $links );
		
		// Add notice
		acs_add_admin_notice( $text, 'success' );
	}
}

// initialize
acs_register_admin_tool( 'ACS_Admin_Tool_Import' );

endif; // class_exists check

?>