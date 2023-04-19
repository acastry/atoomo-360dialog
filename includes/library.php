<?php 



abstract class AW_Meta_Box {


	/**
	 * Set up and add the meta box.
	 */
	public static function add() {

		$screens = [ 'whatsapp' ];
		foreach ( $screens as $screen ) {
			add_meta_box(
				'wporg_box_id',          // Unique ID
				'-', // Box title
				[ self::class, 'html' ],   // Content callback, must be of type callable
				$screen                  // Post type
			);
		}
	}


	public static function fields() {
	    return [
	        'aw_phone_number' => [
	            'label'  => 'Phone number',
	            'type'   => 'tel',
	            // 'small'  => 'Mandatory !',
	        ],
	        'aw_name' => [
	            'label'  => 'Account',
	            'type'   => 'text',
	            // 'small'  => 'ADMIN user handling by default',
	        ],


	        'aw_client' => [
	            'label'  => 'Client',
	            'type'   => 'text',
	            // 'small'  => 'ADMIN user handling by default',
	        ],
	        'aw_channels' => [
	            'label'  => 'Channels',
	            'type'   => 'text',
	            // 'small'  => 'ADMIN user handling by default',
	        ],

	        'aw_onboarding_logs' => [
	            'label'  => 'ONBOARDING LOGS',
	            'type'   => 'textarea',
	            // 'small'  => 'ADMIN user handling by default',
	        ],

	        'aw_activated' => [
	            'label'  => 'Activated',
	            'type'   => 'select',
	            // 'small'  => 'Mandatory !',
	            'datas'	=> array(0=>"No",1=>"Yes")
	        ],
		];
	}

	/**
	 * Save the meta box selections.
	 *
	 * @param int $post_id  The post ID.
	 */
	public static function save( int $post_id ) {

		foreach( AW_Meta_Box::fields() as $key=>$field){ 
			if ( array_key_exists( $key, $_POST ) ) {
				$value = sanitize_text_field($_POST[$key]);
				update_post_meta(
					$post_id,
					$key,
					$value
				);
			}			
		}					
	}


	/**
	 * Display the meta box HTML to the user.
	 *
	 * @param \WP_Post $post   Post object.
	 */
	public static function html( $post ) {

		foreach( AW_Meta_Box::fields() as $key=>$field){ ?>
			<p>
			<?php 
			$value = get_post_meta( $post->ID, $key, true );
			switch($field["type"]){
				case "tel": ?>
						<label for="<?php echo $key?>"><?php echo $field["label"]?></label>
						<input value="<?php echo $value?>" name="<?php echo $key?>" type="tel" />
						<?php 
				break;
				case "text": ?>
						<label for="<?php echo $key?>"><?php echo $field["label"]?></label>
						<input value="<?php echo $value?>" name="<?php echo $key?>" />
						<?php 
				break;
				case "textarea": ?>
						<label for="<?php echo $key?>"><?php echo $field["label"]?></label>
						<textarea style="width:100%;height:300px" name="<?php echo $key?>"><?php echo $value?></textarea>
						<?php 
				break;
				case "select": ?>
						<label for="<?php echo $key?>"><?php echo $field["label"]?></label>
						<select name="<?php echo $key?>" id="<?php echo $key?>">
							<?php 
								foreach($field["datas"] as $key_data=>$data){
									?><option value="<?php echo $key_data?>" <?php selected( $value, $key_data, true ); ?>><?php echo $data?></option><?php 
								}
							?>							
						</select>
						<?php 
				break;				
				default:
				break;
			}
			if (isset($field['small'])) {
            	?><small><?php echo $field['small']; ?></small><?php
            }?>
			</p>
			<?php 			
		}
		?>
		<?php if(empty(get_post_meta( $post->ID, "aw_client_id", true )) || empty(get_post_meta( $post->ID, "aw_channel_id", true )) ){?>
		<h1 style="font-size:40px"><a href="<?php echo add_query_arg( array(
    'onboarding' => get_post_meta($post->ID, "aw_phone_number", true)
			), site_url() 
			)?>">Onboarding 360 Dialog</a></h1>
		<?php }?>

		<?php 
	}
}

