<?php
/*
  Template Name: Home Page
*/

  get_header();

  get_template_part('content-home/content-hero', 'hero');
  get_template_part('content-home/content-boostincome','boostincome');
  get_template_part('content-home/content-benefit','benefit');
  get_template_part('content-home/content-features','features');
  get_template_part('content-home/content-finalproject','finalproject');
  get_template_part('content-home/content-video','video');
  get_template_part('content-home/content-testimonial','testimonial');
  get_footer();

?>
