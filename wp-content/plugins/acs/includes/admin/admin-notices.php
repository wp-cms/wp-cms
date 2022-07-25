<?php 
/**
 * ACS Admin Notices
 *
 * Functions and classes to manage admin notices.
 *
 * @date	10/1/19
 * @since	5.7.10
 */

// Exit if accessed directly.
if( !defined('ABSPATH') ) exit;  

// Register notices store.
acs_register_store( 'notices' );

/**
 * ACS_Admin_Notice
 *
 * Class used to create an admin notice.
 *
 * @date	10/1/19
 * @since	5.7.10
 */
if( ! class_exists('ACS_Admin_Notice') ) :

class ACS_Admin_Notice extends ACS_Data {
	
	/** @var array Storage for data. */
	var $data = array(
		
		/** @type string Text displayed in notice. */
		'text' => '',
		
		/** @type string Optional HTML alternative to text. 
		'html' => '', */
		
		/** @type string The type of notice (warning, error, success, info). */
		'type' => 'info',
		
		/** @type bool If the notice can be dismissed. */
		'dismissible' => true,
	);
	
	/**
	*  render
	*
	*  Renders the notice HTML.
	*
	*  @date	27/12/18
	*  @since	5.8.0
	*
	*  @param	void
	*  @return	void
	*/
	function render() {
		
		// Ensure text contains punctuation.
		// todo: Remove this after updating translations.
		$text = $this->get('text');
		if( substr($text, -1) !== '.' && substr($text, -1) !== '>' ) {
			$text .= '.';
		} 
		
		// Print HTML.
		printf('<div class="acs-admin-notice notice notice-%s %s">%s</div>',
			
			// Type class.
			$this->get('type'),
			
			// Dismissible class.
			$this->get('dismissible') ? 'is-dismissible' : '',
			
			// InnerHTML
			$this->has('html') ? $this->get('html') : wpautop($text)
		);
	}
}

endif; // class_exists check

/**
*  acs_new_admin_notice
*
*  Instantiates and returns a new model.
*
*  @date	23/12/18
*  @since	5.8.0
*
*  @param	array $data Optional data to set.
*  @return	ACS_Admin_Notice
*/
function acs_new_admin_notice( $data = false ) {
	
	// Create notice.
	$instance = new ACS_Admin_Notice( $data );
	
	// Register notice.
	acs_get_store( 'notices' )->set( $instance->cid, $instance );
	
	// Return notice.
	return $instance;
}

/**
 * acs_render_admin_notices
 *
 * Renders all admin notices HTML.
 *
 * @date	10/1/19
 * @since	5.7.10
 *
 * @param	void
 * @return	void
 */
function acs_render_admin_notices() {
	
	// Get notices.
	$notices = acs_get_store( 'notices' )->get_data();
	
	// Loop over notices and render.
	if( $notices ) {
		foreach( $notices as $notice ) {
			$notice->render();
		}
	}
}

// Render notices during admin action.
add_action('admin_notices', 'acs_render_admin_notices', 99);

/**
 * acs_add_admin_notice
 *
 * Creates and returns a new notice.
 *
 * @date		17/10/13
 * @since		5.0.0
 *
 * @param	string $text The admin notice text.
 * @param	string $class The type of notice (warning, error, success, info).
 * @return	ACS_Admin_Notice
 */
function acs_add_admin_notice( $text = '', $type = 'info' ) {
	return acs_new_admin_notice( array( 'text' => $text, 'type' => $type ) );
}