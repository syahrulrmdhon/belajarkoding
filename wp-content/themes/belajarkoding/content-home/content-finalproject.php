<?php
  $final_project = get_field('final_project');
  $tittle_final_project = get_field('tittle_final_project');
  $content_final_project = get_field('content_final_project');
?>

<!-- project Features -->
<section id="project-features">
  <div class="container">
    <h2><?php echo $tittle_final_project;?></h2>
    <p class="lead"><?php echo $content_final_project;?></p>

    <div class="row">
      <?php foreach ($final_project as $key) {
        $i=0;
        $a[$i] = wp_get_attachment_url( $key['icon_final'] );
      ?>
      <div class="col-sm-4">
        <img src="<?php echo $a[$i]?>" alt="Design">
        <h3><?php echo $key['tittle_final']?></h3>
        <p><?php echo $key['explanation_final']?></p>
      </div><!-- col -->
    <?php $i++;} ?>
    </div><!-- row -->

  </div><!-- container -->
</section>
