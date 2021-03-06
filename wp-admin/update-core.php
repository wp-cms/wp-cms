<?php
/**
 * Update Core administration panel.
 */

/** WP Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

wp_enqueue_style( 'plugin-install' );
wp_enqueue_script( 'plugin-install' );
wp_enqueue_script( 'updates' );
add_thickbox();

if ( is_multisite() && ! is_network_admin() ) {
	wp_redirect( network_admin_url( 'update-core.php' ) );
	exit();
}

if ( ! current_user_can( 'update_core' ) && ! current_user_can( 'update_themes' ) && ! current_user_can( 'update_plugins' ) && ! current_user_can( 'update_languages' ) ) {
	wp_die( __( 'Sorry, you are not allowed to update this site.' ) );
}

/**
 *
 * @global string $wp_local_package
 * @global wpdb   $wpdb
 *
 * @staticvar bool $first_pass
 *
 * @param object $update
 */
function list_core_update( $update ) {
 	global $wpdb;

    $current_wp_version = get_bloginfo( 'version' );

    // Determine if we are already on the latest available version or not
	if ( $update->version === $current_wp_version ) {
        return;
	}

    $form_action   = 'update-core.php?action=do-core-upgrade';
    $php_version   = phpversion();
    $mysql_version = $wpdb->db_version();
    $show_buttons  = true;

    $php_compat = version_compare( $php_version, $update->php_version, '>=' );

    if ( file_exists( WP_CONTENT_DIR . '/db.php' ) && empty( $wpdb->is_mysql ) ) {
        $mysql_compat = true;
    } else {
        $mysql_compat = version_compare( $mysql_version, $update->mysql_version, '>=' );
    }

    if ( ! $mysql_compat && ! $php_compat ) {
        /* translators: 1: WP version number, 2: Minimum required PHP version number, 3: Minimum required MySQL version number, 4: Current PHP version number, 5: Current MySQL version number */
        $message = sprintf( __( 'You cannot update because WP %1$s requires PHP version %2$s or higher and MySQL version %3$s or higher. You are running PHP version %4$s and MySQL version %5$s.' ), $update->current, $update->php_version, $update->mysql_version, $php_version, $mysql_version );
    } elseif ( ! $php_compat ) {
        /* translators: 1: WP version number, 2: Minimum required PHP version number, 3: Current PHP version number */
        $message = sprintf( __( 'You cannot update because WP %1$s requires PHP version %2$s or higher. You are running version %3$s.' ), $update->current, $update->php_version, $php_version );
    } elseif ( ! $mysql_compat ) {
        /* translators: 1: WP version number, 2: Minimum required MySQL version number, 3: Current MySQL version number */
        $message = sprintf( __( 'You cannot update because WP %1$s requires MySQL version %2$s or higher. You are running version %3$s.' ), $update->current, $update->mysql_version, $mysql_version );
    } else {
        /* translators: 1: WP version number */
        $message = sprintf( __( 'You can update to WP %1$s automatically:' ), $update->version );
    }

    if ( ! $mysql_compat || ! $php_compat ) {
        $show_buttons = false;
    }

	echo '<p>' . $message . '</p>
    <form method="post" action="' . $form_action . '" name="upgrade" class="upgrade">';
	wp_nonce_field( 'upgrade-core' );
	echo '<p>
    <input name="version" value="' . esc_attr( $update->version ) . '" type="hidden">';
	if ( $show_buttons ) {
		submit_button( __( 'Update Now' ), '', 'upgrade', false );
	}

    if ( ! isset( $update->dismissed ) || ! $update->dismissed ) {
        submit_button( __( 'Hide this update' ), '', 'dismiss', false );
    } else {
        submit_button( __( 'Bring back this update' ), '', 'undismiss', false );
    }

	echo '</p>
    </form>';

}

/**
 * Display dismissed updates.
 */
