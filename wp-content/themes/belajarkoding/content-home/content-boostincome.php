<?php

$icon_boost_income = get_field('icon_boost_income');
$header_boost_income = get_field('header_boost_income');
$tittle_1 = get_field('tittle_1');
$reason_1 = get_field('reason_1');
$tittle_2 = get_field('tittle_2');
$reason_2 = get_field('reason_2');
$audience = get_field('audience');
?>
<!-- boost income -->
<section id="boost-income">
  <div class="container">
    <div class="section-header">
      <?php if ($icon_boost_income != '') {
      ?>
        <img src="<?php echo $icon_boost_income?>" alt="Chart">
      <?php } ?>
      <h2><?php echo $header_boost_income;?></h2>
    </div><!-- section-header -->

    <p class="lead"><?php echo $audience?></p>
    <div class="row">
      <div class="col-sm-6">
        <h3><?php echo $tittle_1?></h3>
        <p><?php echo $reason_1?></p>
      </div><!-- end col -->

      <div class="col-sm-6">
        <h3><?php echo $tittle_2?></h3>
        <p><?php echo $reason_2?></p>
      </div><!-- end col -->
    </div><!-- row -->
  </div><!-- container -->
</section>
