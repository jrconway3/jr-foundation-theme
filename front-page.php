<?php
/**
 * Homepage route wrapper.
 *
 * @package JRThemeFoundation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$context = array();
if ( class_exists( '\\Timber\\Timber' ) ) {
	$archived_cat = get_category_by_slug( 'archived' );
	$exclude_cats = $archived_cat ? array( $archived_cat->term_id ) : array();

	$all_posts  = \Timber\Timber::get_posts(
		array(
			'post_type'        => array( 'post', 'review' ),
			'posts_per_page'   => 6,
			'orderby'          => 'date',
			'order'            => 'DESC',
			'category__not_in' => $exclude_cats,
		)
	);
	$posts_arr  = array();
	if ( $all_posts ) {
		foreach ( $all_posts as $p ) {
			$posts_arr[] = $p;
		}
	}
	$context['featured_post']        = ! empty( $posts_arr ) ? $posts_arr[0] : null;
	$context['recent_posts']         = array_slice( $posts_arr, 1 );
	$context['featured_playlist_id'] = get_option( 'jr_featured_playlist_id', '' );

	$live                   = get_option( 'jr_live_stream' );
	$context['live_stream'] = ( is_array( $live ) && ! empty( $live['active'] ) ) ? $live : null;

	$playlists_raw            = get_option( 'jr_playlists', '[]' );
	$decoded                  = json_decode( $playlists_raw, true );
	$context['playlists']     = is_array( $decoded ) ? $decoded : array();
}

jr_theme_foundation_render_template( 'front-page.twig', $context );
