<?php
/**
 * Single route wrapper.
 *
 * @package JRThemeFoundation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$context = array();
if ( class_exists( '\\Timber\\Timber' ) ) {
	$timber_post      = \Timber\Timber::get_post();
	$context['post']  = $timber_post;

	if ( $timber_post ) {
		$word_count              = str_word_count( wp_strip_all_tags( $timber_post->post_content ) );
		$context['reading_time'] = max( 1, (int) round( $word_count / 200 ) );
	}

	$context['featured_playlist_id'] = get_option( 'jr_featured_playlist_id', '' );
}

jr_theme_foundation_render_template( 'single.twig', $context );
