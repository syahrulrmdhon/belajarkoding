<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package belajarkoding
 */

get_header(); ?>

<section class="feature-image feature-image-default-alt" data-type="background" data-speed="2">
	<h1 class="page-title"><?php the_title(); ?></h1>
</section>
<div class="container">
		<div class="row" id="primary">
			<main id="content" class="col-sm-8">
					<?php
					while ( have_posts() ) : the_post();

						get_template_part( 'template-parts/content-page', 'single' );

						the_post_navigation();

						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;

					endwhile; // End of the loop.
					?>
			</main>
			<aside class="col-sm-4">
				<?php get_sidebar(); ?>
			</aside>
		</div>
		<?php wp_link_pages( 'before=<ul class="page-links">&after=</ul>&link_before=<li class="page-link">&link_after=</li>' ); ?>
	</div>

<?php
get_footer();
