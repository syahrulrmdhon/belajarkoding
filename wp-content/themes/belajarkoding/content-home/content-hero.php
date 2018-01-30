<?php
$background_hero = get_field('background_hero');
$company_logo = get_field('company_logo');
$paket = get_field('menu_paket');
$optin_label = get_field('optin_label');
$optin_button = get_field('optin_button');
?>
<section id="hero" data-type="background" style="background: url('<?php echo $background_hero;?>') 50% 0 repeat fixed;" data-speed="5">
  <article>
    <div class="container clearfix">
      <div class="row">

        <div class="col-sm-5">
          <img src="<?php echo $company_logo;?>" alt="belajarkoding" class="logo">
        </div><!-- col -->
        <h1>&nbsp;</h1>
        <div class="col-sm-7 hero-text">
              <p class="lead"><?php bloginfo('description');?></p>
              <div id="price-timeline">
              <?php foreach ($paket as $key) {
                ?>
                  <div class="price active">
                    <h4><?php echo $key['nama_paket'];?></h4>
                    <span><?php echo $key['harga_paket'];?></span>
                  </div><!-- end price -->
              <?php
              }?>
              </div><!-- price-timeline -->
              <p><a class="btn btn-lg btn-danger" data-toggle="modal" data-target="#myModal" role="button">Daftar Sekarang &raquo;</a></p>
        </div><!-- col -->

      </div><!-- row -->
    </div><!-- container -->
  </article>
</section>
<!-- Opt in section -->
<section id="optin">
  <div class="container">
    <div class="row">
      <div class="col-sm-8">
        <p class="optin"><?php echo $optin_label?></p>
      </div><!-- end col -->

      <div class="col-sm-4">
        <button class="btn btn-success btn-lg btn-block" data-toggle="modal" data-target="#myModal">
          <?php echo $optin_button?>
        </button>
      </div><!-- end col -->
    </div><!-- row -->
  </div><!-- container -->
</section>
