<?php
/**
 * Taxonomy archive for the 'platform' taxonomy.
 *
 * @package JRThemeFoundation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

jr_theme_foundation_render_template( 'taxonomy-term.twig', jr_build_term_context( 'platform_type', 'platform' ) );
