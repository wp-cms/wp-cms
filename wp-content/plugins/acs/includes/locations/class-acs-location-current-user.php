<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('ACS_Location_Current_User') ) :

class ACS_Location_Current_User extends ACS_Location {
	
	/**
	 * Initializes props.
	 *
	 * @date	5/03/2014
	 * @since	5.0.0
	 *
	 * @param	void
	 * @return	void
	 */
	public function initialize() {
		$this->name = 'current_user';
		$this->label = __( "Current User", 'acs' );
		$this->category = 'user';
	}
	
	/**
	 * Matches the provided rule against the screen args returning a bool result.
	 *
	 * @date	9/4/20
	 * @since	5.9.0
	 *
	 * @param	array $rule The location rule.
	 * @param	array $screen The screen args.
	 * @param	array $field_group The field group settings.
	 * @return	bool
	 */
	public function match( $rule, $screen, $field_group ) {
		switch( $rule['value'] ) {
			case 'logged_in':
				$result = is_user_logged_in();
				break;
			case 'viewing_front':
				$result = !is_admin();
				break;
			case 'viewing_back':
				$result = is_admin();
				break;
			default:
				$result = false;
				break;
		}
		
		// Reverse result for "!=" operator.
        if( $rule['operator'] === '!=' ) {
        	return !$result;
        }
		return $result;
	}
	
	/**
	 * Returns an array of possible values for this rule type.
	 *
	 * @date	9/4/20
	 * @since	5.9.0
	 *
	 * @param	array $rule A location rule.
	 * @return	array
	 */
	public function get_values( $rule ) {
		return array(
			'logged_in'		=> __( 'Logged in', 'acs' ),
			'viewing_front'	=> __( 'Viewing front end', 'acs' ),
			'viewing_back'	=> __( 'Viewing back end', 'acs' )
		);
	}
}

// Register.
acs_register_location_type( 'ACS_Location_Current_User' );

endif; // class_exists check
