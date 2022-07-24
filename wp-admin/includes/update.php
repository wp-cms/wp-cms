<?php
/**
 * ClassicPress Administration Update API
 *
 * @package ClassicPress
 * @subpackage Administration
 */

/**
 * Get available core updates.
 *
 * @param array $options Set $options['dismissed'] to true to show dismissed upgrades too,
 *                       set $options['available'] to false to skip not-dismissed updates.
 * @return array|false Array of the update objects on success, false on failure.
 */
function get_core_updates( $options = array() ) {

    $options = array_merge(
        array(
        'available' => true,
        'dismissed' => false,
        ),
        $options
    );

	$dismissed_updates = get_site_option( 'dismissed_update_core' );

	if ( ! is_array( $dismissed_updates ) ) {
		$dismissed_updates = array();
	}

	$available_updates_information = get_site_transient( 'update_core' );

	if ( ! isset( $available_updates_information->updates ) || ! is_array( $available_updates_information->updates ) ) {
		return false;
	}

	$updates = $available_updates_information->updates;
	$result  = array();
	foreach ( $updates as $update ) {
		if ( array_key_exists( $update->version, $dismissed_updates ) ) {
			if ( $options['dismissed'] ) {
				$update->dismissed = true;
				$result[]          = $update;
			}
		} else {
			if ( $options['available'] ) {
				$update->dismissed = false;
				$result[]          = $update;
			}
		}
	}
	return $result;
}

/**
 *
 * @param object $update
 * @return bool
 */
function dismiss_core_update( $update ) {
	$dismissed                     = get_site_option( 'dismissed_update_core' );
	$dismissed[ $update->version ] = true;
	return update_site_option( 'dismissed_update_core', $dismissed );
}

/**
 *
 * @param string $version
 * @return bool
 */
function undismiss_core_update( $version ) {
	$dismissed = get_site_option( 'dismissed_update_core' );

	if ( ! isset( $dismissed[ $version ] ) ) {
		return false;
	}

	unset( $dismissed[ $version ] );
	return update_site_option( 'dismissed_update_core', $dismissed );
}

/**
 *
 * @param string $version
 * @return object|false
 */
function find_core_update( $version ) {
	$from_api = get_site_transient( 'update_core' );

	if ( ! isset( $from_api->updates ) || ! is_array( $from_api->updates ) ) {
		return false;
	}

	$updates = $from_api->updates;
	foreach ( $updates as $update ) {
		if ( $update->version == $version ) {
			return $update;
		}
	}

	return false;
}

/**
 * @since WP-2.9.0
 *
 * @return array
 */
function get_plugin_updates() {
	$all_plugins = get_plugins();
	$upgrade_plugins = array();
	$current = get_site_transient( 'update_plugins' );
	foreach ( (array)$all_plugins as $plugin_file => $plugin_data) {
		if ( isset( $current->response[ $plugin_file ] ) ) {
			$upgrade_plugins[ $plugin_file ] = (object) $plugin_data;
			$upgrade_plugins[ $plugin_file ]->update = $current->response[ $plugin_file ];
		}
	}

	return $upgrade_plugins;
}

/**
 * @since WP-2.9.0
 */
function wp_plugin_update_rows() {
	if ( !current_user_can('update_plugins' ) )
		return;

	$plugins = get_site_transient( 'update_plugins' );
	if ( isset($plugins->response) && is_array($plugins->response) ) {
		$plugins = array_keys( $plugins->response );
		foreach ( $plugins as $plugin_file ) {
			add_action( "after_plugin_row_$plugin_file", 'wp_plugin_update_row', 10, 2 );
		}
	}
}

/**
 * Displays update information for a plugin.
 *
 * @param string $file        Plugin basename.
 * @param array  $plugin_data Plugin information.
 * @return false|void
 */
