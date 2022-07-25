<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('ACS_Ajax_Check_Screen') ) :

class ACS_Ajax_Check_Screen extends ACS_Ajax {
	
	/** @var string The AJAX action name. */
	var $action = 'acs/ajax/check_screen';
	
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
		
		// vars
		$args = wp_parse_args($this->request, array(
			'screen'	=> '',
			'post_id'	=> 0,
			'ajax'		=> true,
			'exists'	=> array()
		));
		
		// vars
		$response = array(
			'results'	=> array(),
			'style'		=> ''
		);
		
		// get field groups
		$field_groups = acs_get_field_groups( $args );
		
		// loop through field groups
		if( $field_groups ) {
			foreach( $field_groups as $i => $field_group ) {
				
				// vars
				$item = array(
					'id'		=> 'acs-' . $field_group['key'],
					'key'		=> $field_group['key'],
					'title'		=> $field_group['title'],
					'position'	=> $field_group['position'],
					'style'		=> $field_group['style'],
					'label'		=> $field_group['label_placement'],
					'edit'		=> acs_get_field_group_edit_link( $field_group['ID'] ),
					'html'		=> ''
				);
				
				// append html if doesnt already exist on page
				if( !in_array($field_group['key'], $args['exists']) ) {
					
					// load fields
					$fields = acs_get_fields( $field_group );
	
					// get field HTML
					ob_start();
					
					// render
					acs_render_fields( $fields, $args['post_id'], 'div', $field_group['instruction_placement'] );
					
					$item['html'] = ob_get_clean();
				}
				
				// append
				$response['results'][] = $item;
			}
			
			// Get style from first field group.
			$response['style'] = acs_get_field_group_style( $field_groups[0] );
		}
		
		// Custom metabox order.
		if( $this->get('screen') == 'post' ) {
			$response['sorted'] = get_user_option('meta-box-order_' . $this->get('post_type'));
		}
		
		// return
		return $response;
	}
}

acs_new_instance('ACS_Ajax_Check_Screen');

endif; // class_exists check

?>