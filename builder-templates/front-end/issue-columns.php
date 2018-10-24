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

<section id="<?php echo esc_attr( $section_id ); ?>" class="<?php echo esc_attr( $section_classes ); ?>">

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

		/**
		 * Updated code to use with the shortcode
		 */

		$column_classes = array( 'column' );
		$column_classes[] = $column_count[ $count ];
		$column_classes[] = ( ! empty( $column['classes'] ) ) ? str_replace( ' ', ',', $column['classes'] ) : '';

		$shortcode_atts_array = array(
			'classes'              => implode( ' ', $column_classes ),
			'id'                   => ( ! empty( $column['post-id'] ) ) ? $column['post-id'] : '',
			'display-image'        => ( ! empty( $column['display-image'] ) ) ? $column['display-image'] : '',
			'header'               => ( ! empty( $column['header'] ) ) ? $column['header'] : '',
			'subheader'            => ( ! empty( $column['subheader'] ) ) ? $column['subheader'] : '',
			'background-position'  => ( ! empty( $column['background-position'] ) ) ? $column['background-position'] : '',
			'display-excerpt'      => ( isset( $column['display-excerpt'] ) ) ? $column['display-excerpt'] : '',
		);

		$issue_array = array();

		foreach ( $shortcode_atts_array as $key => $value ) {

			if ( '' !== $value ) {

				$issue_array[] = esc_attr( $key ) . '=\"' . esc_attr( $value ) . '\" ';

			} // End if
		} // End foreach

		?>[issue_article <?php echo wp_kses_post( implode( ' ', $issue_array ) ); ?>][/issue_article]<?php
		$count++;
	}
	?>

</section>
