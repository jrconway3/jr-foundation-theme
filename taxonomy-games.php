<?php
/**
 * Taxonomy archive for the 'games' taxonomy.
 *
 * WordPress resolves this via taxonomy-{taxonomy_name}.php, so the file
 * is named 'taxonomy-games' (the registered taxonomy name), not 'taxonomy-game'
 * (the URL slug). Both game and platform archives render taxonomy-term.twig.
 *
 * @package JRThemeFoundation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$context = class_exists( '\\Timber\\Timber' ) ? jr_build_term_context( 'game_type', 'game' ) : array();
jr_theme_foundation_render_template( 'taxonomy-term.twig', $context );
