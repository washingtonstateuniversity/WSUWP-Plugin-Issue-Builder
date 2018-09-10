<?php
global $ttfmake_section_data, $ttfmake_sections;

// Default to single if a section type has not been specified.
$section_type = ( isset( $ttfmake_section_data['section-type'] ) ) ? $ttfmake_section_data['section-type'] : 'wsuwp_issue_single';

if ( 'wsuwp_issue_single' === $section_type ) {
	$section_layout = 'single';
} elseif ( 'wsuwp_issue_halves' === $section_type ) {
	$section_layout = 'halves';
} elseif ( 'wsuwp_issue_thirds' === $section_type ) {
	$section_layout = 'thirds';
}

// Provide a list matching the number of columns to the selected section type.
$section_type_columns = array(
	'wsuwp_issue_thirds' => 3,
	'wsuwp_issue_halves' => 2,
	'wsuwp_issue_single' => 1,
);

// Retrieve data for the column being output.
$data_columns = spine_get_column_data( $ttfmake_section_data, $section_type_columns[ $section_type ] );

// Build the section ID.
$section_id = ( isset( $ttfmake_section_data['section-id'] ) ) ? sanitize_key( $ttfmake_section_data['section-id'] ) : 'builder-section-' . esc_attr( $ttfmake_section_data['id'] );
?>

<section id="<?php echo esc_attr( $section_id ); ?>" class="row <?php echo esc_attr( $section_layout ); ?>">

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

		$header = ( ! empty( $column['header'] ) ) ? $column['header'] : get_the_title( $column['post-id'] );
		$subheader = ( ! empty( $column['subheader'] ) ) ? $column['subheader'] : false;
		$bg_id = ( ! empty( $column['background-id'] ) ) ? $column['background-id'] : false;
		$bg_size = ( ! empty( $column['background-size'] ) ) ? esc_html( $column['background-size'] ) : 'full';
		$bg_position = ( ! empty( $column['background-position'] ) ) ? $column['background-position'] : 'center';
		?>

		<article class="column <?php echo esc_attr( $column_count[ $count ] ); ?>">

			<header>
				<h2><a href="<?php get_permalink( get_permalink( $column['post-id'] ) ); ?>"><?php echo esc_html( $header ); ?></a></h2>
				<?php if ( $subheader ) { ?><p class="subheader"><?php echo esc_html( $subheader ); ?></p><?php } ?>
			</header>

		</article>

		<?php
		$count++;
	}
	?>

</section>
