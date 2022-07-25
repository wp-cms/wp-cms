<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('ACS_Media') ) :

class ACS_Media {
	
	
	/*
	*  __construct
	*
	*  Initialize filters, action, variables and includes
	*
	*  @type	function
	*  @date	23/06/12
	*  @since	5.0.0
	*
	*  @param	N/A
	*  @return	N/A
	*/
	
	function __construct() {
		
		// actions
		add_action('acs/enqueue_scripts',			array($this, 'enqueue_scripts'));
		add_action('acs/save_post', 				array($this, 'save_files'), 5, 1);
		
		
		// filters
		add_filter('wp_handle_upload_prefilter', 	array($this, 'handle_upload_prefilter'), 10, 1);
		
		
		// ajax
		add_action('wp_ajax_query-attachments',		array($this, 'wp_ajax_query_attachments'), -1);
	}
	
	
	/**
	*  enqueue_scripts
	*
	*  Localizes data
	*
	*  @date	27/4/18
	*  @since	5.6.9
	*
	*  @param	void
	*  @return	void
	*/
	
	function enqueue_scripts(){
		if( wp_script_is('acs-input') ) {
			acs_localize_text(array(
				'Select.verb'			=> _x('Select', 'verb', 'acs'),
				'Edit.verb'				=> _x('Edit', 'verb', 'acs'),
				'Update.verb'			=> _x('Update', 'verb', 'acs'),
				'Uploaded to this post'	=> __('Uploaded to this post', 'acs'),
				'Expand Details' 		=> __('Expand Details', 'acs'),
				'Collapse Details' 		=> __('Collapse Details', 'acs'),
				'Restricted'			=> __('Restricted', 'acs'),
				'All images'			=> __('All images', 'acs')
			));
			acs_localize_data(array(
				'mimeTypeIcon'	=> wp_mime_type_icon(),
				'mimeTypes'		=> get_allowed_mime_types()
			));
		}
	}
		
		
	/*
	*  handle_upload_prefilter
	*
	*  description
	*
	*  @type	function
	*  @date	16/02/2015
	*  @since	5.1.5
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function handle_upload_prefilter( $file ) {
		
		// bail early if no acs field
		if( empty($_POST['_acsuploader']) ) {
			return $file;
		}
		
		
		// load field
		$field = acs_get_field( $_POST['_acsuploader'] );
		if( !$field ) {
			return $file;
		}
		
		
		// get errors
		$errors = acs_validate_attachment( $file, $field, 'upload' );
		
		
		/**
		*  Filters the errors for a file before it is uploaded to WordPress.
		*
		*  @date	16/02/2015
		*  @since	5.1.5
		*
		*  @param	array $errors An array of errors.
		*  @param	array $file An array of data for a single file.
		*  @param	array $field The field array.
		*/
		$errors = apply_filters( "acs/upload_prefilter/type={$field['type']}",	$errors, $file, $field );
		$errors = apply_filters( "acs/upload_prefilter/name={$field['_name']}",	$errors, $file, $field );
		$errors = apply_filters( "acs/upload_prefilter/key={$field['key']}", 	$errors, $file, $field );
		$errors = apply_filters( "acs/upload_prefilter", 						$errors, $file, $field );
		
		
		// append error
		if( !empty($errors) ) {
			$file['error'] = implode("\n", $errors);
		}
		
		
		// return
		return $file;
	}

	
	/*
	*  save_files
	*
	*  This function will save the $_FILES data
	*
	*  @type	function
	*  @date	24/10/2014
	*  @since	5.0.9
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function save_files( $post_id = 0 ) {
		
		// bail early if no $_FILES data
		if( empty($_FILES['acs']['name']) ) {
			return;
		}
		
		
		// upload files
		acs_upload_files();
	}
	
	
	/*
	*  wp_ajax_query_attachments
	*
	*  description
	*
	*  @type	function
	*  @date	26/06/2015
	*  @since	5.2.3
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function wp_ajax_query_attachments() {
		
		add_filter('wp_prepare_attachment_for_js', 	array($this, 'wp_prepare_attachment_for_js'), 10, 3);
		
	}
	
	function wp_prepare_attachment_for_js( $response, $attachment, $meta ) {
		
		// append attribute
		$response['acs_errors'] = false;
		
		
		// bail early if no acs field
		if( empty($_POST['query']['_acsuploader']) ) {
			return $response;
		}
		
		
		// load field
		$field = acs_get_field( $_POST['query']['_acsuploader'] );
		if( !$field ) {
			return $response;
		}
		
		
		// get errors
		$errors = acs_validate_attachment( $response, $field, 'prepare' );
		
		
		// append errors
		if( !empty($errors) ) {
			$response['acs_errors'] = implode('<br />', $errors);
		}
		
		
		// return
		return $response;
	}
}

// instantiate
acs_new_instance('ACS_Media');

endif; // class_exists check

?>