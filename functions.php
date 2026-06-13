<?php
/**
 * Theme bootstrap for JR Theme Foundation.
 *
 * @package JRThemeFoundation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$jr_theme_foundation_autoload = ABSPATH . 'vendor/autoload.php';
if ( file_exists( $jr_theme_foundation_autoload ) ) {
	require_once $jr_theme_foundation_autoload;
}

$jr_theme_foundation_includes = array(
	__DIR__ . '/inc/helpers.php',
	__DIR__ . '/inc/theme.php',
	__DIR__ . '/inc/menus.php',
	__DIR__ . '/inc/enqueue.php',
	__DIR__ . '/inc/timber.php',
);

foreach ( $jr_theme_foundation_includes as $jr_theme_foundation_file ) {
	if ( file_exists( $jr_theme_foundation_file ) ) {
		require_once $jr_theme_foundation_file;
	}
}

/**
 * Render a Twig template when Timber is present, otherwise render a simple fallback.
 *
 * @param string $template Twig template path.
 * @param array  $context  View context.
 */
function jr_theme_foundation_render_template( $template, $context = array() ) {
	if ( class_exists( '\\Timber\\Timber' ) ) {
		$base_context = \Timber\Timber::context();
		\Timber\Timber::render( $template, array_merge( $base_context, $context ) );
		return;
	}

	status_header( is_404() ? 404 : 200 );
	?><!doctype html>
	<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<main class="site-main" style="max-width: 960px; margin: 2rem auto; padding: 0 1rem;">
		<?php if ( have_posts() ) : ?>
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article <?php post_class(); ?>>
					<h1><?php the_title(); ?></h1>
					<?php the_content(); ?>
				</article>
			<?php endwhile; ?>
		<?php else : ?>
			<h1><?php esc_html_e( 'Nothing found', 'jr-theme-foundation' ); ?></h1>
		<?php endif; ?>
	</main>
	<?php wp_footer(); ?>
	</body>
	</html>
	<?php
}
