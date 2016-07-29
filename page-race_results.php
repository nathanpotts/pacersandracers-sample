<?php get_header(); ?>
<div class='hidden'>/*template name: Race Results*/</div>
<?php nectar_page_header($post->ID); ?>

<div class="container-wrap" style="padding-bottom: 0px;">

<div class="container main-content">

<?php 
//Getting a selected Year
$selected_year = intval(sanitize_text_field($_GET['selected_year']));

//No Year, Bad Year, or something else;
if(empty($selected_year) || $selected_year == 0){
$querystr = "
				SELECT wposts.* 
				FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta
				WHERE wposts.ID = wpostmeta.post_id 
				AND wposts.post_status = 'publish'
				AND wpostmeta.meta_key = 'race_time' 
				AND wposts.post_type = 'race_result' 
				ORDER BY wpostmeta.meta_value DESC
	";
}
else{
//Fetching a Specific Year
$min_year = $selected_year . '-01-01 00:00:00';
$max_year = $selected_year . '-12-31 23:59:59';
$querystr = "
				SELECT wposts.* 
				FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta
				WHERE wposts.ID = wpostmeta.post_id 
				AND wposts.post_status = 'publish'
				AND wpostmeta.meta_key = 'race_time' 
				AND wposts.post_type = 'race_result' 
				AND wpostmeta.meta_value >= '$min_year'
				AND wpostmeta.meta_value <= '$max_year'
				ORDER BY wpostmeta.meta_value DESC
	";
}

$calendar_posts = $wpdb->get_results($querystr, OBJECT);
$prevMonth = '';
$content = '';
wp_enqueue_style('bootstrap_pacers');
wp_enqueue_style('race_calendar_css');
wp_enqueue_script('calendar_js');
/*************************************

				Building the Page

*************************************/
$years = getRaceResultsYears();
$args = array(
'name' => 'selected_year',
'class' => 'col-lg-8 col-md-3 year_selection',
);
$content .= "<div class='pacers_header'>";
//$content .= "<h2 class='col-xs-12 col-md-8'>Race Results</h2>";
$pr_website = get_site_url() . '/race-results-form/';
$content .= "<a href='$pr_website' class='hidden-md hidden-lg col-xs-6 pacers_button_link'><div class='pacers_button'>Submit Results</div></a>";
$content .= "<form action='' method='get' class='race_year_container pacer_racers_container hidden-lg hidden-md col-xs-6 col-sm-6'>";
$content .= build_pacers_racers_dropdown($args, $years, $selected_year);
$content .= "</form>";
$content .= "</div>"; //End of Header
$content .= "<div id='race_calander_subheader'>";
$content .= "<ul class='pacers_subheader_list col-md-6 col-xs-12'>";
$content .= "<li class='pacers_subheader_list_element legend_bullet nobullet'>" . build_pacers_racers_legend_results() . "</li>";
$content .= "<li class='pacers_subheader_list_element nobullet'>ROY <span class='mdash'>&mdash;</span> Runner of the Year series races</li>";
$content .= "</ul>";
$content .= "<a href='$pr_website' class='hidden-sm hidden-xs col-md-6 pacers_button_link'><div class='pacers_button'>Submit Race Results</div></a>";
$content .= "<div class='col-md-12 hidden-sm hidden-xs'><hr class='pacers_racers_hr col-md-12 hidden-xs hidden-sm'></div>";
	$content .= "<form action='' method='get' class='race_year_container pacer_racers_container hidden-sm hidden-xs col-md-9 col-lg-3'>";
	$content .= build_pacers_racers_dropdown($args, $years, $selected_year);
	$content .= "</form>";
	
