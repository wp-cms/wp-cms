<?php

// Prepare content
$text  = '<div class="text wysiwyg">' . $args['text'] . '</div>';
$image = '<img src="' . esc_url( $args['image']['url'] ) . '" alt="Side Image">';

?>


<section class="sidebyside-text-image <?php echo 'order-' . esc_attr( $args['display_order'] ); ?>">
    <?php
    if ( 'text-image' === $args['display_order'] ) {
        echo $text . $image;
    } else {
	    echo $image . $text;
    }
    ?>
</section>
