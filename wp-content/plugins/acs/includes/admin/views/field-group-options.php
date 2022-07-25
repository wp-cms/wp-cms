<?php

// global
global $field_group;
		
		
// active
acs_render_field_wrap(array(
	'label'			=> __('Active','acs'),
	'instructions'	=> '',
	'type'			=> 'true_false',
	'name'			=> 'active',
	'prefix'		=> 'acs_field_group',
	'value'			=> $field_group['active'],
	'ui'			=> 1,
	//'ui_on_text'	=> __('Active', 'acs'),
	//'ui_off_text'	=> __('Inactive', 'acs'),
));


// style
acs_render_field_wrap(array(
	'label'			=> __('Style','acs'),
	'instructions'	=> '',
	'type'			=> 'select',
	'name'			=> 'style',
	'prefix'		=> 'acs_field_group',
	'value'			=> $field_group['style'],
	'choices' 		=> array(
		'default'			=>	__("Standard (WP metabox)",'acs'),
		'seamless'			=>	__("Seamless (no metabox)",'acs'),
	)
));


// position
acs_render_field_wrap(array(
	'label'			=> __('Position','acs'),
	'instructions'	=> '',
	'type'			=> 'select',
	'name'			=> 'position',
	'prefix'		=> 'acs_field_group',
	'value'			=> $field_group['position'],
	'choices' 		=> array(
		'acs_after_title'	=> __("High (after title)",'acs'),
		'normal'			=> __("Normal (after content)",'acs'),
		'side' 				=> __("Side",'acs'),
	),
	'default_value'	=> 'normal'
));


// label_placement
acs_render_field_wrap(array(
	'label'			=> __('Label placement','acs'),
	'instructions'	=> '',
	'type'			=> 'select',
	'name'			=> 'label_placement',
	'prefix'		=> 'acs_field_group',
	'value'			=> $field_group['label_placement'],
	'choices' 		=> array(
		'top'			=>	__("Top aligned",'acs'),
		'left'			=>	__("Left aligned",'acs'),
	)
));


// instruction_placement
acs_render_field_wrap(array(
	'label'			=> __('Instruction placement','acs'),
	'instructions'	=> '',
	'type'			=> 'select',
	'name'			=> 'instruction_placement',
	'prefix'		=> 'acs_field_group',
	'value'			=> $field_group['instruction_placement'],
	'choices' 		=> array(
		'label'		=>	__("Below labels",'acs'),
		'field'		=>	__("Below fields",'acs'),
	)
));


// menu_order
acs_render_field_wrap(array(
	'label'			=> __('Order No.','acs'),
	'instructions'	=> __('Field groups with a lower order will appear first','acs'),
	'type'			=> 'number',
	'name'			=> 'menu_order',
	'prefix'		=> 'acs_field_group',
	'value'			=> $field_group['menu_order'],
));


// description
acs_render_field_wrap(array(
	'label'			=> __('Description','acs'),
	'instructions'	=> __('Shown in field group list','acs'),
	'type'			=> 'text',
	'name'			=> 'description',
	'prefix'		=> 'acs_field_group',
	'value'			=> $field_group['description'],
));


// hide on screen
$choices = array(
	'permalink'			=>	__("Permalink", 'acs'),
	'the_content'		=>	__("Content Editor",'acs'),
	'excerpt'			=>	__("Excerpt", 'acs'),
	'custom_fields'		=>	__("Custom Fields", 'acs'),
	'discussion'		=>	__("Discussion", 'acs'),
	'comments'			=>	__("Comments", 'acs'),
	'revisions'			=>	__("Revisions", 'acs'),
	'slug'				=>	__("Slug", 'acs'),
	'author'			=>	__("Author", 'acs'),
	'format'			=>	__("Format", 'acs'),
	'page_attributes'	=>	__("Page Attributes", 'acs'),
	'featured_image'	=>	__("Featured Image", 'acs'),
	'categories'		=>	__("Categories", 'acs'),
	'tags'				=>	__("Tags", 'acs'),
	'send-trackbacks'	=>	__("Send Trackbacks", 'acs'),
);
if( acs_get_setting('remove_wp_meta_box') ) {
	unset( $choices['custom_fields'] );	
}

acs_render_field_wrap(array(
	'label'			=> __('Hide on screen','acs'),
	'instructions'	=> __('<b>Select</b> items to <b>hide</b> them from the edit screen.','acs') . '<br /><br />' . __("If multiple field groups appear on an edit screen, the first field group's options will be used (the one with the lowest order number)",'acs'),
	'type'			=> 'checkbox',
	'name'			=> 'hide_on_screen',
	'prefix'		=> 'acs_field_group',
	'value'			=> $field_group['hide_on_screen'],
	'toggle'		=> true,
	'choices' 		=> $choices
));


// 3rd party settings
do_action('acs/render_field_group_settings', $field_group);
		
?>
<div class="acs-hidden">
	<input type="hidden" name="acs_field_group[key]" value="<?php echo $field_group['key']; ?>" />
</div>
<script type="text/javascript">
if( typeof acs !== 'undefined' ) {
		
	acs.newPostbox({
		'id': 'acs-field-group-options',
		'label': 'left'
	});	

}
</script>