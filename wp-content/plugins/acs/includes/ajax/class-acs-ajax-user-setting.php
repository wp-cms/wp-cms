<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('ACS_Ajax_User_Setting') ) :

class ACS_Ajax_User_Setting extends ACS_Ajax {
	
	/** @var string The AJAX action name. */
	var $action = 'acs/ajax/user_setting';
	
	/** @var bool Prevents access for non-logged in users. */
	var $public = true;
	
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
		
		// update
		if( $this->has('value') ) {
			return acs_update_user_setting( $this->get('name'), $this->get('value') );
		
		// get
		} else {
			return acs_get_user_setting( $this->get('name') );
		}
	}
}

acs_new_instance('ACS_Ajax_User_Setting');

endif; // class_exists check