function get_product_id_from_phone_number($phone_number){
      $phone_number = str_replace(" ", "+", $phone_number);
    global $wpdb;

    $sql = "SELECT ID from {$wpdb->posts} p";
    $sql .=" join {$wpdb->postmeta} pm on p.ID=pm.post_id and pm.meta_key=%s where 1";
    $sql .=" and pm.meta_value=%s order by p.ID desc";
    $sql = $wpdb->prepare($sql, ["aw_phone_number",$phone_number]);

    $result = $wpdb->get_row($sql);
    if($result)
      return $result->ID;
    return false;
}

add_action( 'rest_api_init', function () {
  register_rest_route( 'aw', '/onboarding', array(
    'callback' => 'aw_sync',
    'permission_callback' => '__return_true',
  ) );
} );

function aw_sync( $data ) {

	$inputs = var_export(file_get_contents('php://input'), true);	
	$posts = var_export(!empty($_POST)?$_POST:[], true);
	$gets = var_export(!empty($_GET)?$_GET:[], true);


	$datas = date("Y-m-d H:i:s")." \n".$inputs." \n".$posts." \n".$gets." \n"." \n";

	if(isset($_GET["whatsapp"])){

		$product_id = get_product_id_from_phone_number($_GET["whatsapp"]);
		if($product_id){
			update_post_meta($product_id, "aw_onboarding_logs", $datas);
		}
	}


  $file_path = WP_CONTENT_DIR . '/uploads/360dialog.json';
  file_put_contents( $file_path, $datas , FILE_APPEND | LOCK_EX);

  return 1;
}


// function apci_create_conversation($datas){

// 	$curl = curl_init();
	
// 	$args = ["inbox_id"=>$datas["inbox_id"], "source_id"=>$datas["source_id"], "contact_id"=>$datas["contact_id"], "status"=>"open"];

// 	curl_setopt_array($curl, array(
// 	  CURLOPT_URL => $datas["http_host"].'/api/v1/accounts/'.$datas["account_id"].'/conversations',
// 	  CURLOPT_RETURNTRANSFER => true,
// 	  CURLOPT_ENCODING => '',
// 	  CURLOPT_MAXREDIRS => 10,
// 	  CURLOPT_TIMEOUT => 0,
// 	  CURLOPT_FOLLOWLOCATION => true,
// 	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
// 	  CURLOPT_CUSTOMREQUEST => "POST",
// 	    CURLOPT_POSTFIELDS => json_encode($args),
// 	    CURLOPT_HTTPHEADER => [
// 		    // 'api_access_token: MxrTNDvg8EEKCWTkCqH4PZiZ'
// 		  "api_access_token: ".$datas["api_key"],
// 	      "content-type: application/json",
// 	    ],
// 	));

// 	$response = curl_exec($curl);
// 	$err = curl_error($curl);


// 	if ($err) {
// 		var_export($err);exit;
// 	} else {

// 		if (!curl_errno($curl)) {
// 		  switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
// 		    case 200:  # OK
// 		      $response = json_decode($response);
// 		      return $response;
// 		      break;
// 		    default:{
// 		      var_export($response);exit;
// 		    }
// 		  }
// 		}
// 	}

// 	curl_close($curl);
// 	return $response;
// }

// function apci_create_message($datas){

// 	$curl = curl_init();
	
// 	$args = ["content"=>$datas["content"]
// 	, 
// 	// "sender"=>$datas["sender"],
// 	"private"=>true,
// 	// "content_type"=>"form"
// 	];