function wp_plugin_update_row( $file, $plugin_data ) {
	$current = get_site_transient( 'update_plugins' );
	if ( ! isset( $current->response[ $file ] ) ) {
		return false;
	}

	$response = $current->response[ $file ];

	$plugins_allowedtags = array(
		'a'       => array( 'href' => array(), 'title' => array() ),
		'abbr'    => array( 'title' => array() ),
		'acronym' => array( 'title' => array() ),
		'code'    => array(),
		'em'      => array(),
		'strong'  => array(),
	);

	$plugin_name   = wp_kses( $plugin_data['Name'], $plugins_allowedtags );
	$details_url   = self_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $response->slug . '&section=changelog&TB_iframe=true&width=600&height=800' );

	/** @var WP_Plugins_List_Table $wp_list_table */
	$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );

	if ( is_network_admin() || ! is_multisite() ) {
		if ( is_network_admin() ) {
			$active_class = is_plugin_active_for_network( $file ) ? ' active' : '';
		} else {
			$active_class = is_plugin_active( $file ) ? ' active' : '';
		}

		$requires_php   = isset( $response->requires_php ) ? $response->requires_php : null;
		$compatible_php = is_php_version_compatible( $requires_php );
		$notice_type    = $compatible_php ? 'notice-warning' : 'notice-error';

		echo '<tr class="plugin-update-tr' . $active_class . '" id="' . esc_attr( $response->slug . '-update' ) . '" data-slug="' . esc_attr( $response->slug ) . '" data-plugin="' . esc_attr( $file ) . '"><td colspan="' . esc_attr( $wp_list_table->get_column_count() ) . '" class="plugin-update colspanchange"><div class="update-message notice inline ' . $notice_type . ' notice-alt"><p>';

		if ( ! current_user_can( 'update_plugins' ) ) {
			/* translators: 1: plugin name, 2: details URL, 3: additional link attributes, 4: version number */
			printf( __( 'There is a new version of %1$s available. <a href="%2$s" %3$s>View version %4$s details</a>.' ),
				$plugin_name,
				esc_url( $details_url ),
				sprintf( 'class="thickbox open-plugin-details-modal" aria-label="%s"',
					/* translators: 1: plugin name, 2: version number */
					esc_attr( sprintf( __( 'View %1$s version %2$s details' ), $plugin_name, $response->new_version ) )
				),
				$response->new_version
			);
		} elseif ( empty( $response->package ) ) {
			/* translators: 1: plugin name, 2: details URL, 3: additional link attributes, 4: version number */
			printf( __( 'There is a new version of %1$s available. <a href="%2$s" %3$s>View version %4$s details</a>. <em>Automatic update is unavailable for this plugin.</em>' ),
				$plugin_name,
				esc_url( $details_url ),
				sprintf( 'class="thickbox open-plugin-details-modal" aria-label="%s"',
					/* translators: 1: plugin name, 2: version number */
					esc_attr( sprintf( __( 'View %1$s version %2$s details' ), $plugin_name, $response->new_version ) )
				),
				$response->new_version
			);
		} else {
			/* translators: 1: plugin name, 2: details URL, 3: additional link attributes, 4: version number, 5: update URL, 6: additional link attributes */
			printf( __( 'There is a new version of %1$s available. <a href="%2$s" %3$s>View version %4$s details</a> or <a href="%5$s" %6$s>update now</a>.' ),
				$plugin_name,
				esc_url( $details_url ),
				sprintf( 'class="thickbox open-plugin-details-modal" aria-label="%s"',
					/* translators: 1: plugin name, 2: version number */
					esc_attr( sprintf( __( 'View %1$s version %2$s details' ), $plugin_name, $response->new_version ) )
				),
				$response->new_version,
				wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $file, 'upgrade-plugin_' . $file ),
				sprintf( 'class="update-link" aria-label="%s"',
					/* translators: %s: plugin name */
					esc_attr( sprintf( __( 'Update %s now' ), $plugin_name ) )
				)
			);
		}

		/**
		 * Fires at the end of the update message container in each
		 * row of the plugins list table.
		 *
		 * The dynamic portion of the hook name, `$file`, refers to the path
		 * of the plugin's primary file relative to the plugins directory.
		 *
		 * @since WP-2.8.0
		 *
		 * @param array $plugin_data {
		 *     An array of plugin metadata.
		 *
		 *     @type string $name        The human-readable name of the plugin.
		 *     @type string $plugin_uri  Plugin URI.
		 *     @type string $version     Plugin version.
		 *     @type string $description Plugin description.
		 *     @type string $author      Plugin author.
		 *     @type string $author_uri  Plugin author URI.
		 *     @type string $text_domain Plugin text domain.
		 *     @type string $domain_path Relative path to the plugin's .mo file(s).
		 *     @type bool   $network     Whether the plugin can only be activated network wide.
		 *     @type string $title       The human-readable title of the plugin.
		 *     @type string $author_name Plugin author's name.
		 *     @type bool   $update      Whether there's an available update. Default null.
		 * }
		 * @param array $response {
		 *     An array of metadata about the available plugin update.
		 *
		 *     @type int    $id          Plugin ID.
		 *     @type string $slug        Plugin slug.
		 *     @type string $new_version New plugin version.
		 *     @type string $url         Plugin URL.
		 *     @type string $package     Plugin update package URL.
		 * }
		 */
		do_action( "in_plugin_update_message-{$file}", $plugin_data, $response );

		echo '</p></div></td></tr>';
	}
}

