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
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\wp_enqueue_scripts' );
add_action( 'add_meta_boxes_' . slug(), __NAMESPACE__ . '\\add_post_stage_meta_box' );
add_action( 'wp_ajax_set_issue_posts', __NAMESPACE__ . '\\ajax_callback' );
add_action( 'save_post_issue', __NAMESPACE__ . '\\save_issue', 10, 2 );
add_filter( 'template_include', __NAMESPACE__ . '\\default_issue_template', 99 );

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
			'thumbnail',
			'excerpt',
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
 * @since 0.0.2 Re-enabled the Header section.
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
 * Output the input fields for configuring post displays.
 *
 * @param string $column_name
 * @param array $section_data
 * @param int $column
 */
function post_configuration_output( $column_name, $section_data, $column = false ) {
	$column_classes = ( ! empty( $section_data['data']['columns'][ $column ]['classes'] ) ) ? $section_data['data']['columns'][ $column ]['classes'] : '';
	$header = ( ! empty( $section_data['data']['columns'][ $column ]['header'] ) ) ? $section_data['data']['columns'][ $column ]['header'] : '';
	$subheader = ( ! empty( $section_data['data']['columns'][ $column ]['subheader'] ) ) ? $section_data['data']['columns'][ $column ]['subheader'] : '';
	$display_image = ( ! empty( $section_data['data']['columns'][ $column ]['display-image'] ) ) ? $section_data['data']['columns'][ $column ]['display-image'] : '';
	$display_excerpt = ( ! empty( $section_data['data']['columns'][ $column ]['display-excerpt'] ) ) ? $section_data['data']['columns'][ $column ]['display-excerpt'] : '';
	$bg_image = ( ! empty( $section_data['data']['columns'][ $column ]['background-image'] ) ) ? $section_data['data']['columns'][ $column ]['background-image'] : '';
	$bg_position = ( ! empty( $section_data['data']['columns'][ $column ]['background-position'] ) ) ? $section_data['data']['columns'][ $column ]['background-position'] : '';
	?>

	<div class="wsuwp-builder-meta">
		<label for="<?php echo esc_attr( $column_name ); ?>[classes]">Column Classes</label>
		<input type="text"
			   id="<?php echo esc_attr( $column_name ); ?>[classes]"
			   name="<?php echo esc_attr( $column_name ); ?>[classes]"
			   class="spine-builder-column-classes widefat"
			   value="<?php echo esc_attr( $column_classes ); ?>"/>
		<p class="description">Enter space delimited class names here to apply them to the <code>article</code> element representing this post.</p>
	</div>

	<div class="wsuwp-builder-meta">
		<label for="<?php echo esc_attr( $column_name ); ?>[header]">Header</label>
		<input type="text"
			   id="<?php echo esc_attr( $column_name ); ?>[header]"
			   name="<?php echo esc_attr( $column_name ); ?>[header]"
			   class="spine-builder-column-header wsuwp-issue-post-meta widefat"
			   value="<?php echo esc_attr( $header ); ?>"/>
		<p class="description">Enter text to display in place of the original post title.</p>
	</div>

	<div class="wsuwp-builder-meta">
		<label for="<?php echo esc_attr( $column_name ); ?>[subheader]">Subheader</label>
		<input type="text"
			   id="<?php echo esc_attr( $column_name ); ?>[subheader]"
			   name="<?php echo esc_attr( $column_name ); ?>[subheader]"
			   class="spine-builder-column-subheader wsuwp-issue-post-meta widefat"
			   value="<?php echo esc_attr( $subheader ); ?>"/>
		<p class="description">Enter text to display as the post subheader.</p>
	</div>

	<div class="wsuwp-builder-meta">
		<label for="<?php echo esc_attr( $column_name ); ?>[display-image]">Post Image</label>
		<select id="<?php echo esc_attr( $column_name ); ?>[display-image]"
				name="<?php echo esc_attr( $column_name ); ?>[display-image]"
				class="spine-builder-column-display-image wsuwp-issue-post-meta">
			<option value="">Select</option>
			<option value="featured-image" <?php selected( $display_image, 'featured-image' ); ?>>Featured image</option>
			<option value="thumbnail-image" <?php selected( $display_image, 'thumbnail-image' ); ?>>Thumbnail image</option>
		</select>
		<p class="description">Select an image assigned to this post to display.</p>
	</div>

	<div class="wsuwp-builder-meta">
		<label for="<?php echo esc_attr( $column_name ); ?>[display-excerpt]">Display Post Excerpt</label>
		<input type="checkbox"
			   id="<?php echo esc_attr( $column_name ); ?>[display-excerpt]"
			   name="<?php echo esc_attr( $column_name ); ?>[display-excerpt]"
			   value="yes"
			   <?php checked( 'yes', $display_excerpt ); ?>
			   class="spine-builder-column-display-excerpt wsuwp-issue-post-meta" />
		</select>
		<p class="description">Display this post's manual excerpt.</p>
	</div>

	<div class="wsuwp-builder-meta">
		<label>Background Image</label>
		<p class="hide-if-no-js">
			<input type="hidden"
				   id="<?php echo esc_attr( $column_name ); ?>[background-image]"
				   name="<?php echo esc_attr( $column_name ); ?>[background-image]"
				   class="spine-builder-column-background-image wsuwp-issue-post-meta"
				   value="<?php echo esc_attr( $bg_image ); ?>" />
			<a href="#" class="spine-builder-column-set-background-image"><?php
				echo ( $bg_image ) ? '<img src="' . esc_url( $bg_image ) . '" />' : 'Set background image';
			?></a>
			<a href="#" class="spine-builder-column-remove-background-image"<?php if ( ! $bg_image ) { echo 'style="display:none;"'; } ?>>Remove background image</a>
		</p>
		<p class="description">Select an image to apply as the post background.</p>
	</div>

	<div class="wsuwp-builder-meta">
		<label for="<?php echo esc_attr( $column_name ); ?>[background-position]">Background Position</label>
		<select id="<?php echo esc_attr( $column_name ); ?>[background-position]"
				name="<?php echo esc_attr( $column_name ); ?>[background-position]"
				class="spine-builder-column-background-position wsuwp-issue-post-meta">
			<option value="">Select</option>
			<option value="left-top" <?php selected( $bg_position, 'left-top' ); ?>>Left top</option>
			<option value="left-center" <?php selected( $bg_position, 'left-center' ); ?>>Left center</option>
			<option value="left-bottom" <?php selected( $bg_position, 'left-bottom' ); ?>>Left bottom</option>
			<option value="center-top" <?php selected( $bg_position, 'center-top' ); ?>>Center top</option>
			<option value="center" <?php selected( $bg_position, 'center' ); ?>>Center center</option>
			<option value="center-bottom" <?php selected( $bg_position, 'center-bottom' ); ?>>Center bottom</option>
			<option value="right-top" <?php selected( $bg_position, 'right-top' ); ?>>Right top</option>
			<option value="right-center" <?php selected( $bg_position, 'right-center' ); ?>>Right center</option>
			<option value="right-bottom" <?php selected( $bg_position, 'right-bottom' ); ?>>Right bottom</option>
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

			if ( isset( $item['classes'] ) ) {
				$clean_data['columns'][ $id ]['classes'] = clean_classes( $item['classes'] );
			}

			if ( isset( $item['header'] ) ) {
				$clean_data['columns'][ $id ]['header'] = sanitize_text_field( $item['header'] );
			}

			if ( isset( $item['subheader'] ) ) {
				$clean_data['columns'][ $id ]['subheader'] = sanitize_text_field( $item['subheader'] );
			}

			if ( isset( $item['display-image'] ) ) {
				$clean_data['columns'][ $id ]['display-image'] = sanitize_text_field( $item['display-image'] );
			}

			if ( isset( $item['display-excerpt'] ) ) {
				$clean_data['columns'][ $id ]['display-excerpt'] = sanitize_text_field( $item['display-excerpt'] );
			}

			if ( isset( $item['background-image'] ) ) {
				$clean_data['columns'][ $id ]['background-image'] = sanitize_text_field( $item['background-image'] );
			}

			if ( isset( $item['background-position'] ) && in_array( $item['background-position'], $background_positions, true ) ) {
				$clean_data['columns'][ $id ]['background-position'] = $item['background-position'];
			}
		}
	}

	if ( isset( $data['section-classes'] ) ) {
		$clean_data['section-classes'] = clean_classes( $data['section-classes'] );
	}

	if ( isset( $data['label'] ) ) {
		$clean_data['label'] = sanitize_text_field( $data['label'] );
	}

	$clean_data = apply_filters( 'spine_builder_save_columns', $clean_data, $data );

	return $clean_data;
}