function dismissed_updates() {

    $dismissed = get_core_updates(
        array(
            'dismissed' => true,
            'available' => false,
        )
    );

	if ( $dismissed ) {
		$show_text = esc_js( __( 'Show dismissed updates' ) );
		$hide_text = esc_js( __( 'Hide dismissed updates' ) );
	?>

	<script type="text/javascript">
		jQuery(function( $ ) {
			$( 'dismissed-updates' ).show();
			$( '#show-dismissed' ).toggle( function() { $( this ).text( '<?php echo $hide_text; ?>' ).attr( 'aria-expanded', 'true' ); }, function() { $( this ).text( '<?php echo $show_text; ?>' ).attr( 'aria-expanded', 'false' ); } );
			$( '#show-dismissed' ).click( function() { $( '#dismissed-updates' ).toggle( 'fast' ); } );
		});
	</script>
	<?php
		echo '<p class="hide-if-no-js"><button type="button" class="button" id="show-dismissed" aria-expanded="false">' . __( 'Show hidden updates' ) . '</button></p>';
		echo '<ul id="dismissed-updates" class="core-updates dismissed">';
		foreach ( $dismissed as $update ) {
			echo '<li>';
			list_core_update( $update );
			echo '</li>';
		}
		echo '</ul>';
	}
}

/**
 * Display the correct upgrade WP message, depending on current version
 */
function core_upgrade_preamble() {

	$wp_version = get_bloginfo( 'version' );
	$updates    = get_core_updates();

	if ( ! isset( $updates[0]->version ) || $wp_version === $updates[0]->version ) {

        echo '<h2>';
        _e( 'You are running the latest available version of WP. Good for you!' );
        echo "</h2>\n";

	} else {

		echo '<div class="notice notice-warning"><p>';
		_e( 'You should backup your stuff (all files and DB) before updating.' );
		echo '</p></div>';

		echo '<h2 class="response">';
		_e( 'An updated version of WP is available.' );
		echo '</h2>';

	}

	echo '<ul class="core-updates">';
	foreach ( (array) $updates as $update ) {
		echo '<li>';
		list_core_update( $update );
		echo '</li>';
	}
	echo '</ul>';

	dismissed_updates();
}

/**
 * Display available plugin updates
 */
