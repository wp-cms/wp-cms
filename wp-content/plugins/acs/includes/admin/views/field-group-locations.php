<?php

// global
global $field_group;

?>
<div class="acs-field">
	<div class="acs-label">
		<label><?php _e("Rules",'acs'); ?></label>
		<p class="description"><?php _e("Create a set of rules to determine which edit screens will use these amazing custom stuff",'acs'); ?></p>
	</div>
	<div class="acs-input">
		<div class="rule-groups">
			
			<?php foreach( $field_group['location'] as $i => $group ): 
				
				// bail ealry if no group
				if( empty($group) ) return;
				
				
				// view
				acs_get_view('html-location-group', array(
					'group'		=> $group,
					'group_id'	=> "group_{$i}"
				));
			
			endforeach;	?>
			
			<h4><?php _e("or",'acs'); ?></h4>
			
			<a href="#" class="button add-location-group"><?php _e("Add rule group",'acs'); ?></a>
			
		</div>
	</div>
</div>
<script type="text/javascript">
if( typeof acs !== 'undefined' ) {
		
	acs.newPostbox({
		'id': 'acs-field-group-locations',
		'label': 'left'
	});	

}
</script>