<?php 

/**
*  html-admin-tools
*
*  View to output admin tools for both archive and single
*
*  @date	20/10/17
*  @since	5.6.3
*
*  @param	string $screen_id The screen ID used to display metaboxes
*  @param	string $active The active Tool
*  @return	n/a
*/

$class = $active ? 'single' : 'grid';

?>
<div class="wrap" id="acs-admin-tools">
	
	<h1><?php _e('Tools', 'acs'); ?> <?php if( $active ): ?><a class="page-title-action" href="<?php echo acs_get_admin_tools_url(); ?>"><?php _e('Back to all tools', 'acs'); ?></a><?php endif; ?></h1>
	
	<div class="acs-meta-box-wrap -<?php echo $class; ?>">
		<?php do_meta_boxes( $screen_id, 'normal', '' ); ?>	
	</div>
	
</div>