/**
 * Clean a passed input value of arbitrary classes.
 *
 * @since 0.0.1
 *
 * @param string $classes A string of arbitrary classes from a text input.
 *
 * @return string Clean, space delimited classes for output.
 */
function clean_classes( $classes ) {
	$classes = explode( ' ', trim( $classes ) );
	$classes = array_map( 'sanitize_key', $classes );
	$classes = implode( ' ', $classes );
	return $classes;
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
	wp_enqueue_script( 'wsuwp-issue-admin', plugins_url( '/js/admin-edit-issue.min.js', dirname( __FILE__ ) ), array( 'jquery-ui-draggable', 'jquery-ui-sortable' ), \WSUWP\Issue_Builder\version(), true );
}


/**
 * Enqueues the scripts used for public Issues.
 *
 * @since 0.0.6
 */
function wp_enqueue_scripts() {
	wp_enqueue_style( 'wsuwp-issue-public', plugins_url( '/css/public-issue.css', dirname( __FILE__ ) ), array(), \WSUWP\Issue_Builder\version() );
}

/**
 * Adds the post staging meta box used by the issue content type.
 *
 * @since 0.0.1
 * @since 0.0.2 Added the "spine-main-header" metabox.
 */
function add_post_stage_meta_box() {
	add_meta_box(
		'wsuwp-issue-posts',
		'Issue Posts',
		__NAMESPACE__ . '\\display_issue_posts_meta_box',
		slug(),
		'side'
	);

	add_meta_box(
		'spine-main-header',
		'Spine Main Header',
		array( new \Spine_Main_Header(), 'display_main_header_meta_box' ),
		slug()
	);
}

