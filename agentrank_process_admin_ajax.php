<?php
/**
* AgentRank Admin Option Ajax Processing Script
* Since Version 1.0
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

/*******************The below process admin setting values posted from Agent Rank admin********************/

/********************Update Options***************************/
if($_POST['update_apikeys'] == 'yes'){

$nonce_value = $_POST['_wpnonce'];

//check nonce to confirm values posted from AgentRank Admin Page
if (!wp_verify_nonce($nonce_value, 'agent_ajax_nonce') ) die('Failed Security check'); 

//process api key
$post_apikey = stripslashes($_POST['apikey']);
//process username
$post_username = stripslashes($_POST['username']);
//process reCAPTCHA public key
$post_cappubkey = stripslashes($_POST['cappubkey']);
//process reCAPTCHA private key
$post_capprikey = stripslashes($_POST['capprikey']);
//process max result
$post_maxresult = stripslashes($_POST['maxresult']);
//process fullname
$post_fullname = stripslashes($_POST['fullname']);

//update all values into option table!
update_option("agent_apikey",$post_apikey);
update_option("agent_username",$post_username);
update_option("agent_captcha_public",$post_cappubkey);
update_option("agent_captcha_private",$post_capprikey);
update_option("agent_max_results",$post_maxresult);
update_option("agent_fullname",$post_fullname);

echo '<div>';
echo '<p><span class="ui-icon ui-icon-check" style="float:left;margin:0px 5px 0px 0px"></span>API Key</p>';
echo '<p><span class="ui-icon ui-icon-check" style="float:left;margin:0px 5px 0px 0px"></span>RealtyBaron Username</p>';
echo '<p><span class="ui-icon ui-icon-check" style="float:left;margin:0px 5px 0px 0px"></span>Your  Full Name</p>';
echo '<p><span class="ui-icon ui-icon-check" style="float:left;margin:0px 5px 0px 0px"></span>reCAPTCHA Public Key</p>';
echo '<p><span class="ui-icon ui-icon-check" style="float:left;margin:0px 5px 0px 0px"></span>reCAPTCHA Private Key</p>';
echo '<p><span class="ui-icon ui-icon-check" style="float:left;margin:0px 5px 0px 0px"></span>Max results in sidebar</p>';
echo '</div>';
}
/*************************End********************************/

?>