// 	curl_setopt_array($curl, array(
// 	  CURLOPT_URL => $datas["http_host"].'/api/v1/accounts/'.$datas["account_id"].'/conversations/'.$datas["conversation_id"].'/messages',
// 	  CURLOPT_RETURNTRANSFER => true,
// 	  CURLOPT_ENCODING => '',
// 	  CURLOPT_MAXREDIRS => 10,
// 	  CURLOPT_TIMEOUT => 0,
// 	  CURLOPT_FOLLOWLOCATION => true,
// 	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
// 	  CURLOPT_CUSTOMREQUEST => "POST",
// 	    CURLOPT_POSTFIELDS => json_encode($args),
// 	    CURLOPT_HTTPHEADER => [
// 		    // 'api_access_token: MxrTNDvg8EEKCWTkCqH4PZiZ'
// 		  "api_access_token: ".$datas["api_key"],
// 	      "content-type: application/json",
// 	    ],
// 	));

// 	$response = curl_exec($curl);
// 	$err = curl_error($curl);


// 	if ($err) {
// 		var_export($err);exit;
// 	} else {

// 		if (!curl_errno($curl)) {
// 		  switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
// 		    case 200:  # OK
// 		      $response = json_decode($response);
// 		      return $response;
// 		      break;
// 		    default:{
// 		      var_export($response);exit;
// 		    }
// 		  }
// 		}
// 	}

// 	curl_close($curl);
// 	return $response;
// }


// function apci_create_message__($datas){

// 	$curl = curl_init();
	
// 	$args = ["content"=>$datas["content"]];
// //https://app.chatwoot.com/public/api/v1/inboxes/{inbox_identifier}/contacts/{contact_identifier}/conversations/{conversation_id}/messages
// 	echo $datas["http_host"].'/public/api/v1/inboxes/'.$datas["inbox_identifier"].'/contacts/'.$datas["contact_identifier"].'/conversations/'.$datas["conversation_id"].'/messages';
// 	curl_setopt_array($curl, array(
// 	  CURLOPT_URL => $datas["http_host"].'/public/api/v1/inboxes/'.$datas["inbox_identifier"].'/contacts/'.$datas["contact_identifier"].'/conversations/'.$datas["conversation_id"].'/messages',
// 	  CURLOPT_RETURNTRANSFER => true,
// 	  CURLOPT_ENCODING => '',
// 	  CURLOPT_MAXREDIRS => 10,
// 	  CURLOPT_TIMEOUT => 0,
// 	  CURLOPT_FOLLOWLOCATION => true,
// 	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
// 	  CURLOPT_CUSTOMREQUEST => "POST",
// 	    CURLOPT_POSTFIELDS => json_encode($args),
// 	    CURLOPT_HTTPHEADER => [
// 		    // 'api_access_token: MxrTNDvg8EEKCWTkCqH4PZiZ'
// 		  "api_access_token: ".$datas["api_key"],
// 	      "content-type: application/json",
// 	    ],
// 	));

// 	$response = curl_exec($curl);
// 	$err = curl_error($curl);


// 	if ($err) {
// 		var_export($err);exit;
// 	} else {

// 		if (!curl_errno($curl)) {
// 		  switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
// 		    case 200:  # OK
// 		      $response = json_decode($response);
// 		      return $response;
// 		      break;
// 		    default:{
// 		      var_export($response);exit;
// 		    }
// 		  }
// 		}
// 	}

// 	curl_close($curl);
// 	return $response;
// }


// function apci_add_label_to_conversation($datas){

// 	$curl = curl_init();
	
// 	$args = ["labels"=>["call"]];

// 	curl_setopt_array($curl, array(
// 	  CURLOPT_URL => $datas["http_host"].'/api/v1/accounts/'.$datas["account_id"].'/conversations/'.$datas["conversation_id"].'/labels',
// 	  CURLOPT_RETURNTRANSFER => true,
// 	  CURLOPT_ENCODING => '',
// 	  CURLOPT_MAXREDIRS => 10,
// 	  CURLOPT_TIMEOUT => 0,
// 	  CURLOPT_FOLLOWLOCATION => true,
// 	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
// 	  CURLOPT_CUSTOMREQUEST => "POST",
// 	    CURLOPT_POSTFIELDS => json_encode($args),
// 	    CURLOPT_HTTPHEADER => [
// 		    // 'api_access_token: MxrTNDvg8EEKCWTkCqH4PZiZ'
// 		  "api_access_token: ".$datas["api_key"],
// 	      "content-type: application/json",
// 	    ],
// 	));

