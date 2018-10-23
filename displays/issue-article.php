<article class="issue-article <?php echo esc_attr( $column_classes ); ?>"<?php if ( ! empty( $column_styles ) ) { echo ' style="' . esc_attr( $column_styles ) . '"'; } ?>>
	<?php if ( $img_src ) { ?>
	<figure class="<?php echo esc_attr( $display_image ); ?>" style="background-image:url(<?php echo esc_url( $img_src ); ?> )"><img src="<?php echo esc_url( $img_src ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>"></figure>
	<?php } ?>
	<header>
		<h2><?php echo esc_html( $header ); ?></h2>
		<?php if ( $subheader ) { ?><p class="subheader"><?php echo esc_html( $subheader ); ?></p><?php } ?>
	</header>
	<?php if ( ! empty( $excerpt ) ) { ?>
	<div class="issue-post-excerpt">
		<?php echo wp_kses_post( $excerpt ); ?>
	</div>
	<?php } ?>
	<a class="article-link" href="<?php echo esc_url( $link ); ?>">View <?php echo esc_attr( $header ); ?></a>
</article>