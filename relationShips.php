<?php

include_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

$relationship = get_input('relationship');
$meeting_guid = (int) get_input('meeting_guid');
$meeting = get_entity($meeting_guid);

if($meeting instanceof ElggMeeting) {
	set_page_owner($meeting->container_guid);
	webinar_meeting_submenu($meeting);
	
	$title = sprintf(elgg_echo("webinar:meeting:{$relationship}:title"), $meeting->title);
	
	$area2 = elgg_view_title($title);
	
	$area2 .= elgg_list_entities_from_relationship($meeting->getRelationShipOptions($relationship));
}

$body = elgg_view_layout('two_column_left_sidebar', '', $area2);

page_draw($title, $body);