function list_plugin_updates() {
	$wp_version     = get_bloginfo( 'version' );
	$cur_wp_version = preg_replace( '/-.*$/', '', $wp_version );
	global $wp_version;
	$cur_cp_version = preg_replace( '/\+.*$/', '', $wp_version );

	require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
	$plugins = get_plugin_updates();
	if ( empty( $plugins ) ) {
		echo '<h2>' . __( 'Plugins' ) . '</h2>';
		echo '<p>' . __( 'Your plugins are all up to date.' ) . '</p>';
		return;
	}
	$form_action = 'update-core.php?action=do-plugin-upgrade';
	?>
    <h2><?php _e( 'Plugins' ); ?></h2>
    <p><?php _e( 'The following plugins have new versions available. Check the ones you want to update and then click &#8220;Update Plugins&#8221;.' ); ?></p>
    <form method="post" action="<?php echo esc_url( $form_action ); ?>" name="upgrade-plugins" class="upgrade">
        <?php wp_nonce_field( 'upgrade-core' ); ?>
        <p><input id="upgrade-plugins" class="button" type="submit" value="<?php esc_attr_e( 'Update Plugins' ); ?>" name="upgrade"></p>
        <table class="widefat updates-table" id="update-plugins-table">
	    <thead>
        <tr>
            <td class="manage-column check-column"><input type="checkbox" id="plugins-select-all" /></td>
            <td class="manage-column"><label for="plugins-select-all"><?php _e( 'Select All' ); ?></label></td>
        </tr>
        </thead>

        <tbody class="plugins">
        <?php
	    foreach ( (array) $plugins as $plugin_file => $plugin_data ) {
		$plugin_data = (object) _get_plugin_data_markup_translate( $plugin_file, (array) $plugin_data, false, true );

		$icon            = '<span class="dashicons dashicons-admin-plugins"></span>';
		$preferred_icons = array( 'svg', '1x', '2x', 'default' );
		foreach ( $preferred_icons as $preferred_icon ) {
			if ( ! empty( $plugin_data->update->icons[ $preferred_icon ] ) ) {
				$icon = '<img src="' . esc_url( $plugin_data->update->icons[ $preferred_icon ] ) . '" alt="" />';
				break;
			}
		}

		$requires_php   = $plugin_data->update->requires_php ?? null;
		$compatible_php = is_php_version_compatible( $requires_php );

        $compat = '';
		if ( ! $compatible_php && current_user_can( 'update_php' ) ) {
			$compat    .= '<br>' . __( 'This update doesn&#8217;t work with your version of PHP.' );
			$annotation = wp_get_update_php_annotation();

			if ( $annotation ) {
				$compat .= '</p><p><em>' . $annotation . '</em>';
			}
		}

		// Get the upgrade notice for the new plugin version.
		if ( isset( $plugin_data->update->upgrade_notice ) ) {
			$upgrade_notice = '<br>' . strip_tags( $plugin_data->update->upgrade_notice );
		} else {
			$upgrade_notice = '';
		}

		$details_url = self_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $plugin_data->update->slug . '&section=changelog&TB_iframe=true&width=640&height=662' );
		$details     = sprintf(
			'<a href="%1$s" class="thickbox open-plugin-details-modal" aria-label="%2$s">%3$s</a>',
			esc_url( $details_url ),
			/* translators: 1: plugin name, 2: version number */
			esc_attr( sprintf( __( 'View %1$s version %2$s details' ), $plugin_data->name, $plugin_data->update->new_version ) ),
			/* translators: %s: plugin version */
			sprintf( __( 'View version %s details.' ), $plugin_data->update->new_version )
		);

		$checkbox_id = 'checkbox_' . md5( $plugin_data->name );
		?>
		<tr>
			<td class="check-column">
				<?php if ( $compatible_php ) : ?>
					<input type="checkbox" name="checked[]" id="<?php echo $checkbox_id; ?>" value="<?php echo esc_attr( $plugin_file ); ?>" />
					<label for="<?php echo $checkbox_id; ?>" class="screen-reader-text">
						<?php
						/* translators: %s: Plugin name. */
						printf( __( 'Select %s' ), $plugin_data->name );
						?>
					</label>
				<?php endif; ?>
			</td>
			<td class="plugin-title"><p>
				<?php echo $icon; ?>
				<strong><?php echo $plugin_data->name; ?></strong>
				<?php
				printf(
					/* translators: 1: Plugin version, 2: New version. */
					__( 'You have version %1$s installed. Update to %2$s.' ),
					$plugin_data->version,
					$plugin_data->update->new_version
				);

				echo ' ' . $details . $compat . $upgrade_notice;
				?>
			</p></td>
		</tr>
		<?php
	}
?>
	</tbody>

	<tfoot>
	<tr>
		<td class="manage-column check-column"><input type="checkbox" id="plugins-select-all-2" /></td>
		<td class="manage-column"><label for="plugins-select-all-2"><?php _e( 'Select All' ); ?></label></td>
	</tr>
	</tfoot>
</table>
<p><input id="upgrade-plugins-2" class="button" type="submit" value="<?php esc_attr_e( 'Update Plugins' ); ?>" name="upgrade" /></p>
</form>
<?php
}

/**
 * Display available theme updates
 */
