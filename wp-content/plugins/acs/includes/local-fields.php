<?php 

// Register notices stores.
acs_register_store( 'local-fields' );
acs_register_store( 'local-groups' );
acs_register_store( 'local-empty' );

// Register filter.
acs_enable_filter( 'local' );

/**
 * acs_enable_local
 *
 * Enables the local filter.
 *
 * @date	22/1/19
 * @since	5.7.10
 *
 * @param	void
 * @return	void
 */
function acs_enable_local() {
	acs_enable_filter('local');
}

/**
 * acs_disable_local
 *
 * Disables the local filter.
 *
 * @date	22/1/19
 * @since	5.7.10
 *
 * @param	void
 * @return	void
 */
function acs_disable_local() {
	acs_disable_filter('local');
}

/**
 * acs_is_local_enabled
 *
 * Returns true if local fields are enabled.
 *
 * @date	23/1/19
 * @since	5.7.10
 *
 * @param	void
 * @return	bool
 */
function acs_is_local_enabled() {
	return ( acs_is_filter_enabled('local') && acs_get_setting('local') );
}

/**
 * acs_get_local_store
 *
 * Returns either local store or a dummy store for the given name.
 *
 * @date	23/1/19
 * @since	5.7.10
 *
 * @param	string $name The store name (fields|groups).
 * @return	ACS_Data
 */
function acs_get_local_store( $name = '' ) {
	
	// Check if enabled.
	if( acs_is_local_enabled() ) {
		return acs_get_store( "local-$name" );
	
	// Return dummy store if not enabled.
	} else {
		return acs_get_store( "local-empty" );
	}
}

/**
 * acs_reset_local
 *
 * Resets the local data.
 *
 * @date	22/1/19
 * @since	5.7.10
 *
 * @param	void
 * @return	void
 */
function acs_reset_local() {
	acs_get_local_store( 'fields' )->reset();
	acs_get_local_store( 'groups' )->reset();
}

/**
 * acs_get_local_field_groups
 *
 * Returns all local field groups.
 *
 * @date	22/1/19
 * @since	5.7.10
 *
 * @param	void
 * @return	array
 */
function acs_get_local_field_groups() {
	return acs_get_local_store( 'groups' )->get();
}

/**
 * acs_have_local_field_groups
 *
 * description
 *
 * @date	22/1/19
 * @since	5.7.10
 *
 * @param	type $var Description. Default.
 * @return	type Description.
 */
function acs_have_local_field_groups() {
	return acs_get_local_store( 'groups' )->count() ? true : false;
}

/**
 * acs_count_local_field_groups
 *
 * description
 *
 * @date	22/1/19
 * @since	5.7.10
 *
 * @param	type $var Description. Default.
 * @return	type Description.
 */
function acs_count_local_field_groups() {
	return acs_get_local_store( 'groups' )->count();
}

/**
 * acs_add_local_field_group
 *
 * Adds a local field group.
 *
 * @date	22/1/19
 * @since	5.7.10
 *
 * @param	array $field_group The field group array.
 * @return	bool
 */
function acs_add_local_field_group( $field_group ) {
	
	// Apply default properties needed for import.
	$field_group = wp_parse_args($field_group, array(
		'key'		=> '',
		'title'		=> '',
		'fields'	=> array(),
		'local'		=> 'php'
	));
	
	// Generate key if only name is provided.
	if( !$field_group['key'] ) {
		$field_group['key'] = 'group_' . acs_slugify($field_group['title'], '_');
	}
	
	// Bail early if field group already exists.
	if( acs_is_local_field_group($field_group['key']) ) {
		return false;
	}
	
	// Prepare field group for import (adds menu_order and parent properties to fields).
	$field_group = acs_prepare_field_group_for_import( $field_group );
	
	// Extract fields from group.
	$fields = acs_extract_var( $field_group, 'fields' );
	
	// Add to store
	acs_get_local_store( 'groups' )->set( $field_group['key'], $field_group );
	
	// Add fields
	if( $fields ) {
		acs_add_local_fields( $fields );
	}
	
	// Return true on success.
	return true;
}

/**
 * register_field_group
 *
 * See acs_add_local_field_group().
 *
 * @date	22/1/19
 * @since	5.7.10
 *
 * @param	array $field_group The field group array.
 * @return	void
 */
function register_field_group( $field_group ) {
	acs_add_local_field_group( $field_group );
}

