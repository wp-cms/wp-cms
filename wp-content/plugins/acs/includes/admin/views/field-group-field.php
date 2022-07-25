<?php 

// vars
$prefix = 'acs_fields[' . $field['ID'] . ']';
$id = acs_idify( $prefix );

// add prefix
$field['prefix'] = $prefix;

// div
$div = array(
	'class' 	=> 'acs-field-object acs-field-object-' . acs_slugify($field['type']),
	'data-id'	=> $field['ID'],
	'data-key'	=> $field['key'],
	'data-type'	=> $field['type'],
);

$meta = array(
	'ID'			=> $field['ID'],
	'key'			=> $field['key'],
	'parent'		=> $field['parent'],
	'menu_order'	=> $i,
	'save'			=> ''
);

?>
<div <?php echo acs_esc_attr( $div ); ?>>
	
	<div class="meta">
		<?php foreach( $meta as $k => $v ):
			acs_hidden_input(array( 'name' => $prefix . '[' . $k . ']', 'value' => $v, 'id' => $id . '-' . $k ));
		endforeach; ?>
	</div>
	
	<div class="handle">
		<ul class="acs-hl acs-tbody">
			<li class="li-field-order">
				<span class="acs-icon acs-sortable-handle" title="<?php _e('Drag to reorder','acs'); ?>"><?php echo ($i + 1); ?></span>
			</li>
			<li class="li-field-label">
				<strong>
					<a class="edit-field" title="<?php _e("Edit field",'acs'); ?>" href="#"><?php echo acs_get_field_label($field, 'admin'); ?></a>
				</strong>
				<div class="row-options">
					<a class="edit-field" title="<?php _e("Edit field",'acs'); ?>" href="#"><?php _e("Edit",'acs'); ?></a>
					<a class="duplicate-field" title="<?php _e("Duplicate field",'acs'); ?>" href="#"><?php _e("Duplicate",'acs'); ?></a>
					<a class="move-field" title="<?php _e("Move field to another group",'acs'); ?>" href="#"><?php _e("Move",'acs'); ?></a>
					<a class="delete-field" title="<?php _e("Delete field",'acs'); ?>" href="#"><?php _e("Delete",'acs'); ?></a>
				</div>
			</li>
			<?php // whitespace before field name looks odd but fixes chrome bug selecting all text in row ?>
			<li class="li-field-name"> <?php echo $field['name']; ?></li>
			<li class="li-field-key"> <?php echo $field['key']; ?></li>
			<li class="li-field-type"> <?php echo acs_get_field_type_label($field['type']); ?></li>
		</ul>
	</div>
	
	<div class="settings">			
		<table class="acs-table">
			<tbody class="acs-field-settings">
				<?php 
				
				// label
				acs_render_field_setting($field, array(
					'label'			=> __('Field Label','acs'),
					'instructions'	=> __('This is the name which will appear on the EDIT page','acs'),
					'name'			=> 'label',
					'type'			=> 'text',
					'class'			=> 'field-label'
				), true);
				
				
				// name
				acs_render_field_setting($field, array(
					'label'			=> __('Field Name','acs'),
					'instructions'	=> __('Single word, no spaces. Underscores and dashes allowed','acs'),
					'name'			=> 'name',
					'type'			=> 'text',
					'class'			=> 'field-name'
				), true);
				
				
				// type
				acs_render_field_setting($field, array(
					'label'			=> __('Field Type','acs'),
					'instructions'	=> '',
					'type'			=> 'select',
					'name'			=> 'type',
					'choices' 		=> acs_get_grouped_field_types(),
					'class'			=> 'field-type'
				), true);
				
				
				// instructions
				acs_render_field_setting($field, array(
					'label'			=> __('Instructions','acs'),
					'instructions'	=> __('Instructions for authors. Shown when submitting data','acs'),
					'type'			=> 'textarea',
					'name'			=> 'instructions',
					'rows'			=> 5
				), true);
				
				
				// required
				acs_render_field_setting($field, array(
					'label'			=> __('Required?','acs'),
					'instructions'	=> '',
					'type'			=> 'true_false',
					'name'			=> 'required',
					'ui'			=> 1,
					'class'			=> 'field-required'
				), true);
				
				
				// 3rd party settings
				do_action('acs/render_field_settings', $field);
				
				
				// type specific settings
				do_action("acs/render_field_settings/type={$field['type']}", $field);
				
				
				// conditional logic
				acs_get_view('field-group-field-conditional-logic', array( 'field' => $field ));
				
				
				// wrapper
				acs_render_field_wrap(array(
					'label'			=> __('Wrapper Attributes','acs'),
					'instructions'	=> '',
					'type'			=> 'number',
					'name'			=> 'width',
					'prefix'		=> $field['prefix'] . '[wrapper]',
					'value'			=> $field['wrapper']['width'],
					'prepend'		=> __('width', 'acs'),
					'append'		=> '%',
					'wrapper'		=> array(
						'data-name' => 'wrapper',
						'class' => 'acs-field-setting-wrapper'
					)
				), 'tr');
				
				acs_render_field_wrap(array(
					'label'			=> '',
					'instructions'	=> '',
					'type'			=> 'text',
					'name'			=> 'class',
					'prefix'		=> $field['prefix'] . '[wrapper]',
					'value'			=> $field['wrapper']['class'],
					'prepend'		=> __('class', 'acs'),
					'wrapper'		=> array(
						'data-append' => 'wrapper'
					)
				), 'tr');
				
				acs_render_field_wrap(array(
					'label'			=> '',
					'instructions'	=> '',
					'type'			=> 'text',
					'name'			=> 'id',
					'prefix'		=> $field['prefix'] . '[wrapper]',
					'value'			=> $field['wrapper']['id'],
					'prepend'		=> __('id', 'acs'),
					'wrapper'		=> array(
						'data-append' => 'wrapper'
					)
				), 'tr');
				
				?>
				<tr class="acs-field acs-field-save">
					<td class="acs-label"></td>
					<td class="acs-input">
						<ul class="acs-hl">
							<li>
								<a class="button edit-field" title="<?php _e("Close Field",'acs'); ?>" href="#"><?php _e("Close Field",'acs'); ?></a>
							</li>
						</ul>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	
</div>