/**
 * Displays a staging area for loading/sorting issue posts.
 *
 * @since 0.0.1
 *
 * @param WP_Post $post Object for the post currently being edited.
 */
function display_issue_posts_meta_box( $post ) {
	wp_nonce_field( 'save-wsuwp-issue-build', '_wsuwp_issue_build_nonce' );

	$selected_start = get_post_meta( $post->ID, '_wsuwp_issue_post_start_date', true );
	$selected_end = get_post_meta( $post->ID, '_wsuwp_issue_post_end_date', true );
	$post_ids = get_post_meta( $post->ID, '_wsuwp_issue_staged_posts', true );
	$stage_posts = ( $post_ids ) ? implode( ',', $post_ids ) : '';

	$localized_data = array(
		'post_id' => $post->ID,
		'nonce' => wp_create_nonce( 'wsuwp-issue' ),
	);

	// Make any posts already assigned to this issue available to the JS.
	if ( $post_ids ) {
		$localized_data['items'] = build_issue_response( $post_ids );
	}

	wp_localize_script( 'wsuwp-issue-admin', 'wsuwp_issue', $localized_data );

	?>

	<input type="hidden" id="issue-staged-posts" name="_wsuwp_issue_staged_posts" value="<?php echo esc_attr( $stage_posts ); ?>" />

	<div class="issue-posts-loading-actions">

		<fieldset>

			<label for="post-start-date" class="label-responsive">Load posts from:</label>

			<select name="_wsuwp_issue_post_start_date" id="post-start-date">
				<option value="">&mdash; Select &mdash;</option>
				<?php post_date_options( $selected_start ); ?>
			</select><br />

			<label for="post-end-date" class="label-responsive">through:</label>

			<select name="_wsuwp_issue_post_end_date" id="post-end-date">
				<option value="">&mdash; Select &mdash;</option>
				<?php post_date_options( $selected_end ); ?>
			</select>

		</fieldset>

		<input type="button" value="Load Posts" id="load-issue-posts" class="button button-large button-secondary" />

	</div>

	<div id="wsuwp-issue-posts-stage" class="wsuwp-spine-builder-column"></div>

	<?php
}

/**
 * Creates the date options fields for querying posts.
 *
 * @since 0.0.1
 *
 * @param string $selected The issue's meta data for start or end date.
 */
