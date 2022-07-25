<?php
/**
 * WP core upgrade functionality.
 */

/**
 * Stores files to be deleted.
 * @global array $_old_files
 * @var array
 * @name $_old_files
 */
global $_old_files;

$_old_files = array(
	// 'wp-includes/whatever-file.php',
	// 'wp-includes/whatever_folder',
);

/**
 * Stores new files in wp-content to copy
 *
 * The contents of this array indicate any new bundled plugins/themes which
 * should be installed with the WP Upgrade. These items will not be
 * re-installed in future upgrades, this behaviour is controlled by the
 * introduced version present here being older than the current installed version.
 *
 * The content of this array should follow the following format:
 * Filename (relative to wp-content) => Introduced version
 * Directories should be noted by suffixing it with a trailing slash (/)
 * @global array $_new_bundled_files
 * @var array
 * @name $_new_bundled_files
 */
global $_new_bundled_files;

$_new_bundled_files = array(
	// 'plugins/akismet/'        => '2.0',
	// 'themes/twentyseventeen/' => '4.7',
);

/**
 * Upgrades the Core.
 *
 * This will create a .maintenance file at the base of the WP directory
 * to ensure that people can not access the website, when the files are being
 * copied to their locations.
 *
 * The files in the `$_old_files` list will be removed and the new files
 * copied from the zip file after the database is upgraded.
 *
 * The files in the `$_new_bundled_files` list will be added to the installation
 * if the version is greater than or equal to the old version being upgraded.
 *
 * The steps for the upgrader for after the new release is downloaded and
 * unzipped is:
 *   1. Test unzipped location for select files to ensure that unzipped worked.
 *   2. Create the .maintenance file in current WP base.
 *   3. Copy new WP directory over old WP files.
 *   4. Upgrade WP to new version.
 *     4.1. Copy all files/folders other than wp-content
 *     4.2. Copy any language files to WP_LANG_DIR (which may differ from WP_CONTENT_DIR
 *     4.3. Copy any new bundled themes/plugins to their respective locations
 *   5. Delete new WP directory path.
 *   6. Delete .maintenance file.
 *   7. Remove old files.
 *   8. Delete 'update_core' option.
 *
 * There are several areas of failure. For instance if PHP times out before step
 * 6, then you will not be able to access any portion of your site. Also, since
 * the upgrade will not continue where it left off, you will not be able to
 * automatically remove old files and remove the 'update_core' option. This
 * isn't that bad.
 *
 * If the copy of the new WP over the old fails, then the worse is that
 * the new WP directory will remain.
 *
 * If it is assumed that every file will be copied over, including plugins and
 * themes, then if you edit the default theme, you should rename it, so that
 * your changes remain.
 *
 * @param string $from New release unzipped path.
 * @param string $to   Path to old ClassicPress installation.
 *
 * @return WP_Error|null WP_Error on failure, null on success.
 * @global WP_Filesystem_Base $wp_filesystem
 * @global array $_old_files
 * @global array $_new_bundled_files
 * @global wpdb $wpdb
 * @global string $wp_version
 * @global string $required_php_version
 * @global string $required_mysql_version
 *
 */
