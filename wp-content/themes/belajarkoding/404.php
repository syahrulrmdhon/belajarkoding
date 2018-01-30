<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package belajarkoding
 */

get_header(); ?>

<section class="feature-image feature-image-default" data-type="background" data-speed="2">
		<h1 class="page-title">404</h1>
	</section>
	<div class="container">
			<div class="row" id="primary">
				<main id="content" class="col-sm-8">
					<header class="page-header">
						<h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'belajarkoding' ); ?></h1>
					</header><!-- .page-header -->

					<div class="page-content">
						<p><?php esc_html_e( 'It looks like nothing was found at this location.', 'belajarkoding' ); ?></p>

					</div><!-- .page-content -->
				</main>
				<aside class="col-sm-4">
					<?php get_sidebar(); ?>
				</aside>

	</div><!-- #primary -->
</div>

<?php
get_footer();
