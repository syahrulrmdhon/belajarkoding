<?php
/*
  Template Name: Resources Page
*/
get_header();
$image_header = get_field('image_header');
$header = get_field('header');
$paragraph_1 = get_field('paragraph_1');
$paragraph_2 = get_field('paragraph_2');
$resource_section = get_field('resource_section');
?>
<section class="feature-image feature-image-default" style="background: url('<?php echo $image_header;?>')" data-type="background" data-speed="2">
		<h1><?php echo $header;?></h1>
	</section>

 <div class="container">
	    <div class="row" id="primary">

		    <div id="content" class="col-sm-12">

			    <section class="main-content">
			    	<p class="lead"><?php echo $paragraph_1?></p>

			    	<p><?php echo $paragraph_2;?></p>
			    	<hr>

			    	<div class="resource-row clearfix">
							<?php foreach ($resource_section as $key) {
								$i=0;
				        $a[$i] = wp_get_attachment_url( $key['image_resource'] );
							?>
					    	<div class="resource">
						    	<img src="<?php echo $a[$i] ?>" alt="Justhost">
						    	<h3><a href=""><?php echo $key['resources_name'];?></a></h3>
						    	<p><?php echo $key['resources_explanation']?></p>
						    	<a href="<?php echo $key['link_to_resources']?>" class="btn btn-success"><?php echo $key['name_button'];?></a>
					    	</div>
							<?php $i++; }?>
			    	</div>
			    </section>

		    </div><!-- content -->

	    </div><!-- primary -->
    </div><!-- container -->
	<?php
 get_footer();
?>
