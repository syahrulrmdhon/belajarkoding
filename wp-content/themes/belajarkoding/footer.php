<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package belajarkoding
 */
$short = get_option('shortcode_subscribers');
$logo_footer = get_option('logo_footer');

?>

	</div><!-- #content -->
	<!-- sign up -->
    <section id="signup" data-type="background" data-speed="4">
  		<div class="container">
  			<div class="row">
  				<div class="col-sm-6 col-sm-offset-3">
  					<h2>Are you ready to take your coding skills to the <strong>next level</strong>?</h2>
  					<p><a data-toggle="modal" data-target="#myModal" class="btn btn-lg btn-block btn-success">Yes, sign me up!</a></p>
  				</div><!-- end col -->
  			</div><!-- row -->
  		</div><!-- container -->
    </section>
	<footer>
		<div class="container">
			<div class="col-sm-3">
				<p><a href="/"><img src="<?php $a = wp_get_attachment_url($logo_footer); echo $a;?>" alt="Bootstrap to WordPress"></a></p>
			</div><!-- end col -->
			<div class="col-sm-6">
				<nav>
						<?php
								wp_nav_menu(
									array(
										'theme_location' => 'menu-1' ,
										'container' => 'ul',
										'container_class' => 'list-unstyled list-inline',
										'menu_class' => 'nav navbar-nav navbar-right'
									)
								);
						?>
				</nav>
			</div><!-- end col -->
			<div class="col-sm-3">
				<p class="pull-right"><?php bloginfo('name'); ?> &copy; <?php echo date('Y'); ?> <?php the_author_link(); ?></p>
			</div><!-- end col -->
		</div><!-- container -->
	</footer>
	<div class="modal fade" id="myModal">
	<div class="modal-dialog">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel"><i class="fa fa-envelope"></i> Subscribe to our Mailing List</h4>
			</div><!-- modal-header -->

			<div class="modal-body">
				<p>Simply enter your name and email! As a thank you for joining us, we're going to give you one of our best-selling courses, <em>for free!</em></p>
				<?php
				echo do_shortcode($short);
				/*
				<form class="form-inline" role="form">
					<div class="form-group">
						<label class="sr-only" for="subscribe-name">Your first name</label>
						<input type="text" class="form-control" id="subscribe-name" placeholder="Your first name">
					</div><!-- form-group -->
					<div class="form-group">
						<label class="sr-only" for="subscribe-email">and your email</label>
						<input type="text" class="form-control" id="subscribe-email" placeholder="and your email">
					</div><!-- form-group -->

					<input type="submit" class="btn btn-danger" value="Subscribe!">
				</form>
				*/?>

				<hr>

				<p><small>By providing your email you consent to receiving occasional promotional emails &amp; newsletters. <br>No Spam. Just good stuff. We respect your privacy &amp; you may unsubscribe at any time.</small></p>
			</div><!-- modal-body -->

		</div><!-- modal-content -->
	</div><!-- modal-dialog -->
</div><!-- modal -->


</div><!-- #page -->

<?php wp_footer(); ?>
<script type="text/javascript" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/js/main.js"></script>
<script src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/js/bootstrap.min.js"></script>

</body>
</html>
