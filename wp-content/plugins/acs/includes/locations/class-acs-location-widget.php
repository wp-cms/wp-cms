<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('ACS_Location_Widget') ) :

class ACS_Location_Widget extends ACS_Location {
	
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
		$this->name = 'widget';
		$this->label = __( "Widget", 'acs' );
		$this->category = 'forms';
		$this->object_type = 'widget';
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
		if( isset($screen['widget']) ) {
			$widget = $screen['widget'];
		} else {
			return false;
		}
		
		// Compare rule against $widget.
		return $this->compare_to_rule( $widget, $rule );
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
		global $wp_widget_factory;
		
		// Populate choices.
		$choices = array( 
			'all' => __( 'All', 'acs' )
		);
		if( $wp_widget_factory->widgets ) {
			foreach( $wp_widget_factory->widgets as $widget ) {
				$choices[ $widget->id_base ] = $widget->name;
			}
		}
		return $choices;
	}
}

// initialize
acs_register_location_type( 'ACS_Location_Widget' );

endif; // class_exists check
