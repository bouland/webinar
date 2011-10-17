<?php
	require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
	
	gatekeeper();
	
	$page_owner_guid = page_owner();

	// Get the meeting, if it exists
	$meeting_guid = (int) get_input('meeting_guid');
		
	if ($meeting = get_entity($meeting_guid)) {

		set_context('meeting');
		
		if ($page_owner_guid == 0) {
			set_page_owner($meeting->container_guid);
		}
		
		webinar_meeting_submenu($meeting);
		
		if ($meeting->canEdit()) {
		
			// Render the file upload page
			$title = elgg_echo("webinar:meeting:edit");
			$area2 = elgg_view_title($title);
			$area2 .= elgg_view("forms/meeting/edit", array('entity' => $meeting));

		}

	}
	
	$body = elgg_view_layout('two_column_left_sidebar', $area1, $area2);
	
	page_draw($title, $body);
	
?>