function list_theme_updates() {
	$themes = get_theme_updates();
	if ( empty( $themes ) ) {
		echo '<h2>' . __( 'Themes' ) . '</h2>';
		echo '<p>' . __( 'Your themes are all up to date.' ) . '</p>';
		return;
	}

	$form_action = 'update-core.php?action=do-theme-upgrade';
?>
<h2><?php _e( 'Themes' ); ?></h2>
<p><?php _e( 'The following themes have new versions available. Check the ones you want to update and then click &#8220;Update Themes&#8221;.' ); ?></p>
<form method="post" action="<?php echo esc_url( $form_action ); ?>" name="upgrade-themes" class="upgrade">
<?php wp_nonce_field( 'upgrade-core' ); ?>
<p><input id="upgrade-themes" class="button" type="submit" value="<?php esc_attr_e( 'Update Themes' ); ?>" name="upgrade" /></p>
<table class="widefat updates-table" id="update-themes-table">
	<thead>
	<tr>
		<td class="manage-column check-column"><input type="checkbox" id="themes-select-all" /></td>
		<td class="manage-column"><label for="themes-select-all"><?php _e( 'Select All' ); ?></label></td>
	</tr>
	</thead>

	<tbody class="plugins">
    <?php
	foreach ( $themes as $stylesheet => $theme ) {
		$requires_php = $theme->update['requires_php'] ?? null;

		$compatible_php = is_php_version_compatible( $requires_php );

		$compat = '';

		if ( ! $compatible_php ) {
			$compat .= '<br>' . __( 'This update doesn&#8217;t work with your version of PHP.' );
			if ( current_user_can( 'update_php' ) ) {

				$annotation = wp_get_update_php_annotation();

				if ( $annotation ) {
					$compat .= '</p><p><em>' . $annotation . '</em>';
				}
			}
		}

		$checkbox_id = 'checkbox_' . md5( $theme->get( 'Name' ) );
		?>
		<tr>
			<td class="check-column">
				<?php if ( $compatible_php ) : ?>
					<input type="checkbox" name="checked[]" id="<?php echo $checkbox_id; ?>" value="<?php echo esc_attr( $stylesheet ); ?>" />
					<label for="<?php echo $checkbox_id; ?>" class="screen-reader-text">
						<?php
						/* translators: %s: Theme name. */
						printf( __( 'Select %s' ), $theme->display( 'Name' ) );
						?>
					</label>
				<?php endif; ?>
			</td>
			<td class="plugin-title"><p>
				<img src="<?php echo esc_url( $theme->get_screenshot() ); ?>" width="85" height="64" class="updates-table-screenshot" alt="" />
				<strong><?php echo $theme->display( 'Name' ); ?></strong>
				<?php
				printf(
					/* translators: 1: Theme version, 2: New version. */
					__( 'You have version %1$s installed. Update to %2$s.' ),
					$theme->display( 'Version' ),
					$theme->update['new_version']
				);

				echo ' ' . $compat;
				?>
			</p></td>
		</tr>
		<?php
	}
?>
	</tbody>

	<tfoot>
	<tr>
		<td class="manage-column check-column"><input type="checkbox" id="themes-select-all-2" /></td>
		<td class="manage-column"><label for="themes-select-all-2"><?php _e( 'Select All' ); ?></label></td>
	</tr>
	</tfoot>
</table>
<p><input id="upgrade-themes-2" class="button" type="submit" value="<?php esc_attr_e( 'Update Themes' ); ?>" name="upgrade" /></p>
</form>
<?php
}

/**
 * Upgrade WP core display.
 * @global WP_Filesystem_Base $wp_filesystem Subclass
 */
