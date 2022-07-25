<?php

/*
*  ACS Widget Form Class
*
*  All the logic for adding fields to widgets
*
*  @class 		acs_form_widget
*  @package		ACS
*  @subpackage	Forms
*/

if( ! class_exists('acs_form_widget') ) :

class acs_form_widget {
	
	
	/*
	*  __construct
	*
	*  This function will setup the class functionality
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
		
		// vars
		$this->preview_values = array();
		$this->preview_reference = array();
		$this->preview_errors = array();
		
		
		// actions
		add_action('admin_enqueue_scripts',		array($this, 'admin_enqueue_scripts'));
		add_action('in_widget_form', 			array($this, 'edit_widget'), 10, 3);
		add_action('acs/validate_save_post',	array($this, 'acs_validate_save_post'), 5);
		
		
		// filters
		add_filter('widget_update_callback', 	array($this, 'save_widget'), 10, 4);
		
	}
	
	
	/*
	*  admin_enqueue_scripts
	*
	*  This action is run after post query but before any admin script / head actions. 
	*  It is a good place to register all actions.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @date	26/01/13
	*  @since	3.6.0
	*
	*  @param	N/A
	*  @return	N/A
	*/
	
	function admin_enqueue_scripts() {
		
		// validate screen
		if( acs_is_screen('widgets') || acs_is_screen('customize') ) {
		
			// valid
			
		} else {
			
			return;
			
		}
		
		
		// load acs scripts
		acs_enqueue_scripts();
		
		
		// actions
		add_action('acs/input/admin_footer', array($this, 'admin_footer'), 1);

	}
	
	
	/*
	*  acs_validate_save_post
	*
	*  This function will loop over $_POST data and validate
	*
	*  @type	action 'acs/validate_save_post' 5
	*  @date	7/09/2016
	*  @since	5.4.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function acs_validate_save_post() {
		
		// bail ealry if not widget
		if( !isset($_POST['_acs_widget_id']) ) return;
		
		
		// vars
		$id = $_POST['_acs_widget_id'];
		$number = $_POST['_acs_widget_number'];
		$prefix = $_POST['_acs_widget_prefix'];
		
		
		// validate
		acs_validate_values( $_POST[ $id ][ $number ]['acs'], $prefix );
				
	}
	
	
	/*
	*  edit_widget
	*
	*  This function will render the fields for a widget form
	*
	*  @type	function
	*  @date	11/06/2014
	*  @since	5.0.0
	*
	*  @param	$widget (object)
	*  @param	$return (null)
	*  @param	$instance (object)
	*  @return	$post_id (int)
	*/
	
	function edit_widget( $widget, $return, $instance ) {
		
		// vars
		$post_id = 0;
		$prefix = 'widget-' . $widget->id_base . '[' . $widget->number . '][acs]';
		
		
		// get id
		if( $widget->number !== '__i__' ) {
		
			$post_id = "widget_{$widget->id}";
			
		}
		
		
		// get field groups
		$field_groups = acs_get_field_groups(array(
			'widget' => $widget->id_base
		));
		
		
		// render
		if( !empty($field_groups) ) {
			
			// render post data
			acs_form_data(array(
				'screen'		=> 'widget',
				'post_id'		=> $post_id,
				'widget_id'		=> 'widget-' . $widget->id_base,
				'widget_number'	=> $widget->number,
				'widget_prefix'	=> $prefix
			));
			
			
			// wrap
			echo '<div class="acs-widget-fields acs-fields -clear">';
			
			// loop
			foreach( $field_groups as $field_group ) {
				
				// load fields
				$fields = acs_get_fields( $field_group );
				
				
				// bail if not fields
				if( empty($fields) ) continue;
				
				
				// change prefix
				acs_prefix_fields( $fields, $prefix );
				
				
				// render
				acs_render_fields( $fields, $post_id, 'div', $field_group['instruction_placement'] );
				
			}
			
			//wrap
			echo '</div>';
			
			
			// jQuery selector looks odd, but is necessary due to WP adding an incremental number into the ID
			// - not possible to find number via PHP parameters
			if( $widget->updated ): ?>
			<script type="text/javascript">
			(function($) {
				
				acs.doAction('append', $('[id^="widget"][id$="<?php echo $widget->id; ?>"]') );
				
			})(jQuery);	
			</script>
			<?php endif;
				
		}
		
	}
	
	
	/*
	*  save_widget
	*
	*  This function will hook into the widget update filter and save ACS data
	*
	*  @type	function
	*  @date	27/05/2015
	*  @since	5.2.3
	*
	*  @param	$instance (array) widget settings
	*  @param	$new_instance (array) widget settings
	*  @param	$old_instance (array) widget settings
	*  @param	$widget (object) widget info
	*  @return	$instance
	*/
	
	function save_widget( $instance, $new_instance, $old_instance, $widget ) {
		
		// bail ealry if not valid (!customize + acs values + nonce)
		if( isset($_POST['wp_customize']) || !isset($new_instance['acs']) || !acs_verify_nonce('widget') ) return $instance;
		
		
		// save
		acs_save_post( "widget_{$widget->id}", $new_instance['acs'] );
		
		
		// return
		return $instance;
		
	}
	
	
	/*
	*  admin_footer
	*
	*  This function will add some custom HTML to the footer of the edit page
	*
	*  @type	function
	*  @date	11/06/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function admin_footer() {
?>
<script type="text/javascript">
(function($) {
	
	// vars
	acs.set('post_id', 'widgets');
	
	// Only initialize visible fields.
	acs.addFilter('find_fields', function( $fields ){
		
		// not templates
		$fields = $fields.not('#available-widgets .acs-field');
		
		// not widget dragging in
		$fields = $fields.not('.widget.ui-draggable-dragging .acs-field');
		
		// return
		return $fields;
	});
	
	// on publish
	$('#widgets-right').on('click', '.widget-control-save', function( e ){
		
		// vars
		var $button = $(this);
		var $form = $button.closest('form');
		
		// validate
		var valid = acs.validateForm({
			form: $form,
			event: e,
			reset: true
		});
		
		// if not valid, stop event and allow validation to continue
		if( !valid ) {
			e.preventDefault();
			e.stopImmediatePropagation();
		}
	});
	
	// show
	$('#widgets-right').on('click', '.widget-top', function(){
		var $widget = $(this).parent();
		if( $widget.hasClass('open') ) {
			acs.doAction('hide', $widget);
		} else {
			acs.doAction('show', $widget);
		}
	});
	
	$(document).on('widget-added', function( e, $widget ){
		
		// - use delay to avoid rendering issues with customizer (ensures div is visible)
		setTimeout(function(){
			acs.doAction('append', $widget );
		}, 100);
	});
	
})(jQuery);	
</script>
<?php
		
	}
}

new acs_form_widget();

endif;

?>
