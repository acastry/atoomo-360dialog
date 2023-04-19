<?php 



// Add the custom admin page to the WordPress admin menu
function atoomo_whatsapp_admin_menu() {
    add_menu_page(
        'ATOOMO WHATSAPP', // Page title
        'ATOOMO WHATSAPP', // Menu title
        'manage_options', // Capability required to access the page
        'atoomo-whatsapp', // Unique menu slug
        'atoomo_whatsapp_settings_page' // Callback function to render the page
    );
}
add_action('admin_menu', 'atoomo_whatsapp_admin_menu');

// Render the custom admin page
function atoomo_whatsapp_settings_page() {
    // Check if the user has permission to access the page
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'atoomo_whatsapp'));
    }

    // Retrieve the current option value
    $atoomo_whatsapp_auth_email = get_option('atoomo_whatsapp_auth_email');
    $atoomo_whatsapp_auth_password = get_option('atoomo_whatsapp_auth_password');
    $atoomo_whatsapp_partner_access_token = get_option('atoomo_whatsapp_partner_access_token');
    $atoomo_whatsapp_partner_id = get_option('atoomo_whatsapp_partner_id');
    $atoomo_whatsapp_partner_web_hook_url = get_option('atoomo_whatsapp_partner_web_hook_url');
    $atoomo_whatsapp_partner_redirect_url = get_option('atoomo_whatsapp_partner_redirect_url');
    $atoomo_whatsapp_partner_web_hook_url_response = get_option('atoomo_whatsapp_partner_web_hook_url_response');
    $atoomo_whatsapp_partner_redirect_url_response = get_option('atoomo_whatsapp_partner_redirect_url_response');

    // Update the option value if the form has been submitted
    if (isset($_POST['atoomo_whatsapp_submit'])) {


        $atoomo_whatsapp_auth_email = $_POST['atoomo_whatsapp_auth_email'];
        update_option('atoomo_whatsapp_auth_email', $atoomo_whatsapp_auth_email);
        $atoomo_whatsapp_auth_password = $_POST['atoomo_whatsapp_auth_password'];
        update_option('atoomo_whatsapp_auth_password', $atoomo_whatsapp_auth_password);

        $atoomo_whatsapp_partner_id = $_POST['atoomo_whatsapp_partner_id'];
        update_option('atoomo_whatsapp_partner_id', $atoomo_whatsapp_partner_id);

        $atoomo_whatsapp_partner_web_hook_url = $_POST['atoomo_whatsapp_partner_web_hook_url'];
        update_option('atoomo_whatsapp_partner_web_hook_url', $atoomo_whatsapp_partner_web_hook_url);

        $atoomo_whatsapp_partner_redirect_url = $_POST['atoomo_whatsapp_partner_redirect_url'];
        update_option('atoomo_whatsapp_partner_redirect_url', $atoomo_whatsapp_partner_redirect_url);


        $atoomo_whatsapp_partner_access_token = false;
        if(
        	// empty(get_option('atoomo_whatsapp_auth_token')) && 
        	!empty(get_option('atoomo_whatsapp_auth_email'))&&
        	!empty(get_option('atoomo_whatsapp_auth_password'))
    		){
        	$atoomo_whatsapp_partner_access_token = get_360dialog_partner_access_token(["email"=>get_option('atoomo_whatsapp_auth_email'), "password"=>get_option('atoomo_whatsapp_auth_password')]);
        }
        if(
        	!empty($atoomo_whatsapp_partner_web_hook_url) && $atoomo_whatsapp_partner_access_token && $atoomo_whatsapp_partner_id && $atoomo_whatsapp_partner_web_hook_url
    		){
        	set_360dialog_partner_web_hook_url(["token"=>$atoomo_whatsapp_partner_access_token, "partner_id"=>$atoomo_whatsapp_partner_id, "url"=>$atoomo_whatsapp_partner_web_hook_url]);
        }
        if(
        	!empty($atoomo_whatsapp_partner_redirect_url) && !empty(get_option('atoomo_whatsapp_partner_web_hook_url_response')) && 
        	$atoomo_whatsapp_partner_access_token && $atoomo_whatsapp_partner_id && $atoomo_whatsapp_partner_web_hook_url
    		){
        	set_360dialog_partner_redirect_url([
        		"token"=>$atoomo_whatsapp_partner_access_token, 
        		"partner_id"=>$atoomo_whatsapp_partner_id, 
        		"webhook_url"=>get_option('atoomo_whatsapp_partner_web_hook_url'),
        		"partner_redirect_url"=>$atoomo_whatsapp_partner_redirect_url
        	]);
        }

        echo '<div class="notice notice-success"><p>' . __('Settings saved.', 'atoomo_whatsapp') . '</p></div>';
    }
    ?>

    <div class="wrap">
        <h1><?php echo __('ATOOMO WHATSAPP Settings', 'atoomo_whatsapp'); ?></h1>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row"><?php echo __('AUTH EMAIL', 'atoomo_whatsapp'); ?></th>
                    <td>
                        <input type="text" name="atoomo_whatsapp_auth_email" value="<?php echo $atoomo_whatsapp_auth_email; ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __('AUTH PASSWORD', 'atoomo_whatsapp'); ?></th>
                    <td>
                        <input type="password" name="atoomo_whatsapp_auth_password" value="<?php echo $atoomo_whatsapp_auth_password; ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __('AUTH TOKEN', 'atoomo_whatsapp'); ?></th>
                    <td>
                        <input type="text" name="atoomo_whatsapp_partner_access_token" value="<?php echo $atoomo_whatsapp_partner_access_token; ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __('PARTNER ID', 'atoomo_whatsapp'); ?></th>
                    <td>
                        <input type="text" name="atoomo_whatsapp_partner_id" value="<?php echo $atoomo_whatsapp_partner_id; ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __('PARTNER WEB HOOK URL', 'atoomo_whatsapp'); ?></th>
                    <td>
                        <input type="text" name="atoomo_whatsapp_partner_web_hook_url" value="<?php echo $atoomo_whatsapp_partner_web_hook_url; ?>">
                        <input type="text" name="atoomo_whatsapp_partner_web_hook_url_response" value="<?php echo $atoomo_whatsapp_partner_web_hook_url_response; ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __('PARTNER REDIRECT URL', 'atoomo_whatsapp'); ?></th>
                    <td>
                        <input type="text" name="atoomo_whatsapp_partner_redirect_url" value="<?php echo $atoomo_whatsapp_partner_redirect_url; ?>">
                        <input type="text" name="atoomo_whatsapp_partner_redirect_url_response" value="<?php echo $atoomo_whatsapp_partner_redirect_url_response; ?>">
                    </td>
                </tr>




            </table>
            <p class="submit">
                <input type="submit" name="atoomo_whatsapp_submit" class="button-primary" value="<?php echo __('Save Changes', 'atoomo_whatsapp'); ?>">
            </p>
        </form>
    </div>

    <?php
}


