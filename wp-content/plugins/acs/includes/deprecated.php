<?php 

// Register deprecated filters ( $deprecated, $version, $replacement ).
acs_add_deprecated_filter( 'acs/settings/export_textdomain',	'5.3.3', 'acs/settings/l10n_textdomain' );
acs_add_deprecated_filter( 'acs/settings/export_translate',		'5.3.3', 'acs/settings/l10n_field' );
acs_add_deprecated_filter( 'acs/settings/export_translate',		'5.3.3', 'acs/settings/l10n_field_group' );
acs_add_deprecated_filter( 'acs/settings/dir',					'5.6.8', 'acs/settings/url' );
acs_add_deprecated_filter( 'acs/get_valid_field',				'5.5.6', 'acs/validate_field' );
acs_add_deprecated_filter( 'acs/get_valid_field_group',			'5.5.6', 'acs/validate_field_group' );
acs_add_deprecated_filter( 'acs/get_valid_post_id',				'5.5.6', 'acs/validate_post_id' );
acs_add_deprecated_filter( 'acs/get_field_reference',			'5.6.5', 'acs/load_reference' );
acs_add_deprecated_filter( 'acs/get_field_group',				'5.7.11', 'acs/load_field_group' );
acs_add_deprecated_filter( 'acs/get_field_groups',				'5.7.11', 'acs/load_field_groups' );
acs_add_deprecated_filter( 'acs/get_fields',					'5.7.11', 'acs/load_fields' );

// Register variations for deprecated filters.
acs_add_filter_variations( 'acs/get_valid_field', array('type'), 0 );

/**
 * acs_render_field_wrap_label
 *
 * Renders the field's label.
 *
 * @date	19/9/17
 * @since	5.6.3
 * @deprecated 5.6.5
 *
 * @param	array $field The field array.
 * @return	void
 */
function acs_render_field_wrap_label( $field ) {
	
	// Warning.
	_deprecated_function( __FUNCTION__, '5.7.11', 'acs_render_field_label()' );
	
	// Render.
	acs_render_field_label( $field );
}

/**
 * acs_render_field_wrap_description
 *
 * Renders the field's instructions.
 *
 * @date	19/9/17
 * @since	5.6.3
 * @deprecated 5.6.5
 *
 * @param	array $field The field array.
 * @return	void
 */
function acs_render_field_wrap_description( $field ) {
	
	// Warning.
	_deprecated_function( __FUNCTION__, '5.7.11', 'acs_render_field_instructions()' );
	
	// Render.
	acs_render_field_instructions( $field );
}

/*
 * acs_get_fields_by_id
 *
 * Returns and array of fields for the given $parent_id.
 *
 * @date	27/02/2014
 * @since	5.0.0.
 * @deprecated	5.7.11
 *
 * @param	int $parent_id The parent ID.
 * @return	array
 */
function acs_get_fields_by_id( $parent_id = 0 ) {
	
	// Warning.
	_deprecated_function( __FUNCTION__, '5.7.11', 'acs_get_fields()' );
	
	// Return fields.
	return acs_get_fields(array( 'ID' => $parent_id, 'key' => "group_$parent_id" ));
}

/**
 * acs_update_option
 *
 * A wrapper for the WP update_option but provides logic for a 'no' autoload
 *
 * @date	4/01/2014
 * @since	5.0.0
 * @deprecated	5.7.11
 *
 * @param	string $option The option name.
 * @param	string $value The option value.
 * @param	string $autoload An optional autoload value.
 * @return	bool
 */
function acs_update_option( $option = '', $value = '', $autoload = null ) {
	
	// Warning.
	_deprecated_function( __FUNCTION__, '5.7.11', 'update_option()' );
	
	// Update.
	if( $autoload === null ) {
		$autoload = (bool) acs_get_setting('autoload');
	}
	return update_option( $option, $value, $autoload );
}

/**
 * acs_get_field_reference
 *
 * Finds the field key for a given field name and post_id.
 *
 * @date	26/1/18
 * @since	5.6.5
 * @deprecated	5.6.8
 *
 * @param	string	$field_name	The name of the field. eg 'sub_heading'
 * @param	mixed	$post_id	The post_id of which the value is saved against
 * @return	string	$reference	The field key
 */
function acs_get_field_reference( $field_name, $post_id ) {
	
	// Warning.
	_deprecated_function( __FUNCTION__, '5.6.8', 'acs_get_reference()' );
	
	// Return reference.
	return acs_get_reference( $field_name, $post_id );
}

/**
 * acs_get_dir
 *
 * Returns the plugin url to a specified file.
 *
 * @date	28/09/13
 * @since	5.0.0
 * @deprecated	5.6.8
 *
 * @param	string $filename The specified file.
 * @return	string
 */
function acs_get_dir( $filename = '' ) {
	
	// Warning.
	_deprecated_function( __FUNCTION__, '5.6.8', 'acs_get_url()' );
	
	// Return.
	return acs_get_url( $filename );
}