function update_core( string $from, string $to ) {
	global $wp_filesystem, $_old_files, $_new_bundled_files, $wpdb;

	set_time_limit( 300 );

	/**
	 * Filters feedback messages displayed during the core update process.
	 *
	 * The filter is first evaluated after the zip file for the latest version
	 * has been downloaded and unzipped. It is evaluated five more times during
	 * the process:
	 *
	 * 1. Before WP begins the core upgrade process.
	 * 2. Before Maintenance Mode is enabled.
	 * 3. Before WP begins copying over the necessary files.
	 * 4. Before Maintenance Mode is disabled.
	 * 5. Before the database is upgraded.
	 *
	 * @param string $feedback The core update feedback messages.
	 */
	apply_filters( 'update_feedback', __( 'Verifying the unpacked files&#8230;' ) );

	// Import $wp_version, $wp_version, $required_php_version, and
	// $required_mysql_version from the new version.
	//
	// NOTE: These variables are NOT modified in the global scope, and this
	// function is using all variables imported from `version-current.php` in
	// the local scope!  Do not declare any of these variables as global.
	$versions_file = trailingslashit( $wp_filesystem->wp_content_dir() ) . 'upgrade/version-current.php';

    if ( ! $wp_filesystem->copy( $from . '/wp-cms/wp-includes/version.php', $versions_file ) ) {
		$wp_filesystem->delete( $from, true );
		return new WP_Error( 'copy_failed_for_version_file', __( 'The update cannot be installed because we will be unable to copy some files. This is usually due to inconsistent file permissions.' ), 'wp-includes/version.php' );
	}

	$wp_filesystem->chmod( $versions_file, FS_CHMOD_FILE );
	require( WP_CONTENT_DIR . '/upgrade/version-current.php' );
	$wp_filesystem->delete( $versions_file );

	$php_version       = phpversion();
	$mysql_version     = $wpdb->db_version();
	$old_wp_version    = $GLOBALS['wp_version']; // The version of ClassicPress or WordPress we're updating from
	$development_build = ( str_contains( $old_wp_version . $wp_version, '-' ) ); // a dash in the version indicates a Development release
	$php_compat        = version_compare( $php_version, $required_php_version, '>=' );

    if ( file_exists( WP_CONTENT_DIR . '/db.php' ) && empty( $wpdb->is_mysql ) ) {
		$mysql_compat = true;
	} else {
		$mysql_compat = version_compare( $mysql_version, $required_mysql_version, '>=' );
	}

	if ( ! $mysql_compat || ! $php_compat ) {
		$wp_filesystem->delete( $from, true );
	}

	if ( ! $mysql_compat && ! $php_compat ) {
		/* translators: Versions */
		return new WP_Error( 'php_mysql_not_compatible', sprintf( __( 'The update cannot be installed because ClassicPress %1$s requires PHP version %2$s or higher and MySQL version %3$s or higher. You are running PHP version %4$s and MySQL version %5$s.' ), $wp_version, $required_php_version, $required_mysql_version, $php_version, $mysql_version ) );
	} elseif ( ! $php_compat ) {
		/* translators: Versions */
		return new WP_Error( 'php_not_compatible', sprintf( __( 'The update cannot be installed because ClassicPress %1$s requires PHP version %2$s or higher. You are running version %3$s.' ), $wp_version, $required_php_version, $php_version ) );
	} elseif ( ! $mysql_compat ) {
		/* translators: Versions */
		return new WP_Error( 'mysql_not_compatible', sprintf( __( 'The update cannot be installed because ClassicPress %1$s requires MySQL version %2$s or higher. You are running version %3$s.' ), $wp_version, $required_mysql_version, $mysql_version ) );
	}

	/** This filter is documented in wp-admin/includes/update-core.php */
	apply_filters( 'update_feedback', __( 'Preparing to install the latest version&#8230;' ) );

	// Don't copy wp-content, we'll deal with that below
	// We also copy version.php last so failed updates report their old version
	$skip              = array( 'wp-content', 'wp-includes/version.php' );
	$check_is_writable = array();

	// If we're using the direct method, we can predict write failures that are due to permissions.
	if ( $check_is_writable && 'direct' === $wp_filesystem->method ) {
		$files_writable = array_filter( $check_is_writable, array( $wp_filesystem, 'is_writable' ) );
		if ( $files_writable !== $check_is_writable ) {
			$files_not_writable = array_diff_key( $check_is_writable, $files_writable );
			foreach ( $files_not_writable as $relative_file_not_writable => $file_not_writable ) {
				// If the writable check failed, chmod file to 0644 and try again, same as copy_dir().
				$wp_filesystem->chmod( $file_not_writable, FS_CHMOD_FILE );
				if ( $wp_filesystem->is_writable( $file_not_writable ) ) {
					unset( $files_not_writable[ $relative_file_not_writable ] );
				}
			}

			// Store package-relative paths (the key) of non-writable files in the WP_Error object.
			$error_data = array_keys( $files_not_writable );

			if ( $files_not_writable ) {
				return new WP_Error( 'files_not_writable', __( 'The update cannot be installed because we will be unable to copy some files. This is usually due to inconsistent file permissions.' ), implode( ', ', $error_data ) );
			}
		}
	}

	/** This filter is documented in wp-admin/includes/update-core.php */
	apply_filters( 'update_feedback', __( 'Enabling Maintenance mode&#8230;' ) );

    // Create maintenance file to signal that we are upgrading
	$maintenance_string = '<?php $upgrading = ' . time() . '; ?>';
	$maintenance_file   = $to . '.maintenance';

    $wp_filesystem->delete( $maintenance_file );
	$wp_filesystem->put_contents( $maintenance_file, $maintenance_string, FS_CHMOD_FILE );

	/** This filter is documented in wp-admin/includes/update-core.php */
	apply_filters( 'update_feedback', __( 'Copying the required files&#8230;' ) );

    // Copy new versions of WP files into place.
	$result = _copy_dir( $from . '/wp-cms/', $to, $skip );
	if ( is_wp_error( $result ) ) {
		$result = new WP_Error( $result->get_error_code(), $result->get_error_message(), substr( $result->get_error_data(), strlen( $to ) ) );
	}

	// Since we know the core files have copied over, we can now copy the version file
	if ( ! is_wp_error( $result ) ) {
		if ( ! $wp_filesystem->copy( $from . '/wp-cms/wp-includes/version.php', $to . 'wp-includes/version.php', true /* overwrite */ ) ) {
			$wp_filesystem->delete( $from, true );
			$result = new WP_Error( 'copy_failed_for_version_file', __( 'The update cannot be installed because we will be unable to copy some files. This is usually due to inconsistent file permissions.' ), 'wp-includes/version.php' );
		}
		$wp_filesystem->chmod( $to . 'wp-includes/version.php', FS_CHMOD_FILE );
	}

	// Custom Content Directory needs updating now.
	// Copy Languages
	if ( ! is_wp_error( $result ) && $wp_filesystem->is_dir( $from . '/wp-cms/wp-content/languages' ) ) {

        if ( WP_LANG_DIR !== ABSPATH . WPINC . '/languages' || is_dir( WP_LANG_DIR ) ) {
			$lang_dir = WP_LANG_DIR;
		} else {
			$lang_dir = WP_CONTENT_DIR . '/languages';
		}

		if ( ! is_dir( $lang_dir ) && str_starts_with( $lang_dir, ABSPATH ) ) { // Check the language directory exists first
			$wp_filesystem->mkdir( $to . str_replace( ABSPATH, '', $lang_dir ), FS_CHMOD_DIR ); // If it's within the ABSPATH we can handle it here, otherwise they're out of luck.
			clearstatcache(); // for FTP, Need to clear the stat cache
		}

		if ( is_dir( $lang_dir ) ) {
			$wp_lang_dir = $wp_filesystem->find_folder( $lang_dir );
			if ( $wp_lang_dir ) {
				$result = copy_dir( $from . '/wp-cms/wp-content/languages/', $wp_lang_dir );
				if ( is_wp_error( $result ) ) {
					$result = new WP_Error( $result->get_error_code() . '_languages', $result->get_error_message(), substr( $result->get_error_data(), strlen( $wp_lang_dir ) ) );
				}
			}
		}
	}

	/** This filter is documented in wp-admin/includes/update-core.php */
	apply_filters( 'update_feedback', __( 'Disabling Maintenance mode&#8230;' ) );
	// Remove maintenance file, we're done with potential site-breaking changes
	$wp_filesystem->delete( $maintenance_file );

	// Copy New bundled plugins & themes
	// This gives us the ability to install new plugins & themes bundled with future versions of ClassicPress whilst avoiding the re-install upon upgrade issue.
	// $development_build controls us overwriting bundled themes and plugins when a non-stable release is being updated
	if ( ! is_wp_error( $result ) && ( ! defined( 'CORE_UPGRADE_SKIP_NEW_BUNDLED' ) || ! CORE_UPGRADE_SKIP_NEW_BUNDLED ) ) {
		foreach ( $_new_bundled_files as $file => $introduced_version ) {
			// If $introduced version is greater than what the site was previously running
			if ( version_compare( $introduced_version, $old_wp_version, '>' ) ) {

                $directory               = ( '/' === $file[ strlen( $file ) - 1 ] );
				list( $type, $filename ) = explode( '/', $file, 2 );

				// Check to see if the bundled items exist before attempting to copy them
				if ( ! $wp_filesystem->exists( $from . '/wp-cms/wp-content/' . $file ) ) {
					continue;
				}

				if ( 'plugins' === $type ) {
					$dest = $wp_filesystem->wp_plugins_dir();
				} elseif ( 'themes' === $type ) {
					$dest = $wp_filesystem->wp_themes_dir();
				} else {
					continue;
				}

				if ( ! $directory ) {

                    if ( ! $development_build && $wp_filesystem->exists( $dest . $filename ) ) {
						continue;
					}

					if ( ! $wp_filesystem->copy( $from . '/wp-cms/wp-content/' . $file, $dest . $filename, FS_CHMOD_FILE ) ) {
						$result = new WP_Error( "copy_failed_for_new_bundled_$type", __( 'Could not copy file.' ), $dest . $filename );
					}
				} else {

					if ( $wp_filesystem->is_dir( $dest . $filename ) ) {
						continue;
					}

					$wp_filesystem->mkdir( $dest . $filename, FS_CHMOD_DIR );
					$_result = copy_dir( $from . '/wp-cms/wp-content/' . $file, $dest . $filename );

					// If an error occurs partway through this final step, keep the error flowing through, but keep process going.
					if ( is_wp_error( $_result ) ) {
						if ( ! is_wp_error( $result ) ) {
							$result = new WP_Error;
						}
						$result->add( $_result->get_error_code() . "_$type", $_result->get_error_message(), substr( $_result->get_error_data(), strlen( $dest ) ) );
					}
				}
			}
		} //end foreach
	}

    // Update bundled Amazing Custom Plugin and Theme (only if the plugin hasn't been deleted by the user before)
    $acs_destination_folder = $wp_filesystem->wp_plugins_dir() . 'acs';
	if ( $wp_filesystem->exists( $acs_destination_folder ) ) {
		$wp_filesystem->delete( $acs_destination_folder, true );
		$wp_filesystem->mkdir( $acs_destination_folder, FS_CHMOD_DIR );
		$_result = copy_dir( $from . '/wp-cms/wp-content/plugins/acs', $acs_destination_folder );

		// If an error occurs partway through this step, keep the error flowing through, but keep process going.
		if ( is_wp_error( $_result ) ) {
			if ( ! is_wp_error( $result ) ) {
				$result = new WP_Error;
			}
			$result->add( $_result->get_error_code() . '_plugins', $_result->get_error_message(), substr( $_result->get_error_data(), strlen( $wp_filesystem->wp_plugins_dir() ) ) );
		}

        // Update bundled Amazing Custom Theme
        $act_destination_folder = $wp_filesystem->wp_themes_dir() . 'act';
		$wp_filesystem->delete( $act_destination_folder, true );
		$wp_filesystem->mkdir( $act_destination_folder, FS_CHMOD_DIR );
		$_result = copy_dir( $from . '/wp-cms/wp-content/themes/act', $act_destination_folder );

		// If an error occurs partway through this step, keep the error flowing through, but keep process going.
		if ( is_wp_error( $_result ) ) {
			if ( ! is_wp_error( $result ) ) {
				$result = new WP_Error;
			}
			$result->add( $_result->get_error_code() . '_themes', $_result->get_error_message(), substr( $_result->get_error_data(), strlen( $wp_filesystem->wp_themes_dir() ) ) );
		}
	}

	// Handle $result error from the above blocks
	if ( is_wp_error( $result ) ) {
		$wp_filesystem->delete( $from, true );
		return $result;
	}

	// Remove old files
	foreach ( $_old_files as $old_file ) {
		$old_file = $to . $old_file;

        if ( ! $wp_filesystem->exists( $old_file ) ) {
			continue;
		}

		// If the file isn't deleted, try writing an empty string to the file instead.
		if ( ! $wp_filesystem->delete( $old_file, true ) && $wp_filesystem->is_file( $old_file ) ) {
			$wp_filesystem->put_contents( $old_file, '' );
		}
	}

	// Upgrade DB with separate request
	/** This filter is documented in wp-admin/includes/update-core.php */
	apply_filters( 'update_feedback', __( 'Upgrading database&#8230;' ) );
	$db_upgrade_url = admin_url( 'upgrade.php?step=upgrade_db' );
	wp_remote_post( $db_upgrade_url, array( 'timeout' => 60 ) );

	// Clear the cache to prevent an update_option() from saving a stale db_version to the cache
	wp_cache_flush();

	// (Not all cache back ends listen to 'flush')
	wp_cache_delete( 'alloptions', 'options' );

	// Remove working directory
	$wp_filesystem->delete( $from, true );

	// Force refresh of update information
	delete_site_transient( 'update_core' );

	/**
	 * Fires after WP core has been successfully updated.
	 *
	 * @param string $wp_version The current equivalent WordPress version, for compatibility.
	 */
	do_action( '_core_updated_successfully', $wp_version );

	return $wp_version;
}

