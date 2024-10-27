<?php
/**
* AgentRank Admin Notice
* Since Version 1.0
*/

//function to get current page url to be used for 
//condition check in the below function agentrank_admin_warning()
function agentrank_current_page_url() {
    $pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    }
    else
    {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

//admin warnings notice if any of the required elements missing
function agentrank_admin_warning() {

    //setup admin url to agentrank settings page
    $agentrank_settings_url = admin_url() . "options-general.php?page=agentrank_option";
    //setup url to widgets.php
    $agentrank_wp_widget_url = admin_url() . "widgets.php";

    //setup empty global message variable to be used in callback function in admin_notices hook.
    global $agentrank_admin_warning_message;
    $agentrank_admin_warning_message = '';

    global $agentrank_widget_warning_message;
    $agentrank_widget_warning_message = '';

    global $agentrank_setting_warning_message;
    $agentrank_setting_warning_message = '';

    //check whether widget deployed!
    if (is_active_widget($callback = false, $widget_id = false, $id_base = 'agentrank_sale', $skip_inactive = true)||is_active_widget($callback = false, $widget_id = false, $id_base = 'agentrank_client', $skip_inactive = true)||is_active_widget($callback = false, $widget_id = false, $id_base = 'agentrank_forecast', $skip_inactive = true)) {
	//retuns nothing
	}else{
        $agentrank_widget_warning_message = "<li>Please <a href='$agentrank_wp_widget_url'>visit the Widgets Page</a> to install at least one of the plugin's sidebar widgets.</li>";
    }
    //check empty api key
    $check_api_key = get_option('agent_apikey');
    if (empty($check_api_key)) {
        $agentrank_admin_warning_message .= "<li>Please enter an API Key.</li>";

        $agentrank_setting_warning_message = "<li>Please <a href='$agentrank_settings_url'>visit the Settings Page</a> to configure the plugin.</li>";
    }
    //check empty reCaptcha pub api key
    $check_recapcha_public_key = get_option('agent_captcha_public');
    if (empty($check_recapcha_public_key)) {
        $agentrank_admin_warning_message .= "<li>Please enter reCAPTCHA Public Key or request for one, in Real Estate Agent Settings Page.</li>";

        $agentrank_setting_warning_message = "<li>Please <a href='$agentrank_settings_url'>visit the Settings Page</a> to configure the plugin.</li>";
    }
    //check empty reCaptcha private api key
    $check_recapcha_private_key = get_option('agent_captcha_private');
    if (empty($check_recapcha_private_key)) {
        $agentrank_admin_warning_message .= "<li>Please enter reCAPTCHA Private Key, in Real Estate Agent Settings Page.</li>";

        $agentrank_setting_warning_message = "<li>Please <a href='$agentrank_settings_url'>visit the Settings Page</a> to configure the plugin.</li>";
    }


    function agentrank_warning() {
        //setup admin url to agentrank settings page
        $agentrank_settings_url = admin_url() . "options-general.php?page=agentrank_option";
        //setup url to widgets.php
        $agentrank_wp_widget_url = admin_url() . "widgets.php";
        //setup current url
        $current_page_url = agentrank_current_page_url();

        if ($current_page_url == $agentrank_settings_url) {

            // retrieve error message if any, from global variable
            global $agentrank_admin_warning_message;

            if (!empty($agentrank_admin_warning_message)) {
                echo "<div id='agentrank-warning' class='updated fade'>";
                echo "<p><strong>Real Estate Agent WordPress Plugin is almost ready.";
                echo " The following needs your attention!</strong></p>";
                echo "<ol>";
                echo $agentrank_admin_warning_message;
                echo "</ol>";
                echo "</div>";
            }

        } elseif ($current_page_url == $agentrank_wp_widget_url) {

            // retrieve error message if any, from global variable
            global $agentrank_widget_warning_message;

            if (!empty($agentrank_widget_warning_message)) {
                echo "<div id='agentrank-warning' class='updated fade'>";
                echo "<p><strong>Click the <em>AgentRank Sale Transactions Widget</em> or <em>AgentRank Client Reviews Widget</em> or <em>AgentRank Market Forecasts Widget</em> in <em>Available Widgets</em> and drag to <em>Sidebar 1</em></strong>.</p>";
                echo "</div>";
            }

        } else {

            // retrieve error message if any, from global variable
            global $agentrank_widget_warning_message;

            global $agentrank_setting_warning_message;

            if (!empty($agentrank_widget_warning_message) || !empty($agentrank_setting_warning_message)) {
                echo "<div id='agentrank-warning' class='updated fade'>";
                echo "<p><strong>Real Estate Agent WordPress Plugin is almost ready.";
                echo " The following needs your attention!</strong></p>";
                echo "<ol>";
                echo $agentrank_setting_warning_message;
                echo $agentrank_widget_warning_message;
                echo "</ol>";
                echo "</div>";
            }

        }
        //end else

    }

    //end function agentrank_warning
    add_action('admin_notices', 'agentrank_warning');

}

add_action('init', 'agentrank_admin_warning');
?>