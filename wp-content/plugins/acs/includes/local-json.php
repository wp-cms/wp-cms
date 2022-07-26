<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ACS_Local_JSON' ) ) {

	class ACS_Local_JSON {

		/**
		 * The found JSON field group files.
		 *
		 * @since 5.9.0
		 * @var array
		 */
		private $files = array();

		/**
		 * Constructor.
		 *
		 * @date    14/4/20
		 *
		 * @param void
		 *
		 * @return    void
		 * @since    5.9.0
		 *
		 */
		public function __construct() {

			// Update settings with default local JSON path(s).
			$default_json_paths = array_unique(
				array(
				get_stylesheet_directory() . '/acs-json',  // (Child) theme, primary save point by default
				get_template_directory() . '/acs-json',    // Parent theme, if present, in 2nd order
				)
			);
			acs_update_setting( 'load_json', $default_json_paths );
			acs_update_setting( 'save_json', $default_json_paths );

			// Add listeners.
			add_action( 'acs/update_field_group', array( $this, 'update_field_group' ) );
			add_action( 'acs/untrash_field_group', array( $this, 'update_field_group' ) );
			add_action( 'acs/trash_field_group', array( $this, 'delete_field_group' ) );
			add_action( 'acs/delete_field_group', array( $this, 'delete_field_group' ) );

			// Include fields.
			add_action( 'acs/include_fields', array( $this, 'include_fields' ) );
		}

		/**
		 * Returns true if this component is enabled.
		 *
		 * @date    14/4/20
		 *
		 * @param void
		 *
		 * @return    bool.
		 * @since    5.9.0
		 *
		 */
		public function is_enabled() {
			return (bool) acs_get_setting( 'json' );
		}

		/**
		 * Writes field group data to JSON file.
		 *
		 * @date    14/4/20
		 *
		 * @param array $field_group The field group.
		 *
		 * @return    void
		 * @since    5.9.0
		 *
		 */
		public function update_field_group( $field_group ) {

			// Bail early if disabled.
			if ( ! $this->is_enabled() ) {
				return false;
			}

			// Append fields.
			$field_group['fields'] = acs_get_fields( $field_group );

			// Save to file.
			$this->save_file( $field_group['key'], $field_group );
		}

		/**
		 * Deletes a field group JSON file.
		 *
		 * @date    14/4/20
		 *
		 * @param array $field_group The field group.
		 *
		 * @return    void
		 * @since    5.9.0
		 *
		 */
		public function delete_field_group( $field_group ) {

			// Bail early if disabled.
			if ( ! $this->is_enabled() ) {
				return false;
			}

			// WP appends '__trashed' to end of 'key' (post_name).
			$key = str_replace( '__trashed', '', $field_group['key'] );

			// Delete file.
			$this->delete_file( $key );
		}

		/**
		 * Includes all local JSON fields.
		 *
		 * @date    14/4/20
		 *
		 * @param void
		 *
		 * @return    void
		 * @since    5.9.0
		 *
		 */
		public function include_fields() {

			// Bail early if disabled.
			if ( ! $this->is_enabled() ) {
				return false;
			}

			// Get load paths.
			$files = $this->scan_field_groups();
			foreach ( $files as $key => $file ) {
				$json               = json_decode( file_get_contents( $file ), true );
				$json['local']      = 'json';
				$json['local_file'] = $file;
				acs_add_local_field_group( $json );
			}
		}

		/**
		 * Scans for JSON field groups.
		 *
		 * @date    14/4/20
		 *
		 * @param void
		 *
		 * @return    array
		 * @since    5.9.0
		 *
		 */
		function scan_field_groups() {
			$json_files = array();

			// Loop over "local_json" paths and parse JSON files.
			$paths = (array) acs_get_setting( 'load_json' );
			foreach ( $paths as $path ) {
				if ( is_dir( $path ) ) {
					$files = scandir( $path );
					if ( $files ) {
						foreach ( $files as $filename ) {

							// Ignore hidden files.
							if ( '.' === $filename[0] ) {
								continue;
							}

							// Ignore sub directories.
							$file = untrailingslashit( $path ) . '/' . $filename;
							if ( is_dir( $file ) ) {
								continue;
							}

							// Ignore non JSON files.
							$ext = pathinfo( $filename, PATHINFO_EXTENSION );
							if ( 'json' !== $ext ) {
								continue;
							}

							// Read JSON data.
							$json = json_decode( file_get_contents( $file ), true );
							if ( ! is_array( $json ) || ! isset( $json['key'] ) ) {
								continue;
							}

							// Append data.
							$json_files[ $json['key'] ] = $file;
						}
					}
				}
			}

			// Store data and return.
			$this->files = $json_files;

			return $json_files;
		}

		/**
		 * Returns an array of found JSON field group files.
		 *
		 * @date    14/4/20
		 *
		 * @param void
		 *
		 * @return    array
		 * @since    5.9.0
		 *
		 */
		public function get_files() {
			return $this->files;
		}

		/**
		 * Saves a field group JSON file.
		 *
		 * @date    17/4/20
		 *
		 * @param string $key The field group key.
		 * @param array $field_group The field group.
		 *
		 * @return    bool
		 * @since    5.9.0
		 *
		 */
		public function save_file( $key, $field_group ) {

			// Update save_json setting with potentially modified load_json values.
			acs_update_setting( 'save_json', acs_get_setting( 'load_json' ) );

			// Present save_json setting to plugins and functions, cast to array to retain legacy compability with single-string "save_json" return value.
			$paths = (array) acs_get_setting( 'save_json' );

			// By default, assume we have nowhere to save.
			$file = false;

			// Check if one of the paths already has the matching JSON file.
			foreach ( $paths as $check_path ) {
				$check_file = trailingslashit( $check_path ) . $key . '.json';
				if ( is_file( $check_file ) ) {
					$file = $check_file;
					break;
				}
			}

			// If no matching file location was found look for the first writable path.
			if ( ! $file ) {
				foreach ( $paths as $check_path ) {
					if ( is_writable( $check_path ) ) {
						$file = trailingslashit( $check_path ) . $key . '.json';
						break;
					}
				}
			}

			// No matching file and no writable path found: nowhere to save, return false.
			if ( ! $file ) {
				return false;
			}

			// Append modified time.
			if ( $field_group['ID'] ) {
				$field_group['modified'] = get_post_modified_time( 'U', true, $field_group['ID'] );
			} else {
				$field_group['modified'] = strtotime( 'now' );
			}

			// Prepare for export.
			$field_group = acs_prepare_field_group_for_export( $field_group );

			// Save and return true if bytes were written.
			$result = file_put_contents( $file, acs_json_encode( $field_group ) );

			return is_int( $result );
		}

		/**
		 * Deletes a field group JSON file.
		 *
		 * @date    17/4/20
		 *
		 * @param string $key The field group key.
		 *
		 * @return    bool True on success.
		 * @since    5.9.0
		 *
		 */
		public function delete_file( $key ) {

			// Update save_json setting with potentially modified load_json values.
			acs_update_setting( 'save_json', acs_get_setting( 'load_json' ) );

			// Present save_json setting to plugins and functions, cast to array to retain legacy compability with single-string "save_json" return value.
			$paths = (array) acs_get_setting( 'save_json' );

			// Default to first path in array, check other paths for existence of the matching JSON file..
			$path = $paths[0];
			foreach ( $paths as $check_path ) {
				$file = untrailingslashit( $check_path ) . '/' . $key . '.json';
				if ( is_file( $file ) ) {
					$path = $check_path;
				}
			}

			$file = untrailingslashit( $path ) . '/' . $key . '.json';
			if ( is_writable( $file ) ) {
				unlink( $file );

				return true;
			}

			return false;
		}

	}

// Initialize.
	acs_new_instance( 'ACS_Local_JSON' );

} // class_exists check

/**
 * Returns an array of found JSON field group files.
 *
 * @date	14/4/20
 * @since	5.9.0
 *
 * @param	type $var Description. Default.
 * @return	type Description.
 */
function acs_get_local_json_files() {
	return acs_get_instance( 'ACS_Local_JSON' )->get_files();
}

/**
 * Saves a field group JSON file.
 *
 * @date	5/12/2014
 * @since	5.1.5
 *
 * @param	array $field_group The field group.
 * @return	bool
 */
function acs_write_json_field_group( $field_group ) {
	return acs_get_instance( 'ACS_Local_JSON' )->save_file( $field_group['key'], $field_group );	
}

/**
 * Deletes a field group JSON file.
 *
 * @date	5/12/2014
 * @since	5.1.5
 *
 * @param	string $key The field group key.
 * @return	bool True on success.
 */
function acs_delete_json_field_group( $key ) {
	return acs_get_instance( 'ACS_Local_JSON' )->delete_file( $key );	
}
