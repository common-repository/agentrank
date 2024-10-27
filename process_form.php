<?php
/**
* processes all post datas to API.
*/

//include wp-config
$root = dirname(dirname(dirname(dirname(__FILE__))));
if (file_exists($root.'/wp-load.php')) {
// WP 2.6
require_once($root.'/wp-load.php');
} else {
// Before 2.6
require_once($root.'/wp-config.php');
}

/**process API registration form!*********************/

if(isset($_POST['process_register_form'])== 'yes_process'){
	
	$nonce_value = $_POST['_wpnonce'];
	
	if (!wp_verify_nonce($nonce_value, 'agent_ajax_nonce') ) die('Failed Security check');
	$fname = $_POST['fname'];
	$lname = $_POST['lname'];
	$email = $_POST['email'];
	$fname = rawurlencode($fname);
	$lname = rawurlencode($lname);
	$email = rawurlencode($email);

	$postdata = "fname=$fname&lname=$lname&email=$email";


	global $agapi;
	 
	$post_response = $agapi->register_api_key($postdata);
		
	 /****Start error checking*****/
	 
	 //check if empty response from api response with service unavailable message
	 if(empty($post_response)){
     echo '<div id="register_error_message" class="updated fade"><p><strong>Sorry, Service Unavailable.';
	 echo ' Please try again later</p></strong></div>';
	 die(); 
	 }
	 
	 //if not empty response try whether it is xml, if not response with service unavailable message
	 try{
	 $xml = @new SimpleXMLElement($post_response);
	   //check if there is status code, if not it is probably 404 or 503 apache html response
	   if(empty($xml->status['code'])){
       echo '<div id="register_error_message" class="updated fade"><p><strong>Sorry, Service Unavailable.';
	   echo ' Please try again later</p></strong></div>';
	   die();  
	   }
     }	 
	 catch(Exception $e)
     {
	  //xml error message of unable to parse string as xml
	  //$message = $e->getMessage();
	  //response with service unavailable message
     echo '<div id="register_error_message" class="updated fade"><p><strong>Sorry, Service Unavailable.';
	 echo ' Please try again later</p></strong></div>';
	 die(); 
	 }
	 
     /*********end error checking************/
	 
	 	 //get status code 400 error 200 ok
	 $status_code = $xml->status['code'];
	 
	 if($status_code == '500'){//server internal error
	
	//echo error message
	echo "<div id=\"message\" class=\"updated fade\">";
	echo "<strong><p>Sorry, registration was not successful. Please try again later.</p></strong>";
	echo "</div>";
	   
	  }//end foreach
		 
	 
	 if($status_code == '400'){// error
	
	 $account_exist = '';
	
	 //parse error message response and show to user
	 $status_message = "<div id=\"register_error_message\" class=\"updated fade\"><p><strong>The following needs your attention!</strong></p><ol>"; 
	 foreach ($xml->status->messages->message as $mess){
	     
		 //check if account exist message, if exist change variable to true.
		 if(preg_match("/^Your account already exists/", $mess)){$account_exist='true';}
		 
		 //populate message	  
		 $status_message .= "<li>".$mess."</li>";
		
		 }
		 
     $status_message .= "</ol></div>";
	 
		 if($account_exist=='true'){
		 //if account exist, add in javascript to hide form.
		 $java_script_hide = '<script type="text/javascript">agent_hide_register_form();';
		 $java_script_hide .="$('#error_dialog').empty();$('#error_dialog').append('$status_message');$('#error_dialog').show();</script>";
		 //reset
		 $account_exist = '';
		 echo $java_script_hide;
		 }else{
		 //echo normal error message.	 
		 echo $status_message;
		 }
	   
	   }//end if($status_code == '400')
	   
	 
	 if($status_code == '200'){// success
	 
         $response_api_key = $xml->id;
		 $response_username = $xml->login;
		 
		 //cast xml object into array
		 $response_api_key_array = (array)$response_api_key;
		 //assign index [0] which is the api key to updated into option
		 $update_in_option_apikey = $response_api_key_array[0];
		 
		 $response_username_array = (array)$response_username;
		 $update_in_option_username = $response_username_array[0];
	   
	     update_option('agent_apikey',$update_in_option_apikey);
		 update_option('agent_username',$update_in_option_username);
	
	//echo script to hide admin warning
	echo"<script type='text/javascript'>$('#realanswers-warning').hide();$('#register_form_back_link').hide();</script>";
	
	$admin_setting_url = admin_url()."options-general.php?page=agentrank_option";
		 
	//echo success message in header!
	echo "<div id=\"message\" class=\"updated fade\">";
	echo "<strong><p>Your registration was successful! <a style=\"text-decoration: none;\" href=\"$admin_setting_url\">Please click here to setup remaining options.</a></p></strong>";
	echo "</div>";
	   
	   }//end if($status_code == '200')
	 
}//end if(isset($_POST['process_register_form'])== 'yes_process')


