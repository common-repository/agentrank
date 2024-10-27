<?php
/**
 * AgentRank REST API Class
 * Since Version 1.0
 */
class agentrankapi {
//api key
  var $ag_apikey;
//username from wordpress admin option
  var $ag_username;
//max result from wordpress admin option
  var $ag_max_results;
  //class constructor
  function agentrankapi() {
    //get api key from wordpress admin options
    $this->ag_apikey = get_option('agent_apikey');
    //get agent username from wordpress admin options
    $this->ag_username = get_option('agent_username');
    //get max_results from wordpress admin options
    $this->ag_max_results = get_option('agent_max_results');
  }
  //sidebar widget sale transactions api call
  function sidebar_sale_transactions($start_index) {
    $request_url = "http://www.agentrank.com/api/rest/sales/agent/";
    $request_url .= "$this->ag_username?api_key=$this->ag_apikey";
    $request_url .= "&start_index=$start_index&max_results=$this->ag_max_results";
    $request_url = urlencode($request_url);
    //Adding the "@" symbol in front of any function call will suppress any
    //PHP-generated error messages from that function call.
    $response_xml = @simplexml_load_file($request_url);
    //check xml structure, it should return a status code
    //if not, do not return xml to sidebar widget.
    //Checking of structure will ensure that 404 or 503 from Apache Server, which is html page
    //will not pass through to sidebar widget and cause xml parsing error
    if (!empty($response_xml->status['code'])) {
      return $response_xml;
    }
  }
  function view_sale_transactions($sale_id) {
    $request_url = "http://www.agentrank.com/api/rest/sale/view/";
    $request_url .= "$sale_id?api_key=$this->ag_apikey";
    $request_url = urlencode($request_url);
    $response_xml = @simplexml_load_file($request_url);
    if (!empty($response_xml->status['code'])) {
      return $response_xml;
    }
  }
  function view_all_sales($start_index, $display) {
    $request_url = "http://www.agentrank.com/api/rest/sales/agent/";
    $request_url .= "$this->ag_username?api_key=$this->ag_apikey&chart_size=xsmall";
    $request_url .= "&start_index=$start_index&max_results=$display";
    $request_url = urlencode($request_url);
    $response_xml = @simplexml_load_file($request_url);
    if (!empty($response_xml->status['code'])) {
      return $response_xml;
    }
  }
  //use php curl extension to post data to realtybaron add agent api and parse xml response.
  function register_api_key($postdata) {
    $rs_ch = curl_init("http://www.agentrank.com/api/rest/user/add/AGENT");
    curl_setopt($rs_ch, CURLOPT_POST, 1);
    curl_setopt($rs_ch, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($rs_ch, CURLOPT_HEADER, 0); // DO NOT RETURN HTTP HEADERS
    curl_setopt($rs_ch, CURLOPT_RETURNTRANSFER, 1); // RETURN THE CONTENTS OF THE CALL
    $res = curl_exec($rs_ch);
    curl_close($rs_ch);
    return $res;
  }
  //sidebar widget client reviews api call
  function sidebar_client_reviews($start_index) {
    $request_url = "http://www.agentrank.com/api/rest/reviews/agent/";
    $request_url .= "$this->ag_username?api_key=$this->ag_apikey";
    $request_url .= "&look_back=3&start_index=$start_index&max_results=$this->ag_max_results";
    $request_url = urlencode($request_url);
    $response_xml = @simplexml_load_file($request_url);
    if (!empty($response_xml->status['code'])) {
      return $response_xml;
    }
  }
  function view_client_review($review_id) {
    $request_url = "http://www.agentrank.com/api/rest/review/view/";
    $request_url .= "$review_id?api_key=$this->ag_apikey";
    $request_url = urlencode($request_url);
    $response_xml = @simplexml_load_file($request_url);
    if (!empty($response_xml->status['code'])) {
      return $response_xml;
    }
  }
  function view_all_reviews($start_index, $display) {
    $request_url = "http://www.agentrank.com/api/rest/reviews/agent/";
    $request_url .= "$this->ag_username?api_key=$this->ag_apikey&chart_size=xsmall";
    $request_url .= "&start_index=$start_index&max_results=$display";
    $request_url = urlencode($request_url);
    $response_xml = @simplexml_load_file($request_url);
    if (!empty($response_xml->status['code'])) {
      return $response_xml;
    }
  }
  //sidebar widget market forecasts api call
  function sidebar_market_forecasts($start_index) {
    $request_url = "http://www.agentrank.com/api/rest/forecasts/agent/";
    $request_url .= "$this->ag_username?api_key=$this->ag_apikey";
    $request_url .= "&start_index=$start_index&max_results=$this->ag_max_results";
    $request_url = urlencode($request_url);
    $response_xml = @simplexml_load_file($request_url);
    if (!empty($response_xml->status['code'])) {
      return $response_xml;
    }
  }
  function view_market_forecast($forecast_id) {
    $request_url = "http://www.agentrank.com/api/rest/forecast/view/";
    $request_url .= "$forecast_id?api_key=$this->ag_apikey";
    $request_url = urlencode($request_url);
    $response_xml = @simplexml_load_file($request_url);
    if (!empty($response_xml->status['code'])) {
      return $response_xml;
    }
  }
  function view_all_forecasts($start_index, $display) {
    $request_url = "http://www.agentrank.com/api/rest/forecasts/agent/";
    $request_url .= "$this->ag_username?api_key=$this->ag_apikey";
    $request_url .= "&start_index=$start_index&max_results=$display";
    $request_url = urlencode($request_url);
    $response_xml = @simplexml_load_file($request_url);
    if (!empty($response_xml->status['code'])) {
      return $response_xml;
    }
  }
  function get_username_reminder($email) {
    $request_url = "http://www.agentrank.com/api/xml/user/get/credentials?";
    $request_url .= "email=$email&api_key=$this->ag_apikey";
    $request_url = urlencode($request_url);
    $response_xml = @simplexml_load_file($request_url);
    if (!empty($response_xml->status['code'])) {
      return $response_xml;
    }
  }
  //use php curl extension to post data to realtybaron contact agent api.
  function contact_agent($postdata, $username) {
    $rs_ch = curl_init("http://www.agentrank.com/api/rest/agent/contact/$username");
    curl_setopt($rs_ch, CURLOPT_POST, 1);
    curl_setopt($rs_ch, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($rs_ch, CURLOPT_HEADER, 0); // DO NOT RETURN HTTP HEADERS
    curl_setopt($rs_ch, CURLOPT_RETURNTRANSFER, 1); // RETURN THE CONTENTS OF THE CALL
    $res = curl_exec($rs_ch);
    curl_close($rs_ch);
    return $res;
  }
}

if (!isset($agapi)) {
  $agapi = new agentrankapi;
}
?>