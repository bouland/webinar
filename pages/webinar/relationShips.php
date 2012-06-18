<?php

include_once( $_SERVER['DOCUMENT_ROOT'] . "/engine/start.php");

$relationship = get_input('relationship');
$webinar_guid = (int) get_input('guid');
$webinar = get_entity($webinar_guid);

if($webinar instanceof ElggWebinar) {
	set_page_owner($webinar->container_guid);
	webinar_submenu($webinar);
	
	$title = sprintf(elgg_echo("webinar:{$relationship}:title"), $webinar->title);
	
	$area2 = elgg_view_title($title);
	
	$area2 .= elgg_list_entities_from_relationship($webinar->getRelationShipOptions($relationship));
}

$body = elgg_view_layout('two_column_left_sidebar', '', $area2);

page_draw($title, $body);
