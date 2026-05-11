<?php
/**
 * Timber bootstrap and context setup.
 *
 * @package JRThemeFoundation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\\Timber\\Timber' ) ) {
	add_action(
		'admin_notices',
		function () {
			echo '<div class="notice notice-warning"><p>';
			echo esc_html__( 'JR Theme Foundation requires Timber. Run Composer install to enable Twig rendering.', 'jr-theme-foundation' );
			echo '</p></div>';
		}
	);
	return;
}

\Timber\Timber::init();
\Timber\Timber::$dirname = array( 'templates' );

add_filter(
	'timber/context',
	function ( $context ) {
		$context['site']         = new \Timber\Site();
		$context['menu_primary'] = has_nav_menu( 'primary' ) ? new \Timber\Menu( 'primary' ) : null;
		$context['menu_footer']  = has_nav_menu( 'footer' ) ? new \Timber\Menu( 'footer' ) : null;
		$context['menu_social']  = has_nav_menu( 'social' ) ? new \Timber\Menu( 'social' ) : null;
		$admin_users            = get_users( array( 'role' => 'administrator', 'number' => 1, 'orderby' => 'ID', 'order' => 'ASC' ) );
		$context['site_author'] = ! empty( $admin_users ) ? \Timber\Timber::get_user( $admin_users[0]->ID ) : null;
		$context['games_list']  = \Timber\Timber::get_terms(
			array(
				'taxonomy'   => 'games',
				'hide_empty' => false,
				'parent'     => 0,
				'number'     => 10,
				'orderby'    => 'count',
				'order'      => 'DESC',
			)
		);
		return $context;
	}
);
