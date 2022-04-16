<?php

namespace Core\ClonePosts;

/**
 * Register the admin settings page
 */
function admin_page() {

    add_options_page(
        'Clone Posts Settings',
        'Clone Posts',
        'manage_options',
        'clone-posts-options',
        'Core\ClonePosts\render_settings_page',
        null
    );

}


/**
 * Render the settings page content
 */
function render_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('Clone Posts Settings', 'clone-posts'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'clone_post_settings' );
            do_settings_sections( 'clone-posts-options' );
            submit_button();
            ?>
        </form>
    </div>
    <?php
}


/**
 * Register the actual settings
 */
function register_settings() {

    add_settings_section(
        'clone_posts_settings_section',
        '',
        '',
        'clone-posts-options'
    );

    register_setting(
        'clone_post_settings',
        'clone_posts_post_status',
        'sanitize_text_field'
    );

    add_settings_field(
        'clone_posts_post_status',
        'Post Status',
        'Core\ClonePosts\option_post_status',
        'clone-posts-options',
        'clone_posts_settings_section',
        array(
            'label_for' => 'clone_posts_post_status',
            'class' => 'clone-posts',
        )
    );

    register_setting(
        'clone_post_settings',
        'clone_posts_post_date',
        'sanitize_text_field'
    );

    add_settings_field(
        'clone_posts_post_date',
        'Post Date',
        'Core\ClonePosts\option_post_date',
        'clone-posts-options',
        'clone_posts_settings_section',
        array(
            'label_for' => 'clone_posts_post_date',
            'class' => 'clone-posts',
        )
    );

    register_setting(
        'clone_post_settings',
        'clone_posts_post_type',
        'Core\ClonePosts\sanitize_array'
    );

    add_settings_field(
        'clone_posts_post_type',
        'Post Type',
        'Core\ClonePosts\option_post_type',
        'clone-posts-options',
        'clone_posts_settings_section',
        array(
            'label_for' => 'clone_posts_post_type',
            'class' => 'clone-posts',
        )
    );

}


/**
 * Field for Post Status option
 */
function option_post_status() {
    $option = get_option('clone_posts_post_status');
    ?>
    <?php _e('Select a default status for the cloned post:', 'clone-posts'); ?><br><br>
    <select name="clone_posts_post_status" id="clone_posts_post_status">
        <option value="draft" <?php selected($option, 'draft'); ?>><?php _e('Draft', 'clone-posts'); ?></option>
        <option value="publish" <?php selected($option, 'publish'); ?>><?php _e('Publish', 'clone-posts'); ?></option>
        <option value="private" <?php selected($option, 'private'); ?>><?php _e('Private', 'clone-posts'); ?></option>
        <option value="pending" <?php selected($option, 'pending'); ?>><?php _e('Pending', 'clone-posts'); ?></option>
    </select>
    <?php
}


/**
 * Field for Post Date option
 */
function option_post_date() {
    $option = get_option('clone_posts_post_date');
    ?>
    <?php _e('Select a default date for the cloned post:', 'clone-posts'); ?><br><br>
    <select name="clone_posts_post_date" id="clone_posts_post_date">
        <option value="current" <?php selected($option, 'current'); ?>><?php _e('Current time', 'clone-posts'); ?></option>
        <option value="original" <?php selected($option, 'original'); ?>><?php _e('Same as original', 'clone-posts'); ?></option>
    </select>
    <?php
}


/**
 * Field for Post Type option
 */
function option_post_type() {
    $options = maybe_unserialize( get_option('clone_posts_post_type') );
    if ( !is_array($options) ) {
        $options = ['post', 'page'];
    }
    $exclude_cpt = ['attachment'];
    $post_types = get_post_types( array( 'public' => true, ), 'objects', 'and' );
    echo '<fieldset>';
    if ( $post_types ) {
        echo __('Select the post types that should allow the cloning feature:', 'clone-posts').'<br><br>';
        foreach ( $post_types as $post_type ) {
            if ( !in_array($post_type->name, $exclude_cpt) ) {
                ?>
                <div>
                    <input type="checkbox" name="clone_posts_post_type[]" value="<?php echo $post_type->name ?>" id="post_type_<?php echo $post_type->name ?>" <?php checked( in_array( $post_type->name, $options ), 1 ); ?>>
                    <label for="post_type_<?php echo $post_type->name; ?>"><?php echo $post_type->labels->name; ?></label>
                </div>
                <?php
            }
        }
    }else{
        _e('There are no post types available.', 'clone-posts');
    }
    echo '</fieldset>';
}

/**
 * A custom sanitization function for arrays.
 *
 * @since    2.0.0
 * @param    array    $input        The posted array.
 * @return   array    $output	    The array sanitized.
 */
function sanitize_array( $input ) {
    $output = array();
    foreach ( $input as $key => $val ) {
        $output[ $key ] = ( isset( $input[ $key ] ) ) ? sanitize_text_field( $val ) : '';
    }
    return $output;
}