/**
 *
 * @return array
 */
function get_theme_updates() {
	$current = get_site_transient('update_themes');

	if ( ! isset( $current->response ) )
		return array();

	$update_themes = array();
	foreach ( $current->response as $stylesheet => $data ) {
		$update_themes[ $stylesheet ] = wp_get_theme( $stylesheet );
		$update_themes[ $stylesheet ]->update = $data;
	}

	return $update_themes;
}

/**
 * @since WP-3.1.0
 */
function wp_theme_update_rows() {
	if ( !current_user_can('update_themes' ) )
		return;

	$themes = get_site_transient( 'update_themes' );
	if ( isset($themes->response) && is_array($themes->response) ) {
		$themes = array_keys( $themes->response );

		foreach ( $themes as $theme ) {
			add_action( "after_theme_row_$theme", 'wp_theme_update_row', 10, 2 );
		}
	}
}

/**
 * Displays update information for a theme.
 *
 * @param string   $theme_key Theme stylesheet.
 * @param WP_Theme $theme     Theme object.
 * @return false|void
 */
function wp_theme_update_row( $theme_key, $theme ) {
	$current = get_site_transient( 'update_themes' );

	if ( ! isset( $current->response[ $theme_key ] ) ) {
		return false;
	}

	$response = $current->response[ $theme_key ];

	$details_url = add_query_arg( array(
		'TB_iframe' => 'true',
		'width'     => 1024,
		'height'    => 800,
	), $current->response[ $theme_key ]['url'] );

	/** @var WP_MS_Themes_List_Table $wp_list_table */
	$wp_list_table = _get_list_table( 'WP_MS_Themes_List_Table' );

	$active = $theme->is_allowed( 'network' ) ? ' active' : '';

	$requires_wp  = isset( $response['requires'] ) ? $response['requires'] : null;
	$requires_php = isset( $response['requires_php'] ) ? $response['requires_php'] : null;

	$compatible_wp  = is_wp_version_compatible( $requires_wp );
	$compatible_php = is_php_version_compatible( $requires_php );

	printf(
		'<tr class="plugin-update-tr%s" id="%s" data-slug="%s">' .
		'<td colspan="%s" class="plugin-update colspanchange">' .
		'<div class="update-message notice inline notice-warning notice-alt"><p>',
		$active,
		esc_attr( $theme->get_stylesheet() . '-update' ),
		esc_attr( $theme->get_stylesheet() ),
		$wp_list_table->get_column_count()
	);

	if ( $compatible_wp && $compatible_php ) {
		if ( ! current_user_can( 'update_themes' ) ) {
			printf(
				/* translators: 1: Theme name, 2: Details URL, 3: Additional link attributes, 4: Version number. */
				__( 'There is a new version of %1$s available. <a href="%2$s" %3$s>View version %4$s details</a>.' ),
				$theme['Name'],
				esc_url( $details_url ),
				sprintf(
					'class="thickbox open-plugin-details-modal" aria-label="%s"',
					/* translators: 1: Theme name, 2: Version number. */
					esc_attr( sprintf( __( 'View %1$s version %2$s details' ), $theme['Name'], $response['new_version'] ) )
				),
				$response['new_version']
			);
		} elseif ( empty( $response['package'] ) ) {
			printf(
				/* translators: 1: Theme name, 2: Details URL, 3: Additional link attributes, 4: Version number. */
				__( 'There is a new version of %1$s available. <a href="%2$s" %3$s>View version %4$s details</a>. <em>Automatic update is unavailable for this theme.</em>' ),
				$theme['Name'],
				esc_url( $details_url ),
				sprintf(
					'class="thickbox open-plugin-details-modal" aria-label="%s"',
					/* translators: 1: Theme name, 2: Version number. */
					esc_attr( sprintf( __( 'View %1$s version %2$s details' ), $theme['Name'], $response['new_version'] ) )
				),
				$response['new_version']
			);
		} else {
			printf(
				/* translators: 1: Theme name, 2: Details URL, 3: Additional link attributes, 4: Version number, 5: Update URL, 6: Additional link attributes. */
				__( 'There is a new version of %1$s available. <a href="%2$s" %3$s>View version %4$s details</a> or <a href="%5$s" %6$s>update now</a>.' ),
				$theme['Name'],
				esc_url( $details_url ),
				sprintf(
					'class="thickbox open-plugin-details-modal" aria-label="%s"',
					/* translators: 1: Theme name, 2: Version number. */
					esc_attr( sprintf( __( 'View %1$s version %2$s details' ), $theme['Name'], $response['new_version'] ) )
				),
				$response['new_version'],
				wp_nonce_url( self_admin_url( 'update.php?action=upgrade-theme&theme=' ) . $theme_key, 'upgrade-theme_' . $theme_key ),
				sprintf(
					'class="update-link" aria-label="%s"',
					/* translators: %s: Theme name. */
					esc_attr( sprintf( _x( 'Update %s now', 'theme' ), $theme['Name'] ) )
				)
			);
		}
	} else {
		if ( ! $compatible_wp && ! $compatible_php ) {
			printf(
				/* translators: %s: Theme name. */
				__( 'There is a new version of %s available, but it doesn&#8217;t work with your version of PHP or support ClassicPress.' ),
				$theme['Name']
			);
			if ( current_user_can( 'update_php' ) ) {
				printf(
					/* translators: %s: URL to Update PHP page. */
					' ' . __( '<a href="%s">Learn more about updating PHP</a>.' ),
					esc_url( wp_get_update_php_url() )
				);
				wp_update_php_annotation( '</p><p><em>', '</em>' );
			}
		} elseif ( ! $compatible_wp ) {
			printf(
				/* translators: %s: Theme name. */
				__( 'There is a new version of %s available, but it doesn&#8217;t support ClassicPress.' ),
				$theme['Name']
			);
		} elseif ( ! $compatible_php ) {
			printf(
				/* translators: %s: Theme name. */
				__( 'There is a new version of %s available, but it doesn&#8217;t work with your version of PHP.' ),
				$theme['Name']
			);
			if ( current_user_can( 'update_php' ) ) {
				printf(
					/* translators: %s: URL to Update PHP page. */
					' ' . __( '<a href="%s">Learn more about updating PHP</a>.' ),
					esc_url( wp_get_update_php_url() )
				);
				wp_update_php_annotation( '</p><p><em>', '</em>' );
			}
		}
	}

	/**
	 * Fires at the end of the update message container in each
	 * row of the themes list table.
	 *
	 * The dynamic portion of the hook name, `$theme_key`, refers to
	 * the theme slug as found in the ClassicPress.net themes repository.
	 *
	 * @since WP-3.1.0
	 *
	 * @param WP_Theme $theme    The WP_Theme object.
	 * @param array    $response {
	 *     An array of metadata about the available theme update.
	 *
	 *     @type string $new_version New theme version.
	 *     @type string $url         Theme URL.
	 *     @type string $package     Theme update package URL.
	 * }
	 */
	do_action( "in_theme_update_message-{$theme_key}", $theme, $response );

	echo '</p></div></td></tr>';
}

