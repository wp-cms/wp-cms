<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('ACS_Location_Block') ) :

class ACS_Location_Block extends ACS_Location {
	
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
		$this->name = 'block';
		$this->label = __( "Block", 'acs' );
		$this->category = 'forms';
		$this->object_type = 'block';
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
		if( isset($screen['block']) ) {
			$block = $screen['block'];
		} else {
			return false;
		}

		// Compare rule against $block.
		return $this->compare_to_rule( $block, $rule );
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
		$choices = array();
		
		// Append block types.
		$blocks = acs_get_block_types();
		if( $blocks ) {
			$choices[ 'all' ] = __( 'All', 'acs' );
			foreach( $blocks as $block ) {
				$choices[ $block['name'] ] = $block['title'];
			}
		} else {
			$choices[ '' ] = __( 'No block types exist', 'acs' );
		}
		
		// Return choices.
		return $choices;
	}
}

// initialize
acs_register_location_type( 'ACS_Location_Block' );

endif; // class_exists check
