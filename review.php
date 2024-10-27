<?php
/**
 *AgentRank View Sale Transactions
 *Since Version 1.0 
*/

$review_id = $_GET['id'];

//escape
$review_id = esc_attr($review_id);
//check is number, if not returns nothing
if(!is_numeric($review_id)){
return; 
}

global $agapi;
		
$xml = $agapi->view_client_review($review_id);

//if empty response print service unavailable message
if(empty($xml)){
$htm = "<div class='sidebar_error'>Service is Unavailable</div>";
return $htm;
}

//check that $xml response is returned from api
if(!empty($xml)){

	if ($xml->status['code']=='400'){
	$error_message=$xml->status->messages->message;
	return "<p>$error_message</p><p>Please check your admin setting</p>";
	}
	
	if ($xml->status['code']=='200'){
	//prepared returned data
    $title = $xml->review->title;
	$comment = $xml->review->comment;
	$date_time = $xml->review->date;
	$date = array();
	//separate api returned date time.
	//example 2010-11-29T12:00:39-08:00
	//explode T and use first array variable
	$date = explode("T",$date_time);
	$name = $xml->review->name;
	//$recommend = $xml->review->recommend;
	//$satisfied = $xml->review->satisfied;
	
	$html  = '<div class="agentrank_review">';
	$html .= '<h2 class="review_title" >'.$title.'</h2>';
	$html .= '<blockquote><p>'.$comment.'</p></blockquote>';
	$html .= '<p class="review_person" >'.$date[0].' by '.$name.'</p>';
	$html .= '</div>';
	
    return $html;
	}

}
?>