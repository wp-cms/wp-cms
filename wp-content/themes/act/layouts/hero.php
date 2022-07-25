<section class="hero">
	<img src="<?php echo esc_url( $args['background']['image']['url'] ); ?>" alt="Hero Background"
    style="object-position: <?php echo esc_attr( $args['background']['position'] ); ?>;">
	<div class="content">
		<h1 class="font-secondary"><?php echo esc_html( $args['text']['header'] ); ?></h1>
        <p><?php echo esc_html( $args['text']['body'] ); ?></p>
	</div>
</section>
