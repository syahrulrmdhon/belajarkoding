<?php
  $course_features_tittle = get_field('course_features_tittle');
  $course_features_image = get_field('course_features_image');
  $course_features = get_field('course_features');
?>

<!-- Course Features -->
<section id="course-features">
  <div class="container">
      <div class="section-header">

          <img src="<?php echo $course_features_image ?>" alt="Rocket">
          <h2><?php echo $course_features_tittle ?></h2>
      </div><!-- section-header -->
      <div class="row">
        <?php foreach ($course_features as $key) {
         ?>
            <div class="col-sm-2">
              <i class="<?php echo $key['icon_feature']?>"></i>
              <h4><?php echo $key['feature_tittle']?></h4>
            </div><!-- end col -->
        <?php }?>
      </div><!-- row -->
    </div><!-- container -->
</section>
