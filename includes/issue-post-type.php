<?php

namespace WSUWP\Issue_Builder\Issue_Post_Type;

add_action( 'init', __NAMESPACE__ . '\\register_issue_post_type' );
add_action( 'admin_init', __NAMESPACE__ . '\\register_builder_support' );
add_action( 'current_screen', __NAMESPACE__ . '\\remove_builder_sections' );
add_action( 'current_screen', __NAMESPACE__ . '\\add_builder_sections' );
add_filter( 'get_post_metadata', __NAMESPACE__ . '\\force_page_builder_meta', 10, 3 );
add_filter( 'spine_builder_force_builder', __NAMESPACE__ . '\\force_builder' );
add_filter( 'make_will_be_builder_page', __NAMESPACE__ . '\\force_builder' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\admin_enqueue_scripts' );

/**
 * Returns the issue post type slug.
 *
 * @since 0.0.1
 *
 * @return string The issue post type slug.
 */
function slug() {
	return 'issue';
}

/**
 * Registers a post type for displaying a collection of posts as an issue.
 *
 * @since 0.0.1
 */
function register_issue_post_type() {
	$args = array(
		'labels' => array(
			'name' => 'Issues',
			'singular_name' => 'Issue',
			'all_items' => 'All Issues',
			'view_item' => 'View Issue',
			'add_new_item' => 'Add New Issue',
			'add_new' => 'Add New',
			'edit_item' => 'Edit Issue',
			'update_item' => 'Update Issue',
			'search_items' => 'Search Issues',
			'not_found' => 'Not found',
			'not_found_in_trash' => 'Not found in Trash',
		),
		'description' => 'A post type for displaying collections of posts as issues',
		'public' => true,
		'hierarchical' => false,
		'menu_position' => 5,
		'menu_icon' => 'dashicons-book-alt',
		'supports' => array(
			'title',
			'editor',
			'revisions',
		),
		'taxonomies' => array(),
		'has_archive' => true,
	);

	register_post_type( slug(), $args );
}

/**
 * Adds support for the page builder to issues.
 *
 * @since 0.0.1
 */
function register_builder_support() {
	add_post_type_support( slug(), 'make-builder' );
}

/**
 * Removes sections defined for the default implemenation of the page builder.
 *
 * @since 0.0.1
 */
function remove_builder_sections( $current_screen ) {
	if ( slug() !== $current_screen->id ) {
		return;
	}

	ttfmake_remove_section( 'wsuwpsingle' );
	ttfmake_remove_section( 'wsuwphalves' );
	ttfmake_remove_section( 'wsuwpsidebarleft' );
	ttfmake_remove_section( 'wsuwpsidebarright' );
	ttfmake_remove_section( 'wsuwpthirds' );
	ttfmake_remove_section( 'wsuwpquarters' );
	ttfmake_remove_section( 'wsuwpheader' );
	ttfmake_remove_section( 'banner' );
}

/**
 * Adds custom sections for the magazine issue implementation of the page builder.
 *
 * @since 0.0.1
 */
function add_builder_sections( $current_screen ) {
	if ( slug() !== $current_screen->id ) {
		return;
	}

	ttfmake_add_section(
		'wsuwp_issue_single',
		'Single',
		plugins_url( '/images/single.png', dirname( __FILE__ ) ),
		'A single column.',
		__NAMESPACE__ . '\\save_columns',
		'admin/issue-columns',
		'front-end/issue-columns',
		100,
		plugin_dir_path( __DIR__ ) . 'builder-templates/'
	);

	ttfmake_add_section(
		'wsuwp_issue_halves',
		'Halves',
		plugins_url( '/images/halves.png', dirname( __FILE__ ) ),
		'Two equal columns.',
		__NAMESPACE__ . '\\save_columns',
		'admin/issue-columns',
		'front-end/issue-columns',
		200,
		plugin_dir_path( __DIR__ ) . 'builder-templates/'
	);

	ttfmake_add_section(
		'wsuwp_issue_thirds',
		'Thirds',
		plugins_url( '/images/thirds.png', dirname( __FILE__ ) ),
		'Three equal columns.',
		__NAMESPACE__ . '\\save_columns',
		'admin/issue-columns',
		'front-end/issue-columns',
		300,
		plugin_dir_path( __DIR__ ) . 'builder-templates/'
	);
}

/**
 * Output the input fields for configuring article displays.
 *
 * @param string $column_name
 * @param array $section_data
 * @param int $column
 */
function article_configuration_output( $column_name, $section_data, $column = false ) {
	if ( $column ) {
		$headline = ( isset( $section_data['data']['columns'][ $column ]['headline'] ) ) ? $section_data['data']['columns'][ $column ]['headline'] : '';
		$subtitle = ( isset( $section_data['data']['columns'][ $column ]['subtitle'] ) ) ? $section_data['data']['columns'][ $column ]['subtitle'] : '';
		$bg_id = ( isset( $section_data['data']['columns'][ $column ]['background-id'] ) ) ? $section_data['data']['columns'][ $column ]['background-id'] : '';
		$bg_image = ( ! empty( $bg_id ) ) ? wp_get_attachment_image_src( $bg_id, 'full' )[0] : '';
		$bg_size = ( isset( $section_data['data']['columns'][ $column ]['background-size'] ) ) ? $section_data['data']['columns'][ $column ]['background-size'] : '';
		$bg_position = ( isset( $section_data['data']['columns'][ $column ]['background-position'] ) ) ? $section_data['data']['columns'][ $column ]['background-position'] : '';
	} else {
		$headline = ( isset( $section_data['data']['headline'] ) ) ? $section_data['data']['headline'] : '';
		$subtitle = ( isset( $section_data['data']['subtitle'] ) ) ? $section_data['data']['subtitle'] : '';
		$background = ( isset( $section_data['data']['background-id'] ) ) ? $section_data['data']['background-id'] : '';
		$bg_size = ( isset( $section_data['data']['background-size'] ) ) ? $section_data['data']['background-size'] : '';
		$bg_position = ( isset( $section_data['data']['background-position'] ) ) ? $section_data['data']['background-position'] : '';
	}
	?>
	<div class="wsuwp-builder-meta">
		<label for="<?php echo esc_attr( $column_name ); ?>[headline]">Headline</label>
		<input type="text"
			   id="<?php echo esc_attr( $column_name ); ?>[headline]"
			   name="<?php echo esc_attr( $column_name ); ?>[headline]"
			   class="spine-builder-column-headline wsm-article-meta widefat"
			   value="<?php echo esc_attr( $headline ); ?>"/>
		<p class="description">Enter text to display in place of the original article headline or title.</p>
	</div>
	<div class="wsuwp-builder-meta">
		<label for="<?php echo esc_attr( $column_name ); ?>[subtitle]">Subtitle</label>
		<input type="text"
			   id="<?php echo esc_attr( $column_name ); ?>[subtitle]"
			   name="<?php echo esc_attr( $column_name ); ?>[subtitle]"
			   class="spine-builder-column-subtitle wsm-article-meta widefat"
			   value="<?php echo esc_attr( $subtitle ); ?>"/>
		<p class="description">Enter text to display as the article subtitle.</p>
	</div>
	<div class="wsuwp-builder-meta">
		<label>Background Image</label>
		<p class="hide-if-no-js">
			<input type="hidden"
				   id="<?php echo esc_attr( $column_name ); ?>[background-id]"
				   name="<?php echo esc_attr( $column_name ); ?>[background-id]"
				   class="spine-builder-column-background-id wsm-article-meta"
				   value="<?php echo esc_attr( $bg_id ); ?>" />
			<a href="#" class="spine-builder-column-set-background-image"><?php
				echo ( $bg_image ) ? '<img src="' . esc_url( $bg_image ) . '" />' : 'Set background image';
			?></a>
			<a href="#" class="spine-builder-column-remove-background-image"<?php if ( ! $bg_image ) { echo 'style="display:none;"'; } ?>>Remove background image</a>
		</p>
		<p class="description">Select an image to apply as the article background.</p>
	</div>
	<div class="wsuwp-builder-meta">
		<label for="<?php echo esc_attr( $column_name ); ?>[background-size]">Background Size</label>
		<select id="<?php echo esc_attr( $column_name ); ?>[background-size]"
				name="<?php echo esc_attr( $column_name ); ?>[background-size]"
				class="spine-builder-column-background-size wsm-article-meta">
			<option value="">Full</option>
			<?php
			$sizes = array(
				'thumbnail',
				'medium',
				'large',
				'spine-large_size',
			);
			foreach ( $sizes as $size ) {
				$image = wp_get_attachment_image_src( $bg_id, $size );
				if ( ! empty( $image ) ) {
					$name = ucfirst( $size ) . ' (' . $image[1] . 'x' . $image[2] . ')';
					?><option value="<?php echo esc_attr( $size ); ?>" <?php selected( esc_attr( $bg_size ), $size ); ?>><?php echo esc_attr( $name ); ?></option><?php
				}
			}
			?>
		</select>
		<p class="description">Set the size of the background image.</p>
	</div>
	<div class="wsuwp-builder-meta">
		<label for="<?php echo esc_attr( $column_name ); ?>[background-position]">Background Position</label>
		<select id="<?php echo esc_attr( $column_name ); ?>[background-position]"
				name="<?php echo esc_attr( $column_name ); ?>[background-position]"
				class="spine-builder-column-background-position wsm-article-meta">
			<option value="">Select</option>
			<option value="center" <?php selected( $bg_position, 'center' ); ?>>Center</option>
			<option value="center-top" <?php selected( $bg_position, 'center-top' ); ?>>Center-Top</option>
			<option value="right-top" <?php selected( $bg_position, 'right-top' ); ?>>Right-Top</option>
			<option value="right-center" <?php selected( $bg_position, 'right-center' ); ?>>Right-Center</option>
			<option value="right-bottom" <?php selected( $bg_position, 'right-bottom' ); ?>>Right-Bottom</option>
			<option value="center-bottom" <?php selected( $bg_position, 'center-bottom' ); ?>>Center-Bottom</option>
			<option value="left-bottom" <?php selected( $bg_position, 'left-bottom' ); ?>>Left-Bottom</option>
			<option value="left-center" <?php selected( $bg_position, 'left-center' ); ?>>Left-Center</option>
			<option value="left-top" <?php selected( $bg_position, 'left-top' ); ?>>Left-Top</option>
		</select>
		<p class="description">Change the positioning of the background image.</p>
	</div>
	<?php
}

/**
 * Cleans the data being passed from the save of an issue layout.
 *
 * @param array $data Array of data inputs being passed.
 *
 * @return array Clean data.
 */
function save_columns( $data ) {
	$clean_data = array();

	if ( isset( $data['columns-number'] ) ) {
		if ( in_array( $data['columns-number'], range( 1, 3 ), true ) ) {
			$clean_data['columns-number'] = $data['columns-number'];
		}
	}

	if ( isset( $data['columns-order'] ) ) {
		$clean_data['columns-order'] = array_map( array( 'TTFMake_Builder_Save', 'clean_section_id' ), explode( ',', $data['columns-order'] ) );
	}

	if ( isset( $data['columns'] ) && is_array( $data['columns'] ) ) {
		$background_sizes = array(
			'thumbnail',
			'medium',
			'large',
			'spine-large_size',
		);

		$background_positions = array(
			'center',
			'center-top',
			'right-top',
			'right-center',
			'right-bottom',
			'center-bottom',
			'left-bottom',
			'left-center',
			'left-top',
		);

		foreach ( $data['columns'] as $id => $item ) {
			if ( isset( $item['toggle'] ) ) {
				if ( in_array( $item['toggle'], array( 'visible', 'invisible' ), true ) ) {
					$clean_data['columns'][ $id ]['toggle'] = $item['toggle'];
				}
			}

			if ( isset( $item['post-id'] ) ) {
				$clean_data['columns'][ $id ]['post-id'] = sanitize_text_field( $item['post-id'] );
			}

			if ( isset( $item['headline'] ) ) {
				$clean_data['columns'][ $id ]['headline'] = sanitize_text_field( $item['headline'] );
			}

			if ( isset( $item['subtitle'] ) ) {
				$clean_data['columns'][ $id ]['subtitle'] = sanitize_text_field( $item['subtitle'] );
			}

			if ( isset( $item['background-id'] ) ) {
				$clean_data['columns'][ $id ]['background-id'] = sanitize_text_field( $item['background-id'] );
			}

			if ( isset( $item['background-size'] ) && in_array( $item['background-size'], $background_sizes, true ) ) {
				$clean_data['columns'][ $id ]['background-size'] = $item['background-size'];
			}

			if ( isset( $item['background-position'] ) && in_array( $item['background-position'], $background_positions, true ) ) {
				$clean_data['columns'][ $id ]['background-position'] = $item['background-position'];
			}
		}
	}

	if ( isset( $data['section-classes'] ) ) {
		$classes = explode( ' ', trim( $data['section-classes'] ) );
		$classes = array_map( 'sanitize_key', $classes );
		$classes = implode( ' ', $classes );
		$clean_data['section-classes'] = $classes;
	}

	if ( isset( $data['label'] ) ) {
		$clean_data['label'] = sanitize_text_field( $data['label'] );
	}

	$clean_data = apply_filters( 'spine_builder_save_columns', $clean_data, $data );

	return $clean_data;
}

/**
 * Forces the page builder to be enabled for this content type.
 *
 * @since 0.0.1
 *
 * @param null|int $check     Whether to short circuit the meta check. Null by default.
 * @param int      $object_id ID of the post being saved.
 * @param string   $meta_key  The current meta key being saved.
 *
 * @return null|int Unchanged check value or 1.
 */
function force_page_builder_meta( $check, $object_id, $meta_key ) {
	if ( '_ttfmake-use-builder' !== $meta_key ) {
		return $check;
	}

	$post = get_post( $object_id );

	if ( slug() === $post->post_type ) {
		return 1;
	}

	return $check;
}

/**
 * Hides the checkbox used to disable the page builder.
 *
 * The page builder interface is forced via meta key. Returning true
 * here will force the checkbox to be hidden as well.
 *
 * @since 0.0.1
 *
 * @param bool $use_builder Whether the page builder show be used.
 *
 * @return bool True if the magazine issue content type. False if not.
 */
function force_builder( $use_builder ) {
	if ( get_post_type() !== slug() ) {
		return $use_builder;
	}

	return true;
}

/**
 * Enqueues the scripts used for managing issue building.
 *
 * @since 0.0.1
 */
function admin_enqueue_scripts( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	if ( get_current_screen()->id !== slug() ) {
		return;
	}

	wp_enqueue_style( 'wsuwp-issue-admin', plugins_url( '/css/admin-edit-issue.css', dirname( __FILE__ ) ), array( 'ttfmake-builder' ), \WSUWP\Issue_Builder\version() );
}