/**
 * Copies a directory from one location to another via the WP Filesystem Abstraction.
 * Assumes that WP_Filesystem() has already been called and setup.
 *
 * @see copy_dir()
 *
 * @global WP_Filesystem_Base $wp_filesystem
 *
 * @param string $from     source directory
 * @param string $to       destination directory
 * @param array $skip_list a list of files/folders to skip copying
 * @return mixed WP_Error on failure, True on success.
 */
function _copy_dir( $from, $to, $skip_list = array() ) {
	global $wp_filesystem;

	$dirlist = $wp_filesystem->dirlist( $from );
	$from    = trailingslashit( $from );
	$to      = trailingslashit( $to );

	foreach ( (array) $dirlist as $filename => $fileinfo ) {
		if ( in_array( $filename, $skip_list ) ) {
			continue;
		}

		if ( 'f' === $fileinfo['type'] ) {
			if ( ! $wp_filesystem->copy( $from . $filename, $to . $filename, true, FS_CHMOD_FILE ) ) {
				// If copy failed, chmod file to 0644 and try again.
				$wp_filesystem->chmod( $to . $filename, FS_CHMOD_FILE );
				if ( ! $wp_filesystem->copy( $from . $filename, $to . $filename, true, FS_CHMOD_FILE ) ) {
					return new WP_Error( 'copy_failed__copy_dir', __( 'Could not copy file.' ), $to . $filename );
				}
			}
		} elseif ( 'd' === $fileinfo['type'] ) {
			if ( ! $wp_filesystem->is_dir( $to . $filename ) ) {
				if ( ! $wp_filesystem->mkdir( $to . $filename, FS_CHMOD_DIR ) ) {
					return new WP_Error( 'mkdir_failed__copy_dir', __( 'Could not create directory.' ), $to . $filename );
				}
			}

			/*
			 * Generate the $sub_skip_list for the subdirectory as a sub-set
			 * of the existing $skip_list.
			 */
			$sub_skip_list = array();
			foreach ( $skip_list as $skip_item ) {
				if ( str_starts_with( $skip_item, $filename . '/' ) ) {
					$sub_skip_list[] = preg_replace( '!^' . preg_quote( $filename, '!' ) . '/!i', '', $skip_item );
				}
			}

			$result = _copy_dir( $from . $filename, $to . $filename, $sub_skip_list );
			if ( is_wp_error( $result ) ) {
				return $result;
			}
		}
	}
	return true;
}

