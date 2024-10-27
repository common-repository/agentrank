<?php
/**
 *AgentRank View Sale Transactions
 *Since Version 1.0 
*/

$sale_id = $_GET['id'];

//escape
$sale_id = esc_attr($sale_id);
//check is number, if not returns nothing
if(!is_numeric($sale_id)){
return; 
}

global $agapi;
		
$xml = $agapi->view_sale_transactions($sale_id);

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
    $title = $xml->sale->title;
	$address = $xml->sale->address;
	$city = $xml->sale->city;
	$list_price = $xml->sale->list_price_original;
	$sale_price = $xml->sale->sale_price_final;
	$map_url = $xml->maps->image['url'];
	$map_alt = $xml->maps->image['alt'];
	$map_width = $xml->maps->image['width'];
	$map_height = $xml->maps->image['height'];
	
	$html  = '<div class="agentrank_sale">';
	$html .= '<h2 class="sale_title" >'.$title.'</h2>';
	$html .= '<p class="sale_address" >Address : '.$address.' '.$city.'</p>';
    $html .= '<p class="sale_listing_price" >Listing Price : $'.number_format($list_price).'</p>';
	$html .= '<p class="sale_price" >Sale Price : $'.number_format($sale_price).'</p>';
	$html .= "<img src='$map_url' alt='$map_alt' width='$map_width' height='$map_height' class='sale_map'/>";
	$html .= '</div>';
	
    return $html;
	}

}
?>