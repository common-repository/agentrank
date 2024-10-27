<?php
/*
*Agentrank Market Forecasts Widget
*Since Version 1.0
*/ 

//create widget
class AgentRankMarketForecastsWidget extends WP_Widget {

	function AgentRankMarketForecastsWidget() {

	$widget_ops = array( 'classname' => 'agentrank_forecast', 
	'description' => __('A Widget to get Agent Market Forecasts from RealtyBaron\'s AgentRank API  ', 'agentrank_forecast') );
    
	$control_ops = array( 'width' => 200, 'height' => 350, 'id_base' => 'agentrank_forecast' );

	$this->WP_Widget( 'agentrank_forecast', __('AgentRank Market Forecasts', 'agentrank_forecast'), $widget_ops, $control_ops );

	}


	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', $instance['title'] );

		echo $before_widget;

		if ( $title )
		echo $before_title . $title . $after_title;
		
		//check options API Key
		$check_key = get_option('agent_apikey');
		
		//if api key not empty, proceed to do api call
		//else display error message
		if(!empty($check_key)){
        
		//use agentrank rest api class to request xml response
        global $agapi;
		
        $xml = $agapi->sidebar_market_forecasts(0);
		
		if(!empty($xml)){
	    
		//start display widget
		$html = "<div class='agentrank_widget_response'>"; 
		
		//get status code 400 error 200 ok
		$status_code = $xml->status['code'];
		
			if($status_code = "400"){
			
				//catch error message for status 400
				foreach ($xml->status->messages as $mess){ 
					
				$errormessage = $mess->message;
				
				echo "<p class='sidebar_error'>".$errormessage."</p>";	
						
				}
			
			}//end if($status_code = "400")		

		//check status code 200 is ok 400 is error
		if($status_code = "200"){
		
		  if(!empty($xml->forecasts->forecast)){
		  
		        $html .= "<ul>";

				foreach ($xml->forecasts->forecast as $forecast) {
				
				$id = $forecast['id'];
				$title = $forecast->title;
				$forecast_url = get_bloginfo('url')."/agentrank/forecast?id=$id";
		
				$html .= "<li>";
		
				$html .= "<a href='$forecast_url'>".$title."</a>";
		
				$html .= "</li>";
				
				}
				
				$html .= "</ul>";
			
		   }else{
		   		
				$html .= "<div class='sidebar_agent_widget'>";
		
				$html .= "<p class='sidebar_links'>Currently there is no Market Forecast.</p>";
		
				$html .= "</div>"; 
		   }

		}//end if($status_code = "200")
		
		$forecasts_url = get_bloginfo('url')."/agentrank/forecasts";
		
		if(!empty($xml->forecasts->forecast)){

			$html .= "<p class='sidebar_links'><a href='$forecasts_url'>View all Market Forecasts</a></p>";
		}
		
		$html .= "<p class='sidebar_links'><a href='http://wordpress.org/extend/plugins/agentrank' target='_blank'>Powered By Real Estate Agent</a></p>";
		
		echo $html;
	 
	 }//end if(!empty($xml))
	 
  	 	
}else{ echo "<p class='sidebar_error'>You must configure the AgentRank plug-in first.</p>";
	          echo "<p class='sidebar_error'>Please click on the following link and login to plugins settings page.</p>";
	          
			  $plugin_admin_url_structure = get_bloginfo('url')."/wp-admin/options-general.php?page=agentrank_option.php";
			  
			  echo "<a href='$plugin_admin_url_structure' class='sidebar_links'>AgentRank Admin Settings</a>";
	  
	        }//end if(!empty($check_key))
		
		//lastly check if empty xml return from rest api class will indicate service unavailable
		//return Service is Unavailable message!
		if(empty($xml)&&!empty($check_key)){
		echo "<div class='sidebar_error'>Service is Unavailable</div>";
		}
			
		echo $after_widget;
	}//end of function widget


	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}


	function form($instance) {
		
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );

	    $instance['title'] = strip_tags( $instance['title'] );

?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" /></label>
            </p>
			
            <p>
<?php
	}
}
?>