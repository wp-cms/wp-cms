<?php
namespace Core\UserAvatar;

/**
 * Add filters and actions for the admin area
 */
function setup_admin_area() {
    global $pagenow;

    // Translations
    load_plugin_textdomain('wp-user-avatar', 'wp-user-avatars/languages');

    // Default avatar
    add_filter('default_avatar_select', 'Core\UserAvatar\render_default_avatar_editing_section', 10);
    add_filter('whitelist_options', 'Core\UserAvatar\whitelist_options', 10);

    // For own profile
    add_action('show_user_profile', 'Core\UserAvatar\render_avatar_editing_section');
    add_action('personal_options_update', 'Core\UserAvatar\handle_avatar_update');

    // For someone else's profile
    add_action('edit_user_profile', 'Core\UserAvatar\render_avatar_editing_section');
    add_action('edit_user_profile_update', 'Core\UserAvatar\handle_avatar_update');

    // For new user creation
    add_action('user_new_form', 'Core\UserAvatar\render_avatar_editing_section');
    add_action('user_register', 'Core\UserAvatar\handle_avatar_update');

    // Load scripts if necessary
    $pages = array('profile.php', 'options-discussion.php', 'user-edit.php', 'user-new.php');
    if(in_array($pagenow, $pages) || ($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == 'wp-user-avatar')) {
        add_action('admin_enqueue_scripts', 'Core\UserAvatar\enqueue_scripts');
    }

}


/**
 * Update user meta
 * @param int $user_id
 */
function handle_avatar_update($user_id) {

    // Check for updated value of attachment ID
    $updated_avatar = isset($_POST['wp-user-avatar']) ? strip_tags($_POST['wp-user-avatar']) : false;

    // Update or remove meta
    if($updated_avatar) {
        $attachment = get_post($updated_avatar);
        if (!empty($attachment)) {
            update_user_meta($user_id, 'user_avatar', $updated_avatar);
        }
    }else{
        delete_user_meta($user_id, 'user_avatar');
    }

}

/**
 * Render the avatar section in the admin area user editing screens
 * @param object $user
 */
function render_avatar_editing_section($user) {

    // Check if the current user has a custom avatar
    $has_wp_user_avatar = has_wp_user_avatar($user->ID);

    // Get current avatar ID
    $current_avatar_id = get_user_meta($user->ID, 'user_avatar', true);

    // Show remove button if is set
    $hide_remove = !$has_wp_user_avatar ? ' style="display:none;"' : '';

    // Check if user has wp_user_avatar, if not show image from above
    $avatar_thumbnail = get_user_avatar($user->ID, 150);

    echo '<h2 class="user-profile-avatar">'.__('Avatar', 'wp-user-avatars').'</h2>';
    ?>

    <table class="form-table">
    <tbody><tr id="password" class="user-pass1-wrap">
    <th><?php _e('Avatar', 'wp-user-avatars'); ?></th>
    <td>

    <input type="hidden" name="wp-user-avatar" id="wp-user-avatar" value="<?php echo $current_avatar_id; ?>" />

    <?php
    // Button to launch Media Uploader
    if(current_user_can('upload_files')){
        ?>
        <p id="wpua-add-button">
            <button type="button" class="button" id="wpua-add" name="wpua-add" data-title="<?php _e('Choose Image'); ?>: <?php echo $user->display_name; ?>"><?php _e('Choose Image', 'wp-user-avatars'); ?></button>
        </p>
        <?php
    }
    ?>

    <p id="current-image-container">
        <?php echo $avatar_thumbnail; ?>
    </p>
    <button type="button" class="button" id="wpua-remove-button"<?php echo $hide_remove; ?>><?php _e('Remove Image', 'wp-user-avatars'); ?></button>
    <button type="button" class="button" id="wpua-undo-button" style="display: none;"><?php _e('Undo', 'wp-user-avatars'); ?></button>

    </td>
    </tr>
    </tbody>
    </table>
    <?php
}


/**
 * Enqueue necessary scripts and styles in the admin area
 * @param object $user
 */
function enqueue_scripts($user='') {

    wp_enqueue_style('wp-user-avatar', plugin_dir_url( __DIR__ ).'css/wp-user-avatar.css');

    if(current_user_can('upload_files')) {
        // Enqueue all scripts, styles, settings, and templates necessary to use all media JS APIs.
        wp_enqueue_media();
        wp_enqueue_script('wp-user-avatar', plugin_dir_url( __DIR__ ).'js/wp-user-avatar.js', array('jquery', 'media-editor'), false, true);
    }

    // Original user avatar
    $avatar_medium_src = includes_url().'images/default-avatar.png';
    wp_localize_script('wp-user-avatar', 'wpua_custom', array('avatar_thumb' => $avatar_medium_src));

}


/**
 * Render the section that allows to edit the default avatar
 * @return string
 */
function render_default_avatar_editing_section() {

    // Set avatar_list variable
    $select_default_avatar_html = '<div id="wpua-select-default-avatar">';

    // Default WP Mystery Avatar
    $selected = (get_option('avatar_default') == 'mystery') ? ' checked="checked" ' : "";
    $select_default_avatar_html .= '<label>
    <input type="radio" name="avatar_default" value="mystery"'.$selected.'"/> 
    <img src="'.includes_url().'images/default-avatar.png" width="32" height="32">
    Mystery
    </label>';

    // Autogenerated SVG with first letter
    $selected = (get_option('avatar_default') == 'letter') ? ' checked="checked" ' : "";
    $select_default_avatar_html .= '<label>
    <input type="radio" name="avatar_default" value="letter"'.$selected.'"/> 
    <img src="'.get_svg_letter('A').'" width="32" height="32">
    First Letter
    </label>';

    // Custom Avatar
    if(!empty(get_option('wpua_default_avatar')) && wp_attachment_is_image(get_option('wpua_default_avatar'))) {
        $avatar_thumb_src = wp_get_attachment_image_src(get_option('wpua_default_avatar'), array(32,32));
        $avatar_thumb = '<img src="'.$avatar_thumb_src[0].'" width="32" height="32" />';
        $hide_remove = '';
    } else {
        $avatar_thumb = '<img src="'.plugin_dir_url( __DIR__ ).'img/image-placeholder.jpg" width="32" height="32" />';
        $hide_remove = ' style="display: none;"';
    }

    $selected = (get_option('avatar_default') == 'custom') ? ' checked="checked" ' : '';
    $select_default_avatar_html .= '<label><input type="radio" name="avatar_default" value="custom"'.$selected.'"/>
<p id="current-image-container">'.$avatar_thumb.'</p>'
        .__('Custom avatar', 'wp-user-avatar').'</label>
        <p id="wpua-edit">
        <button type="button" class="button" id="wpua-add" name="wpua-add" data-avatar_default="true" data-title="'.__('Choose Image', 'wp-user-avatars').': '.__('Default Avatar', 'wp-user-avatars').'">'.__('Choose Image', 'wp-user-avatars').'</button>
        <button type="button" class="button" id="wpua-remove-button"'.$hide_remove.'>'.__('Remove', 'wp-user-avatars').'</button>
        <button type="button" class="button" id="wpua-undo-button" style="display: none;">'.__('Undo', 'wp-user-avatars').'</button>
        </p>
        <input type="hidden" id="wp-user-avatar" name="wpua_default_avatar" value="'.get_option('wpua_default_avatar').'">
    </div>';


    return $select_default_avatar_html;

}


/**
 * Add default avatar_default to whitelist
 * @param array $options
 * @return array $options
 */
function whitelist_options($options) {
    $options['discussion'][] = 'wpua_default_avatar';
    return $options;
}