/**process API reminder form!*********************/

if(isset($_POST['process_reminder_form'])== 'yes_process'){
	
	$nonce_value = $_POST['_wpnonce'];
	
	if (!wp_verify_nonce($nonce_value, 'agent_ajax_nonce') ) die('Failed Security check');

	 $email = $_POST['email'];
	 
	 $email = rawurlencode($email);

	 global $agapi;
	 
	 $xml = $agapi->get_username_reminder($email);
 
	 //check if empty response from api response with service unavailable message
	 if(empty($xml)){
     echo '<div id="register_error_message" class="updated fade"><p><strong>Sorry, Service Unavailable.';
	 echo ' Please try again later</p></strong></div>';
	 die(); 
	 }

	 
	 //get status code 
	 $status_code = $xml->status['code'];
	 
	 if($status_code == '500'){//server internal error
	
	//echo error message
	echo "<div id=\"message\" class=\"updated fade\">";
	echo "<strong><p>Sorry, Service Unavailable. 3 Please try again later.</p></strong>";
	echo "</div>";
	   
	  }//end foreach
		 
	 
	 if($status_code == '400'){// error
	
	 //parse error message response and show to user
	 $status_message = "<div id=\"register_error_message\" class=\"updated fade\"><p><strong>The following needs your attention!</strong></p><ol>"; 
	 foreach ($xml->status->messages->message as $mess){ 
		 $status_message .= "<li>".$mess."</li>";
		 }
     $status_message .= "</ol></div>";
	 
	 echo $status_message;
	   
	   }//end foreach
	   
	   
	 if($status_code == '404'){// error
	
	 //parse error message response and show to user
	 $status_message = "<div id=\"register_error_message\" class=\"updated fade\"><p><strong>The following needs your attention!</strong></p><ol>"; 
	 foreach ($xml->status->messages->message as $mess){ 
		 $status_message .= "<li>".$mess."</li>";
		 }
     $status_message .= "</ol></div>";
	 
	 echo $status_message;
	   
	   }//end foreach
	   
	 
	 if($status_code == '200'){// success
	 
	 $message = $xml->status->messages->message;
	 
	//echo success message in header!
	echo "<div id=\"message\" class=\"updated fade\">";
	echo "<strong><p>$message, <a style=\"text-decoration: none;\" href=\"$admin_setting_url\">Please click here to setup remaining options.</a></p></strong>";
	echo "</div>";
	   
	   }//end foreach
	 
}//end if(isset($_POST['process_reminder_form'])== 'yes_process')



/*************The below checks post from contact.php agent contact form********************/

