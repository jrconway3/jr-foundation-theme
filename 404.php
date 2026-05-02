<?php
/**
 * 404 route wrapper.
 *
 * @package JRThemeFoundation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

status_header( 404 );

jr_theme_foundation_render_template( '404.twig' );
