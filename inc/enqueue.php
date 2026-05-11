<?php
/**
 * Asset loading — reads the Vite manifest when built; falls back to raw files in dev.
 *
 * @package JRThemeFoundation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'wp_enqueue_scripts',
	function () {
		$theme_uri  = get_template_directory_uri();
		$theme_path = get_template_directory();
		$manifest   = $theme_path . '/assets/dist/.vite/manifest.json';

		if ( file_exists( $manifest ) ) {
			// Production: load hashed files from Vite manifest.
			$data = json_decode( file_get_contents( $manifest ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

			if ( isset( $data['assets/scss/main.scss']['file'] ) ) {
				wp_enqueue_style(
					'jr-plays-theme-style',
					$theme_uri . '/assets/dist/' . $data['assets/scss/main.scss']['file'],
					array(),
					null
				);
			}

			if ( isset( $data['assets/js/theme.js']['file'] ) ) {
				wp_enqueue_script(
					'jr-plays-theme-js',
					$theme_uri . '/assets/dist/' . $data['assets/js/theme.js']['file'],
					array(),
					null,
					true
				);
			}
		} else {
			// Dev fallback: load from legacy css path if present.
			$style_path = $theme_path . '/assets/css/style.css';
			if ( file_exists( $style_path ) ) {
				wp_enqueue_style(
					'jr-plays-theme-style',
					$theme_uri . '/assets/css/style.css',
					array(),
					(string) filemtime( $style_path )
				);
			}

			$script_path = $theme_path . '/assets/js/theme.js';
			if ( file_exists( $script_path ) ) {
				wp_enqueue_script(
					'jr-plays-theme-js',
					$theme_uri . '/assets/js/theme.js',
					array(),
					(string) filemtime( $script_path ),
					true
				);
			}
		}
	}
);
