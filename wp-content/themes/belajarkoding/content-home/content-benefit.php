<?php
$whos_benefits_icon = get_field('whos_benefits_icon');
$header_target_audience = get_field('header_target_audience');
$who_is_it = get_field('who_is_it');
?>
<!-- who benefit -->
<section id="who-benefits">
  <div class="container">
  <div class="section-header">
    <img src="<?php echo $whos_benefits_icon?>" alt="Pad and pencil">
    <h2><?php echo $header_target_audience;?></h2>
  </div><!-- section-header -->

  <div class="row">
    <div class="col-sm-8 col-sm-offset-2">
      <?php foreach ($who_is_it as $key) {
        # code...
      ?>
        <h3><?php echo $key['jobs_name'];?></h3>
        <p><?php echo $key['reason_why']?></p>
      <?php
      }
      ?>
      </div><!-- end col -->
    </div><!-- row -->

  </div><!-- container -->
</section>
