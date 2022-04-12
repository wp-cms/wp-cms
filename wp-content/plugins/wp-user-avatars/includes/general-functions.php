<?php

namespace Core\UserAvatar;

/**
 * Filter the native WP get_avatar() function
 * @param string $avatar
 * @param string|object $id_or_email
 * @param string $size
 * @param string $alt
 * @return string
 */
function get_avatar_filter($avatar, $id_or_email='', $size='', $alt='') {

    // Ignore native $avatar and return our own
    return get_user_avatar($id_or_email, $size, $alt);
}


/**
 * Retrieve the default image src (either custom set by admin or WP default)
 * @param $size
 * @return array
 */
function default_image($size) {

    $default_image_details = array();

    // Custom Default Avatar
    if(!empty(get_option('wpua_default_avatar')) && wp_attachment_is_image(get_option('wpua_default_avatar'))) {

        // Get image source
        $default_image_src = wp_get_attachment_image_src(get_option('wpua_default_avatar'), array($size,$size));

        // Image src
        $default = $default_image_src[0];
        $default_image_details['dimensions'] = ' width="'.$default_image_src[1].'" height="'.$default_image_src[2].'"';

    // Use WP Default
    } else {
        $default = includes_url().'images/default-avatar.png';
        $default_image_details['dimensions'] = ' width="'.$size.'" height="'.$size.'"';
    }

    $default_image_details['size'] = $size;
    $default_image_details['src'] = $default;

    return $default_image_details;

}

/**
 * Retrieve user_avatar for a given user id or email
 * @since 1.0
 * @param int|string $id_or_email
 * @return bool
 */
function get_user_avatar_meta($id_or_email='') {

    if(is_object($id_or_email)) {
        if(!empty($id_or_email->comment_author_email)) {
            $user = get_user_by('email', $id_or_email->comment_author_email);
        }else{
            return false;
        }
    } else {
        // Find user by ID or e-mail address
        $user = is_numeric($id_or_email) ? get_user_by('id', $id_or_email) : get_user_by('email', $id_or_email);
    }

    // Return false if no user found
    if(!$user){
        return false;
    }

    // Get the custom avatar if set
    $custom_avatar = get_user_meta($user->ID, 'user_avatar', true);

    // Return whether a custom avatar is set
    return !empty($custom_avatar) ? $custom_avatar : false;

}


/**
 * Check if user has an avatar set with this plugin
 * @since 1.0
 * @param int|string $id_or_email
 * @return bool
 */
function has_wp_user_avatar($id_or_email='') {

    $custom_avatar = get_user_avatar_meta($id_or_email);

    // Return whether a custom avatar is set
    return !empty($custom_avatar);

}

/**
 * Get the user avatar or return the default one
 * @param string $id_or_email
 * @param string $size
 * @param string $align
 * @param string $alt
 * @return mixed|void
 */
function get_user_avatar($id_or_email='', $size='96', $align='', $alt='') {

    $custom_avatar = get_user_avatar_meta($id_or_email);

    // Create alignment class
    $alignclass = !empty($align) && ($align == 'left' || $align == 'right' || $align == 'center') ? ' align'.$align : ' alignnone';

    if(!empty($custom_avatar) && wp_attachment_is_image($custom_avatar)) {

        // Numeric size use size array
        $get_size = is_numeric($size) ? array($size,$size) : $size;

        // Get image src
        $image_data = wp_get_attachment_image_src($custom_avatar, $get_size);
        $image_src = $image_data[0];

        // Add dimensions to img only if numeric size was specified
        $dimensions = is_numeric($size) ? ' width="'.$image_data[1].'" height="'.$image_data[2].'"' : "";

    } else {

        $default_image_details = default_image($size);
        $image_src = $default_image_details['src'];
        $dimensions = $default_image_details['dimensions'];

    }

    // Construct the img tag
    $avatar = '<img src="' . $image_src . '" ' . $dimensions . ' alt="'.$alt.'" class="avatar avatar-'.$size.' wp-user-avatar wp-user-avatar-' . $size . $alignclass . ' photo" />';

    /**
     * Filter get_user_avatar
     * @since 1.9
     * @param string $avatar
     * @param int|string $id_or_email
     * @param int|string $size
     * @param string $align
     * @param string $alt
     */
    return apply_filters('get_user_avatar', $avatar, $id_or_email, $size, $align, $alt);
}