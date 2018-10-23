<?php

namespace WSUWP\Issue_Builder;

add_action( 'after_setup_theme', __NAMESPACE__ . '\\bootstrap' );

/**
 * Returns a version number for breaking cache.
 *
 * @since 0.0.1
 *
 * @return string The plugin version number.
 */
function version() {
	return '0.0.3';
}

/**
 * Starts things up.
 *
 * @since 0.0.1
 */
function bootstrap() {
	require_once __DIR__ . '/issue-post-type.php';
	require_once __DIR__ . '/issue-promo-shortcode.php';
}
