<?php

/* 
File Created By: Nathan Potts

*/


/*
This function controls the left column information in the race form which is the race information. 
The args function takes a post_id, and a context on if it is a race_result. 
It also calls several child functions that are listed below. 

$args is an associated array that contains a post_id and a context.
*/
function pr_race_information($args){
$post_id = $args['post_id'];
$race_calendar_event = get_post_type($post_id) == 'race_calendar_event';
if(get_post_type($post_id) == 'race_calander_event' || get_post_type($post_id) == 'race_result'){
$title = get_the_title($post_id);
}
else{
$title = '';
}
$race_info = '';
$race_info = "<div id='race_information' class='col-xs-12'>";
			$race_info .= "<h2 class='col-xs-12'>Race Information</h2>";
			$race_info .= build_racers_textlabel(array('name' => 'race_name', 'input_id' => 'racename', 'div_id' => 'pacerracename', 'label_description' => 'Race Name', 'div_class' => 'col-xs-12', 'value' => $title));
			if($args['context'] != 'race_results'){
			$race_info .= "<div id='race_event_type' class='col-xs-12 pacerracer_form_container'>";
			$race_info .= "<label class='pacerrace_label'>Race Event Type</label>";
			$race_types = getAssociatedRaceTypes();
			$selected_race_type = get_post_meta($post_id, 'race_event_type', true);
			$dd_args = array('name' => 'race_event_type', 'class' => 'pacerrace_dropdown', 'id' => '');
			$race_info .= build_pacers_racers_dropdown($dd_args, $race_types, $selected_race_type);
			$race_info .= "</div>";//End of Race Event

			
			//$race_info .= "<div id='distance_time_container' class=''>";
		
			$race_info .= build_racers_textlabel(array('name' => 'race_distance', 'input_id' => 'race_distance_field', 'div_id' => 'race_distance', 'label_description' => 'Distance', 'div_class' => 'col-xs-5 col-md-6', 'value' => get_post_meta($post_id, 'race_distance', true)));
			$race_info .= "<div id='pacer_miles_kilo' class='col-md-6 col-xs-7'>";
			$miles_or_kilos = get_post_meta($post_id, 'miles_or_kilos', true);
			$radios = array(array('value' => 'kilometers', 'f_name' => 'Kilometers',), array('value' => 'miles', 'f_name' => 'Miles'));
			$r_args = array('name' => 'miles_or_kilos', 'input_class' => 'pacerrace_buttons', 'label_class' => 'pacerrace_label', 'container_class' => 'col-xs-12');
			$race_info .= build_pacers_radio_buttons($r_args, $radios, $miles_or_kilos);
			$race_info .= "</div>";
			$race_info .= pr_race_time($args);
			}
			else{
			$race_info .= build_racers_textlabel(array('name' => 'race_type', 'input_id' => 'race_type_field', 'div_id' => 'race_type', 'label_description' => 'Result Type', 'div_class' => 'col-xs-12 col-md-12', 'value' => get_post_meta($post_id, 'race_type', true)));
			}
			//$race_info .= "</div>"; //Distance Time Container
			if(!empty($post_id)){
			$date = get_post_meta($post_id, 'race_time', true);
			$date = DateTime::createFromFormat('Y-m-d H:i:s', $date);
			$date = $date->format('m/d/Y');
			}
			$race_info .= build_racers_textlabel(array('name' => 'race_date', 'input_id' => 'race_form_datepicker', 'div_id' => 'pacer_race_calendar', 'label_description' => 'Race Date', 'div_class' => 'col-md-6 col-xs-12', 'value' => $date));
			
			if($args['context'] != 'race_results'){
			$race_info .= "<div id='race_full_location' class=''>";
			$race_info .= build_racers_textlabel(array('name' => 'race_location', 'input_id' => 'race_location_field', 'div_id' => 'pacer_race_location', 'label_description' => 'City/Location', 'div_class' => 'col-md-12 col-xs-12', 'value' => get_post_meta($post_id, 'race_location', true)));
			$race_info .=		"<div id='pacer_race_state' class='col-md-6 col-xs-12 pacerracer_form_container'>";
			$race_info .=		"<label class='pacerrace_label'>State</label>";
			$states_list = getAssociatedStates();
			$dd_args = array('name' => 'race_state', 'class' => 'pacerrace_dropdown', 'id' => '');
			$selected_state = get_post_meta($post_id, 'race_state', true);
			$race_info .= 	build_pacers_racers_dropdown($dd_args, $states_list, $selected_state);
			$race_info .= 	"</div>";
			$race_info .= "</div>";
			}
			if($args['context'] == 'race_results'){
			$race_info .= pr_results_form($args);
			}
			else{
			$race_info .= pr_prefered_contact_form($args);
			}
			$race_info .= "</div>"; //End of Race Info
			return $race_info;
}