if($calendar_posts){
	$content .= "<div id='race_results_calendar' class='col-lg-12 col-md-12 hidden-xs hidden-sm'>"; 
	$mobile_view = "<div id='mobile_race_calendar' class='col-xs-12 visible-xs visible-sm'>";
	$currentYear = '';
	$prevYear = '';
	foreach($calendar_posts as $post){	
		$race_id = get_the_ID();
		$customData = get_post_custom(get_the_ID());
		$dateTime = $customData['race_time'];

		$dateTime = $dateTime[0];
		if($dateTime){
			$dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $dateTime);
			$currentMonth = $dateTime->format('F');
			$currentYear = $dateTime->format('Y');
			// Check to see if the post is a new month.
			if($prevMonth != $currentMonth){
				//echo "Prev Month doesn't equal Current";
				if(!empty($prevMonth)){
					if($currentYear != $prevYear && $prevYear != ''){
						break; //End of the First Year when there is no selected year
					}
					$content .= $row;
					$row = '';
					$content .= "</table>"; //End of Old Table
					$content .= "</div>"; //End of Old Month

				}
				$content .= "<div class='raceMonthContainer'>";//Beginning of a new Month Added to Content
				
				$monthHeader = $dateTime->format('F Y'); //New Month
				$content .= "<h3 class='monthTitle'>$monthHeader</h3>";
				$mobile_view .= "<h3 class='mobileMonthTitle'>$monthHeader</h3>";
				
				$content .= "<table class='monthTable'>"; //New Table

				$montharray = array('Date', 'Race Name', 'Results Type', 'Results');//Header
				$content .= buildRaceHeader($montharray);

				$prevMonth = $currentMonth;
				$prevYear = $currentYear;
			}
			//echo "After the Header</br>";
			$title = get_the_title();
			//$content .= "<tr class='race_row'>";
			$race_date = $dateTime->format('D n/j');
			$mobile_date = $dateTime->format('l, n/j');
			$location = $customData['race_location'];
			$location = $location[0];
			$state = $customData['race_state'];
			$state = $state[0];
			$distance = $customData['race_distance'];
			$distance = $distance[0];
			$miles_or_kilos = $customData['miles_or_kilos'];
			$miles_or_kilos = $miles_or_kilos[0];
			$letter = get_M_or_K($distance, $miles_or_kilos);
			$distance = $distance . $letter;
			$race_event_type = $customData['race_event_type'];
			$race_event_type = $race_event_type[0];
			$sponsored = $customData['race_sponsorship'];
			$sponsored = $sponsored[0];
			$race_type = $customData['race_type'];
			$race_type = $race_type[0];
			//echo "Race Type is: $race_type";

			$contact = getResultsFileURL($race_id,array('link_class' => 'contact_link', 'admin' => false));
			$mobile_contact = getResultsFileURL($race_id, array('link_class' => 'mobile_contact', 'admin' => false));
			$row_data = array('race_date' => $race_date,'title' => $title, 'race_type' => $race_type, 'results' => $contact);
			$row = buildRaceRow($row_data,$sponsored) . $row;

			
			/*Make Mobile View */
			$mobile_view .= "<div class='mobile_card container'>";
			$mobile_view .= "<div class='mobile_race_date col-xs-8 col-sm-10'>";
			$mobile_view .= $mobile_date;
			$mobile_view .= "</div>";// End of Mobile Race Date
			$mobile_view .= "<div class='mobile_sponsorship_icon col-xs-4 col-sm-2'>";
			$mobile_view .= PRisSponsoredImg($sponsored);
			$mobile_view .= "</div>";//End of Mobile Sponsorship
			$mobile_view .= "<div class='mobile_race_title col-xs-12'>";
			$mobile_view .= "$title"; 
			$mobile_view .= "</div>";//End of Mobile Race Event and Title
			$mobile_view .= "<div class='mobile_race_type col-xs-12'>";
			$mobile_view .= "$race_type";
			$mobile_view .= "</div>";
			$mobile_view .= "<div class='mobile_attachment col-xs-12'>";
			$mobile_view .= $mobile_contact;
			$mobile_view .= "</div>";//End of Mobile Attachment
			$mobile_view .= "</div>";// End of Mobile Container
		}
	}
	$content .= $row;
	$content .= "</table>"; //End of Last Table
	$content .= "</div>"; //End of Last Month
	$content .= "</div>"; //End of Race Calendar Container

	$mobile_view .= "</div>";//End of Mobile Calendar
}
echo $content;
echo $mobile_view;
?>


</div><!--/container-->

</div>
<?php get_footer() ?>