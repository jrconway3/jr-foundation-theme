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
	$queried  = get_queried_object();
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
		$playlists[] = array(
			'title'            => get_the_title( $p ),
			'permalink'        => get_permalink( $p ),
			'yt_thumbnail_url' => get_post_meta( $p->ID, 'yt_thumbnail_url', true ),
			'yt_video_count'   => (int) get_post_meta( $p->ID, 'yt_video_count', true ),
		);
	}

	// Regular posts tagged with this term
	$posts_q = new WP_Query( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		array(
			'post_type'      => 'post',
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
		$cats      = wp_get_post_categories( $p->ID, array( 'fields' => 'slugs' ) );
		$is_review = in_array( 'reviews', (array) $cats, true );
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