function do_core_upgrade() {
	global $wp_filesystem;

	include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

    $url = 'update-core.php?action=do-core-upgrade';

	$url = wp_nonce_url( $url, 'upgrade-core' );

	$version = $_POST['version'] ?? false;
	$update  = find_core_update( $version );
	if ( ! $update ) {
		return;
	}

	// Allow relaxed file ownership writes for User-initiated upgrades when the API specifies
	// that it's safe to do so. This only happens when there are no new files to create.
	$allow_relaxed_file_ownership = isset( $update->new_files ) && ! $update->new_files;
?>
	<div class="wrap">
	<h1><?php _e( 'Update WP' ); ?></h1>
<?php

    $credentials = request_filesystem_credentials( $url, '', false, ABSPATH, array( 'version' ), $allow_relaxed_file_ownership );
	if ( false === $credentials ) {
		echo '</div>';
		return;
	}

	if ( ! WP_Filesystem( $credentials, ABSPATH, $allow_relaxed_file_ownership ) ) {
		// Failed to connect, Error and request again
		request_filesystem_credentials( $url, '', true, ABSPATH, array( 'version', 'locale' ), $allow_relaxed_file_ownership );
		echo '</div>';
		return;
	}

	if ( $wp_filesystem->errors->get_error_code() ) {
		foreach ( $wp_filesystem->errors->get_error_messages() as $message ) {
			show_message( $message );
		}
		echo '</div>';
		return;
	}

	add_filter( 'update_feedback', 'show_message' );

	$upgrader = new Core_Upgrader();
	$result   = $upgrader->upgrade(
        $update,
        array(
        'allow_relaxed_file_ownership' => $allow_relaxed_file_ownership,
        )
    );

	if ( is_wp_error( $result ) ) {
		show_message( $result );
		switch ( $result->get_error_code() ) {
			case 'up_to_date':
				// WP is already up-to-date, no need to show a different message
				break;

			case 'locked':
				// Show a bit more info for this fairly common error
				show_message( __( 'It\'s possible that an update started, but the server encountered a temporary issue and could not continue.' ) );
				show_message( __( 'Or, you may have clicked the update button multiple times.' ) );
				show_message( __( 'Please wait <strong>15 minutes</strong> and try again.' ) );
				break;

			default:
				// Show a generic failure message
				show_message( __( 'Installation Failed' ) );
				break;
		}
		echo '</div>';
		return;
	}

	show_message( __( 'ClassicPress updated successfully' ) );
    /* translators: 1: WP version, 2: admin URL */
	show_message( '<span class="hide-if-no-js">' . sprintf( __( 'Welcome to WP %1$s. You will be redirected to the About ClassicPress screen. If not, click <a href="%2$s">here</a>.' ), $result, esc_url( self_admin_url( 'about.php?updated' ) ) ) . '</span>' );
    /* translators: 1: WP version, 2: admin URL */
	show_message( '<span class="hide-if-js">' . sprintf( __( 'Welcome to WP %1$s. <a href="%2$s">Learn more</a>.' ), $result, esc_url( self_admin_url( 'about.php?updated' ) ) ) . '</span>' );
	?>
	</div>
	<script type="text/javascript">
	window.location = '<?php echo self_admin_url( 'about.php?updated' ); ?>';
	</script>
	<?php
}

/**
 * Dismiss a core update.
 */
function do_dismiss_core_update() {
	$version = $_POST['version'] ?? false;
	$update  = find_core_update( $version );
	if ( ! $update ) {
		return;
	}
	dismiss_core_update( $update );
	wp_redirect( wp_nonce_url( 'update-core.php?action=upgrade-core', 'upgrade-core' ) );
	exit;
}

/**
 * Undismiss a core update.
 */
function do_undismiss_core_update() {
	$version = $_POST['version'] ?? false;
	$update  = find_core_update( $version );
	if ( ! $update ) {
		return;
	}
	undismiss_core_update( $version );
	wp_redirect( wp_nonce_url( 'update-core.php?action=upgrade-core', 'upgrade-core' ) );
	exit;
}

$action = $_GET['action'] ?? 'upgrade-core';

$upgrade_error = false;
if ( ( 'do-theme-upgrade' === $action || ( 'do-plugin-upgrade' === $action && ! isset( $_GET['plugins'] ) ) )
	&& ! isset( $_POST['checked'] ) ) {
	$upgrade_error = 'do-theme-upgrade' === $action ? 'themes' : 'plugins';
	$action        = 'upgrade-core';
}

