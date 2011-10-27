<?php
	/**
	 * Elgg Pages
	 *
	 * @package ElggPages
	 */

	require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

	$meeting_guid = get_input('meeting_guid');
	set_context('meeting');
	
	$meeting = get_entity($meeting_guid);
	if (!$meeting) forward();

	$container = $meeting->container_guid;

	if ($container) {
		set_page_owner($container);
	} else {
		set_page_owner($meeting->owner_guid);
	}
	webinar_meeting_submenu($meeting);
	
	$title = $meeting->title;

	$body .= elgg_view_title($meeting->title);
	$body .= elgg_view_entity($meeting, true);

	//add comments
	$body .= elgg_view_comments($meeting);

	$body = elgg_view_layout('two_column_left_sidebar', '', $body, '');

	// Finally draw the page
	page_draw($title, $body);

?>
