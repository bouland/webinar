<?php
	require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
	
	gatekeeper();
	
	set_context('groups');
	
	
	$container_guid = page_owner();
	set_page_owner($container_guid);
	
	// Render the file upload page
	$title = elgg_echo("webinar:meeting:new");
	$area2 = elgg_view_title($title);

	$area2 .= elgg_view("forms/meeting/edit", array('container_guid' => $container_guid));
	
	$body = elgg_view_layout('two_column_left_sidebar', $area1, $area2);
	
	page_draw($title, $body);
	
?>