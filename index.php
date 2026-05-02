<?php
/**
 * Index route wrapper.
 *
 * @package JRThemeFoundation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$context = array();
if ( class_exists( '\\Timber\\Timber' ) ) {
	$context['posts'] = \Timber\Timber::get_posts();
}

jr_theme_foundation_render_template( 'index.twig', $context );
