<?php
/**
* AgentRank Admin Page
* Since Version 1.0
*/

// Hook for adding admin menus
add_action('admin_menu', 'agentrank_add_option_page');

function agentrank_add_option_page(){

//add settings page with ajax processing  
$agentrank_insert_javascript_admin_head = add_options_page('AgentRank Settings','Real Estate Agent','manage_options','agentrank_option','agentrank_plugin_admin');

//print javascript in AgentRank setting page only
add_action("admin_print_scripts-$agentrank_insert_javascript_admin_head", "agentrank_hook_javascript_in_admin_head");
	
//whitelist agentrank_options 
register_setting('agentrank_plugin_options', 'agent_apikey');
register_setting('agentrank_plugin_options', 'agent_username');
register_setting('agentrank_plugin_options', 'agent_max_results');
register_setting('agentrank_plugin_options', 'agent_captcha_public');
register_setting('agentrank_plugin_options', 'agent_captcha_private');
register_setting('agentrank_plugin_options', 'agent_fullname');

}

//AgentRank Admin Page
function agentrank_plugin_admin(){

?>
    <div class="wrap">
            
    <?php
    //realtybaron logo
    $logo_url = WP_PLUGIN_URL . '/agentrank/realtybaron_logo.gif';
    echo "<div style='margin:10px 0px 0px 0px'><a href='http://realtybaron.com' target='_blank'><img src='$logo_url' alt='logo' id='logo' style='float:left;margin:0px 10px 0px -10px'></a>";
    ?><h2 style="padding:17px 0px 0px 0px;display:block">AgentRank Settings</h2></div>
    
    <?php
    //check api key to determine whether to show settings form or register form
	$check_real_apikey = get_option('agent_apikey');

		if(empty($check_real_apikey)){
		$display_setting = 'display:none;';
		}else{
		$display_setting = 'display:block;';
		}
	
    ?>
    
    <?php 
	//print out registration form
	echo create_agentrank_register_form($fname,$lname,$email,$email2,$role,$status_message);
	?>
    
    <?php 
	//print out reminder form
	echo create_agentrank_reminder_form($email,$status_message);
	?>
    
    <div id="settings_form" style="<?php echo $display_setting?>">
    
    <div id='error_dialog' style="display:none"></div>

    <form name="AgentRankForm1" id="AgentRankForm1" method="post" action=""
          onsubmit="agentrank_update_apikeys();return false;">

    <?php settings_fields('agentrank_plugin_options'); ?>

        <p>
            <strong>API Key:</strong>
            <input type="text" id="agent_apikey" name="agent_apikey" style="width:200px"
                   value="<?php $agent_apikey = get_option('agent_apikey'); echo $agent_apikey; ?>"
                   onchange="agent_highlight_input();"/>
            (Need an API key? <a href="#" onclick="agent_show_register_form()">Request
            one here</a>)           
        </p>
        
        <p>
            <strong>RealtyBaron Username:</strong>
            <input type="text" id="agent_username" name="agent_username" style="width:200px"
                   value="<?php $agent_username = get_option('agent_username'); echo $agent_username; ?>"/>
            (Forgot username? <a href="#" onclick="agent_show_reminder_form()">Ask for a reminder.</a>)
         
        </p>
        
         <p>
            <strong>Your Full Name:</strong>
            <input type="text" id="agent_fullname" name="agent_fullname" style="width:200px"
                   value="<?php $agent_fullname = get_option('agent_fullname'); echo $agent_fullname; ?>"/>
            (Your full name to be used in Contact Form title.)
         
        </p>

        <p>
            <strong>reCAPTCHA Public Key:</strong>
            <input type="text" id="agent_captcha_public" name="agent_captcha_public" style="width:350px"
                   value="<?php $agent_captcha_public = get_option('agent_captcha_public'); echo $agent_captcha_public; ?>"
                   onchange="agent_highlight_input();"/>
            (Need an API key? <a href="https://admin.recaptcha.net/accounts/signup/?next=%2Frecaptcha%2Fcreatesite%2F"
                                 target="_blank">Request one here</a>)
        </p>

        <p>
            <strong>reCAPTCHA Private Key:</strong>
            <input type="text" id="agent_captcha_private" name="agent_captcha_private" style="width:350px"
                   value="<?php $agent_captcha_private = get_option('agent_captcha_private'); echo $agent_captcha_private; ?>"
                   onchange="agent_highlight_input();"/>
        </p>

        <p>
            <strong>Max results in sidebar:</strong>
            <input type="text" id="agent_max_results" name="agent_max_results" style="width:25px"
                   value="<?php $agent_max_results = get_option('agent_max_results'); echo $agent_max_results; ?>"
                   onchange="agent_highlight_input();"/>
        </p>

        <p class="submit">
            <input type="hidden" name="submitted1" value="update_keys"/>
            <input type="submit" name="submit" value="Update Plugin Settings" class="button-primary"/>
        <?php
        //ajax image indicator
        $keys_indicator_url = WP_PLUGIN_URL . '/agentrank/indicator.gif';
        echo "<img src='$keys_indicator_url' alt='keys_indicator' id='keys_ajax_indicator' style='position:relative;top:5px;display:none;'/>";
        ?>
        </p>

    </form>
    
    </div>
    
    </div>
    
<?php
}//end og AgentRank Admin Page




