<?php
	/**
	 * Elgg Pages
	 * 
	 * @package ElggPages
	 */

	require_once($_SERVER['DOCUMENT_ROOT'] . "/engine/start.php");

	global $CONFIG;
	
	// access check for closed groups
	group_gatekeeper();
	
	$owner_guid = page_owner();
	set_page_owner($owner_guid);
	
	// Get objects
	$limit = get_input("limit", 10);
	$offset = get_input("offset", 0);
	$objects = elgg_list_entities(array('types' => 'object', 'subtypes' => 'webinar', 'container_guid' => page_owner(), 'limit' => $limit, 'offset' => $offset, 'full_view' => FALSE));
		
	if($owner instanceof ElggGroup){
		$title = sprintf(elgg_echo("webinar:index"),$owner->name);
	}else{
		$title = sprintf(elgg_echo("webinar:index"),$owner->name);
	}
	$body = elgg_view_title($title);
	//theme_inria add
	if (!$objects) {
		$objects = elgg_view('page_elements/contentwrapper',array('body' => elgg_echo("webinar:none")));
	}
	$body .= $objects;
	
	$body = elgg_view_layout('two_column_left_sidebar', $area1, $body);
	
	// Finally draw the page
	page_draw($title, $body);
?>