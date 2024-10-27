<?php
/**
* AgentRank helper functions
* Since Version 1.0
*/

// load scripts into theme
function load_agentrank_script(){

    if(!is_admin()){

// load temporary style
        wp_enqueue_style('agentrank_default_css', WP_PLUGIN_URL.'/agentrank/agentrank-styles.css', $deps, '1.0', 'screen');
    }

}

//add script to theme <head>
add_action('init', 'load_agentrank_script');

//load contact form check email script
function load_agent_contact_form_script(){
echo"
<script type='text/javascript'>
function agent_form_check_email(){
var email1 = document.getElementById('email').value;
var email2 = document.getElementById('email2').value;
	if(email1==email2){
	document.getElementById('agent_contact_form_errors').innerHTML = '';
	return true;
	}else{
    var error_msg = '<div class=\"new_question_error\"><ul><li><span class=\"label-highlight\">Email Address and Email Address (re-type)</span> is different.</li></ul></div>';
	document.getElementById('agent_contact_form_errors').innerHTML = error_msg;
	return false;
	}
}
</script>
";
}
add_action('wp_head','load_agent_contact_form_script');

/****function to create registration form****/
function create_agentrank_register_form($fname, $lname, $email, $email2, $role, $status_message) {

    $check_real_apikey = get_option('agent_apikey');

    if (empty($check_real_apikey)) {
        $display = 'display:block;';
    } else {
        $display = 'display:none;';
    }

//remove slashes added in message by PHP, so that style classes are not escaped!
    $status_messages = stripslashes($status_message);

//create form
    $form = "<div class=\"agentrank_register_form\" id=\"agentrank_register_form\"style=\"$display\">";

//title
    $form .= "<h3 class='realanswers_register_form_title'>New Account Registration <span style='font-size:12px; margin:0 0 0 0;'><a href='#' id='register_form_back_link' style='text-decoration:none;' onclick='agent_hide_register_form()'>(If you already have an API key, click here to enter it now.)</a></span></h3>";

    $post_url_raw = WP_PLUGIN_URL . "/agentrank/process_form.php";

    $post_url = esc_url($post_url_raw);

    $form .= "<form name=\"register_form\" id=\"register_form\" onsubmit='real_ajax_register_form();return false;'>";

//form errors
    $form .= "<div id=\"agent_register_form_errors\">$status_messages</div>";

    $form .= "<table class='widefat'>";

    $form .= "<tr>";
//fname
    $form .= "<td width='200px'><label id='fname_label'>Your First Name: </label></td><td><input name=\"fname\" id=\"fname\" value=\"$fname\" type=\"text\" maxlength=\"90\" size=\"50\"></td>";
    $form .= '</tr>';

//lname
    $form .= '<tr class="odd">';
    $form .= "<td><label id='lname_label'>Your Last Name: </label></td><td><input name=\"lname\" id=\"lname\" value=\"$lname\" type=\"text\" maxlength=\"90\" size=\"50\"></td>";
    $form .= '</tr>';

//email
    $form .= '<tr>';
    $form .= "<td><label id='email_label'>Your Email Address: </label></td><td><input name=\"email\" id=\"email\" value=\"$email\" type=\"text\" maxlength=\"90\" size=\"50\"></td>";
    $form .= '</tr>';

//email2 retype
    $form .= '<tr class="odd">';
    $form .= "<td><label id='email2_label'>Your Email Address (retype): </label></td><td><input name=\"email2\" id=\"email2\" value=\"$email2\" type=\"text\" maxlength=\"90\" size=\"50\"></td>";
    $form .= '</tr>';


//create nonce for checking in process_form before posting to api.
//so as to determine data posted from form and not elsewhere.
    $nonce = wp_create_nonce('agentrank-nonce');

    $form .= "</table>";

//submit button
    $form .= '<p>';
    $form .= '<input type="submit" id="form_submit_button" value="Submit" class="button-primary">';

    $register_ajax_image_src = WP_PLUGIN_URL . "/agentrank/indicator.gif";

    $form .= "<img style=\"position: relative; top: 5px; display: none;\" id=\"register_ajax_indicator\" alt=\"register_indicator\" src=\"$register_ajax_image_src\">";
    $form .= '</p>';

    $form .= '</form>';


    $form .= '</div>';

    return $form;
}

