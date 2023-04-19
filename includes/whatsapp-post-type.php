<?php 


include "library.php";

function aw_post_type_whatsapps() {

	$supports = array(
	'title', // post title
	// 'editor', // post content
	// 'author', // post author
	// 'thumbnail', // featured images
	// 'excerpt', // post excerpt
	// 'custom-fields', // custom fields
	// 'comments', // post comments
	// 'revisions', // post revisions
	// 'post-formats', // post formats
	);

	$labels = array(
	'name' => _x('whatsapps', 'plural'),
	'singular_name' => _x('whatsapps', 'singular'),
	'menu_name' => _x('whatsapps', 'admin menu'),
	'name_admin_bar' => _x('whatsapps', 'admin bar'),
	'add_whatsapp' => _x('Add New', 'add whatsapp'),
	'add_whatsapp_item' => __('Add New whatsapps'),
	'whatsapp_item' => __('New whatsapps'),
	'edit_item' => __('Edit whatsapps'),
	'view_item' => __('View whatsapps'),
	'all_items' => __('All whatsapps'),
	'search_items' => __('Search whatsapps'),
	'not_found' => __('No whatsapps found.'),
	);

	$args = array(
	'supports' => $supports,
	'labels' => $labels,
	'public' => true,
	'query_var' => true,
	// 'rewrite' => array('slug' => 'whatsapps'),
	'has_archive' => true,
	'hierarchical' => false,
	);
	register_post_type('whatsapp', $args);
}
add_action('init', 'aw_post_type_whatsapps');

/*Custom Post type end*/



add_action( 'add_meta_boxes', [ 'AW_Meta_Box', 'add' ] );
add_action( 'save_post', [ 'AW_Meta_Box', 'save' ] );


// add_action( 'rest_api_init', function () {
//   register_rest_route( 'apci', '/sync', array(
//     'methods' => 'POST',
//     'callback' => 'apci_sync',
//     'permission_callback' => '__return_true',
//   ) );
// } );

// function apci_sync( $data ) {

// 	// Takes raw data from the request
// 	$call = file_get_contents('php://input');	
	
// 	 $call = json_decode($call);

// //	$dada = var_export($call, true);
// //	$dada = file_put_contents("dada.txt",$dada);


// 	if(!$call) {
// 		print("APCI ERROR - Cannot get call datas");
// 		return wp_send_json(false);
// 	}

// 	if(!isset($call->data->from_number)) {
// 		print("APCI ERROR - Cannot get call whatsapp number");
// 		return wp_send_json(false);
// 	}

// 	if(!isset($call->data->start_time)) {
// 		print("APCI ERROR - Cannot get call start time");
// 		return wp_send_json(false);
// 	}

// 	$product_id = get_product_id_from_whatsapp_number($call->data->to_number);

// 	if(get_post_meta($product_id,"apci_activated", true)!=1){
// 		print("APCI ERROR - Phone number not activated");
// 		return wp_send_json(false);
// 	}

// 	if(!get_post_meta($product_id,"apci_inbox_id", true)){
// 		print("APCI ERROR - No Inbox ID");
// 		return wp_send_json(false);
// 	}

// 	if(!get_post_meta($product_id,"apci_account_id", true)){
// 		print("APCI ERROR - No Account ID");
// 		return wp_send_json(false);
// 	}
// 	if(!get_post_meta($product_id,"apci_whatsapp_api_key", true)){
// 		print("APCI ERROR - No Phone API Key");
// 		return wp_send_json(false);
// 	}
// 	if(!get_post_meta($product_id,"apci_url", true)){
// 		print("APCI ERROR - No URL");
// 		return wp_send_json(false);
// 	}

// 	$datas = [];
// 	$datas["http_host"] = get_post_meta($product_id,"apci_url", true);
// 	$datas["api_key"] = get_post_meta($product_id,"apci_whatsapp_api_key", true);
// 	$datas["account_id"] = get_post_meta($product_id,"apci_account_id", true);
// 	$datas["q"] = "+".$call->data->from_number;
// 	$datas["inbox_id"] = get_post_meta($product_id,"apci_inbox_id", true);

// 	// var_dump($datas);exit;

// 	$format      = get_option('date_format') . ' ' . get_option('time_format');
// 	$start_time = date_i18n($format, $call->data->start_time);	

// 	$contacts = apci_search_contact($datas);
// 	$contact = false;
// 	if(!empty($contacts->payload[0])){
// 		$contact = $contacts->payload[0];
// 	} else {
// 		$datas["whatsapp_number"] = $datas["q"];
// 		$datas["name"] = $datas["whatsapp_number"];
// 		$contact = apci_create_contact($datas);
// 	}
// 	if(!$contact) {
// 		error_log("APCI ERROR - Cannot get contact either existing or created");
// 		return wp_send_json(false);
// 	}

// 	// var_dump("contact", $contact);exit;

// 	$source_id = false;

// 	if(!empty($contact->contact_inboxes[0]->source_id))
// 		$source_id = $contact->contact_inboxes[0]->source_id;

// 	if(!$source_id) {
// 		error_log("APCI ERROR - Cannot get source_id");
// 		return wp_send_json(false);
// 	}

// 	$datas["contact_id"] = $contact->id;
// 	$datas["source_id"] = $source_id;

// 	$conversation = apci_create_conversation($datas);

// 	if(!$conversation) {
// 		error_log("APCI ERROR - Cannot create conversation");
// 		return wp_send_json(false);
// 	}

// 	$datas["conversation_id"] = $conversation->id;
// 	$datas["content"] = $datas["q"]."\n".$start_time;
// 	if(!empty($call->data->record))
// 	$datas["content"] = $datas["q"]."\n".$start_time."\n".$call->data->record;
// 	if(!empty($call->data->message))
// 	$datas["content"] = $datas["q"]."\n".$start_time."\n".$call->data->message;

// 	$datas["inbox_identifier"] = $datas["inbox_id"];
// 	$datas["contact_identifier"] = $datas["source_id"];
// 	$datas["conversation_id"] = $datas["conversation_id"];

// 	$label = apci_add_label_to_conversation($datas);

// 	$message = apci_create_message($datas);

// 	if(!$message) {
// 		error_log("APCI ERROR - Cannot create message");
// 		return wp_send_json(false);
// 	}

// 	wp_send_json(true);
// }