<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('ACS_Ajax_Local_JSON_Diff') ) :

class ACS_Ajax_Local_JSON_Diff extends ACS_Ajax {
	
	/** @var string The AJAX action name. */
	var $action = 'acs/ajax/local_json_diff';
	
	/** @var bool Prevents access for non-logged in users. */
	var $public = false;
	
	/**
	 * get_response
	 *
	 * Returns the response data to sent back.
	 *
	 * @date	31/7/18
	 * @since	5.7.2
	 *
	 * @param	array $request The request args.
	 * @return	mixed The response data or WP_Error.
	 */
	function get_response( $request ) {
		$json = array();
		
		// Extract props.
		$id = isset( $request['id'] ) ? intval( $request['id'] ) : 0;
		
		// Bail ealry if missing props.
		if( !$id ) {
			return new WP_Error( 'acs_invalid_param', __( 'Invalid field group parameter(s).', 'acs' ), array( 'status' => 404 ) );
		}
		
		// Disable filters and load field group directly from database.
		acs_disable_filters();
		$field_group = acs_get_field_group( $id );
		if( !$field_group ) {
			return new WP_Error( 'acs_invalid_id', __( 'Invalid field group ID.', 'acs' ), array( 'status' => 404 ) );
		}
		$field_group['fields'] = acs_get_fields( $field_group );
		$field_group['modified'] = get_post_modified_time( 'U', true, $field_group['ID'] );
		$field_group = acs_prepare_field_group_for_export( $field_group );
		
		// Load local field group file.
		$files = acs_get_local_json_files();
		$key = $field_group['key'];
		if( !isset( $files[ $key ] ) ) {
			return new WP_Error( 'acs_cannot_compare', __( 'Sorry, this field group is unavailable for diff comparison.', 'acs' ), array( 'status' => 404 ) );
		}
		$local_field_group = json_decode( file_get_contents( $files[ $key ] ), true );
		
		// Render diff HTML.
		$date_format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
		$date_template = __( 'Last updated: %s', 'acs' );
		$json['html'] = '
		<div class="acs-diff">
			<div class="acs-diff-title">
				<div class="acs-diff-title-left">
					<strong>' . __( 'Original field group', 'acs' ) .  '</strong>
					<span>' . sprintf( $date_template, wp_date( $date_format, $field_group['modified'] ) ) . '</span>
				</div>
				<div class="acs-diff-title-right">
					<strong>' . __( 'JSON field group (newer)', 'acs' ) .  '</strong>
					<span>' . sprintf( $date_template, wp_date( $date_format, $local_field_group['modified'] ) ) . '</span>
				</div>
			</div>
			<div class="acs-diff-content">
				' . wp_text_diff( acs_json_encode( $field_group ), acs_json_encode( $local_field_group ) ) . '
			</div>
		</div>';
		return $json;
	}
}

acs_new_instance('ACS_Ajax_Local_JSON_Diff');

endif; // class_exists check