//AgentRank Admin Ajax!
function agentrank_hook_javascript_in_admin_head(){
    
    //assign values to variable for use in creating javascripts below!
    $site_url = get_bloginfo('url');
    $plugin_url = WP_PLUGIN_URL;
    $agent_ajax_url = WP_PLUGIN_URL . '/agentrank/agentrank_process_admin_ajax.php';
	$agent_ajax_register_url = WP_PLUGIN_URL . '/agentrank/process_form.php';
    $agent_nonce = wp_create_nonce('agent_ajax_nonce');
	
echo <<<END

<!--The following scripts are generated by AgentRank admin settings--->
<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js'></script>
<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js'></script>
<link href='http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/ui-lightness/jquery-ui.css' rel='stylesheet' type='text/css'/>

<!--scripts for AJAX update options!-->
<script type="text/javascript">

// check to see if input is alphanumeric
function isAlphaNumeric(val)
{
	if (val.match(/^[a-zA-Z0-9]+$/))
	{
	return true;
	}
	else
	{
	return false;
	} 
}

//remove all spaces before saving in option! so that not errors in api calls!
function removeSpaces(string) {
 return string.split(' ').join('');
}

//test for integer
function is_int(value){
  if((parseFloat(value) == parseInt(value)) && !isNaN(value)){
      return true;
  } else {
      return false;
  }
}
 
function agentrank_update_apikeys(){

//show ajax indicator!
$('#keys_ajax_indicator').show();

//do form validation first!

var error_message = "";//declare global error message string!

//check API key
var check_api_key = $('#agent_apikey').val();
check_api_key = removeSpaces(check_api_key);
if(check_api_key==0){
error_message+='<li>API Key is empty! Please enter API Key or Request for one!</li>';
}

if(isAlphaNumeric(check_api_key)==false){
error_message+='<li>Only Alphanumeric Characters are allowed in API Key!</li>';
}

var api_length = check_api_key.length;
if(api_length>16){
error_message+='<li>Only 16 Alphanumeric Characters are allowed in API Key!</li>';
}

//check Realty Baron username
var check_username = $('#agent_username').val();
check_username = removeSpaces(check_username);
if(check_username==0){
error_message+='<li>Realty Baron Username is empty! Please enter your username!</li>';
}

var check_fullname = $('#agent_fullname').val();
if(check_fullname==0){
error_message+='<li>Fullname is empty! Please enter your Fullname!</li>';
}


if(isAlphaNumeric(check_username)==false){
error_message+='<li>Only Alphanumeric Characters are allowed in Realty Baron Username!</li>';
}

var user_length = check_username.length;
if(user_length>15){
error_message+='<li>Only 15 Alphanumeric Characters are allowed in Realty Baron Username!</li>';
}

//check CAPCHA Pub key
var check_capcha_pub_key = $('#agent_captcha_public').val();
check_capcha_pub_key = removeSpaces(check_capcha_pub_key);
if(check_capcha_pub_key==0){
error_message+='<li>reCAPTCHA Public Key is empty! Please enter Key or Request for one!</li>';
}

//check CAPCHA Private key
var check_capcha_pri_key = $('#agent_captcha_private').val();
check_capcha_pri_key = removeSpaces(check_capcha_pri_key);
if(check_capcha_pri_key==0){
error_message+='<li>reCAPTCHA Private Key is empty! Please enter Key or Request for one!</li>';
}

//check Max results in sidebar
var check_max = $('#agent_max_results').val();
check_max = removeSpaces(check_max);
if(check_max==0){
error_message+='<li>Max results in sidebar is empty! Please enter a value!</li>';
}

if(is_int(check_max)==false){
error_message+='<li>Max results in sidebar is not an integer! Please enter whole numbers only!</li>';
}

//check whether got error message, if there is, display it!
if(error_message!=""){
$('#error_dialog').empty();
$('#error_dialog').html('<div><br/><ol>'+error_message+'</ol></div>');
$("#error_dialog").dialog("destroy");
$("#error_dialog").dialog({
	modal: true,
	title: '<span class="ui-icon ui-icon-alert" style="float:left; margin:2px 5px 0px 0px;"></span>Attention! There are problems with the following field(s),',
	width:500,
	buttons: {
				Ok: function() {
					$(this).dialog('close');
				}
			}

});
$('#keys_ajax_indicator').hide();
}else{
// if no error message!

//hide the dialog box
$('#error_dialog').hide();

//concatenate post data string!
var postdata = 'update_apikeys=yes&_wpnonce=$agent_nonce';
    postdata += '&apikey='+check_api_key+'&username='+check_username+'&cappubkey='+check_capcha_pub_key+'';
	postdata += '&capprikey='+check_capcha_pri_key+'&maxresult='+check_max+'&fullname='+check_fullname+'';
	
//post values to process_admin_ajax.php using jQuery AJAX!
$.ajax({
	   url: '$agent_ajax_url',
	   type: 'Post',
	   cache: false,
	   data: postdata,
	   success: function(data) {
			  $('#error_dialog').empty();
			  $('#error_dialog').append(data);
			  $("#error_dialog").dialog("destroy");
			  $("#error_dialog").dialog({
					modal: true,
					title: 'Success! All Settings are updated!',
					buttons: {
								Ok: function() {
									$(this).dialog('close');
								}
							}

				});
			  //hide ajax indicator!
              $('#keys_ajax_indicator').hide();
			  }
	 });


}//end else
		  
}//end of function
</script>
<script type="text/javascript">
//show reminder form and hide register and settings form
function agent_show_reminder_form(){
$('#agentrank_reminder_form').show();
$('#agentrank_register_form').hide();
$('#settings_form').hide();
}
</script>
<script type="text/javascript">
//show register form and hide settings form
function agent_show_register_form(){
$('#agentrank_register_form').show();
$('#agentrank_reminder_form').hide();
$('#settings_form').hide();
}
</script>
<script type="text/javascript">
//hide register form and show settings form
function agent_hide_register_form(){
$('#agentrank_register_form').hide();
$('#agentrank_reminder_form').hide();
$('#settings_form').show();
}
</script>
<script type="text/javascript">
//Register form validation
function real_ajax_register_form(){

    $('#agent_register_form_errors').empty();
	//error mesaage
	var error_message = '';
	
	//retrieve role value
	
	var check_role = $('input:radio[name=role]:checked').val();
	
	//check for empty values
	var check_fname = $('#fname').val();

	
	var check_lname = $('#lname').val();

	
	var check_email = $('#email').val();
	var check_email2 = $('#email2').val();
	
	if(check_email!==check_email2){
	error_message += '<li>Email Address (retype) is different from Email Address!</li>';
	}
		
	if(error_message!==''){
	var show_error = '<div id="register_error_message" class="updated fade">';
	show_error += '<p><strong>The following needs your attention!</strong></p><ol>';
	show_error += error_message;
	show_error += '</ol></div>';
	$('#agent_register_form_errors').html(show_error);
	}
	
	if(error_message==''){
	//if pass form validation, register via ajax!
	$('#register_ajax_indicator').show();

		//concatenate post data string!
		var postdata = 'process_register_form=yes_process&_wpnonce=$agent_nonce';
		postdata += '&fname='+check_fname+'&lname='+check_lname+'&email='+check_email+'&role='+check_role+'';
		 
		
		//post values to process_form.php using jQuery AJAX!
		$.ajax({
			   url: '$agent_ajax_register_url',
			   type: 'Post',
			   cache: false,
			   data: postdata,
			   success: function(data) {
					  $('#agent_register_form_errors').empty();
					  $('#agent_register_form_errors').append(data);
					  $('#register_ajax_indicator').hide();
					  }
			 });
	
	}
}
//Reminder form validation
function real_ajax_reminder_form(){

    $('#agent_reminder_form_errors').empty();
	//error mesaage
	var error_message = '';
	
	var check_api = $('#agent_apikey').val();
	
	if(check_api == ''){
	error_message += '<li>Sorry, an API Key is needed for Username Reminder request, Please <a href="#" onclick="agent_show_register_form()">click here</a> to Register for an API Key. <a href="#" onclick="agent_hide_register_form()">(If you already have an API key, click here to enter it now.)</a></li>';
	}
	
	var check_email = $('#email3').val();
			
	if(check_api!=='' && check_email == ''){
	error_message += '<li>Email Address is needed for Username Reminder!</li>';
	}
	
//	if(check_email!=='' && validateEmail(check_email) == false){
//	error_message += '<li>Please enter a valid Email Address!</li>';
//	}
		
	if(error_message!==''){
	var show_error = '<div id="register_error_message" class="updated fade">';
	show_error += '<p><strong>The following needs your attention!</strong></p><ol>';
	show_error += error_message;
	show_error += '</ol></div>';
	$('#agent_reminder_form_errors').html(show_error);
	}
	
	if(error_message==''){
	//if pass form validation, register via ajax!
	$('#register_ajax_indicator2').show();

		//concatenate post data string!
		var postdata = 'process_reminder_form=yes_process&_wpnonce=$agent_nonce';
		postdata += '&email='+check_email+'';
		 
		
		//post values to process_form.php using jQuery AJAX!
		$.ajax({
			   url: '$agent_ajax_register_url',
			   type: 'Post',
			   cache: false,
			   data: postdata,
			   success: function(data) {
					  $('#agent_reminder_form_errors').empty();
					  $('#agent_reminder_form_errors').append(data);
					  $('#register_ajax_indicator2').hide();
					  }
			 });
	
	}
}

//function to valicate email address
function validateEmail(elementValue){
   var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
   return emailPattern.test(elementValue);
 }
</script>
END;
}
?>