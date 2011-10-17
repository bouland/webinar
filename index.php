<?php
	/**
	 * Elgg Pages
	 * 
	 * @package ElggPages
	 */

	require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

	global $CONFIG;
	
	// access check for closed groups
	group_gatekeeper();
	
	$owner_guid = page_owner();
	set_page_owner($owner_guid);
	set_context('groups');
	
	// Get objects
	$limit = get_input("limit", 10);
	$offset = get_input("offset", 0);
	$objects = elgg_list_entities(array('types' => 'object', 'subtypes' => 'meeting', 'container_guid' => page_owner(), 'limit' => $limit, 'offset' => $offset, 'full_view' => FALSE));
		
	if($owner instanceof ElggGroup){
		$title = sprintf(elgg_echo("webinar:meeting:index"),$owner->name);
	}else{
		$title = sprintf(elgg_echo("webinar:meeting:index"),$owner->name);
	}
	$body = elgg_view_title($title);
	$body .= $objects;
	$body = elgg_view_layout('two_column_left_sidebar', $area1, $body);
	
	// Finally draw the page
	page_draw($title, $body);