function get_360dialog_partner_access_token($datas){

	$url = "https://hub.360dialog.io/api/v2/token";
	$headers = array(
	    "Authorization: ",
	    "Content-Type: application/json"
	);
	$data = array(
	    "username" => $datas["email"],
	    "password" => $datas["password"]
	);

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$response = curl_exec($ch);

	$response = json_decode($response);

	$access_token = false;

	if($response && !empty($response->access_token)){
		update_option('atoomo_whatsapp_partner_access_token', $response->access_token);
		$access_token = $response->access_token;
	} else {
		update_option('atoomo_whatsapp_partner_access_token', "");		
	}
	curl_close($ch);

	return $response->access_token;
}

function set_360dialog_partner_web_hook_url($datas){

	$url = "https://hub.360dialog.io/api/v2/partners/{$datas["partner_id"]}/webhook_url";

	$headers = array(
	    "Authorization: Bearer {$datas["token"]}",
	    "Content-Type: application/json"
	);

	$data = array(
	    "webhook_url" => $datas["url"]
	);

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$response = curl_exec($ch);

	$response = json_decode($response);

	if($response && !empty($response->id)){

		update_option('atoomo_whatsapp_partner_web_hook_url_response', $response->id);		
	}	else {
			
		update_option('atoomo_whatsapp_partner_web_hook_url_response', "");		
		return false;
	}

	curl_close($ch);
	return true;
}


function set_360dialog_partner_redirect_url($datas){

	$url = "https://hub.360dialog.io/api/v2/partners/{$datas["partner_id"]}";

	$headers = array(
	    "Authorization: Bearer {$datas["token"]}",
	    "Content-Type: application/json"
	);

	$data = array(
	    "webhook_url" => $datas["webhook_url"],
	    "partner_redirect_url" => $datas["partner_redirect_url"]
	);

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$response = curl_exec($ch);

	$response = json_decode($response);

	if($response && !empty($response->id)){

		update_option('atoomo_whatsapp_partner_redirect_url_response', $response->id);		
	}	else {
			
		update_option('atoomo_whatsapp_partner_redirect_url_response', "");		
		return false;
	}

	curl_close($ch);
	return true;
}