/**
 * acs_remove_local_field_group
 *
 * Removes a field group for the given key.
 *
 * @date	22/1/19
 * @since	5.7.10
 *
 * @param	string $key The field group key.
 * @return	bool
 */
function acs_remove_local_field_group( $key = '' ) {
	return acs_get_local_store( 'groups' )->remove( $key );
}

/**
 * acs_is_local_field_group
 *
 * Returns true if a field group exists for the given key.
 *
 * @date	22/1/19
 * @since	5.7.10
 *
 * @param	string $key The field group key.
 * @return	bool
 */
function acs_is_local_field_group( $key = '' ) {
	return acs_get_local_store( 'groups' )->has( $key );
}

/**
 * acs_is_local_field_group_key
 *
 * Returns true if a field group exists for the given key.
 *
 * @date	22/1/19
 * @since	5.7.10
 *
 * @param	string $key The field group group key.
 * @return	bool
 */
function acs_is_local_field_group_key( $key = '' ) {
	return acs_get_local_store( 'groups' )->is( $key );
}

/**
 * acs_get_local_field_group
 *
 * Returns a field group for the given key.
 *
 * @date	22/1/19
 * @since	5.7.10
 *
 * @param	string $key The field group key.
 * @return	(array|null)
 */
function acs_get_local_field_group( $key = '' ) {
	return acs_get_local_store( 'groups' )->get( $key );
}

/**
 * acs_add_local_fields
 *
 * Adds an array of local fields.
 *
 * @date	22/1/19
 * @since	5.7.10
 *
 * @param	array $fields An array of un prepared fields.
 * @return	array
 */
function acs_add_local_fields( $fields = array() ) {
	
	// Prepare for import (allows parent fields to offer up children).
	$fields = acs_prepare_fields_for_import( $fields );
	
	// Add each field.
	foreach( $fields as $field ) {
		acs_add_local_field( $field, true );
	}
}

/**
 * acs_get_local_fields
 *
 * Returns all local fields for the given parent.
 *
 * @date	22/1/19
 * @since	5.7.10
 *
 * @param	string $parent The parent key.
 * @return	array
 */
function acs_get_local_fields( $parent = '' ) {
	
	// Return children
	if( $parent ) {
		return acs_get_local_store( 'fields' )->query(array(
			'parent' => $parent
		));
	
	// Return all.
	} else {
		return acs_get_local_store( 'fields' )->get();
	}
}

/**
 * acs_have_local_fields
 *
 * Returns true if local fields exist.
 *
 * @date	22/1/19
 * @since	5.7.10
 *
 * @param	string $parent The parent key.
 * @return	bool
 */
function acs_have_local_fields( $parent = '' ) {
	return acs_get_local_fields($parent) ? true : false;
}

/**
 * acs_count_local_fields
 *
 * Returns the number of local fields for the given parent.
 *
 * @date	22/1/19
 * @since	5.7.10
 *
 * @param	string $parent The parent key.
 * @return	int
 */
function acs_count_local_fields( $parent = '' ) {
	return count( acs_get_local_fields($parent) );
}

/**
 * acs_add_local_field
 *
 * Adds a local field.
 *
 * @date	22/1/19
 * @since	5.7.10
 *
 * @param	array $field The field array.
 * @param	bool $prepared Whether or not the field has already been prepared for import.
 * @return	void
 */
function acs_add_local_field( $field, $prepared = false ) {
	
	// Apply default properties needed for import.
	$field = wp_parse_args($field, array(
		'key'		=> '',
		'name'		=> '',
		'type'		=> '',
		'parent'	=> '',
	));
	
	// Generate key if only name is provided.
	if( !$field['key'] ) {
		$field['key'] = 'field_' . $field['name'];
	}
	
	// If called directly, allow sub fields to be correctly prepared.
	if( !$prepared ) {
		return acs_add_local_fields( array( $field ) );
	}
	
	// Extract attributes.
	$key = $field['key'];
	$name = $field['name'];
	
	// Allow sub field to be added multipel times to different parents.
	$store = acs_get_local_store( 'fields' );
	if( $store->is($key) ) {
		$old_key = _acs_generate_local_key( $store->get($key) );
		$new_key = _acs_generate_local_key( $field );
		if( $old_key !== $new_key ) {
			$key = $new_key;
		}
	}
	
	// Add field.
	$store->set( $key, $field )->alias( $key, $name );
}

/**
 * _acs_generate_local_key
 *
 * Generates a unique key based on the field's parent.
 *
 * @date	22/1/19
 * @since	5.7.10
 *
 * @param	string $key The field key.
 * @return	bool
 */
