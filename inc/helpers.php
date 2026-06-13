<?php
/**
 * Theme helper functions for taxonomy URLs and shared archive context.
 *
 * @package JRThemeFoundation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build a hierarchical URL for a game or platform taxonomy term.
 *
 * Mirrors the plugin's term_link filter so PHP templates can compute URLs
 * without going through get_term_link(). Falls back to get_term_link() for
 * any other taxonomy.
 *
 * @param WP_Term $term The term object.
 * @return string Absolute URL, or '' on failure.
 */
function jr_get_term_link( WP_Term $term ): string {
	$taxonomy = $term->taxonomy;

	if ( 'games' !== $taxonomy && 'platform' !== $taxonomy ) {
		$link = get_term_link( $term );
		return is_wp_error( $link ) ? '' : $link;
	}

	$base = ( 'games' === $taxonomy ) ? 'game' : 'platform';

	if ( ! $term->parent ) {
		return user_trailingslashit( home_url( "/{$base}/{$term->slug}" ), 'category' );
	}

	$ancestors = get_ancestors( $term->term_id, $taxonomy, 'taxonomy' );
	$ancestors = array_reverse( $ancestors );

	$slugs = array();
	foreach ( $ancestors as $ancestor_id ) {
		$ancestor = get_term( $ancestor_id, $taxonomy );
		if ( ! $ancestor || is_wp_error( $ancestor ) ) {
			// Ancestor is gone — building a partial path would produce a broken URL.
			$link = get_term_link( $term );
			return is_wp_error( $link ) ? '' : $link;
		}
		$slugs[] = $ancestor->slug;
	}
	$slugs[] = $term->slug;

	return user_trailingslashit( home_url( "/{$base}/" . implode( '/', $slugs ) ), 'category' );
}

/**
 * Build the shared Timber context for game and platform taxonomy archives.
 *
 * @param string $type_meta_key Meta key for the term type (e.g. 'game_type').
 * @param string $default_type  Fallback when meta is unset (e.g. 'game').
 * @return array Context ready for Timber::render().
 */
function jr_build_term_context( string $type_meta_key, string $default_type ): array {
	$queried = get_queried_object();
	if ( ! $queried instanceof WP_Term ) {
		return array();
	}
	$taxonomy = $queried->taxonomy;

	$term_type = get_term_meta( $queried->term_id, $type_meta_key, true );
	if ( ! $term_type ) {
		$term_type = $default_type;
	}

	// Child terms
	$child_terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'parent'     => $queried->term_id,
			'hide_empty' => false,
		)
	);
	$children = array();
	if ( ! is_wp_error( $child_terms ) ) {
		foreach ( $child_terms as $ct ) {
			$children[] = array(
				'name' => $ct->name,
				'slug' => $ct->slug,
				'url'  => jr_get_term_link( $ct ),
			);
		}
	}

	// Playlists tagged with this term
	$playlist_q = new WP_Query( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		array(
			'post_type'      => 'playlist',
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'no_found_rows'  => true,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'tax_query'      => array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $queried->term_id,
				),
			),
		)
	);
	$playlists = array();
	foreach ( $playlist_q->posts as $p ) {
		$video_posts = get_posts( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			array(
				'post_type'              => 'video',
				'posts_per_page'         => 20,
				'orderby'                => 'date',
				'order'                  => 'DESC',
				'post_status'            => 'publish',
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'meta_query'             => array(
					array(
						'key'   => 'wp_playlist_id',
						'value' => $p->ID,
					),
				),
			)
		);
		$playlists[] = array(
			'ID'               => $p->ID,
			'title'            => get_the_title( $p ),
			'permalink'        => get_permalink( $p ),
			'yt_playlist_id'   => get_post_meta( $p->ID, 'yt_playlist_id', true ),
			'yt_thumbnail_url' => get_post_meta( $p->ID, 'yt_thumbnail_url', true ),
			'yt_video_count'   => (int) get_post_meta( $p->ID, 'yt_video_count', true ),
			'videos'           => function_exists( 'jr_content_core_format_video' )
				? array_map( 'jr_content_core_format_video', $video_posts )
				: array(),
		);
	}

	// Regular posts tagged with this term
	$posts_q = new WP_Query( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		array(
			'post_type'      => array( 'post', 'review' ),
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'no_found_rows'  => true,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'tax_query'      => array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $queried->term_id,
				),
			),
		)
	);
	$posts = array();
	foreach ( $posts_q->posts as $p ) {
		$is_review = 'review' === $p->post_type;
		$posts[]   = array(
			'post'            => class_exists( '\\Timber\\Timber' ) ? \Timber\Timber::get_post( $p ) : null,
			'is_review'       => $is_review,
			'review_rating'   => $is_review ? get_post_meta( $p->ID, 'jr_review_rating', true ) : '',
			'review_genre'    => $is_review ? get_post_meta( $p->ID, 'jr_review_genre', true ) : '',
			'review_platform' => $is_review ? get_post_meta( $p->ID, 'jr_review_platform', true ) : '',
		);
	}

	return array(
		'term'      => class_exists( '\\Timber\\Timber' ) ? \Timber\Timber::get_term( $queried ) : $queried,
		'term_type' => $term_type,
		'children'  => $children,
		'playlists' => $playlists,
		'posts'     => $posts,
	);
}
