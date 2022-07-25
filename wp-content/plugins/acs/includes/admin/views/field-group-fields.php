<div class="acs-field-list-wrap">
	
	<ul class="acs-hl acs-thead">
		<li class="li-field-order"><?php _e('Order','acs'); ?></li>
		<li class="li-field-label"><?php _e('Label','acs'); ?></li>
		<li class="li-field-name"><?php _e('Name','acs'); ?></li>
		<li class="li-field-key"><?php _e('Key','acs'); ?></li>
		<li class="li-field-type"><?php _e('Type','acs'); ?></li>
	</ul>
	
	<div class="acs-field-list<?php if( !$fields ){ echo ' -empty'; } ?>">
		
		<div class="no-fields-message">
			<?php _e("No fields. Click the <strong>+ Add Field</strong> button to create your first field.",'acs'); ?>
		</div>
		
		<?php if( $fields ):
			
			foreach( $fields as $i => $field ):
				
				acs_get_view('field-group-field', array( 'field' => $field, 'i' => $i ));
				
			endforeach;
		
		endif; ?>
		
	</div>
	
	<ul class="acs-hl acs-tfoot">
		<li class="acs-fr">
			<a href="#" class="button button-primary button-large add-field"><?php _e('+ Add Field','acs'); ?></a>
		</li>
	</ul>
	
<?php if( !$parent ):
	
	// get clone
	$clone = acs_get_valid_field(array(
		'ID'		=> 'acscloneindex',
		'key'		=> 'acscloneindex',
		'label'		=> __('New Field','acs'),
		'name'		=> 'new_field',
		'type'		=> 'text'
	));
	
	?>
	<script type="text/html" id="tmpl-acs-field">
	<?php acs_get_view('field-group-field', array( 'field' => $clone, 'i' => 0 )); ?>
	</script>
<?php endif;?>
	
</div>