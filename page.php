<?php
/**
 * Page route wrapper.
 *
 * @package JRThemeFoundation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$context = array();
if ( class_exists( '\\Timber\\Timber' ) ) {
	$context['post']                   = \Timber\Timber::get_post();
	$context['featured_playlist_id']   = get_option( 'jr_featured_playlist_id', '' );
}

jr_theme_foundation_render_template( 'page.twig', $context );