if(isset($_POST['process_contact_form'])== 'yes_process_contact_form'){

//recaptcha codes
require_once(WP_PLUGIN_DIR.'/agentrank/agentrank-recaptchalib.php');
$real_captcha_private = get_option('agent_captcha_private');
$resp = ag_recaptcha_check_answer ($real_captcha_private,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);
								
//check wp_nonce first!
$nonce_value = $_POST['_wpnonce'];
if (!wp_verify_nonce($nonce_value, 'agentrank-nonce') ) die('Failed Security check'); 


//data posted from form assigned to variables to be posted to realtybaron post api
$subject = $_POST['subject'];
$body = $_POST['body'];
$api_key = get_option('agent_apikey');
$username = get_option('agent_username');
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$email = $_POST['email'];

//recaptcha check
//if invalid return form as redo with posted contents.
if (!$resp->is_valid) {

	$status_message = "<div class='new_question_error'><ul>"; 
	$status_message .= "<li><span class='label-highlight'>Incorrect CAPTCHA.</span></li>";
	$status_message .= "<li><span class='label-highlight'>Please re-type the two words found in the reCAPTCHA form.</span></li>";
	$status_message .= "</ul></div>";
	 
//url encode data before sending back to form, so as not to cause newline error in header() function.
	 $subject_e = rawurlencode($subject);
	 $body_e = rawurlencode($body);
	 $fname_e = rawurlencode($fname);
	 $lname_e = rawurlencode($lname);
     $email_e = rawurlencode($email);
	 $message_e = rawurlencode($status_message);

	 //construction url back to newquestion form
	 $redo_url = get_bloginfo('url')."/agentrank/contact";
	 
	 //populate content to send back to form in newquestions.php
	 $content = "status=redo&subject=$subject_e&body=$body_e";
	 $content .= "&fname=$fname_e&lname=$lname_e&email=$email_e&status_message=$message_e";

     //redirect back to form with populated values.
	 header("location:$redo_url/?$content");
     
	 //prevent php from executing further!
     die();
}//end recaptcha check

//construct post data to be posted to realtybaron post api
$postdata = "fname=$fname&lname=$lname&email=$email&subject=$subject&body=$body&api_key=$api_key";

	 //use php curl extension to post data to realtybaron post api and parse xml response.
	 
	 global $agapi;
	 
	 $post_response = $agapi->contact_agent($postdata,$username);
	 
	 /****Start error checking*****/
	 
	 //check if empty response from api redirect with service unavailable message
	 if(empty($post_response)){
	 $redo_url = get_bloginfo('url')."/agentrank/contact";
	 header("location:$redo_url/?status=redo&status_message=Service Unavailable");
	 }
	 
	 //if not empty response try whether it is xml, if not redirect back with service unavailable message
	 try{
	 $xml = @new SimpleXMLElement($post_response);
	   //check if there is status code, if not it is probably 404 or 503 apache html response
	   if(empty($xml->status['code'])){
	   $redo_url = get_bloginfo('url')."/agentrank/contact";
	   header("location:$redo_url/?status=redo&status_message=Service Unavailable");
	   }
     }	 
	 catch(Exception $e)
     {
	  //xml error message of unable to parse string as xml
	  //$message = $e->getMessage();
	  //construction url back to newquestion form redirect back with service unavailable message
	  $redo_url = get_bloginfo('url')."/agentrank/contact";
	  header("location:$redo_url/?status=redo&status_message=Service Unavailable");
	 }
	 
     /*********end error checking************/
	 
	 
	 //get status code 400 error 200 ok
	 $status_code = $xml->status['code'];
		 
	 if($status_code == '200'){// status ok show user the message
	
		 $status_message = "<div class='new_question_error'>"; 

			 $status_message .= "<p class=\"label-highlight\"><strong>Thank you for your Question/Comment. Our Agent will contact you shortly.</strong></p>";

		 $status_message .= "</div>";
		 
		 $message_e = rawurlencode($status_message);
		 
		 //construction url back to newquestion form
		 $redo_url = get_bloginfo('url')."/agentrank/contact";
		 
		 $content = "status=redo&status_message=$message_e";
		 //redirect back to form with populated values.
	     header("location:$redo_url/?$content");
   
     
	 }elseif($status_code == '400'){// status error show error message and set form status to redo
	 
	 $status_message = "<div class='new_question_error'><ul>"; 
	 foreach ($xml->status->messages->message as $mess){ 
		 $status_message .= "<li>".$mess."</li>";
		 }
     $status_message .= "</ul></div>";
	 
//url encode data before sending back to form, so as not to cause newline error in header() function.
	 $subject_e = rawurlencode($subject);
	 $body_e = rawurlencode($body);
	 $fname_e = rawurlencode($fname);
	 $lname_e = rawurlencode($lname);
     $email_e = rawurlencode($email);
	 $message_e = rawurlencode($status_message);

	 //construction url back to newquestion form
	 $redo_url = get_bloginfo('url')."/agentrank/contact";
	 
	 //populate content to send back to form in newquestions.php
	 $content = "status=redo&subject=$subject_e&body=$body_e";
	 $content .= "&fname=$fname_e&lname=$lname_e&email=$email_e&status_message=$message_e";

     //redirect back to form with populated values.
	 header("location:$redo_url/?$content");
	 
	 }//end if($status_code == '200')

}
?>