/**
 * Redirect to the About WP page after a successful upgrade.
 *
 * @global string $wp_version
 * @global string $pagenow
 * @global string $action
 *
 * @param string $new_version
 */
function _redirect_to_about_wordpress( $new_version ) {
	global $wp_version, $pagenow, $action;

	// Ensure we only run this on the update-core.php page. The Core_Upgrader may be used in other contexts.
	if ( 'update-core.php' !== $pagenow ) {
		return;
	}

 	if ( 'do-core-upgrade' !== $action ) {
	    return;
    }

	// Load the updated default text localization domain for new strings.
	load_default_textdomain();

	// See do_core_upgrade()
	show_message( __( 'WP updated successfully' ) );

	// self_admin_url() won't exist when upgrading from <= 3.0, so relative URLs are intentional.
	/* translators: 1: Version */
	show_message( '<span class="hide-if-no-js">' . sprintf( __( 'Welcome to WP %1$s. You will be redirected to the About screen. If not, click <a href="%2$s">here</a>.' ), $new_version, 'about.php?updated' ) . '</span>' );
	/* translators: 1: Version */
    show_message( '<span class="hide-if-js">' . sprintf( __( 'Welcome to WP %1$s. <a href="%2$s">Learn more</a>.' ), $new_version, 'about.php?updated' ) . '</span>' );
	echo '</div>';
	?>
    <script type="text/javascript">
    window.location = 'about.php?updated';
    </script>
	<?php
	// Include admin-footer.php and exit.
	include( ABSPATH . 'wp-admin/admin-footer.php' );
	exit();
}