/*
This function handles the displaying of time on the race_form. It uses a post_id from $args. 

$args is an associated array that contains a post_id and a context.
*/
function pr_race_time($args){
$race_info = "<div id='pacer_race_time' class='col-md-12 pacerracer_form_container col-xs-12'>";
			//$race_info .= "<div class='col-xs-5 col-md-6'>";
			$race_info .= "<label class='pacerrace_label col-xs-12'>Race Time</label>";
			$post_id = $args['post_id'];			
			if(!empty($post_id)){
			$date = get_post_meta($post_id, 'race_time', true);
			$date = DateTime::createFromFormat('Y-m-d H:i:s', $date);
			$hour = $date->format('h');
			$minute = $date->format('i');
			$am_pm = $date->format('a');
			}
			else{
			$hour = 8;
			}
			$args = array('name' => 'race_hour', 'class' => 'pacerrace_dropdown col-xs-3', 'id' => 'race_hour_dd');
			$hours = getAssociatedHours();
			$race_info .= build_pacers_racers_dropdown($args, $hours, $hour);
			$race_info .= 	"<span id='time_colon' class='col-xs-1'>:</span>";
			$args = array('id' => 'race_minute_dd', 'class' => 'pacerrace_dropdown col-xs-3', 'name' => 'race_minute');
			
			$race_info .= build_pacers_racers_dropdown($args, getAssociatedMinutes(), $minute);
			$args = array('id' => 'race_am_pm_dd', 'class' => 'pacerrace_dropdown col-xs-3', 'name' => 'race_am_pm');
			$race_info .= build_pacers_racers_dropdown($args, getAssociatedAMPM(), $am_pm);
			//$race_info .= "</div>";
			//$race_info .= "</div>"; //end of Col-xs-5 and col-md-6
			$race_info .= "</div>";// End of distance_time_container
			return $race_info;
}

/*
This function displays the information on the right column for the “Your Information” column. 
It also fills the data in, if it is on the edit screen
*/
function pr_race_user_info($args){
if($args['admin'] == true && $args['edit'] == false){
$username = get_option('default-form-name');
$phone = get_option('default-form-phone');
$email = get_option('default-form-email');
}
else{
$post_id = $args['post_id'];
$username = get_post_meta($post_id, 'username', true);
$phone = get_post_meta($post_id, 'telephone', true);
$email = get_post_meta($post_id, 'user_email', true);
}
			$race_info = "<div id='user_info' class=''>";
			$race_info .= "<h2 class='col-xs-12'>Your Information</h2>";
			$race_info .= "<h3 class='pacersracers_subheader col-xs-12'>Personal information will not be displayed publicly</h3>";
						
			$race_info .= build_racers_textlabel(array('name' => 'username', 'input_id' => 'username_field', 'div_id' => 'username', 'label_description' => 'Your Name', 'div_class' => 'col-md-12 col-xs-12', 'value' => $username));
			$race_info .= build_racers_textlabel(array('name' => 'telephone', 'input_id' => 'user_telephone', 'div_id' => 'telephone', 'label_description' => 'Your Telephone Number', 'div_class' => 'col-md-12 col-xs-12', 'value' => $phone));
			$race_info .= build_racers_textlabel(array('name' => 'user_email', 'input_id' => 'username_email_address_form_field', 'div_id' => 'user_email_address', 'label_description' => 'Your Email Address', 'div_class' => 'col-md-12 col-xs-12', 'value' => $email));
			$race_info .= "</div>"; //End of User Information
			//if($args['context'] == 'race_results'){
			$race_info .= "<div id='pacers_racers_disclaimer' class='col-xs-12 pacerracer_form_container'>";
			$race_info .= "<h4>Disclaimer</h4>";
			$race_info .= "<p class='pacerrace_label'>Please only submit races within 75 miles of New Albany, IN. Contact Pacers & Racers with any questions.</p>";
			//$race_info .= "<p class='pacerrace_label'>Nam fermentum, nulla luctus pharetra vulputate, felis tellus mollis orci, sed rhoncus sapien nunc eget odio.</p>";
			$race_info .= "</div>";
			//}
			
			return $race_info;
}

