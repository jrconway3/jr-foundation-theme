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
	$context['post'] = \Timber\Timber::get_post();
}

jr_theme_foundation_render_template( 'page.twig', $context );