/**
 *
 * @global int $upgrading
 * @return false|void
 */
function maintenance_nag() {
	include( ABSPATH . WPINC . '/version.php' ); // include an unmodified $wp_version
	global $upgrading;
	$nag = isset( $upgrading );
	if ( ! $nag ) {
		$failed = get_site_option( 'auto_core_update_failed' );
		/*
		 * If an update failed critically, we may have copied over version.php but not other files.
		 * In that case, if the installation claims we're running the version we attempted, nag.
		 * This is serious enough to err on the side of nagging.
		 *
		 * If we simply failed to update before we tried to copy any files, then assume things are
		 * OK if they are now running the latest.
		 *
		 * This flag is cleared whenever a successful update occurs using Core_Upgrader.
		 */
		$comparison = ! empty( $failed['critical'] ) ? '>=' : '>';

		if ( isset( $failed['attempted'] ) && version_compare( $failed['attempted'], $wp_version, $comparison ) ) {
			$nag = true;
		}
	}

	if ( ! $nag )
		return false;

	if ( current_user_can('update_core') )
		$msg = sprintf( __('An automated ClassicPress update has failed to complete - <a href="%s">please attempt the update again now</a>.'), 'update-core.php' );
	else
		$msg = __('An automated ClassicPress update has failed to complete! Please notify the site administrator.');

	echo "<div class='update-nag'>$msg</div>";
}

/**
 * Prints the JavaScript templates for update admin notices.
 *
 * Template takes one argument with four values:
 *
 *     param {object} data {
 *         Arguments for admin notice.
 *
 *         @type string id        ID of the notice.
 *         @type string className Class names for the notice.
 *         @type string message   The notice's message.
 *         @type string type      The type of update the notice is for. Either 'plugin' or 'theme'.
 *     }
 *
 * @since WP-4.6.0
 */
