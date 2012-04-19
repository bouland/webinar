<?php
	/**
	 * Elgg Pages
	 *
	 * @package ElggPages
	 */

	require_once($_SERVER['DOCUMENT_ROOT'] . "/engine/start.php");

	$webinar_guid = get_input('webinar_guid');
	set_context('webinar');
	
	$webinar = get_entity($webinar_guid);
	if (!$webinar) forward();

	$container = $webinar->container_guid;

	if ($container) {
		set_page_owner($container);
	} else {
		set_page_owner($webinar->owner_guid);
	}
	
	webinar_submenu($webinar);
	
	$title = $webinar->title;

	$body .= elgg_view_title($webinar->title);
	$body .= elgg_view_entity($webinar, true);

	//add comments
	$body .= elgg_view_comments($webinar);

	$body = elgg_view_layout('two_column_left_sidebar', '', $body, '');

	// Finally draw the page
	page_draw($title, $body);

?>
