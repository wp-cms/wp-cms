<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('ACS_Assets') ) :

class ACS_Assets {
	
	/**
	 * Storage for i18n data.
	 *
	 * @since 5.6.9
	 * @var array
	 */
	public $text = array();
	
	/**
	 * Storage for l10n data.
	 *
	 * @since 5.6.9
	 * @var array
	 */
	public $data = array();

	/**
	 * List of enqueue flags.
	 *
	 * @since 5.9.0
	 * @var bool
	 */
	private $enqueue = array();

	/**
	 * Constructor.
	 *
	 * @date	10/4/18
	 * @since	5.6.9
	 *
	 * @param	void
	 * @return	void
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_scripts' ) );
	}

	/**
	 * Magic __call method for backwards compatibility.
	 *
	 * @date	10/4/20
	 * @since	5.9.0
	 *
	 * @param	string $name The method name.
	 * @param	array $arguments The array of arguments.
	 * @return	mixed
	 */
	public function __call( $name, $arguments ) {
		switch ( $name ) {
			case 'admin_enqueue_scripts':
			case 'admin_print_scripts':
			case 'admin_head':
			case 'admin_footer':
			case 'admin_print_footer_scripts':
				_doing_it_wrong( __FUNCTION__, 'The ACS_Assets class should not be accessed directly.', '5.9.0' );
		}
    }
	
	/**
	 * Appends an array of i18n data.
	 *
	 * @date	13/4/18
	 * @since	5.6.9
	 *
	 * @param	array $text An array of text for i18n.
	 * @return	void
	 */
	public function add_text( $text ) {
		foreach( (array) $text as $k => $v ) {
			$this->text[ $k ] = $v;
		}
	}
	
	/**
	 * Appends an array of l10n data.
	 *
	 * @date	13/4/18
	 * @since	5.6.9
	 *
	 * @param	array $data An array of data for l10n.
	 * @return	void
	 */
	public function add_data( $data ) {
		foreach( (array) $data as $k => $v ) {
			$this->data[ $k ] = $v;
		}
	}
	
