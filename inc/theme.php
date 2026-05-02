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
			)
		);
	}
);

add_action(
	'widgets_init',
	function () {
		register_sidebar(
			array(
				'name'          => __( 'Footer Widget Area', 'jr-theme-foundation' ),
				'id'            => 'footer-1',
				'description'   => __( 'Footer widgets for the foundation theme.', 'jr-theme-foundation' ),
				'before_widget' => '<section class="widget %2$s" id="%1$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);
	}
);
