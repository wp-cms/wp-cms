<section class="eyecatching-presentation">
    <?php

    // Display header if set
    if ( isset( $args['header'] ) ) {
	    echo '<h1>' . esc_html( $args['header'] ) . '</h1>';
    }

    // Display body text if set
    if ( isset( $args['body'] ) ) {
	    echo '<p>' . esc_html( $args['body'] ) . '</p>';
    }

    // Display button if set
    if ( isset( $args['button'] ) ) {
	    echo '<a href="' . esc_url( $args['button']['url'] ) . '" class="awesome-button dark"><span><strong>' . esc_html( $args['button']['title'] ) . '</span></strong></a>';
    }

    ?>
</section>
