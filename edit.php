<?php
	require_once( $_SERVER['DOCUMENT_ROOT'] . "/engine/start.php");
	
	gatekeeper();
	
	// Get the webinar, if it exists
	$webinar_guid = (int) get_input('webinar_guid');
		
	if (($webinar = get_entity($webinar_guid)) && $webinar instanceof ElggWebinar) {

		if ($page_owner_guid == 0) {
			set_page_owner($webinar->container_guid);
		}
		
		if ($webinar->canEdit()) {
		
			webinar_submenu($webinar);
			// Render the file upload page
			$title = elgg_echo("webinar:edit");
			$area2 = elgg_view_title($title);
			$area2 .= elgg_view("forms/webinar/edit", array('entity' => $webinar));

		}

	}
	
	$body = elgg_view_layout('two_column_left_sidebar', $area1, $area2);
	
	page_draw($title, $body);
	
?>