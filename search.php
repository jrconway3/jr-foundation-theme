<?php
/**
 * Search route wrapper.
 *
 * @package JRThemeFoundation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$context = array(
	'search_query' => get_search_query(),
);

if ( class_exists( '\\Timber\\Timber' ) ) {
	$context['posts'] = \Timber\Timber::get_posts();
}

jr_theme_foundation_render_template( 'search.twig', $context );