	/**
	 * Registers the ACS scripts and styles.
	 *
	 * @date	10/4/18
	 * @since	5.6.9
	 *
	 * @param	void
	 * @return	void
	 */
	public function register_scripts() {
		
		// Extract vars.
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		$version = acs_get_setting('version');
		
		// Register scripts.
		wp_register_script( 'acs', acs_get_url( 'assets/js/acs' . $suffix . '.js' ), array( 'jquery' ), $version );
		wp_register_script( 'acs-input', acs_get_url( 'assets/js/acs-input' . $suffix . '.js' ), array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-resizable', 'acs' ), $version );
		wp_register_script( 'acs-field-group', acs_get_url( 'assets/js/acs-field-group' . $suffix . '.js' ), array( 'acs-input' ), $version );
		
		// Register styles.
		wp_register_style( 'acs-global', acs_get_url( 'assets/css/acs-global.css' ), array(), $version );
		wp_register_style( 'acs-input', acs_get_url( 'assets/css/acs-input.css' ), array('acs-global'), $version );
		wp_register_style( 'acs-field-group', acs_get_url( 'assets/css/acs-field-group.css' ), array('acs-input'), $version );
		
		/**
		 * Fires after core scripts and styles have been registered.
		 *
		 * @since	5.6.9
		 *
		 * @param	string $version The ACS version.
		 * @param	string $suffix The potential ".min" filename suffix.
		 */
		do_action( 'acs/register_scripts', $version, $suffix );
	}

	/**
	 * Enqueues a script and sets up actions for priting supplemental scripts.
	 *
	 * @date	27/4/20
	 * @since	5.9.0
	 *
	 * @param	string $name The script name.
	 * @return	void
	 */
	public function enqueue_script( $name ) {
		wp_enqueue_script( $name );
		$this->add_actions();
	}

	/**
	 * Enqueues a style.
	 *
	 * @date	27/4/20
	 * @since	5.9.0
	 *
	 * @param	string $name The style name.
	 * @return	void
	 */
	public function enqueue_style( $name ) {
		wp_enqueue_style( $name );
	}
	
	/**
	 * Adds the actions needed to print supporting inline scripts.
	 *
	 * @date	27/4/20
	 * @since	5.9.0
	 *
	 * @param	void
	 * @return	void
	 */
	private function add_actions() {
		
		// Only run once.
		if( acs_has_done('ACS_Assets::add_actions') ) {
			return;
		}
		
		// Add actions.
		$this->add_action( 'admin_enqueue_scripts', 'enqueue_scripts' , 20 );
		$this->add_action( 'admin_print_scripts', 'print_scripts', 20 );
		$this->add_action( 'admin_print_footer_scripts', 'print_footer_scripts', 20 );
	}
	
	/**
	 * Extends the add_action() function with two additional features:
	 * 1. Renames $action depending on the current page (customizer, login, front-end).
	 * 2. Alters the priotiry or calls the method directly if the action has already passed.
	 *
	 * @date	28/4/20
	 * @since	5.9.0
	 *
	 * @param	string $action The action name.
	 * @param	string $method The method name.
	 * @param	int $priority See add_action().
	 * @param	int $accepted_args See add_action().
	 * @return	void
	 */
	public function add_action( $action, $method, $priority = 10, $accepted_args = 1 ) {
		
		// Generate an array of action replacements.
		$replacements = array(
			'customizer' => array(
				'admin_enqueue_scripts' 		=> 'admin_enqueue_scripts',
				'admin_print_scripts' 			=> 'customize_controls_print_scripts',
				'admin_head' 					=> 'customize_controls_print_scripts',
				'admin_footer'					=> 'customize_controls_print_footer_scripts',
				'admin_print_footer_scripts'	=> 'customize_controls_print_footer_scripts'
			),
			'login' => array(
				'admin_enqueue_scripts' 		=> 'login_enqueue_scripts',
				'admin_print_scripts' 			=> 'login_head',
				'admin_head' 					=> 'login_head',
				'admin_footer'					=> 'login_footer',
				'admin_print_footer_scripts'	=> 'login_footer'
			),
			'wp' => array(
				'admin_enqueue_scripts' 		=> 'wp_enqueue_scripts',
				'admin_print_scripts' 			=> 'wp_print_scripts',
				'admin_head' 					=> 'wp_head',
				'admin_footer'					=> 'wp_footer',
				'admin_print_footer_scripts'	=> 'wp_print_footer_scripts'
			)
		);
		
		// Determine the current context.
		if( did_action('customize_controls_init') ) {
			$context = 'customizer';
		} elseif( did_action('login_form_register') ) { 
			$context = 'login';
		} elseif( is_admin() ) {
			$context = 'admin';
		} else {
			$context = 'wp';
		}
		
		// Replace action if possible.
		if( isset( $replacements[ $context ][ $action ] ) ) {
			$action = $replacements[ $context ][ $action ];
		}
		
		// Check if action is currently being or has already been run.
		if( did_action($action) ) {
			$doing = acs_doing_action( $action );
			if( $doing && $doing < $priority ) {
				// Allow action to be added as per usual.
			} else {
				// Call method directly.
				return call_user_func( array( $this, $method ) );
			}
		}
		
		// Add action.
		add_action( $action, array( $this, $method ), $priority, $accepted_args );
	}
	
	/**
	 * Generic controller for enqueuing scripts and styles.
	 *
	 * @date	28/4/20
	 * @since	5.9.0
	 *
	 * @param	array $args {
	 * 		@type bool $uploader Whether or not to enqueue uploader scripts.
	 * }
	 * @return	void
	 */
	public function enqueue( $args = array() ) {

		// Apply defaults.
		$args = wp_parse_args($args, array(
			'input'		=> true,
			'uploader'	=> false
		));

		// Set enqueue flags and add actions.
		if( $args['input'] ) {
			$this->enqueue[] = 'input';
		}
		if( $args['uploader'] ) {
			$this->enqueue[] = 'uploader';
		}
		$this->add_actions();
	}

	/**
	 * Enqueues the scripts and styles needed for the WP media uploader.
	 *
	 * @date	27/10/2014
	 * @since	5.0.9
	 *
	 * @param	void
	 * @return	void
	 */
	public function enqueue_uploader() {
		
		// Only run once.
		if( acs_has_done('ACS_Assets::enqueue_uploader') ) {
			return;
		}
		
		// Enqueue media assets.
		if( current_user_can('upload_files') ) {
			wp_enqueue_media();
		}

		// Add actions.
		$this->add_action( 'admin_footer', 'print_uploader_scripts', 1 );

		/**
		 * Fires when enqueuing the uploader.
		 *
		 * @since	5.6.9
		 *
		 * @param	void
		 */
		do_action( 'acs/enqueue_uploader' );
	}
	
	/**
	 * Enqueues and localizes scripts.
	 *
	 * @date	27/4/20
	 * @since	5.9.0
	 *
	 * @param	void
	 * @return	void
	 */
	public function enqueue_scripts() {
		
		// Enqueue input scripts.
		if( in_array('input', $this->enqueue) ) {
			wp_enqueue_script( 'acs-input' );
			wp_enqueue_style( 'acs-input' );
		}

		// Enqueue media scripts.
		if( in_array('uploader', $this->enqueue) ) {
			$this->enqueue_uploader();
		}

		// Localize text.
		acs_localize_text(array(
			
			// Tooltip
			'Are you sure?'			=> __('Are you sure?','acs'),
			'Yes'					=> __('Yes','acs'),
			'No'					=> __('No','acs'),
			'Remove'				=> __('Remove','acs'),
			'Cancel'				=> __('Cancel','acs'),
		));
		
		// Localize "input" text.
		if( wp_script_is('acs-input') ) {
			acs_localize_text(array(
				
				// Unload
				'The changes you made will be lost if you navigate away from this page'	=> __('The changes you made will be lost if you navigate away from this page', 'acs'),
				
				// Validation
				'Validation successful'			=> __('Validation successful', 'acs'),
				'Validation failed'				=> __('Validation failed', 'acs'),
				'1 field requires attention'	=> __('1 field requires attention', 'acs'),
				'%d fields require attention'	=> __('%d fields require attention', 'acs'),
				
				// Other
				'Edit field group'	=> __('Edit field group', 'acs'),
			));
			
			/**
			 * Fires during "admin_enqueue_scripts" when ACS scripts are enqueued.
			 *
			 * @since	5.6.9
			 *
			 * @param	void
			 */
			do_action( 'acs/input/admin_enqueue_scripts' );
		}
		
		/**
		 * Fires during "admin_enqueue_scripts" when ACS scripts are enqueued.
		 *
		 * @since	5.6.9
		 *
		 * @param	void
		 */
		do_action( 'acs/admin_enqueue_scripts' );
		do_action( 'acs/enqueue_scripts' );

		// Filter i18n translations that differ from English and localize script.
		$text = array();
		foreach( $this->text as $k => $v ) {
			if( str_replace('.verb', '', $k) !== $v ) {
				$text[ $k ] = $v;
			}
		}
		if( $text ) {
			wp_localize_script( 'acs', 'acsL10n', $text );
		}
	}
	
	/**
	 * Prints scripts in head.
	 *
	 * @date	27/4/20
	 * @since	5.9.0
	 *
	 * @param	void
	 * @return	void
	 */
	public function print_scripts() {
		if( wp_script_is('acs-input') ) {
			
			/**
			 * Fires during "admin_head" when ACS scripts are enqueued.
			 *
			 * @since	5.6.9
			 *
			 * @param	void
			 */
			do_action( 'acs/input/admin_head' );
			do_action( 'acs/input/admin_print_scripts' );
		}

		/**
		 * Fires during "admin_head" when ACS scripts are enqueued.
		 *
		 * @since	5.6.9
		 *
		 * @param	void
		 */
		do_action( 'acs/admin_head' );
		do_action( 'acs/admin_print_scripts' );
	}
	
	/**
	 * Prints scripts in footer.
	 *
	 * @date	27/4/20
	 * @since	5.9.0
	 *
	 * @param	void
	 * @return	void
	 */
	public function print_footer_scripts() {
		global $wp_version;
		
		// Bail early if 'acs' script was never enqueued (fixes Elementor enqueue reset conflict).
		if( !wp_script_is('acs') ) {
			return;
		}
		
		// Localize data.
		acs_localize_data(array(
			'admin_url'		=> admin_url(),
			'ajaxurl'		=> admin_url( 'admin-ajax.php' ),
			'nonce'			=> wp_create_nonce( 'acs_nonce' ),
			'acs_version'	=> acs_get_setting('version'),
			'wp_version'	=> $wp_version,
			'browser'		=> acs_get_browser(),
			'locale'		=> acs_get_locale(),
			'rtl'			=> is_rtl(),
			'screen'		=> acs_get_form_data('screen'),
			'post_id'		=> acs_get_form_data('post_id'),
			'validation'	=> acs_get_form_data('validation'),
			'editor'		=> acs_is_block_editor() ? 'block' : 'classic'
		));
		
		// Print inline script.
		printf( "<script>\n%s\n</script>\n", 'acs.data = ' . wp_json_encode( $this->data ) . ';' );
		
		if( wp_script_is('acs-input') ) {
			
			/**
			 * Filters an empty array for compat l10n data.
			 *
			 * @since	5.0.0
			 *
			 * @param	array $data An array of data to append to.
			 */
			$compat_l10n = apply_filters( 'acs/input/admin_l10n', array() );
			if( $compat_l10n ) {
				printf( "<script>\n%s\n</script>\n", 'acs.l10n = ' . wp_json_encode( $compat_l10n ) . ';' );
			}
			
			/**
			 * Fires during "admin_footer" when ACS scripts are enqueued.
			 *
			 * @since	5.6.9
			 *
			 * @param	void
			 */
			do_action( 'acs/input/admin_footer' );
			do_action( 'acs/input/admin_print_footer_scripts' );
		}
		
		/**
		 * Fires during "admin_footer" when ACS scripts are enqueued.
		 *
		 * @since	5.6.9
		 *
		 * @param	void
		 */
		do_action( 'acs/admin_footer' );
		do_action( 'acs/admin_print_footer_scripts' );
		
		// Once all data is localized, trigger acs.prepare() to execute functionality before DOM ready.
		printf( "<script>\n%s\n</script>\n", "acs.doAction( 'prepare' );" );
	}
	
	/**
	 * Prints uploader scripts in footer.
	 *
	 * @date	11/06/2020
 	 * @since	5.9.0
	 *
	 * @param	void
	 * @return	void
	 */
	public function print_uploader_scripts() {
		// Todo: investigate output-buffer to hide HTML.
		?>
		<div id="acs-hidden-wp-editor" style="display: none;">
			<?php wp_editor( '', 'acs_content' ); ?>
		</div>
		<?php

		/**
		 * Fires when printing uploader scripts.
		 *
		 * @since	5.6.9
		 *
		 * @param	void
		 */
		do_action( 'acs/admin_print_uploader_scripts' );
	}
}

// instantiate
acs_new_instance('ACS_Assets');

endif; // class_exists check

/**
 * Appends an array of i18n data for localization.
 *
 * @date	13/4/18
 * @since	5.6.9
 *
 * @param	array $text An array of text for i18n.
 * @return	void
 */
function acs_localize_text( $text ) {
	return acs_get_instance('ACS_Assets')->add_text( $text );
}

/**
 * Appends an array of l10n data for localization.
 *
 * @date	13/4/18
 * @since	5.6.9
 *
 * @param	array $data An array of data for l10n.
 * @return	void
 */
function acs_localize_data( $data ) {
	return acs_get_instance('ACS_Assets')->add_data( $data );
}

/**
 * Enqueues a script with support for supplemental inline scripts.
 *
 * @date	27/4/20
 * @since	5.9.0
 *
 * @param	string $name The script name.
 * @return	void
 */
function acs_enqueue_script( $name ) {
	return acs_get_instance('ACS_Assets')->enqueue_script( $name );
}

/**
 * Enqueues the input scripts required for fields.
 *
 * @date	13/4/18
 * @since	5.6.9
 *
 * @param	array $args See ACS_Assets::enqueue_scripts() for a list of args.
 * @return	void
 */
function acs_enqueue_scripts( $args = array() ) {
	return acs_get_instance('ACS_Assets')->enqueue( $args );
}

/**
 * Enqueues the WP media uploader scripts and styles.
 *
 * @date	27/10/2014
 * @since	5.0.9
 *
 * @param	void
 * @return	void
 */
function acs_enqueue_uploader() {
	return acs_get_instance('ACS_Assets')->enqueue_uploader();
}
