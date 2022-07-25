<?php 

// Register store for form data.
acs_register_store( 'form' );

/**
 * acs_set_form_data
 *
 * Sets data about the current form.
 *
 * @date	6/10/13
 * @since	5.0.0
 *
 * @param	string $name The store name.
 * @param	array $data Array of data to start the store with.
 * @return	ACS_Data
 */
function acs_set_form_data( $name = '', $data = false ) {
	return acs_get_store( 'form' )->set( $name, $data );
}

/**
 * acs_get_form_data
 *
 * Gets data about the current form.
 *
 * @date	6/10/13
 * @since	5.0.0
 *
 * @param	string $name The store name.
 * @return	mixed
 */
function acs_get_form_data( $name = '' ) {
	return acs_get_store( 'form' )->get( $name );
}

/**
 * acs_form_data
 *
 * Called within a form to set important information and render hidden inputs.
 *
 * @date	15/10/13
 * @since	5.0.0
 *
 * @param	void
 * @return	void
 */
function acs_form_data( $data = array() ) {
	
	// Apply defaults.
	$data = wp_parse_args($data, array(
		
		/** @type string The current screen (post, user, taxonomy, etc). */
		'screen' => 'post',
		
		/** @type int|string The ID of current post being edited. */
		'post_id' => 0,
		
		/** @type bool Enables AJAX validation. */
		'validation' => true,
	));
	
	// Create nonce using screen.
	$data['nonce'] = wp_create_nonce( $data['screen'] );
	
	// Append "changed" input used within "_wp_post_revision_fields" action.
	$data['changed'] = 0;
	
	// Set data.
	acs_set_form_data( $data );
	
	// Render HTML.
	?>
	<div id="acs-form-data" class="acs-hidden">
		<?php 
		
		// Create hidden inputs from $data
		foreach( $data as $name => $value ) {
			acs_hidden_input(array(
				'id'	=> '_acs_' . $name,
				'name'	=> '_acs_' . $name,
				'value'	=> $value
			));
		}
		
		/**
		 * Fires within the #acs-form-data element to add extra HTML.
		 *
		 * @date	15/10/13
		 * @since	5.0.0
		 *
		 * @param	array $data The form data.
		 */
		do_action( 'acs/form_data', $data );
		do_action( 'acs/input/form_data', $data );
		
		?>
	</div>
	<?php
}


/**
 * acs_save_post
 *
 * Saves the $_POST data.
 *
 * @date	15/10/13
 * @since	5.0.0
 *
 * @param	int|string $post_id The post id.
 * @param	array $values An array of values to override $_POST.
 * @return	bool True if save was successful.
 */
function acs_save_post( $post_id = 0, $values = null ) {
	
	// Override $_POST data with $values.
	if( $values !== null ) {
		$_POST['acs'] = $values;
	}
	
	// Bail early if no data to save.
	if( empty($_POST['acs']) ) {
		return false;
	}
	
	// Set form data (useful in various filters/actions).
	acs_set_form_data( 'post_id', $post_id );
	
	// Filter $_POST data for users without the 'unfiltered_html' capability.
	if( !acs_allow_unfiltered_html() ) {
		$_POST['acs'] = wp_kses_post_deep( $_POST['acs'] );
	}
	
	// Do generic action.
	do_action( 'acs/save_post', $post_id );
	
	// Return true.
	return true;
}

/**
 * _acs_do_save_post
 *
 * Private function hooked into 'acs/save_post' to actually save the $_POST data.
 * This allows developers to hook in before and after ACS has actually saved the data.
 *
 * @date	11/1/19
 * @since	5.7.10
 *
 * @param	int|string $post_id The post id.
 * @return	void
 */
function _acs_do_save_post( $post_id = 0 ) {
	
	// Check and update $_POST data.
	if( $_POST['acs'] ) {
		acs_update_values( $_POST['acs'], $post_id );
	}	
}

// Run during generic action.
add_action( 'acs/save_post', '_acs_do_save_post' );
