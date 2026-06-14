<?php
/**
 * Menu customisations — social link URL override.
 *
 * @package JRThemeFoundation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Replace the URL of social CPT menu items with the jrblog_social_url meta field.
 * This filter runs before Timber wraps items, so the corrected URL flows through normally.
 */
add_filter(
	'wp_get_nav_menu_items',
	function ( $items, $menu, $args ) {
		foreach ( $items as $item ) {
			if ( isset( $item->object ) && 'social' === $item->object ) {
				$url = get_post_meta( (int) $item->object_id, 'jrblog_social_url', true );
				if ( $url ) {
					$item->url = esc_url_raw( $url );
				}
			}
		}
		return $items;
	},
	10,
	3
);
