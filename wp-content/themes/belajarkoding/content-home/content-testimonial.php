<?php
  $testmoni = get_field('testimoni');
  $tittle_testimoni = get_field('tittle_testimoni');

 ?>

<!-- testimonial -->
<section id="testimonial">
  <div class="container">
    <div class="row">

      <div class="col-sm-8 col-sm-offset-2">
        <h2> <?php echo $tittle_testimoni; ?> </h2>

        <?php foreach ($testmoni as $key) {
            $i=0;
            $a[$i] = wp_get_attachment_url( $key['image_testimoni'] );
         ?>
            <!-- TESTIMONIAL -->
            <div class="row testimonial">
              <div class="col-sm-4">
                <img src="<?php echo $a[$i]?>" alt="Brennan">
              </div><!-- end col -->
              <div class="col-sm-8">
                <blockquote>
                  <?php echo $key['field_testimoni']; ?>
                  <cite>&mdash;<?php echo $key['name_testimonial']; ?></cite>
                </blockquote>
              </div><!-- end col -->
            </div><!-- row -->
          <?php $i++;}?>

      </div><!-- end col -->

    </div><!-- row -->
  </div><!-- container -->
</section>
