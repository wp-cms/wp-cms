<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('ACS_Location_Page') ) :

class ACS_Location_Page extends ACS_Location {
	
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
		$this->name = 'page';
		$this->label = __( "Page", 'acs' );
		$this->category = 'page';
    	$this->object_type = 'post';
    	$this->object_subtype = 'page';
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
		return acs_get_location_type( 'post' )->match( $rule, $screen, $field_group );
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
		
		// Get grouped posts.
		$groups = acs_get_grouped_posts(array(
			'post_type' => array( 'page' )
		));
		
		// Get first group.
		$posts = reset( $groups );
		
		// Append to choices.
		if( $posts ) {
			foreach( $posts as $post ) {
				$choices[ $post->ID ] = acs_get_post_title( $post );
			}
		}
		return $choices;
	}
}

// Register.
acs_register_location_type( 'ACS_Location_Page' );

endif; // class_exists check
