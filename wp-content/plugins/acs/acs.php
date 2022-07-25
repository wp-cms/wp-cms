<?php
/*
Plugin Name: Amazing Custom Stuff
Plugin URI: https://github.com/wp-cms/amazing-custom-stuff
Description: Add boxes with editable fields into almost any area of your backend.
Version: 1.0.0
Author: Hairy Plotter (forked from ACF 5.9.5 by Delicious Brains)
Text Domain: acs
Domain Path: /languages
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('ACS') ) :

class ACS {
	
	/** @var string The plugin version number. */
	var $version = '5.9.5';
	
	/** @var array The plugin settings array. */
	var $settings = array();
	
	/** @var array The plugin data array. */
	var $data = array();
	
	/** @var array Storage for class instances. */
	var $instances = array();
	
	/**
	 * __construct
	 *
	 * A dummy constructor to ensure ACS is only setup once.
	 *
	 * @date	23/06/12
	 * @since	5.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function __construct() {
		// Do nothing.
	}
	
	/**
	 * initialize
	 *
	 * Sets up the ACS plugin.
	 *
	 * @date	28/09/13
	 * @since	5.0.0
	 *
	 * @param	void
	 * @return	void
	 */
	function initialize() {
		
		// Define constants.
		$this->define( 'ACS', true );
		$this->define( 'ACS_PATH', plugin_dir_path( __FILE__ ) );
		$this->define( 'ACS_BASENAME', plugin_basename( __FILE__ ) );
		$this->define( 'ACS_VERSION', $this->version );
		$this->define( 'ACS_MAJOR_VERSION', 5 );
		
		// Define settings.
		$this->settings = array(
			'name'						=> __('Amazing Custom Stuff', 'acs'),
			'slug'						=> dirname( ACS_BASENAME ),
			'version'					=> ACS_VERSION,
			'basename'					=> ACS_BASENAME,
			'path'						=> ACS_PATH,
			'file'						=> __FILE__,
			'url'						=> plugin_dir_url( __FILE__ ),
			'show_admin'				=> true,
			'show_updates'				=> true,
			'stripslashes'				=> false,
			'local'						=> true,
			'json'						=> true,
			'save_json'					=> '',
			'load_json'					=> array(),
			'default_language'			=> '',
			'current_language'			=> '',
			'capability'				=> 'manage_options',
			'uploader'					=> 'wp',
			'autoload'					=> false,
			'l10n'						=> true,
			'l10n_textdomain'			=> '',
			'google_api_key'			=> '',
			'google_api_client'			=> '',
			'enqueue_google_maps'		=> true,
			'enqueue_select2'			=> true,
			'enqueue_datepicker'		=> true,
			'enqueue_datetimepicker'	=> true,
			'select2_version'			=> 4,
			'row_index_offset'			=> 1,
			'remove_wp_meta_box'		=> true
		);
		
		// Include utility functions.
		include_once( ACS_PATH . 'includes/acs-utility-functions.php');
		
		// Include previous API functions.
		acs_include('includes/api/api-helpers.php');
		acs_include('includes/api/api-template.php');
		acs_include('includes/api/api-term.php');
		
		// Include classes.
		acs_include('includes/class-acs-data.php');
		acs_include('includes/fields/class-acs-field.php');
		acs_include('includes/locations/abstract-acs-legacy-location.php');
		acs_include('includes/locations/abstract-acs-location.php');
		
		// Include functions.
		acs_include('includes/acs-helper-functions.php');
		acs_include('includes/acs-hook-functions.php');
		acs_include('includes/acs-field-functions.php');
		acs_include('includes/acs-field-group-functions.php');
		acs_include('includes/acs-form-functions.php');
		acs_include('includes/acs-meta-functions.php');
		acs_include('includes/acs-post-functions.php');
		acs_include('includes/acs-user-functions.php');
		acs_include('includes/acs-value-functions.php');
		acs_include('includes/acs-input-functions.php');
		acs_include('includes/acs-wp-functions.php');
		
		// Include core.
		acs_include('includes/fields.php');
		acs_include('includes/locations.php');
		acs_include('includes/assets.php');
		acs_include('includes/compatibility.php');
		acs_include('includes/deprecated.php');
		acs_include('includes/l10n.php');
		acs_include('includes/local-fields.php');
		acs_include('includes/local-meta.php');
		acs_include('includes/local-json.php');
		acs_include('includes/loop.php');
		acs_include('includes/media.php');
		acs_include('includes/revisions.php');
		acs_include('includes/validation.php');
		acs_include('includes/options-page.php');
		
		// Include ajax.
		acs_include('includes/ajax/class-acs-ajax.php');
		acs_include('includes/ajax/class-acs-ajax-check-screen.php');
		acs_include('includes/ajax/class-acs-ajax-user-setting.php');
		acs_include('includes/ajax/class-acs-ajax-query.php');
		acs_include('includes/ajax/class-acs-ajax-query-users.php');
		acs_include('includes/ajax/class-acs-ajax-local-json-diff.php');
		
		// Include forms.
		acs_include('includes/forms/form-attachment.php');
		acs_include('includes/forms/form-comment.php');
		acs_include('includes/forms/form-customizer.php');
		acs_include('includes/forms/form-front.php');
		acs_include('includes/forms/form-nav-menu.php');
		acs_include('includes/forms/form-post.php');
		acs_include('includes/forms/form-gutenberg.php');
		acs_include('includes/forms/form-taxonomy.php');
		acs_include('includes/forms/form-user.php');
		acs_include('includes/forms/form-widget.php');
		
		// Include admin.
		if( is_admin() ) {
			acs_include('includes/admin/admin.php');
			acs_include('includes/admin/admin-field-group.php');
			acs_include('includes/admin/admin-field-groups.php');
			acs_include('includes/admin/admin-notices.php');
			acs_include('includes/admin/admin-tools.php');
			acs_include('includes/admin/admin-options-page.php');
		}
		
		// Include legacy.
		acs_include('includes/legacy/legacy-locations.php');
		
		// Include tests.
		if( defined('ACS_DEV') && ACS_DEV ) {
			acs_include('tests/tests.php');
		}
		
		// Add actions.
		add_action( 'init', array($this, 'init'), 5 );
		add_action( 'init', array($this, 'register_post_types'), 5 );
		add_action( 'init', array($this, 'register_post_status'), 5 );
		
		// Add filters.
		add_filter( 'posts_where', array($this, 'posts_where'), 10, 2 );
	}
	
	/**
	 * init
	 *
	 * Completes the setup process on "init" of earlier.
	 *
	 * @date	28/09/13
	 * @since	5.0.0
	 *
	 * @param	void
	 * @return	void
	 */
	function init() {
		
		// Bail early if called directly from functions.php or plugin file.
		if( !did_action('plugins_loaded') ) {
			return;
		}
		
		// This function may be called directly from template functions. Bail early if already did this.
		if( acs_did('init') ) {
			return;
		}
		
		// Update url setting. Allows other plugins to modify the URL (force SSL).
		acs_update_setting( 'url', plugin_dir_url( __FILE__ ) );
		
		// Load textdomain file.
		acs_load_textdomain();
		
		// Include 3rd party compatiblity.
		acs_include('includes/third-party.php');
		
		// Include fields.
		acs_include('includes/fields/class-acs-field-text.php');
		acs_include('includes/fields/class-acs-field-textarea.php');
		acs_include('includes/fields/class-acs-field-number.php');
		acs_include('includes/fields/class-acs-field-range.php');
		acs_include('includes/fields/class-acs-field-email.php');
		acs_include('includes/fields/class-acs-field-url.php');
		acs_include('includes/fields/class-acs-field-password.php');
		acs_include('includes/fields/class-acs-field-image.php');
		acs_include('includes/fields/class-acs-field-file.php');
		acs_include('includes/fields/class-acs-field-wysiwyg.php');
		acs_include('includes/fields/class-acs-field-oembed.php');
		acs_include('includes/fields/class-acs-field-select.php');
		acs_include('includes/fields/class-acs-field-checkbox.php');
		acs_include('includes/fields/class-acs-field-radio.php');
		acs_include('includes/fields/class-acs-field-button-group.php');
		acs_include('includes/fields/class-acs-field-true_false.php');
		acs_include('includes/fields/class-acs-field-link.php');
		acs_include('includes/fields/class-acs-field-post_object.php');
		acs_include('includes/fields/class-acs-field-page_link.php');
		acs_include('includes/fields/class-acs-field-relationship.php');
		acs_include('includes/fields/class-acs-field-taxonomy.php');
		acs_include('includes/fields/class-acs-field-user.php');
		acs_include('includes/fields/class-acs-field-google-map.php');
		acs_include('includes/fields/class-acs-field-date_picker.php');
		acs_include('includes/fields/class-acs-field-date_time_picker.php');
		acs_include('includes/fields/class-acs-field-time_picker.php');
		acs_include('includes/fields/class-acs-field-color_picker.php');
		acs_include('includes/fields/class-acs-field-message.php');
		acs_include('includes/fields/class-acs-field-accordion.php');
		acs_include('includes/fields/class-acs-field-tab.php');
		acs_include('includes/fields/class-acs-field-group.php');
		acs_include('includes/fields/class-acs-field-clone.php');
		acs_include('includes/fields/class-acs-field-flexible-content.php');
		acs_include('includes/fields/class-acs-field-gallery.php');
		acs_include('includes/fields/class-acs-field-repeater.php');
		
		/**
		 * Fires after field types have been included.
		 *
		 * @date	28/09/13
		 * @since	5.0.0
		 *
		 * @param	int $major_version The major version of ACS.
		 */
		do_action( 'acs/include_field_types', ACS_MAJOR_VERSION );
		
		// Include locations.
		acs_include('includes/locations/class-acs-location-post-type.php');
		acs_include('includes/locations/class-acs-location-post-template.php');
		acs_include('includes/locations/class-acs-location-post-status.php');
		acs_include('includes/locations/class-acs-location-post-format.php');
		acs_include('includes/locations/class-acs-location-post-category.php');
		acs_include('includes/locations/class-acs-location-post-taxonomy.php');
		acs_include('includes/locations/class-acs-location-post.php');
		acs_include('includes/locations/class-acs-location-page-template.php');
		acs_include('includes/locations/class-acs-location-page-type.php');
		acs_include('includes/locations/class-acs-location-page-parent.php');
		acs_include('includes/locations/class-acs-location-page.php');
		acs_include('includes/locations/class-acs-location-current-user.php');
		acs_include('includes/locations/class-acs-location-current-user-role.php');
		acs_include('includes/locations/class-acs-location-user-form.php');
		acs_include('includes/locations/class-acs-location-user-role.php');
		acs_include('includes/locations/class-acs-location-taxonomy.php');
		acs_include('includes/locations/class-acs-location-attachment.php');
		acs_include('includes/locations/class-acs-location-comment.php');
		acs_include('includes/locations/class-acs-location-widget.php');
		acs_include('includes/locations/class-acs-location-nav-menu.php');
		acs_include('includes/locations/class-acs-location-nav-menu-item.php');
		acs_include('includes/locations/class-acs-location-block.php');
		acs_include('includes/locations/class-acs-location-options-page.php');
		
		/**
		 * Fires after location types have been included.
		 *
		 * @date	28/09/13
		 * @since	5.0.0
		 *
		 * @param	int $major_version The major version of ACS.
		 */
		do_action( 'acs/include_location_rules', ACS_MAJOR_VERSION );
		
		/**
		 * Fires during initialization. Used to add local fields.
		 *
		 * @date	28/09/13
		 * @since	5.0.0
		 *
		 * @param	int $major_version The major version of ACS.
		 */
		do_action( 'acs/include_fields', ACS_MAJOR_VERSION );
		
		/**
		 * Fires after ACS is completely "initialized".
		 *
		 * @date	28/09/13
		 * @since	5.0.0
		 *
		 * @param	int $major_version The major version of ACS.
		 */
		do_action( 'acs/init', ACS_MAJOR_VERSION );
	}
	
	/**
	 * register_post_types
	 *
	 * Registers the ACS post types.
	 *
	 * @date	22/10/2015
	 * @since	5.3.2
	 *
	 * @param	void
	 * @return	void
	 */	
	function register_post_types() {
		
		// Vars.
		$cap = acs_get_setting('capability');
		
		// Register the Field Group post type.
		register_post_type('acs-field-group', array(
			'labels'			=> array(
			    'name'					=> __( 'Field Groups', 'acs' ),
				'singular_name'			=> __( 'Field Group', 'acs' ),
			    'add_new'				=> __( 'Add New' , 'acs' ),
			    'add_new_item'			=> __( 'Add New Field Group' , 'acs' ),
			    'edit_item'				=> __( 'Edit Field Group' , 'acs' ),
			    'new_item'				=> __( 'New Field Group' , 'acs' ),
			    'view_item'				=> __( 'View Field Group', 'acs' ),
			    'search_items'			=> __( 'Search Field Groups', 'acs' ),
			    'not_found'				=> __( 'No Field Groups found', 'acs' ),
			    'not_found_in_trash'	=> __( 'No Field Groups found in Trash', 'acs' ),
			),
			'public'			=> false,
			'hierarchical'		=> true,
			'show_ui'			=> true,
			'show_in_menu'		=> false,
			'_builtin'			=> false,
			'capability_type'	=> 'post',
			'capabilities'		=> array(
				'edit_post'			=> $cap,
				'delete_post'		=> $cap,
				'edit_posts'		=> $cap,
				'delete_posts'		=> $cap,
			),
			'supports' 			=> array('title'),
			'rewrite'			=> false,
			'query_var'			=> false,
		));
		
		
		// Register the Field post type.
		register_post_type('acs-field', array(
			'labels'			=> array(
			    'name'					=> __( 'Fields', 'acs' ),
				'singular_name'			=> __( 'Field', 'acs' ),
			    'add_new'				=> __( 'Add New' , 'acs' ),
			    'add_new_item'			=> __( 'Add New Field' , 'acs' ),
			    'edit_item'				=> __( 'Edit Field' , 'acs' ),
			    'new_item'				=> __( 'New Field' , 'acs' ),
			    'view_item'				=> __( 'View Field', 'acs' ),
			    'search_items'			=> __( 'Search Fields', 'acs' ),
			    'not_found'				=> __( 'No Fields found', 'acs' ),
			    'not_found_in_trash'	=> __( 'No Fields found in Trash', 'acs' ),
			),
			'public'			=> false,
			'hierarchical'		=> true,
			'show_ui'			=> false,
			'show_in_menu'		=> false,
			'_builtin'			=> false,
			'capability_type'	=> 'post',
			'capabilities'		=> array(
				'edit_post'			=> $cap,
				'delete_post'		=> $cap,
				'edit_posts'		=> $cap,
				'delete_posts'		=> $cap,
			),
			'supports' 			=> array('title'),
			'rewrite'			=> false,
			'query_var'			=> false,
		));
	}
	
	/**
	 * register_post_status
	 *
	 * Registers the ACS post statuses.
	 *
	 * @date	22/10/2015
	 * @since	5.3.2
	 *
	 * @param	void
	 * @return	void
	 */
	function register_post_status() {
		
		// Register the Disabled post status.
		register_post_status('acs-disabled', array(
			'label'                     => _x( 'Disabled', 'post status', 'acs' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Disabled <span class="count">(%s)</span>', 'Disabled <span class="count">(%s)</span>', 'acs' ),
		));
	}
	
	/**
	 * posts_where
	 *
	 * Filters the $where clause allowing for custom WP_Query args.
	 *
	 * @date	31/8/19
	 * @since	5.8.1
	 *
	 * @param	string $where The WHERE clause.
	 * @return	WP_Query $wp_query The query object.
	 */
	function posts_where( $where, $wp_query ) {
		global $wpdb;
		
		// Add custom "acs_field_key" arg.
		if( $field_key = $wp_query->get('acs_field_key') ) {
			$where .= $wpdb->prepare(" AND {$wpdb->posts}.post_name = %s", $field_key );
	    }
	    
	    // Add custom "acs_field_name" arg.
	    if( $field_name = $wp_query->get('acs_field_name') ) {
			$where .= $wpdb->prepare(" AND {$wpdb->posts}.post_excerpt = %s", $field_name );
	    }
	    
	    // Add custom "acs_group_key" arg.
		if( $group_key = $wp_query->get('acs_group_key') ) {
			$where .= $wpdb->prepare(" AND {$wpdb->posts}.post_name = %s", $group_key );
	    }
	    
	    // Return.
	    return $where;
	}
	
	/**
	 * define
	 *
	 * Defines a constant if doesnt already exist.
	 *
	 * @date	3/5/17
	 * @since	5.5.13
	 *
	 * @param	string $name The constant name.
	 * @param	mixed $value The constant value.
	 * @return	void
	 */
	function define( $name, $value = true ) {
		if( !defined($name) ) {
			define( $name, $value );
		}
	}
	
	/**
	 * has_setting
	 *
	 * Returns true if a setting exists for this name.
	 *
	 * @date	2/2/18
	 * @since	5.6.5
	 *
	 * @param	string $name The setting name.
	 * @return	boolean
	 */
	function has_setting( $name ) {
		return isset($this->settings[ $name ]);
	}
	
	/**
	 * get_setting
	 *
	 * Returns a setting or null if doesn't exist.
	 *
	 * @date	28/09/13
	 * @since	5.0.0
	 *
	 * @param	string $name The setting name.
	 * @return	mixed
	 */
	function get_setting( $name ) {
		return isset($this->settings[ $name ]) ? $this->settings[ $name ] : null;
	}
	
	/**
	 * update_setting
	 *
	 * Updates a setting for the given name and value.
	 *
	 * @date	28/09/13
	 * @since	5.0.0
	 *
	 * @param	string $name The setting name.
	 * @param	mixed $value The setting value.
	 * @return	true
	 */
	function update_setting( $name, $value ) {
		$this->settings[ $name ] = $value;
		return true;
	}
	
	/**
	 * get_data
	 *
	 * Returns data or null if doesn't exist.
	 *
	 * @date	28/09/13
	 * @since	5.0.0
	 *
	 * @param	string $name The data name.
	 * @return	mixed
	 */
	function get_data( $name ) {
		return isset($this->data[ $name ]) ? $this->data[ $name ] : null;
	}
	
	/**
	 * set_data
	 *
	 * Sets data for the given name and value.
	 *
	 * @date	28/09/13
	 * @since	5.0.0
	 *
	 * @param	string $name The data name.
	 * @param	mixed $value The data value.
	 * @return	void
	 */
	function set_data( $name, $value ) {
		$this->data[ $name ] = $value;
	}
	
	/**
	 * get_instance
	 *
	 * Returns an instance or null if doesn't exist.
	 *
	 * @date	13/2/18
	 * @since	5.6.9
	 *
	 * @param	string $class The instance class name.
	 * @return	object
	 */
	function get_instance( $class ) {
		$name = strtolower($class);
		return isset($this->instances[ $name ]) ? $this->instances[ $name ] : null;
	}
	
	/**
	 * new_instance
	 *
	 * Creates and stores an instance of the given class.
	 *
	 * @date	13/2/18
	 * @since	5.6.9
	 *
	 * @param	string $class The instance class name.
	 * @return	object
	 */
	function new_instance( $class ) {
		$instance = new $class();
		$name = strtolower($class);
		$this->instances[ $name ] = $instance;
		return $instance;
	}
	
	/**
	 * Magic __isset method for backwards compatibility.
	 *
	 * @date	24/4/20
	 * @since	5.9.0
	 *
	 * @param	string $key Key name.
	 * @return	bool
	 */
	public function __isset( $key ) {
		return in_array( $key, array( 'locations', 'json' ) );
	}
	
	/**
	 * Magic __get method for backwards compatibility.
	 *
	 * @date	24/4/20
	 * @since	5.9.0
	 *
	 * @param	string $key Key name.
	 * @return	mixed
	 */
	public function __get( $key ) {
		switch ( $key ) {
			case 'locations':
				return acs_get_instance( 'ACS_Legacy_Locations' );
			case 'json':
				return acs_get_instance( 'ACS_Local_JSON' );
		}
		return null;
	}
}

/*
 * acs
 *
 * The main function responsible for returning the one true acs Instance to functions everywhere.
 * Use this function like you would a global variable, except without needing to declare the global.
 *
 * Example: <?php $acs = acs(); ?>
 *
 * @date	4/09/13
 * @since	4.3.0
 *
 * @param	void
 * @return	ACS
 */
function acs() {
	global $acs;
	
	// Instantiate only once.
	if( !isset($acs) ) {
		$acs = new ACS();
		$acs->initialize();
	}
	return $acs;
}

// Instantiate.
acs();

endif; // class_exists check
