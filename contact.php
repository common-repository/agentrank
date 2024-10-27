<?php
/**
 *AgentRank Contact Form
 *Since Version 1.0 
*/

/*******************The below checks post from sidebar widget********************/
if(isset($_POST['process_agent_contact_form'])== 'yes_process_con_form'){
//check nonce
$nonce = $_POST['process_agent_contact_nonce'];
if (! wp_verify_nonce($nonce, 'agentrank-form') ) die(); 

//process subject.
$subject = $_POST['subject'];
//return $subject;

//create and populate form.
$agent_contact_form = create_agentrank_contact_form($fname,$lname,$email,$email2,$subject,$body,$status_message);

return $agent_contact_form;
}//end if(isset($_POST['process_agent_contact_form'])== 'yes_process_con_form')



/****The below gets data back to populate form if status code 400 error****/

if(isset($_GET['status'])=='redo'){

$subject = $_GET['subject'];
$body = $_GET['body'];
$fname = $_GET['fname'];
$lname = $_GET['lname'];
$email = $_GET['email'];
$status_message = $_GET['status_message'];

//repopulate form
//create and populate form.
$agent_contact_form = create_agentrank_contact_form($fname,$lname,$email,$email2,$subject,$body,$status_message);

return $agent_contact_form;
}//end if(isset($_GET['status'])=='redo')


/****if not under above if conditions*****/
/****This is a fresh view, show blank form.****/
$agent_contact_form = create_agentrank_contact_form('','','','','','',$status_message);
//output form to browser!
return $agent_contact_form;

//end of contact.php
?>