function post_date_options( $selected = '' ) {
	global $wpdb, $wp_locale;

	$query = $wpdb->prepare( "SELECT DISTINCT YEAR( post_date ) AS year, MONTH( post_date ) AS month FROM $wpdb->posts WHERE post_type = %s AND post_status = 'publish' ORDER BY post_date DESC", 'post' );
	$last_changed = wp_cache_get_last_changed( 'posts' );
	$key = md5( $query );
	$key = "wsuwp_issue_builder_get_months:$key:$last_changed";
	$results = wp_cache_get( $key, 'posts' );

	if ( ! $results ) {
		$results = $wpdb->get_results( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		wp_cache_set( $key, $results, 'posts' );
	}

	if ( $results ) {
		$month_count = count( $results );

		if ( ! $month_count || ( 1 === $month_count && 0 === $results[0]->month ) ) {
			return;
		}

		foreach ( (array) $results as $date ) {
			if ( 0 === $date->year ) {
				continue;
			}

			$month = zeroise( $date->month, 2 );
			$value = $date->year . '-' . $month;
			$text = $wp_locale->get_month( $month ) . ' ' . $date->year;

			?><option value="<?php echo esc_attr( $value ); ?>"<?php selected( $selected, $value ); ?>><?php echo esc_html( $text ); ?></option><?php
		}
	}
}

/**
 * Builds the list of posts that should be included in an issue.
 *
 * @since 0.0.1
 *
 * @param array  $post_ids   List of specific post IDs to include.
 * @param string $start_date The start date to limit the query to.
 * @param string $end_date   The end date to limit the query to.
 *
 * @return array Containing information on each issue post.
 */
function build_issue_response( $post_ids = array(), $start_date = false, $end_date = false ) {
	$query_args = array(
		'posts_per_page' => 100,
	);

	// If an array of post IDs has been passed, use only those.
	if ( ! empty( $post_ids ) ) {
		$query_args['post__in'] = $post_ids;
		$query_args['orderby']  = 'post__in';
	}

	// If no post IDs have been passed and a date range was provided, use it.
	if ( empty( $post_ids ) && $start_date && $end_date ) {
		$query_args['date_query'] = array(
			array(
				'after' => array(
					'year' => date_i18n( 'Y', strtotime( $start_date ) ),
					'month' => date_i18n( 'n', strtotime( $start_date ) ),
				),
				'before' => array(
					'year' => date_i18n( 'Y', strtotime( $end_date ) ),
					'month' => date_i18n( 'n', strtotime( $end_date ) ),
				),
				'inclusive' => true,
			),
		);
	}

	$items = array();

	$issue_query = get_posts( $query_args );

	foreach ( $issue_query as $post ) {
		setup_postdata( $post );

		$thumbnail = false;

		if ( class_exists( 'MultiPostThumbnails' ) ) {
			$thumbnail = esc_url( \MultiPostThumbnails::get_post_thumbnail_url( 'post', 'thumbnail-image', $post->ID, 'spine-large_size' ) );
		}

		$items[] = array(
			'id' => $post->ID,
			'title' => $post->post_title,
			'featured_image' => esc_url( get_the_post_thumbnail_url( $post->ID, 'spine-large_size' ) ),
			'thumbnail_image' => $thumbnail,
			'excerpt' => wp_kses_post( wpautop( $post->post_excerpt ) ),
		);
	}

	wp_reset_postdata();

	return $items;
}

/**
 * The AJAX callback for pulling a list of posts intto an issue.
 *
 * $since 0.0.1
 */
function ajax_callback() {

	check_ajax_referer( 'wsuwp-issue', 'nonce' );

	if ( ! DOING_AJAX || ! isset( $_POST['action'] ) || 'set_issue_posts' !== $_POST['action'] ) {
		die();
	}

	$post_ids = ( isset( $_POST['post_ids'] ) ) ? explode( ',', $_POST['post_ids'] ) : array();
	$start_date = ( isset( $_POST['start_date'] ) ) ? date( 'Y-n', strtotime( $_POST['start_date'] ) ) : false;
	$end_date = ( isset( $_POST['end_date'] ) ) ? date( 'Y-n', strtotime( $_POST['end_date'] ) ) : false;

	echo wp_json_encode( build_issue_response( $post_ids, $start_date, $end_date ) );

	exit();
}

/**
 * Capture the meta values for an issue.
 *
 * @since 0.0.1
 *
 * @param int     $post_id ID of the current post being saved.
 * @param WP_Post $post    Object of the current post being saved.
 */
function save_issue( $post_id, $post ) {
	if ( ! isset( $_POST['_wsuwp_issue_build_nonce'] ) || ! wp_verify_nonce( $_POST['_wsuwp_issue_build_nonce'], 'save-wsuwp-issue-build' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( 'auto-draft' === $post->post_status ) {
		return;
	}

	if ( ! empty( $_POST['_wsuwp_issue_staged_posts'] ) ) {
		$issue_staged_articles = explode( ',', $_POST['_wsuwp_issue_staged_posts'] );
		$issue_staged_articles = array_map( 'absint', $issue_staged_articles );

		update_post_meta( $post_id, '_wsuwp_issue_staged_posts', $issue_staged_articles );
	} else {
		delete_post_meta( $post_id, '_wsuwp_issue_staged_posts' );
	}

	if ( ! empty( $_POST['_wsuwp_issue_post_start_date'] ) ) {
		update_post_meta( $post_id, '_wsuwp_issue_post_start_date', sanitize_text_field( $_POST['_wsuwp_issue_post_start_date'] ) );
	} else {
		delete_post_meta( $post_id, '_wsuwp_issue_post_start_date' );
	}

	if ( ! empty( $_POST['_wsuwp_issue_post_end_date'] ) ) {
		update_post_meta( $post_id, '_wsuwp_issue_post_end_date', sanitize_text_field( $_POST['_wsuwp_issue_post_end_date'] ) );
	} else {
		delete_post_meta( $post_id, '_wsuwp_issue_post_end_date' );
	}
}

/**
 * Serves the issue content using the builder template.
 *
 * @since 0.0.1
 *
 * @param string $template The path of the template to include.
 *
 * @return string The path of the template to include.
 */
function default_issue_template( $template ) {
	if ( ! is_singular( 'issue' ) ) {
		return $template;
	}

	$new_template = locate_template( array( 'template-builder.php' ) );
	$template = ( ! empty( $new_template ) ) ? $new_template : $template;

	return $template;
}
