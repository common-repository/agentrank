<?php
/**
 *AgentRank View Market Forecast
 *Since Version 1.0 
*/

$forecast_id = $_GET['id'];

//escape
$forecast_id = esc_attr($forecast_id);
//check is number, if not returns nothing
if(!is_numeric($forecast_id)){
return; 
}

global $agapi;
		
$xml = $agapi->view_market_forecast($forecast_id);

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
    $title = $xml->forecast->title;
	$chart_url = $xml->charts->image['url'];
	$chart_alt = $xml->charts->image['alt'];
	$chart_height = $xml->charts->image['height'];
	$chart_width = $xml->charts->image['width'];
	$body = $xml->forecast->body;
	
	$html  = '<div class="agentrank_sale">';
	$html .= '<h2 class="forecast_title" >'.$title.'</h2>';
	$html .= "<img src='$chart_url' alt='$chart_alt' width='$chart_width' height='$chart_height'/>";
	$html .= "<p>$body</p>";
	$html .= '</div>';
	
    return $html;
	}

}
?>