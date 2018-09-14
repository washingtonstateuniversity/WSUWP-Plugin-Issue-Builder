<?php
/*
Plugin Name: WSUWP Issue Builder
Version: 0.0.2
Description: A WordPress plugin for displaying specific collections of posts.
Author: washingtonstateuniversity, philcable
Author URI: https://web.wsu.edu/
Plugin URI: https://github.com/washingtonstateuniversity/WSUWP-Plugin-Issue-Builder
Text Domain: wsuwp-plugin-issue-builder
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// This plugin uses namespaces and requires PHP 5.3 or greater.
if ( version_compare( PHP_VERSION, '5.3', '<' ) ) {
	add_action(
		'admin_notices',
		function() {
			echo '<div class="error"><p>' . esc_html__( 'WSUWP Issue Builder requires PHP 5.3 to function properly. Please upgrade PHP or deactivate the plugin.', 'wsuwp-plugin-issue-builder' ) . '</p></div>';
		}
	);

	return;
} else {
	include_once __DIR__ . '/includes/wsuwp-issue-builder.php';
}