// 	$response = curl_exec($curl);
// 	$err = curl_error($curl);

// 	if ($err) {
// 		var_export($err);exit;
// 	} else {

// 		if (!curl_errno($curl)) {
// 		  switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
// 		    case 200:  # OK
// 		      $response = json_decode($response);
// 		      return $response;
// 		      break;
// 		    default:{
// 		      var_export($response);exit;
// 		    }
// 		  }
// 		}
// 	}

// 	curl_close($curl);
// 	return $response;
// }




// function apci_search_contact($datas){

// 	$curl = curl_init();

// 	curl_setopt_array($curl, array(
// 	  // CURLOPT_URL => 'http://atoomo.siwo.com/api/v1/accounts/2/contacts/search?q=test',
// 	  CURLOPT_URL => $datas["http_host"].'/api/v1/accounts/'.$datas["account_id"].'/contacts/search?q='.$datas["q"],
// 	  CURLOPT_RETURNTRANSFER => true,
// 	  CURLOPT_ENCODING => '',
// 	  CURLOPT_MAXREDIRS => 10,
// 	  CURLOPT_TIMEOUT => 0,
// 	  CURLOPT_FOLLOWLOCATION => true,
// 	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
// 	  CURLOPT_CUSTOMREQUEST => 'GET',
// 	  CURLOPT_HTTPHEADER => array(
// 	    // 'api_access_token: MxrTNDvg8EEKCWTkCqH4PZiZ'
// 	    'api_access_token: '.$datas["api_key"]
// 	  ),
// 	));

// 	$response = curl_exec($curl);
// 	$err = curl_error($curl);

// 	if ($err) {
// 		var_export($err);exit;
// 	} else {

// 		if (!curl_errno($curl)) {
// 		  switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
// 		    case 200:  # OK
// 		      $response = json_decode($response);
// 		      return $response;
// 		      break;
// 		    default:{
// 		      var_export($response);exit;
// 		    }
// 		  }
// 		}
// 	}

// 	curl_close($curl);
// 	return $response;
// }


// function apci_create_contact($datas){

// 	$curl = curl_init();

// 	$url = $datas["http_host"].'/api/v1/accounts/'.$datas["account_id"].'/contacts';
	
// 	$args = ["inbox_id"=>$datas["inbox_id"], "phone_number"=>$datas["phone_number"], "name"=>$datas["name"]];

// 	curl_setopt_array($curl, array(
// 	  CURLOPT_URL => $datas["http_host"].'/api/v1/accounts/'.$datas["account_id"].'/contacts',
// 	  CURLOPT_RETURNTRANSFER => true,
// 	  CURLOPT_ENCODING => '',
// 	  CURLOPT_MAXREDIRS => 10,
// 	  CURLOPT_TIMEOUT => 0,
// 	  CURLOPT_FOLLOWLOCATION => true,
// 	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
// 	  CURLOPT_CUSTOMREQUEST => "POST",
// 	    CURLOPT_POSTFIELDS => json_encode($args),
// 	    CURLOPT_HTTPHEADER => [
// 		    // 'api_access_token: MxrTNDvg8EEKCWTkCqH4PZiZ'
// 		  "api_access_token: ".$datas["api_key"],
// 	      "content-type: application/json",
// 	    ],
// 	));

// 	$response = curl_exec($curl);
// 	$err = curl_error($curl);

// 	if ($err) {
// 		var_export($err);exit;
// 	} else {

// 		if (!curl_errno($curl)) {
// 		  switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
// 		    case 200:  # OK
// 		      $response = json_decode($response);
// 		      return $response;
// 		      break;
// 		    default:{
// 		      var_export($response);exit;
// 		    }
// 		  }
// 		}
// 	}

// 	curl_close($curl);
// 	return $response;
// }

