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
		$context['site']         = \Timber\Timber::get_site();
		$context['menu_primary'] = has_nav_menu( 'primary' ) ? new \Timber\Menu( 'primary' ) : null;
		$context['menu_footer']  = has_nav_menu( 'footer' ) ? new \Timber\Menu( 'footer' ) : null;
		return $context;
	}
);
