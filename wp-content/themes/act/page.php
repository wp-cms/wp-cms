<?php get_header(); ?>

<?php

if ( have_rows( 'layouts' ) ) {
    while ( have_rows( 'layouts' ) ) {

        the_row();

        switch ( get_row_layout() ) {
            case 'hero':
                $args = array(
                        'background' => get_sub_field( 'background' ),
                        'text'       => get_sub_field( 'text' ),
                );
                get_template_part( 'layouts/hero', $args );
                break;
	        case 'eyecatching_presentation':
		        $args = array(
			        'header' => get_sub_field( 'header' ),
			        'body'   => get_sub_field( 'body' ),
			        'button' => get_sub_field( 'button' ),
		        );
		        get_template_part( 'layouts/eyecatching-presentation', $args );
		        break;
	        case 'sidebyside_text_image':
		        $args = array(
			        'text'          => get_sub_field( 'text' ),
			        'image'         => get_sub_field( 'image' ),
			        'display_order' => get_sub_field( 'display_order' ),
		        );
		        get_template_part( 'layouts/sidebyside-text-image', $args );
		        break;
	        case 'sidebyside_text_video':
		        $args = array(
			        'text'          => get_sub_field( 'text' ),
					// Passing this as an array of data, in case wanna use different providers in future
			        'video'         => array( 'youtube_video_id' => get_sub_field( 'youtube_video_id' ) ),
			        'display_order' => get_sub_field( 'display_order' ),
		        );
		        get_template_part( 'layouts/sidebyside-text-video', $args );
		        break;
        }

    }
}
?>

<?php get_footer(); ?>
