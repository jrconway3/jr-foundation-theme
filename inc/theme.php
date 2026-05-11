<?php
/**
 * Theme supports and registrations.
 *
 * @package JRThemeFoundation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'custom-logo' );
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'script',
				'style',
			)
		);

		register_nav_menus(
			array(
				'primary' => __( 'Primary Menu', 'jr-theme-foundation' ),
				'footer'  => __( 'Footer Menu', 'jr-theme-foundation' ),
				'social'  => __( 'Social Links', 'jr-theme-foundation' ),
			)
		);
	}
);

