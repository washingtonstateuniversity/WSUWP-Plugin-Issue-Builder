<?php
spine_load_section_header();

global $ttfmake_section_data, $ttfmake_is_js_template;

if ( 'wsuwp_issue_thirds' === $ttfmake_section_data['section']['id'] ) {
	$wsuwp_range = 3;
} elseif ( 'wsuwp_issue_halves' === $ttfmake_section_data['section']['id'] ) {
	$wsuwp_range = 2;
} else {
	$wsuwp_range = 1;
}

$section_name = ttfmake_get_section_name( $ttfmake_section_data, $ttfmake_is_js_template );
$section_order = ( ! empty( $ttfmake_section_data['data']['columns-order'] ) ) ? $ttfmake_section_data['data']['columns-order'] : range( 1, $wsuwp_range );
$section_state = ( isset( $ttfmake_section_data['data']['state'] ) ) ? $ttfmake_section_data['data']['state'] : 'open';
$j = 1;
?>
<div class="wsuwp-issue-stage">
	<?php
	foreach ( $section_order as $key => $i ) :

		$column_name = $section_name . '[columns][' . $i . ']';
		$post_id = ( isset( $ttfmake_section_data['data']['columns'][ $i ]['post-id'] ) ) ? $ttfmake_section_data['data']['columns'][ $i ]['post-id'] : '';
		$visible = ( isset( $ttfmake_section_data['data']['columns'][ $i ]['toggle'] ) ) ? $ttfmake_section_data['data']['columns'][ $i ]['toggle'] : 'visible';

		if ( ! in_array( $visible, array( 'visible', 'invisible' ), true ) ) {
			$visible = 'visible';
		}

		$column_style = ( 'invisible' === $visible ) ? ' display: none;' : '';
		$toggle_class = ( 'invisible' === $visible ) ? ' wsuwp-toggle-closed' : '';
		?>
		<div class="wsuwp-spine-builder-column wsuwp-spine-builder-column-position-<?php echo esc_attr( $j ); ?>" data-id="<?php echo esc_attr( $i ); ?>">
			<input type="hidden" class="wsuwp-column-visible wsuwp-issue-post-meta" name="<?php echo esc_attr( $column_name ); ?>[toggle]" value="<?php echo esc_attr( $visible ); ?>" aria-hidden="true" />
			<input type="hidden" class="wsuwp-column-post-id wsuwp-issue-post-meta" name="<?php echo esc_attr( $column_name ); ?>[post-id]" value="<?php echo esc_attr( $post_id ); ?>" aria-hidden="true" />
			<div class="spine-builder-column-overlay">
				<div class="spine-builder-column-overlay-wrapper">
					<div class="spine-builder-overlay-header">
						<div class="spine-builder-overlay-title">Configure Post</div>
						<div class="spine-builder-column-overlay-close">Done</div>
					</div>
					<div class="spine-builder-overlay-body">
						<?php WSUWP\Issue_Builder\Issue_Post_Type\post_configuration_output( $column_name, $ttfmake_section_data, $j ); ?>
					</div>
				</div>
			</div>

			<?php
			if ( $post_id ) :
				$classes = ( isset( $ttfmake_section_data['data']['columns'][ $i ]['classes'] ) ) ? $ttfmake_section_data['data']['columns'][ $i ]['classes'] : '';
				$header = ( isset( $ttfmake_section_data['data']['columns'][ $i ]['header'] ) ) ? $ttfmake_section_data['data']['columns'][ $i ]['header'] : get_the_title( $post_id );
				$subheader = ( isset( $ttfmake_section_data['data']['columns'][ $i ]['subheader'] ) ) ? $ttfmake_section_data['data']['columns'][ $i ]['subheader'] : '';
				$display_image = ( isset( $ttfmake_section_data['data']['columns'][ $i ]['display-image'] ) ) ? $ttfmake_section_data['data']['columns'][ $i ]['display-image'] : '';
				$display_excerpt = ( isset( $ttfmake_section_data['data']['columns'][ $i ]['display-excerpt'] ) ) ? $ttfmake_section_data['data']['columns'][ $i ]['display-excerpt'] : '';
				$bg_image = ( isset( $ttfmake_section_data['data']['columns'][ $i ]['background-image'] ) ) ? $ttfmake_section_data['data']['columns'][ $i ]['background-image'] : '';
				$bg_position = ( isset( $ttfmake_section_data['data']['columns'][ $i ]['background-position'] ) ) ? $ttfmake_section_data['data']['columns'][ $i ]['background-position'] : '';

				// Set up post styles.
				$post_styles = ( ! empty( $bg_image ) ) ? 'background-image: url(' . esc_url( $bg_image ) . ');' : '';
				$post_styles .= ( ! empty( $bg_position ) ) ? ' background-position: ' . esc_attr( str_replace( '-', ' ', $bg_position ) ) . ';' : '';

				// Set up post classes.
				$post_classes = 'wsuwp-issue-post-body wsuwp-column-content';
				$post_classes .= ( ! empty( $classes ) ) ? ' ' . $classes : '';
				$post_classes .= ( in_array( $display_image, array( 'featured-image', 'thumbnail-image' ), true ) ) ? ' display-' . $display_image : '';
				$post_classes .= ( 'yes' === $display_excerpt ) ? ' display-excerpt' : '';

				?>
				<div id="issue-post-<?php echo esc_attr( $post_id ); ?>"
					class="issue-post"
					data-classes="<?php echo esc_attr( $classes ); ?>"
					data-header="<?php echo esc_attr( $header ); ?>"
					data-subheader="<?php echo esc_attr( $subheader ); ?>"
					data-display-image="<?php echo esc_attr( $display_image ); ?>"
					data-display-excerpt="<?php echo esc_attr( $display_excerpt ); ?>"
					data-background-image="<?php echo esc_url( $bg_image ); ?>"
					data-background-position="<?php echo esc_attr( $bg_position ); ?>">

					<div class="ttfmake-sortable-handle ui-sortable-handle" title="Drag and drop this post into place">

						<a href="#" class="spine-builder-column-configure">
							<span>Configure this post</span>
						</a>

						<a href="#" class="wsuwp-column-toggle" title="Click to toggle">
							<div class="handlediv<?php echo esc_attr( $toggle_class ); ?>" aria-expanded="true"></div>
						</a>

						<div class="wsuwp-builder-column-title"><?php echo get_the_title( $post_id ); ?></div>

					</div>

					<article class="<?php echo esc_attr( $post_classes ); ?>" style="<?php echo esc_attr( $post_styles ); ?>">

						<header>
							<h2><?php echo esc_html( $header ); ?></h2>
							<p class="subheader"><?php echo esc_html( $subheader ); ?></p>
						</header>

						<figure class="featured-image">
							<img src="<?php echo esc_url( get_the_post_thumbnail_url( $post_id, 'spine-large_size' ) ); ?>" />
						</figure>

						<figure class="thumbnail-image">
							<img src="<?php echo esc_url( MultiPostThumbnails::get_post_thumbnail_url( 'post', 'thumbnail-image', $post_id, 'spine-large_size' ) ); ?>" />
						</figure>

						<div class="excerpt">
							<?php if ( has_excerpt() ) { echo wp_kses_post( wpautop( get_the_excerpt( $post_id ) ) ); } ?>
						</div>

					</article>
				</div>
				<?php
			endif;
			?>

		</div>
		<?php
		$j++;
	endforeach;
	?>
</div>

<div class="clear"></div>

<div class="spine-builder-overlay">
	<div class="spine-builder-overlay-wrapper">
		<div class="spine-builder-overlay-header">
			<div class="spine-builder-overlay-title">Configure Section</div>
			<div class="spine-builder-overlay-close">Done</div>
		</div>
		<div class="spine-builder-overlay-body">
			<?php
			spine_output_builder_section_classes( $section_name, $ttfmake_section_data );
			spine_output_builder_section_label( $section_name, $ttfmake_section_data );
			do_action( 'spine_output_builder_section', $section_name, $ttfmake_section_data, 'columns' );
			?>
		</div>
	</div>
</div>

<input type="hidden" value="<?php echo esc_attr( implode( ',', $section_order ) ); ?>" name="<?php echo esc_attr( $section_name ); ?>[columns-order]" class="wsuwp-spine-builder-columns-order" />

<input type="hidden" class="ttfmake-section-state" name="<?php echo esc_attr( $section_name ); ?>[state]" value="<?php echo esc_attr( $section_state ); ?>" />
<?php

spine_load_section_footer();
