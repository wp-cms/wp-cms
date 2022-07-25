<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('ACS_Legacy_Locations') ) :

class ACS_Legacy_Locations {
	
	/**
	 * Magic __isset method for backwards compatibility.
	 *
	 * @date	10/4/20
	 * @since	5.9.0
	 *
	 * @param	string $key Key name.
	 * @return	bool
	 */
	public function __isset( $key ) {
		//_doing_it_wrong( __FUNCTION__, __( 'The ACS_Locations class should not be accessed directly.', 'acs' ), '5.9.0' );
		
		return (
			$key === 'locations'
		);
	}
	
	/**
	 * Magic __get method for backwards compatibility.
	 *
	 * @date	10/4/20
	 * @since	5.9.0
	 *
	 * @param	string $key Key name.
	 * @return	mixed
	 */
	public function __get( $key ) {
		//_doing_it_wrong( __FUNCTION__, __( 'The ACS_Locations class should not be accessed directly.', 'acs' ), '5.9.0' );
		
		switch ( $key ) {
			case 'locations':
				return call_user_func( 'acs_get_location_types' );
		}
		return null;
	}
	
	/**
	 * Magic __call method for backwards compatibility.
	 *
	 * @date	10/4/20
	 * @since	5.9.0
	 *
	 * @param	string $name The method name.
	 * @param	array $arguments The array of arguments.
	 * @return	mixed
	 */
	public function __call( $name, $arguments ) {
		//_doing_it_wrong( __FUNCTION__, __( 'The ACS_Locations class should not be accessed directly.', 'acs' ), '5.9.0' );
		
		switch ( $name ) {
			case 'register_location':
				return call_user_func_array( 'acs_register_location_type', $arguments );
			case 'get_location':
				return call_user_func_array( 'acs_get_location_type', $arguments );
			case 'get_locations':
				return call_user_func_array( 'acs_get_location_rule_types', $arguments );
				
		}
    }
}

endif; // class_exists check
