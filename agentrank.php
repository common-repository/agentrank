<?php
/**
 * Plugin Name: Real Estate Agent
 * Plugin URI: http://blog.realtybaron.com/
 * Description: Real Estate Agent plugin enables professionals to host their AgentRank profile in a sidebar.
 * Author: RealtyBaron
 * Author URI: http://blog.realtybaron.com/
 * Version: 1.0.0-rc5
 */
/**
 * Includes all files of AgentRank
 * Since Version 1.0
 */
//agentrank admin notice
require_once(dirname(__FILE__) . "/agentrank-admin-notice.php");
//agentrank admin page
require_once(dirname(__FILE__) . "/agentrank-admin-page.php");
//agentrank rest api
require_once(dirname(__FILE__) . "/agentrank-rest-api.php");
//create pages
require_once(dirname(__FILE__) . "/agentrank-create-page.php");
//functions
require_once(dirname(__FILE__) . "/agentrank-functions.php");
//agentrank sale transactions widget
require_once(dirname(__FILE__) . "/agentrank-sale-transactions-widget.php");
//agentrank client reviews widget
require_once(dirname(__FILE__) . "/agentrank-client-reviews-widget.php");
//agentrank market forecasts widget
require_once(dirname(__FILE__) . "/agentrank-market-forecasts-widget.php");
//agentrank lead form widget
require_once(dirname(__FILE__) . "/agentrank-lead-form-widget.php");
//reCAPTCHA
require_once(dirname(__FILE__) . "/agentrank-recaptchalib.php");
/**
 *Setup widgets
 *since Version 1.0.0-b1
 */
//add widget
add_action('widgets_init', 'load_agentrank_widgets');
//function to register widget
function load_agentrank_widgets() {
  register_widget('AgentRankSaleTransactionsWidget');
  register_widget('AgentRankClientReviewsWidget');
  register_widget('AgentRankMarketForecastsWidget');
  register_widget('AgentRankLeadFormWidget');
}

/**
 *Setup uninstall hook to remove all options
 *since version 1.0
 */
function agentrank_uninstall_options() {
  delete_option('agent_apikey');
  delete_option('agent_max_results');
  delete_option('agent_username');
  delete_option('agent_captcha_public');
  delete_option('agent_captcha_private');
}

register_uninstall_hook(__FILE__, 'agentrank_uninstall_options');
?>