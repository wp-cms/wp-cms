<?php

namespace Core\ClonePosts;


/**
 * Add the custom Bulk Action to the select menus
 */
function admin_footer() {
    $options = maybe_unserialize( get_option('clone_posts_post_type') );

    if ( !is_array($options) ) {
        $options = ['post', 'page'];
    }

    if ( !in_array(  $GLOBALS['post_type'], $options ) ) {
        return;
    }
    ?>
    <script type="text/javascript">
        jQuery(function () {
            jQuery('<option>').val('clone').text('<?php _e('Clone')?>').appendTo("select[name='action']");
            jQuery('<option>').val('clone').text('<?php _e('Clone')?>').appendTo("select[name='action2']");
        });
    </script>
    <?php
}


/**
 * Handle the custom Bulk Action
 */
function bulk_action() {
    global $typenow;
    $post_type = $typenow;

    // get the action
    $wp_list_table = _get_list_table('WP_Posts_List_Table');
    $action = $wp_list_table->current_action();

    $allowed_actions = array("clone");
    if ( ! in_array( $action, $allowed_actions )) {
        return;
    }

    // security check
    check_admin_referer('bulk-posts');

    // make sure ids are submitted.  depending on the resource type, this may be 'media' or 'ids'
    if ( isset( $_REQUEST['post'] )) {
        $post_ids = array_map( 'intval', $_REQUEST['post'] );
    }

    if ( empty( $post_ids )) {
        return;
    }

    // this is based on wp-admin/edit.php
    $sendback = remove_query_arg( array( 'cloned', 'untrashed', 'deleted', 'ids' ), wp_get_referer() );
    if ( ! $sendback ) {
        $sendback = admin_url( "edit.php?post_type=$post_type" );
    }

    $pagenum = $wp_list_table->get_pagenum();
    $sendback = add_query_arg( 'paged', $pagenum, $sendback );

    switch ( $action ) {
        case 'clone':

            $cloned = 0;
            foreach ( $post_ids as $post_id ) {

                if ( !current_user_can('edit_post', $post_id) ) {
                    wp_die( __('You are not allowed to clone this post.', 'clone-posts') );
                }

                if ( ! clone_single( $post_id )) {
                    wp_die( __('Error cloning post.', 'clone-posts') );
                }

                $cloned++;
            }

            $sendback = add_query_arg( array( 'cloned' => $cloned, 'ids' => join(',', $post_ids) ), $sendback );
            break;

        default:
            return;
    }

    $sendback = remove_query_arg( array( 'action', 'action2', 'tags_input', 'post_author',
        'comment_status', 'ping_status', '_status',  'post', 'bulk_edit', 'post_view'), $sendback );

    wp_redirect($sendback);
    exit();
}


/**
 * Display an admin notice on the Posts page after cloning
 */
function admin_notices() {
    global $pagenow;

    if ($pagenow == 'edit.php' && ! isset($_GET['trashed'] )) {
        $cloned = 0;
        if ( isset( $_REQUEST['cloned'] ) && (int) $_REQUEST['cloned'] ) {
            $cloned = (int) $_REQUEST['cloned'];
        } elseif ( isset($_GET['cloned']) && (int) $_GET['cloned'] ) {
            $cloned = (int) $_GET['cloned'];
        }
        if ($cloned) {
            $message = sprintf( _n( 'Post cloned.', '%s posts cloned.', $cloned ), number_format_i18n( $cloned ) );
            echo "<div class=\"updated\"><p>{$message}</p></div>";
        }
    }
}


/**
 * Filters the array of row action links on the admin table.
 */
function post_row_actions( $actions, $post ) {
    global $post_type;

    $options = maybe_unserialize( get_option('clone_posts_post_type') );

    if ( !is_array($options) ) {
        $options = ['post', 'page'];
    }

    if ( !in_array( $post_type, $options ) ) {
        return $actions;
    }

    $url = remove_query_arg( array( 'cloned', 'untrashed', 'deleted', 'ids' ), "" );
    if ( ! $url ) {
        $url = admin_url( "?post_type=$post_type" );
    }
    $url = remove_query_arg( array( 'action', 'action2', 'tags_input', 'post_author',
        'comment_status', 'ping_status', '_status',  'post', 'bulk_edit', 'post_view'), $url );
    $url = add_query_arg( array( 'action' => 'clone-single', 'post' => $post->ID, 'redirect' => $_SERVER['REQUEST_URI'] ), $url );

    $actions['clone'] =  '<a href=\''.$url.'\'>'.__('Clone', 'clone-posts').'</a>';
    return $actions;
}