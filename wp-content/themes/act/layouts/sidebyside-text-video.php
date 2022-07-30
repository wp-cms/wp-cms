<?php

// Prepare content
$text  = '<div class="text wysiwyg">' . $args['text'] . '</div>';
$video = '
<iframe width="560" height="315" src="https://www.youtube.com/embed/' . esc_attr( $args['video']['youtube_video_id'] ) . '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';

?>


<section class="sidebyside-text-video <?php echo 'order-' . esc_attr( $args['display_order'] ); ?>">
    <?php
    if ( 'text-video' === $args['display_order'] ) {
        echo $text . $video;
    } else {
	    echo $video . $text;
    }
    ?>
</section>
