<?php
  $label_video_section = get_field('label_video_section');
  $link_video = get_field('link_video');
 ?>
<!-- video  -->
<section id="featurette">
  <div class="container">
    <div class="row">
      <div class="col-sm-8 col-sm-offset-2">
        <h2><?php echo $label_video_section ?></h2>
        <iframe width="100%" height="415" src="<?php echo $link_video?>" frameborder="0" allowfullscreen></iframe>
      </div><!-- end col -->
    </div><!-- row -->
  </div><!-- container -->
</section>
