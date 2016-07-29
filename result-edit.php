<?php
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['_wpnonce']) &&  0 < wp_verify_nonce($_POST['_wpnonce'])){
	$post_id = sanitize_text_field($_POST['post_id']);
	$data = array(
	'username' => sanitize_text_field($_POST['username']),
	'telephone' => sanitize_text_field($_POST['telephone']),
	'user_email' => sanitize_email($_POST['user_email']),
	'race_name' => sanitize_text_field($_POST['race_name']),
	'race_event_type' => sanitize_text_field($_POST['race_event_type']),
	'race_distance' => sanitize_text_field($_POST['race_distance']),
	'miles_or_kilos' => sanitize_text_field($_POST['miles_or_kilos']),
	'race_hour' => sanitize_text_field($_POST['race_hour']),
	'race_minute' => sanitize_text_field($_POST['race_minute']),
	'race_am_pm' => sanitize_text_field($_POST['race_am_pm']),
	'race_date' => sanitize_text_field($_POST['race_date']),
	'race_location' => sanitize_text_field($_POST['race_location']),
	'race_state' => sanitize_text_field($_POST['race_state']),
	'race_sponsorship' => sanitize_text_field($_POST['race_sponsorship']),
	'post_id' => $post_id,
	'race_status' => sanitize_text_field($_POST['race_status']),
	'race_type' => sanitize_text_field($_POST['race_type']),

	);
	$context = 'admin';
	$success = saveRaceResult($data, $context);
	if(!$post_id && $success){
		$site_url = get_site_url();
		$url = "$site_url" . "/wp-admin/edit.php?post_type=race_result";
		//echo "<div id='admin_popup_message_container' title='Race Result Added'><p class='popup_message'>Race sucessfully added</p></div>";
		print("<script>window.location.href='$url'</script>");
	}
	else{
		if($success){
			$msg = "<div id='admin_popup_message_container' title='Edited'><p class='popup_message'>Race sucessfully edited</p></div>";
		}
		else{
			$msg = "<div id='admin_error_message_container' title='Oh No!'><p class='popup_message'>Your race was not submitted successfully. If you think this is in error, please try submitting again.</p></div>";
		}
	}

}
wp_enqueue_style('race_submission_form_css');
wp_enqueue_style('bootstrap_pacers');
wp_enqueue_script('race_results_submission_form_js');
wp_enqueue_script('admin_form_js');
wp_enqueue_style('race_edit_admin_form');
wp_enqueue_style('jquery-ui-timepicker');
wp_enqueue_script('jQuery_mask');
wp_enqueue_script('jQueryValidate');
wp_enqueue_script('jQueryValidate-additional-methods');
wp_enqueue_style('jqueryui_css');
wp_enqueue_style('jqueryui_struct_css');
wp_enqueue_style('jqueryui_theme_css');
wp_enqueue_style('race_results_submission_form_css');

echo $msg;
$race_results = "<form action='$url' id='pacers_racers_results_form' method='post' enctype='multipart/form-data'>";
$race_results .= "<input type='hidden' value='$post_id' name='post_id'>";
$race_results .= wp_nonce_field(); 
$race_results .= "<div id='race_results_form_left_column' class='col-xs-12 col-md-6'>";
if(!empty($post_id)){
$args = array(
'context' => 'race_results',
'post_id' => $post_id,
'edit' => true,
'admin' => true,
);
}
else{
$args = array(
'context' => 'race_results',
'edit' => false,
'admin' => true,
);
}
$race_results .= pr_race_information($args);
$race_results .="</div>"; //End of Race Results Form Left Column
$race_results .= "<div id='race_results_form_right_column' class='col-xs-12 col-md-6'>";
$race_results .= pr_race_user_info($args);
$race_results .= pr_race_admin_form($args);
$race_results .= "</div>"; //End of second right column
			$race_results .= "<span id='form_buttons_container' class='col-xs-12'>";
			$race_results .= "<span id='form_buttons_centered'>";
			$race_results .= "<input type='submit' value='Submit Your Race Results' id='form_submit_button'>";
			$race_results .= "<a id='form_cancel_button' href='http://pacersandracers.info/?page_id=1053'>Cancel</a>";
			$race_results .= "<div id='disabled_help_text'>Please complete form before submitting</div>";
			$race_results .= "</span>";
			$race_results .= "</span>";
			//echo $race_results;
 
$race_results .= "</form>";
echo $race_results;
?>