/*
This function displays the two dropdowns for approving a race and race sponsorship. 
It only appears on the admin screen and appears on the race calendar and race results 
admin edit and add new screens. 
*/
function pr_race_admin_form($args){
$post_id = $args['post_id'];
$admin_form = "<div id='admin_form_container' class=''>";
$admin_form .= "<h2 class='col-xs-12'>Adminstration Review</h2>";

$admin_form .= "<div id='race_sponsorship' class='col-xs-12 pacerracer_form_container'>";
$admin_form .= "<label class='pacerrace_label'>Pacers and Racers Sponsored</label>";
$sponsored = get_post_meta($post_id, 'race_sponsorship', true);
$dd_args = array('name' => 'race_sponsorship', 'id' => 'race_sponsorship_dropdown', 'class' => 'pacerrace_dropdown');
$admin_form .= build_pacers_racers_dropdown($dd_args, getAssociatedSponsorship(), $sponsored);

$admin_form .= "</div>"; //End of Race Sponsorship

$admin_form .= "<div class='col-xs-12 pacerracer_form_container' id='admin_race_published'>";
$admin_form .= "<label class='pacerrace_label'>Race Published Status</label>";
$status = get_post_status($post_id);
if(!$status){
$status = 'publish';
}
$dd_args = array('name' => 'race_status', 'id' => 'race_status_dropdown', 'class' => 'pacerrace_dropdown');
$admin_form .= build_pacers_racers_dropdown($dd_args, getAssociatedRaceStatus(), $status);
$admin_form .= "</div>"; //End of admin_race_published

$admin_form .= "</div>"; //End of Admin Form Container

return $admin_form;
}

