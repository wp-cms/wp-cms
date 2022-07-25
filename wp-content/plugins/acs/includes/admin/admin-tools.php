<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('acs_admin_tools') ) :

class acs_admin_tools {
	
	
	/** @var array Contains an array of admin tool instances */
	var $tools = array();
	
	
	/** @var string The active tool */
	var $active = '';
	
	
	/**
	*  __construct
	*
	*  This function will setup the class functionality
	*
	*  @date	10/10/17
	*  @since	5.6.3
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
		
		// actions
		add_action('admin_menu', array($this, 'admin_menu'));
		
	}
	
	
	/**
	*  register_tool
	*
	*  This function will store a tool tool class
	*
	*  @date	10/10/17
	*  @since	5.6.3
	*
	*  @param	string $class
	*  @return	n/a
	*/
	
	function register_tool( $class ) {
		
		$instance = new $class();
		$this->tools[ $instance->name ] = $instance;
		
	}
	
	
	/**
	*  get_tool
	*
	*  This function will return a tool tool class
	*
	*  @date	10/10/17
	*  @since	5.6.3
	*
	*  @param	string $name
	*  @return	n/a
	*/
	
	function get_tool( $name ) {
		
		return isset( $this->tools[$name] ) ? $this->tools[$name] : null;
		
	}
	
	
	/**
	*  get_tools
	*
	*  This function will return an array of all tools
	*
	*  @date	10/10/17
	*  @since	5.6.3
	*
	*  @param	n/a
	*  @return	array
	*/
	
	function get_tools() {
		
		return $this->tools;
		
	}
	
	
	/*
	*  admin_menu
	*
	*  This function will add the ACS menu item to the WP admin
	*
	*  @type	action (admin_menu)
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function admin_menu() {
		
		// bail early if no show_admin
		if( !acs_get_setting('show_admin') ) return;
		
		
		// add page
		$page = add_submenu_page('edit.php?post_type=acs-field-group', __('Tools','acs'), __('Tools','acs'), acs_get_setting('capability'), 'acs-tools', array($this, 'html'));
		
		
		// actions
		add_action('load-' . $page, array($this, 'load'));
		
	}
	
	
	/**
	*  load
	*
	*  description
	*
	*  @date	10/10/17
	*  @since	5.6.3
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function load() {
		
		// disable filters (default to raw data)
		acs_disable_filters();
		
		
		// include tools
		$this->include_tools();
		
		
		// check submit
		$this->check_submit();
		
		
		// load acs scripts
		acs_enqueue_scripts();
		
	}
	
	
	/**
	*  include_tools
	*
	*  description
	*
	*  @date	10/10/17
	*  @since	5.6.3
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function include_tools() {
		
		// include
		acs_include('includes/admin/tools/class-acs-admin-tool.php');
		acs_include('includes/admin/tools/class-acs-admin-tool-export.php');
		acs_include('includes/admin/tools/class-acs-admin-tool-import.php');
		
		
		// action
		do_action('acs/include_admin_tools');
		
	}
	
	
	/**
	*  check_submit
	*
	*  description
	*
	*  @date	10/10/17
	*  @since	5.6.3
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function check_submit() {
		
		// loop
		foreach( $this->get_tools() as $tool ) {
			
			// load
			$tool->load();
			
			
			// submit
			if( acs_verify_nonce($tool->name) ) {
				$tool->submit();
			}
			
		}
		
	}
	
	
	/**
	*  html
	*
	*  description
	*
	*  @date	10/10/17
	*  @since	5.6.3
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function html() {
		
		// vars
		$screen = get_current_screen();
		$active = acs_maybe_get_GET('tool');
		
		
		// view
		$view = array(
			'screen_id'	=> $screen->id,
			'active'	=> $active
		);
		
		
		// register metaboxes
		foreach( $this->get_tools() as $tool ) {
			
			// check active
			if( $active && $active !== $tool->name ) continue;
			
			
			// add metabox
			add_meta_box( 'acs-admin-tool-' . $tool->name, $tool->title, array($this, 'metabox_html'), $screen->id, 'normal', 'default', array('tool' => $tool->name) );
			
		}
		
		
		// view
		acs_get_view( 'html-admin-tools', $view );
		
	}
	
	
	/**
	*  meta_box_html
	*
	*  description
	*
	*  @date	10/10/17
	*  @since	5.6.3
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function metabox_html( $post, $metabox ) {
		
		// vars
		$tool = $this->get_tool($metabox['args']['tool']);
		
		
		?>
		<form method="post">
			<?php $tool->html(); ?>
			<?php acs_nonce_input( $tool->name ); ?>
		</form>
		<?php
		
	}
	
}

// initialize
acs()->admin_tools = new acs_admin_tools();

endif; // class_exists check


/*
*  acs_register_admin_tool
*
*  alias of acs()->admin_tools->register_tool()
*
*  @type	function
*  @date	31/5/17
*  @since	5.6.0
*
*  @param	n/a
*  @return	n/a
*/

function acs_register_admin_tool( $class ) {
	
	return acs()->admin_tools->register_tool( $class );
	
}


/*
*  acs_get_admin_tools_url
*
*  This function will return the admin URL to the tools page
*
*  @type	function
*  @date	31/5/17
*  @since	5.6.0
*
*  @param	n/a
*  @return	n/a
*/

function acs_get_admin_tools_url() {
	
	return admin_url('edit.php?post_type=acs-field-group&page=acs-tools');
	
}


/*
*  acs_get_admin_tool_url
*
*  This function will return the admin URL to the tools page
*
*  @type	function
*  @date	31/5/17
*  @since	5.6.0
*
*  @param	n/a
*  @return	n/a
*/

function acs_get_admin_tool_url( $tool = '' ) {
	
	return acs_get_admin_tools_url() . '&tool='.$tool;
	
}


?>