$title       = __( 'WP Updates' );
$parent_file = 'index.php';

$updates_overview  = '<p>' . __( 'On this screen, you can update to the latest version of WP, as well as update your themes and plugins from the ClassicPress.net repositories.' ) . '</p>';
$updates_overview .= '<p>' . __( 'If an update is available, you&#8127;ll see a notification appear in the Toolbar and navigation menu.' ) . ' ' . __( 'Keeping your site updated is important for security. It also makes the internet a safer place for you and your readers.' ) . '</p>';

get_current_screen()->add_help_tab(
    array(
	    'id'      => 'overview',
	    'title'   => __( 'Overview' ),
	    'content' => $updates_overview,
    )
);

$updates_howto  = '<p>' . __( '<strong>Core</strong> &mdash; Updating your WP installation is a simple one-click procedure: just <strong>click on the &#8220;Update Now&#8221; button</strong> when you are notified that a new version is available.' ) . ' ' . __( 'In most cases, WP will automatically apply maintenance and security updates in the background for you.' ) . '</p>';
$updates_howto .= '<p>' . __( '<strong>Themes and Plugins</strong> &mdash; To update individual themes or plugins from this screen, use the checkboxes to make your selection, then <strong>click on the appropriate &#8220;Update&#8221; button</strong>. To update all of your themes or plugins at once, you can check the box at the top of the section to select all before clicking the update button.' ) . '</p>';

get_current_screen()->add_help_tab(
    array(
        'id'      => 'how-to-update',
        'title'   => __( 'How to Update' ),
        'content' => $updates_howto,
    )
);