/****function to create reminder form****/
function create_agentrank_reminder_form($email3,$status_message) {

//remove slashes added in message by PHP, so that style classes are not escaped!
    $status_messages = stripslashes($status_message);

//create form
    $form = "<div class=\"agentrank_reminder_form\" id=\"agentrank_reminder_form\"style=\"display:none\">";

//title
    $form .= "<h3 class='realanswers_register_form_title'>Username Reminder <span style='font-size:12px; margin:0 0 0 0;'><a href='#' id='register_form_back_link' style='text-decoration:none;' onclick='agent_hide_register_form()'>(If you already know your Username, click here to enter it now.)</a></span></h3>";

    $post_url_raw = WP_PLUGIN_URL . "/agentrank/process_form.php";

    $post_url = esc_url($post_url_raw);

    $form .= "<form name=\"reminder_form\" id=\"reminder_form\" onsubmit='real_ajax_reminder_form();return false;'>";

//form errors
    $form .= "<div id=\"agent_reminder_form_errors\">$status_messages</div>";

    $form .= "<table class='widefat'>";

//email
    $form .= '<tr>';
    $form .= "<td width='118px'><label id='email_label'>Your Email Address: </label></td><td><input name=\"email3\" id=\"email3\" value=\"$email3\" type=\"text\" maxlength=\"90\" size=\"50\"></td>";
    $form .= '</tr>';

//create nonce for checking in process_form before posting to api.
//so as to determine data posted from form and not elsewhere.
    $nonce = wp_create_nonce('agentrank-nonce');

    $form .= "</table>";

//submit button
    $form .= '<p>';
    $form .= '<input type="submit" id="form_submit_button2" value="Submit" class="button-primary">';

    $register_ajax_image_src = WP_PLUGIN_URL . "/agentrank/indicator.gif";

    $form .= "<img style=\"position: relative; top: 5px; display: none;\" id=\"register_ajax_indicator2\" alt=\"register_indicator\" src=\"$register_ajax_image_src\">";
    $form .= '</p>';

    $form .= '</form>';


    $form .= '</div>';

    return $form;
}

function create_agentrank_contact_form($fname,$lname,$email,$email2,$subject,$body,$status_message){
    
	//remove slashes added in message by PHP, so that style classes are not escaped!
    $status_messages = stripslashes($status_message);

//create form
    $form = '<div class="agent_contact_form_wrap">';
	
	$ag_fullname = get_option('agent_fullname');
	
    $form .= "<h2 class='agent_contact_form_title'>Connect with $ag_fullname</h2>";

    $post_url_raw = WP_PLUGIN_URL . "/agentrank/process_form.php";

    $post_url = esc_url($post_url_raw);

    $form .= "<form action=\"$post_url\" method=\"post\" name='agent_contact_form' id='agent_contact_form' class='agent_contact_form' onsubmit='return agent_form_check_email();'>";

//form errors
    $form .= "<div id=\"agent_contact_form_errors\">$status_messages</div>";

    $form .= "<div id=\"realanswers_particulars\" style=\"margin:0px 0px 20px 0px\">";

    $form .= '<div id="form_fname" class="ag_form_elements">';
    $form .= "<label id='fname_label'>First Name: </label><input name=\"fname\" id=\"fname\" value=\"$fname\" type=\"text\" maxlength=\"90\">";
    $form .= '</div>';

//Last name
    $form .= '<div id="form_lname" class="ag_form_elements">';
    $form .= "<label id='lname_label'>Last Name: </label><input name=\"lname\" id=\"lname\" value=\"$lname\" type=\"text\" maxlength=\"90\">";
    $form .= '</div>';

//Email
    $form .= '<div id="form_email" class="ag_form_elements">';
    $form .= "<label id='email_label'>Email Address: </label><input name=\"email\" id=\"email\" value=\"$email\" type=\"text\" maxlength=\"90\">";
    $form .= '</div>';
	
//Email2
    $form .= '<div id="form_email" class="ag_form_elements">';
    $form .= "<label id='email_label'>Email Address: (re-type) </label><input name=\"email2\" id=\"email2\" value=\"$email2\" type=\"text\" maxlength=\"90\">";
    $form .= '</div>';
//subject	
	$form .= '<div id="form_subject" class="ag_form_elements">';
    $form .= "<label id='subject_label'>Subject: </label><input name=\"subject\" id=\"subject\" value=\"$subject\" type=\"text\" maxlength=\"90\">";
    $form .= '</div>';
//body	
	$form .= '<div id="form_body" class="ag_form_elements">';
    $form .= "<label id='body_label'>Question / Comment: </label><textarea name=\"body\" id=\"body\">$body</textarea>";
    $form .= '</div>';
	
	$form .= '<div id="form_captcha" class="ag_form_elements">';
	$form .= '<br/>';
//reCAPTCHA 
    $agent_captcha_public = get_option('agent_captcha_public');
    $form .= ag_recaptcha_get_html($agent_captcha_public);
    $form .= '</div>';
	
//create nonce for checking in process_form before posting to api.
//so as to determine data posted from form and not elsewhere.
    $nonce = wp_create_nonce('agentrank-nonce');

//submit button
    $form .= '<div id="form_submit" class="ag_form_elements">';
    $form .= "<input type=\"hidden\" name=\"_wpnonce\" value=\"$nonce\"/>";
    $form .= '<input type="hidden" name="process_contact_form" value="yes_process_contact_form"/>';
    $form .= '<input type="submit" class="ag_form_elements_button" value="Submit">';
	$form .= '<input type="reset" class="ag_form_elements_button" value="Reset">';
    $form .= '</div>';

    $form .= '</div>'; //end of div real answers particulars
	
    $form .= '</form>';

    $form .= '</div>';

    return $form;

}
?>