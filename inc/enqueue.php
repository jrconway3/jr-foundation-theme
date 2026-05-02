<?php
/**
 * Asset loading.
 *
 * @package JRThemeFoundation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'wp_enqueue_scripts',
	function () {
		$style_path = get_template_directory() . '/assets/css/style.css';
		$style_ver  = file_exists( $style_path ) ? (string) filemtime( $style_path ) : wp_get_theme()->get( 'Version' );

		wp_enqueue_style(
			'jr-theme-foundation-style',
			get_template_directory_uri() . '/assets/css/style.css',
			array(),
			$style_ver
		);

		$script_path = get_template_directory() . '/assets/js/theme.js';
		if ( file_exists( $script_path ) ) {
			wp_enqueue_script(
				'jr-theme-foundation-theme',
				get_template_directory_uri() . '/assets/js/theme.js',
				array(),
				(string) filemtime( $script_path ),
				true
			);
		}
	}
);