function wp_print_admin_notice_templates() {
	?>
	<script id="tmpl-wp-updates-admin-notice" type="text/html">
		<div <# if ( data.id ) { #>id="{{ data.id }}"<# } #> class="notice {{ data.className }}"><p>{{{ data.message }}}</p></div>
	</script>
	<script id="tmpl-wp-bulk-updates-admin-notice" type="text/html">
		<div id="{{ data.id }}" class="{{ data.className }} notice <# if ( data.errors ) { #>notice-error<# } else { #>notice-success<# } #>">
			<p>
				<# if ( data.successes ) { #>
					<# if ( 1 === data.successes ) { #>
						<# if ( 'plugin' === data.type ) { #>
							<?php
							/* translators: %s: Number of plugins */
							printf( __( '%s plugin successfully updated.' ), '{{ data.successes }}' );
							?>
						<# } else { #>
							<?php
							/* translators: %s: Number of themes */
							printf( __( '%s theme successfully updated.' ), '{{ data.successes }}' );
							?>
						<# } #>
					<# } else { #>
						<# if ( 'plugin' === data.type ) { #>
							<?php
							/* translators: %s: Number of plugins */
							printf( __( '%s plugins successfully updated.' ), '{{ data.successes }}' );
							?>
						<# } else { #>
							<?php
							/* translators: %s: Number of themes */
							printf( __( '%s themes successfully updated.' ), '{{ data.successes }}' );
							?>
						<# } #>
					<# } #>
				<# } #>
				<# if ( data.errors ) { #>
					<button class="button-link bulk-action-errors-collapsed" aria-expanded="false">
						<# if ( 1 === data.errors ) { #>
							<?php
							/* translators: %s: Number of failed updates */
							printf( __( '%s update failed.' ), '{{ data.errors }}' );
							?>
						<# } else { #>
							<?php
							/* translators: %s: Number of failed updates */
							printf( __( '%s updates failed.' ), '{{ data.errors }}' );
							?>
						<# } #>
						<span class="screen-reader-text"><?php _e( 'Show more details' ); ?></span>
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
				<# } #>
			</p>
			<# if ( data.errors ) { #>
				<ul class="bulk-action-errors hidden">
					<# _.each( data.errorMessages, function( errorMessage ) { #>
						<li>{{ errorMessage }}</li>
					<# } ); #>
				</ul>
			<# } #>
		</div>
	</script>
	<?php
}

/**
 * Prints the JavaScript templates for update and deletion rows in list tables.
 *
 * The update template takes one argument with four values:
 *
 *     param {object} data {
 *         Arguments for the update row
 *
 *         @type string slug    Plugin slug.
 *         @type string plugin  Plugin base name.
 *         @type string colspan The number of table columns this row spans.
 *         @type string content The row content.
 *     }
 *
 * The delete template takes one argument with four values:
 *
 *     param {object} data {
 *         Arguments for the update row
 *
 *         @type string slug    Plugin slug.
 *         @type string plugin  Plugin base name.
 *         @type string name    Plugin name.
 *         @type string colspan The number of table columns this row spans.
 *     }
 *
 * @since WP-4.6.0
 */
function wp_print_update_row_templates() {
	?>
	<script id="tmpl-item-update-row" type="text/template">
		<tr class="plugin-update-tr update" id="{{ data.slug }}-update" data-slug="{{ data.slug }}" <# if ( data.plugin ) { #>data-plugin="{{ data.plugin }}"<# } #>>
			<td colspan="{{ data.colspan }}" class="plugin-update colspanchange">
				{{{ data.content }}}
			</td>
		</tr>
	</script>
	<script id="tmpl-item-deleted-row" type="text/template">
		<tr class="plugin-deleted-tr inactive deleted" id="{{ data.slug }}-deleted" data-slug="{{ data.slug }}" <# if ( data.plugin ) { #>data-plugin="{{ data.plugin }}"<# } #>>
			<td colspan="{{ data.colspan }}" class="plugin-update colspanchange">
				<# if ( data.plugin ) { #>
					<?php
					printf(
						/* translators: %s: Plugin name */
						_x( '%s was successfully deleted.', 'plugin' ),
						'<strong>{{{ data.name }}}</strong>'
					);
					?>
				<# } else { #>
					<?php
					printf(
						/* translators: %s: Theme name */
						_x( '%s was successfully deleted.', 'theme' ),
						'<strong>{{{ data.name }}}</strong>'
					);
					?>
				<# } #>
			</td>
		</tr>
	</script>
	<?php
}
