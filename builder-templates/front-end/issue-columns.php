<?php
global $ttfmake_section_data, $ttfmake_sections;

// Get the provided section ID or create a unique one.
$section_id = ( isset( $ttfmake_section_data['section-id'] ) ) ? sanitize_key( $ttfmake_section_data['section-id'] ) : 'builder-section-' . esc_attr( $ttfmake_section_data['id'] );

// Default to single if a section type has not been specified.
$section_type = ( isset( $ttfmake_section_data['section-type'] ) ) ? $ttfmake_section_data['section-type'] : 'wsuwp_issue_single';

// Start building section classes.
$section_classes = 'row';

// Add the class for the section layout type.
$section_classes .= str_replace( 'wsuwp_issue_', ' ', $section_type );

// Add any default builder or user-provided section classes.
$section_classes .= ( isset( $ttfmake_section_data['section-classes'] ) ) ? ' ' . $ttfmake_section_data['section-classes'] : '';

// Provide a list matching the number of columns to the selected section type.
$section_type_columns = array(
	'wsuwp_issue_thirds' => 3,
	'wsuwp_issue_halves' => 2,
	'wsuwp_issue_single' => 1,
);

// Retrieve data for the columns being output.
$data_columns = spine_get_column_data( $ttfmake_section_data, $section_type_columns[ $section_type ] );
?>

<section id="<?php echo esc_attr( $section_id ); ?>" class="row <?php echo esc_attr( $section_classes ); ?>">

	<?php
	if ( empty( $data_columns ) ) {
		return '';
	}

	// Output the column's number as part of a class.
	$column_count = array( 'one', 'two', 'three' );

	// Track the column count.
	$count = 0;

	foreach ( $data_columns as $column ) {
		if ( empty( $column['post-id'] ) ) {
			continue;
		}

		// Set up column classes.
		$column_classes = 'column ';
		$column_classes .= $column_count[ $count ];
		$column_classes .= ( ! empty( $column['classes'] ) ) ? ' ' . $column['classes'] : '';

		// Set up column styles (background image and positioning are handled inline).
		$bg_image = ( ! empty( $column['background-image'] ) ) ? $column['background-image'] : false;
		$column_styles = '';

		if ( $bg_image ) {
			$column_styles .= 'background-image: url(' . esc_url( $bg_image ) . ');';
			$bg_position = ( ! empty( $column['background-position'] ) ) ? str_replace( '-', ' ', $column['background-position'] ) : false;
			$column_styles .= ( $bg_position ) ? ' background-position: ' . $bg_position . ';' : '';
		}

		// Set up post text to display.
		$header = ( ! empty( $column['header'] ) ) ? $column['header'] : get_the_title( $column['post-id'] );
		$subheader = ( ! empty( $column['subheader'] ) ) ? $column['subheader'] : false;

		// Set up the display of an <img> element.
		$display_image = false;

		if ( ! empty( $column['display-image'] ) ) {
			if ( 'featured-image' === $column['display-image'] ) {
				$display_image = array(
					'class' => 'issue-post-featured-image',
					'src' => get_the_post_thumbnail_url( $column['post-id'], 'spine-large_size' ),
				);
			} elseif ( 'thumbnail-image' === $column['display-image'] && class_exists( 'MultiPostThumbnails' ) ) {
				$display_image = array(
					'class' => 'issue-post-thumbnail-image',
					'src' => MultiPostThumbnails::get_post_thumbnail_url( 'post', 'thumbnail-image', $column['post-id'], 'spine-large_size' ),
				);
			}
		}
		?>

		<article class="column <?php echo esc_attr( $column_classes ); ?>"<?php if ( ! empty( $column_styles ) ) { echo ' style="' . esc_attr( $column_styles ) . '"'; } ?>>

			<header>
				<h2><a href="<?php get_permalink( get_permalink( $column['post-id'] ) ); ?>"><?php echo esc_html( $header ); ?></a></h2>
				<?php if ( $subheader ) { ?><p class="subheader"><?php echo esc_html( $subheader ); ?></p><?php } ?>
			</header>

			<?php if ( $display_image ) { ?>
			<figure class="<?php echo esc_attr( $display_image['class'] ); ?>"><a href="" title="<?php echo esc_attr( $header ); ?>"><img src="<?php echo esc_url( $display_image['src'] ); ?>" /></a></figure>
			<?php } ?>

			<?php if ( ! empty( $column['display-excerpt'] ) && has_excerpt( $column['post-id'] ) ) { ?>
			<div class="issue-post-excerpt">
				<?php echo wp_kses_post( wpautop( get_the_excerpt( $column['post-id'] ) ) ); ?>
			</div>
			<?php } ?>

		</article>

		<?php
		$count++;
	}
	?>

</section>
