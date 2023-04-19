<?php
/*
Plugin Name: Atoomo Whatsapp
Description: Atoomo Whatsapp
Version: 1.0
Author: Alen Castry
Author URI: https://siwo.com
*/

include "includes/page.php";
include "includes/whatsapp-post-type.php";


add_action( 'rest_api_init', 'request_logger_init' );
function request_logger_init() {
  register_rest_route( 'hook', '/360dialog/(?P<id>[^/]+)', array(
    'methods' => 'GET,POST',
    'callback' => 'request_logger_callback',
  ) );
}

function request_logger_callback( $request ) {

	$phone_number = $request['id'];

	$phone_number = ltrim($phone_number, "+");

	$datetime = date( 'Y-m-d H:i:s' );

	$get_params = print_r( $request->get_params(), true );

	$post_params = print_r( $request->get_body_params(), true );

	$log_entry = "[$datetime]\n\nGET:\n$get_params\n\nPOST:\n$post_params\n\n\n";

	if(!file_exists( WP_CONTENT_DIR . '/uploads/atoomo-whatsapp'))
		mkdir( WP_CONTENT_DIR . '/uploads/atoomo-whatsapp');

	$log_file = WP_CONTENT_DIR . '/uploads/atoomo-whatsapp/'.$phone_number.'.log';
	file_put_contents( $log_file, $log_entry, FILE_APPEND );
}