if ( 'upgrade-core' === $action ) {

    // Force an update check when requested
	$force_check = ! empty( $_GET['force-check'] );
	wp_version_check( array(), $force_check );

	require_once( ABSPATH . 'wp-admin/admin-header.php' );
	?>
	<div class="wrap">
	<h1><?php _e( 'WP Updates' ); ?></h1>
	<?php
	if ( $upgrade_error ) {
		echo '<div class="error"><p>';
		if ( 'themes' === $upgrade_error ) {
            _e( 'Please select one or more themes to update.' );
        } else {
			_e( 'Please select one or more plugins to update.' );
        }
		echo '</p></div>';
	}

	$last_update_check = false;
	$current           = get_site_transient( 'update_core' );

	if ( $current && isset( $current->last_checked ) ) {
		$last_update_check = $current->last_checked + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
	}

	echo '<p>';
	/* translators: %1 date, %2 time. */
	printf( __( 'Last checked on %1$s at %2$s.' ), date_i18n( __( 'F j, Y' ), $last_update_check ), date_i18n( __( 'g:i a' ), $last_update_check ) );
	echo ' &nbsp; <a class="button" href="' . esc_url( self_admin_url( 'update-core.php?force-check=1' ) ) . '">' . __( 'Check Again' ) . '</a>';
	echo '</p>';

	if ( current_user_can( 'update_core' ) ) {
		core_upgrade_preamble();
	}
	if ( current_user_can( 'update_plugins' ) ) {
		list_plugin_updates();
	}
	if ( current_user_can( 'update_themes' ) ) {
		list_theme_updates();
	}

	/**
	 * Fires after the core, plugin, and theme update tables.
	 */
	do_action( 'core_upgrade_preamble' );
	echo '</div>';

	wp_localize_script(
        'updates',
        '_wpUpdatesItemCounts',
        array(
            'totals' => wp_get_update_data(),
        )
    );

	include( ABSPATH . 'wp-admin/admin-footer.php' );

} elseif ( 'do-core-upgrade' === $action ) {

	if ( ! current_user_can( 'update_core' ) ) {
        wp_die( __( 'Sorry, you are not allowed to update this site.' ) );
    }

	check_admin_referer( 'upgrade-core' );

	// Do the (un)dismiss actions before headers, so that they can redirect.
	if ( isset( $_POST['dismiss'] ) ) {
		do_dismiss_core_update();
	} elseif ( isset( $_POST['undismiss'] ) ) {
		do_undismiss_core_update();
    }

	require_once( ABSPATH . 'wp-admin/admin-header.php' );

	if ( isset( $_POST['upgrade'] ) ) {
        do_core_upgrade();
    }

	wp_localize_script(
        'updates',
        '_wpUpdatesItemCounts',
        array(
            'totals' => wp_get_update_data(),
	    )
	);

	include( ABSPATH . 'wp-admin/admin-footer.php' );

} elseif ( 'do-plugin-upgrade' === $action ) {

	if ( ! current_user_can( 'update_plugins' ) ) {
		wp_die( __( 'Sorry, you are not allowed to update this site.' ) );
    }

	check_admin_referer( 'upgrade-core' );

	if ( isset( $_GET['plugins'] ) ) {
		$plugins = explode( ',', $_GET['plugins'] );
	} elseif ( isset( $_POST['checked'] ) ) {
		$plugins = (array) $_POST['checked'];
	} else {
		wp_redirect( admin_url( 'update-core.php' ) );
		exit;
	}

	$url = 'update.php?action=update-selected&plugins=' . urlencode( implode( ',', $plugins ) );
	$url = wp_nonce_url( $url, 'bulk-update-plugins' );

	$title = __( 'Update Plugins' );

	require_once( ABSPATH . 'wp-admin/admin-header.php' );
	echo '<div class="wrap">';
	echo '<h1>' . __( 'Update Plugins' ) . '</h1>';
	echo '<iframe src="', $url, '" style="width: 100%; height: 100%; min-height: 750px;" frameborder="0" title="' . esc_attr__( 'Update progress' ) . '"></iframe>';
	echo '</div>';

	wp_localize_script(
        'updates',
        '_wpUpdatesItemCounts',
        array(
            'totals' => wp_get_update_data(),
        )
    );

	include( ABSPATH . 'wp-admin/admin-footer.php' );

} elseif ( 'do-theme-upgrade' === $action ) {

	if ( ! current_user_can( 'update_themes' ) ) {
		wp_die( __( 'Sorry, you are not allowed to update this site.' ) );
    }

	check_admin_referer( 'upgrade-core' );

	if ( isset( $_GET['themes'] ) ) {
		$themes = explode( ',', $_GET['themes'] );
	} elseif ( isset( $_POST['checked'] ) ) {
		$themes = (array) $_POST['checked'];
	} else {
		wp_redirect( admin_url( 'update-core.php' ) );
		exit;
	}

	$url = 'update.php?action=update-selected-themes&themes=' . urlencode( implode( ',', $themes ) );
	$url = wp_nonce_url( $url, 'bulk-update-themes' );

	$title = __( 'Update Themes' );

	require_once( ABSPATH . 'wp-admin/admin-header.php' );
	?>
	<div class="wrap">
		<h1><?php _e( 'Update Themes' ); ?></h1>
		<iframe src="<?php echo $url; ?>" style="width: 100%; height: 100%; min-height: 750px;" title="<?php esc_attr_e( 'Update progress' ); ?>"></iframe>
	</div>
	<?php

	wp_localize_script(
        'updates',
        '_wpUpdatesItemCounts',
        array(
            'totals' => wp_get_update_data(),
        )
	);

	include( ABSPATH . 'wp-admin/admin-footer.php' );

} else {
	/**
	 * Fires for each custom update action on the ClassicPress Updates screen.
	 *
	 * The dynamic portion of the hook name, `$action`, refers to the
	 * passed update action. The hook fires in lieu of all available
	 * default update actions.
	 */
	do_action( "update-core-custom_{$action}" );
}