function _acs_generate_local_key( $field ) {
	return "{$field['key']}:{$field['parent']}";
}

/**
 * acs_remove_local_field
 *
 * Removes a field for the given key.
 *
 * @date	22/1/19
 * @since	5.7.10
 *
 * @param	string $key The field key.
 * @return	bool
 */
function acs_remove_local_field( $key = '' ) {
	return acs_get_local_store( 'fields' )->remove( $key );
}

/**
 * acs_is_local_field
 *
 * Returns true if a field exists for the given key or name.
 *
 * @date	22/1/19
 * @since	5.7.10
 *
 * @param	string $key The field group key.
 * @return	bool
 */
function acs_is_local_field( $key = '' ) {
	return acs_get_local_store( 'fields' )->has( $key );
}

/**
 * acs_is_local_field_key
 *
 * Returns true if a field exists for the given key.
 *
 * @date	22/1/19
 * @since	5.7.10
 *
 * @param	string $key The field group key.
 * @return	bool
 */
function acs_is_local_field_key( $key = '' ) {
	return acs_get_local_store( 'fields' )->is( $key );
}

/**
 * acs_get_local_field
 *
 * Returns a field for the given key.
 *
 * @date	22/1/19
 * @since	5.7.10
 *
 * @param	string $key The field group key.
 * @return	(array|null)
 */
function acs_get_local_field( $key = '' ) {
	return acs_get_local_store( 'fields' )->get( $key );
}

/**
 * _acs_apply_get_local_field_groups
 *
 * Appends local field groups to the provided array. 
 *
 * @date	23/1/19
 * @since	5.7.10
 *
 * @param	array $field_groups An array of field groups.
 * @return	array
 */
function _acs_apply_get_local_field_groups( $groups = array() ) {
	
	// Get local groups
	$local = acs_get_local_field_groups();
	if( $local ) {
		
		// Generate map of "index" => "key" data.
		$map = wp_list_pluck($groups, 'key');
		
		// Loop over groups and update/append local.
		foreach( $local as $group ) {
			
			// Get group allowing cache and filters to run.
			//$group = acs_get_field_group( $group['key'] );
			
			// Update.
			$i = array_search($group['key'], $map);
			if( $i !== false ) {
				unset($group['ID']);
				$groups[ $i ] = array_merge($groups[ $i ], $group);
				
			// Append	
			} else {
				$groups[] = acs_get_field_group( $group['key'] );
			}
		}
		
		// Sort list via menu_order and title.
		$groups = wp_list_sort( $groups, array('menu_order' => 'ASC', 'title' => 'ASC') );
	}
	
	// Return groups.
	return $groups;
}

// Hook into filter.
add_filter( 'acs/load_field_groups', '_acs_apply_get_local_field_groups', 20, 1 );

/**
 * _acs_apply_is_local_field_key
 *
 * Returns true if is a local key.
 *
 * @date	23/1/19
 * @since	5.7.10
 *
 * @param	bool $bool The result.
 * @param	string $id The identifier.
 * @return	bool
 */
function _acs_apply_is_local_field_key( $bool, $id ) {
	return acs_is_local_field_key( $id );
}

// Hook into filter.
add_filter( 'acs/is_field_key', '_acs_apply_is_local_field_key', 20, 2 );

/**
 * _acs_apply_is_local_field_group_key
 *
 * Returns true if is a local key.
 *
 * @date	23/1/19
 * @since	5.7.10
 *
 * @param	bool $bool The result.
 * @param	string $id The identifier.
 * @return	bool
 */
function _acs_apply_is_local_field_group_key( $bool, $id ) {
	return acs_is_local_field_group_key( $id );
}

// Hook into filter.
add_filter( 'acs/is_field_group_key', '_acs_apply_is_local_field_group_key', 20, 2 );

/**
 * _acs_do_prepare_local_fields
 *
 * Local fields that are added too early will not be correctly prepared by the field type class. 
 *
 * @date	23/1/19
 * @since	5.7.10
 *
 * @param	void
 * @return	void
 */
function _acs_do_prepare_local_fields() {
	
	// Get fields.
	$fields = acs_get_local_fields();
	
	// If fields have been registered early, re-add to correctly prepare them.
	if( $fields ) {
		acs_add_local_fields( $fields );
	}
}

// Hook into action.
add_action( 'acs/include_fields', '_acs_do_prepare_local_fields', 0, 1 );

?>