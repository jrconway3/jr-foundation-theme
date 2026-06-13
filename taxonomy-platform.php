<?php
/**
 * Taxonomy archive for the 'platform' taxonomy.
 *
 * @package JRThemeFoundation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$context = class_exists( '\\Timber\\Timber' ) ? jr_build_term_context( 'platform_type', 'platform' ) : array();
jr_theme_foundation_render_template( 'taxonomy-term.twig', $context );