/*
This function controls the preferred contact dropdown and the dropdowns associated with it for the race calendar form. 
It outputs html and it takes $args and contains post_id and a check to see if it’s an edit screen.
*/
function pr_prefered_contact_form($args){
			//print_r($args);
			//Prefered Contact Methods
			$edit = $args['edit'];
			//echo "Edit is $edit";
			$post_id = $args['post_id'];
			$prefered_type = get_post_meta($post_id, 'prefered_contact', true);
			
		
			$dd_args = array('name' => 'prefered_contact', 'id' => 'prefered_contact_method', 'class' => 'pacerrace_dropdown');
			
			$race_info = "<div id='race_contact_method' class='pacerracer_form_container col-xs-12'>";
			$race_info .= "<label class='pacerrace_label'>Entry Submission/Information</label>";
			$race_info .= build_pacers_racers_dropdown($dd_args, getAssociatedPreferedContact(), $prefered_type);

			$race_info .= "</div>"; //Race Contact Methos
			
			$race_info .= "<div id=calendar_contact_container class='pacerracer_form_container'>";
			$race_info .= build_racers_textlabel(array('name' => 'race_website', 'input_id' => 'race_website_field', 'div_id' => 'race_website_container', 'label_description' => 'Website URL', 'div_class' => 'prefered_container col-xs-12', 'value' => get_post_meta($post_id, 'race_website', true)));
			$race_info .= 	"<div id='race_entry_form_container' class='prefered_container col-xs-12'>";
			$race_info .=		"<label class='pacerrace_label col-xs-12'>Upload an Entry Form</label>";
			$race_info .=		"<div id='file_upload_button'>Choose File</div>";
			$race_info .=		"<label id='file_uploader_name' class='pacerrace_label'>No file chosen</label>";
			$race_info .=		"<div id='file_uploader_hidden'>";
			$race_info .=		"<input type='file' name='entry_form' id='secretFileUploader'>";
			$race_info .= "</div>"; //End of File_uploader Hidden
			$race_info .= "<div id='required_filetypes' class='col-xs-12'>";
			$file_type_string = getRaceCalPermittedFileTypesString();
			$race_info .= "<label class='pacerrace_label col-xs-12'>$file_type_string</label>";
			$race_info .= "</div>";
			if($edit && $prefered_type == 'entry'){
			$race_info .= "<div id='admin_entry_form' class='col-xs-12'>";
			$race_info .= "<label id='submitted_entry_form'class='pacerrace_label col-xs-12'>Submitted Entry Form</label>";
			$race_info .= getPreferedContactURL($post_id, $prefered_type, array('link_class' => 'admin_prefered_link col-xs-12', 'admin' => true));
			$race_info .= "</div>";
			}
			$race_info .= "</div>";//End of Race Entry Form Container

			
			$race_info .= build_racers_textlabel(array('name' => 'race_email', 'input_id' => 'race_email_field', 'div_id' => 'race_email_container', 'label_description' => 'Email Address', 'div_class' => 'prefered_container col-xs-12', 'value' => get_post_meta($post_id, 'race_email', true)));
			
			
			$race_info .= build_racers_textlabel(array('name' => 'online_reg', 'input_id' => 'race_online_reg_field', 'div_id' => 'race_online_reg_container', 'label_description' => 'Online Registration', 'div_class' => 'prefered_container col-xs-12', 'value' => get_post_meta($post_id, 'online_reg', true)));
			$race_info .= "</div>"; //End of Contact Container 

return $race_info;
}


/*  
This function displays the html for the document upload for the results submission form. 
It returns html and it takes $args that contains a post_id.
*/
function pr_results_form($args){
			//var_dump($args);
			$post_id = $args['post_id'];
			$post_id = (int)$post_id;
			//var_dump($post_id);
			$race_info .= 	"<div id='race_entry_form_container' class='prefered_container col-xs-12'>";
			$race_info .=		"<label class='pacerrace_label col-xs-12'>Upload Results File</label>";
			$race_info .=		"<div id='file_upload_button'>Choose File</div>";
			$race_info .=		"<label id='file_uploader_name' class='pacerrace_label'>No file chosen</label>";
			$race_info .=		"<div id='file_uploader_hidden'>";
			$race_info .=		"<input type='file' name='entry_form' id='secretFileUploader'>";
			$race_info .= "</div>"; //End of File_uploader Hidden
			$race_info .= "<div id='required_filetypes' class='col-xs-12'>";
			$file_type_string = getRaceResultPermittedFileTypesString();
			$race_info .= "<label class='pacerrace_label col-xs-12'>$file_type_string</label>";
			$race_info .= "</div>";
			if($args['edit']){
			$race_info .= "<div id='admin_entry_form' class='col-xs-12'>";
			$race_info .= "<label id='submitted_entry_form'class='pacerrace_label col-xs-12'>Submitted Entry Form</label>";
			//var_dump(getResultsFileURL($post_id, array('link_class' => 'admin_prefered_link col-xs-12 ')));
			$race_info .= getResultsFileURL($post_id, array('link_class' => 'admin_prefered_link col-xs-12', 'admin' => true));
			$race_info .= "</div>";
			}
			$race_info .= "</div>";//End of Race Entry Form Container
return $race_info;
}

/* RACE RESULTS*/
function build_race_results_form(){
return $race_results;
}
?>