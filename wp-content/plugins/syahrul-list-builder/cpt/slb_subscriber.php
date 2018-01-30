<?php
add_action( 'init', 'slb_register_slb_subscriber' );
function slb_register_slb_subscriber() {

/**
 * Post Type: Subscribers.
 */

$labels = array(
  "name" => __( "Subscribers", "belajarkoding" ),
  "singular_name" => __( "Subscriber", "belajarkoding" ),
);

$args = array(
  "label" => __( "Subscribers", "belajarkoding" ),
  "labels" => $labels,
  "description" => "",
  "public" => false,
  "publicly_queryable" => true,
  "show_ui" => true,
  "show_in_rest" => false,
  "rest_base" => "",
  "has_archive" => false,
  "show_in_menu" => false,
  "exclude_from_search" => true,
  "capability_type" => "post",
  "map_meta_cap" => true,
  "hierarchical" => false,
  "rewrite" => array( "slug" => "slb_subscriber", "with_front" => false ),
  "query_var" => true,
  "supports" => false,
);

register_post_type( "slb_subscriber", $args );
}


  if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_subscriber-details',
		'title' => 'Subscriber Details',
		'fields' => array (
			array (
				'key' => 'field_5a5c886a3ee95',
				'label' => 'First Name',
				'name' => 'slb_fname',
				'type' => 'text',
				'required' => 1,
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_5a5c887d3ee96',
				'label' => 'Email Address',
				'name' => 'slb_email',
				'type' => 'email',
				'required' => 1,
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
			),
			array (
				'key' => 'field_5a5c88b83ee97',
				'label' => 'Subscriptions',
				'name' => 'slb_subscriptions',
				'type' => 'post_object',
				'post_type' => array (
					0 => 'slb_list',
				),
				'taxonomy' => array (
					0 => 'all',
				),
				'allow_null' => 0,
				'multiple' => 0,
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'slb_subscriber',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'default',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}

 ?>
