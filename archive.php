<?php
/**
 * Archive route wrapper.
 *
 * @package JRThemeFoundation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$active_filter = isset( $_GET['filter'] ) ? sanitize_key( $_GET['filter'] ) : 'all'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

$context = array(
	'archive_title'       => get_the_archive_title(),
	'archive_description' => get_the_archive_description(),
	'active_filter'       => $active_filter,
);

if ( class_exists( '\\Timber\\Timber' ) ) {
	$context['posts'] = \Timber\Timber::get_posts();
}

jr_theme_foundation_render_template( 'archive.twig', $context );
