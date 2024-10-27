<?php
/*
*Agentrank Lead Form Widget
*Since Version 1.0
*/ 

//create widget
class AgentRankLeadFormWidget extends WP_Widget {

	function AgentRankLeadFormWidget() {

	$widget_ops = array( 'classname' => 'agentrank_form', 
	'description' => __('A Widget for Realty Baron Agent Contact Form  ', 'agentrank_form') );
    
	$control_ops = array( 'width' => 200, 'height' => 350, 'id_base' => 'agentrank_form' );

	$this->WP_Widget( 'agentrank_form', __('AgentRank Contact Form', 'agentrank_form'), $widget_ops, $control_ops );

	}


	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', $instance['title'] );

		echo $before_widget;

		if ( $title )
		echo $before_title . $title . $after_title;
		
		//check options API Key
		$check_key = get_option('agent_apikey');
		$ag_fullname = get_option('agent_fullname');
		
		//if api key not empty, proceed to do api call
		//else display error message
		if(!empty($check_key)&&!empty($ag_fullname)){
        
		//start of mini form
		$action_url_raw = get_bloginfo('url')."/agentrank/contact";
		
		$action_url = esc_url($action_url_raw);
					
		$html .= "<form action=\"$action_url\" method=\"post\" name=\"agentrank_sidebar_form\" id=\"agentrank_sidebar_form\">";
		
		//subject
		$html .= '<div id="sb_form_subject" class="sb_form_elements">';
		$html .= "<textarea name=\"subject\" id=\"sb_subject\"";
		$html .= "onfocus=\"this.value=(this.value=='Ask your question here....') ? '' : this.value;\" onblur=\"this.value=(this.value=='') ? 'Ask your question here....' : this.value;\">May I interview you to be my agent?</textarea>";
		$html .= '</div>';

	
		//submit button
		$html .= '<div id="sb_form_submit" class="sb_form_elements">';
		
		//create hidden value with wordpress nonce and post to contact form for security check.
		$hidden_value = wp_create_nonce('agentrank-form'); 
		$html .= "<input type='hidden' name='process_agent_contact_nonce' value='$hidden_value'/>";
		$html .= "<input type='hidden' name='process_agent_contact_form' value='yes_process_con_form'/>";
		$html .= "<input type='submit' id='sb_form_submit_button' value='Ask'>";
		$html .= '</div>';
		
        //end of mini form
		$html .= "</form>";
		
		$html .= '<div class="sb_form_elements">';
		
		$html .= "<p class='sidebar_links'><a href='http://wordpress.org/extend/plugins/agentrank' target='_blank'>Powered By Real Estate Agent</a></p>";
		
		$html .= "</div>";
		
		echo $html;
	 
  	 	
}else{ echo "<p class='sidebar_error'>You must configure the AgentRank plug-in first.</p>";
	          echo "<p class='sidebar_error'>Please click on the following link and login to plugins settings page.</p>";
	          
			  $plugin_admin_url_structure = get_bloginfo('url')."/wp-admin/options-general.php?page=agentrank_option.php";
			  
			  echo "<a href='$plugin_admin_url_structure' class='sidebar_links'>AgentRank Admin Settings</a>";
	  
	        }//end if(!empty($check_key))

			
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