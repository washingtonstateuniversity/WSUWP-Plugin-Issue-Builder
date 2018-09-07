<?php

namespace WSUWP\Issue_Builder\Issue_Post_Type;

add_action( 'init', __NAMESPACE__ . '\\register_issue_post_type' );

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
