<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('ACS_Location_Nav_Menu_Item') ) :

class ACS_Location_Nav_Menu_Item extends ACS_Location {
	
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
		$this->name = 'nav_menu_item';
		$this->label = __( "Menu Item", 'acs' );
		$this->category = 'forms';
		$this->object_type = 'menu_item';
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
		
		// Check screen args.
		if( isset($screen['nav_menu_item']) ) {
			$nav_menu_item = $screen['nav_menu_item'];
		} else {
			return false;
		}
		
		// Append "nav_menu" global data to $screen and call 'nav_menu' logic.
		if( !isset($screen['nav_menu']) ) {
			$screen['nav_menu'] = acs_get_data('nav_menu_id');
		}
		return acs_get_location_type( 'nav_menu' )->match( $rule, $screen, $field_group );
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
		return acs_get_location_type( 'nav_menu' )->get_values( $rule );
	}
}

// Register.
acs_register_location_type( 'ACS_Location_Nav_Menu_Item' );

endif; // class_exists check
