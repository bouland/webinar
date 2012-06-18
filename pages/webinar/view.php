<?php
/**
 * Elgg webinar
 *
 * @package Elgg.Webinar
 */

$webinar_guid = get_input('guid');
$webinar = get_entity($webinar_guid);
if (!$webinar) {
	forward();
}

$container = get_entity($webinar->getContainerGUID());
if($container) {
	elgg_set_page_owner_guid($container->getGUID());
	group_gatekeeper();
	
	if (elgg_instanceof($container, 'group')) {
		elgg_push_breadcrumb($container->name, "webinar/group/$container->guid/all");
	} else {
		elgg_push_breadcrumb($container->name, "webinar/owner/$container->username");
	}
}

$title = $webinar->title;
elgg_push_breadcrumb($title);

$content .= elgg_view_entity($webinar, array('full_view' => true));
$content .= elgg_view_comments($webinar);

$body = elgg_view_layout('content', array(
		'filter' => '',
		'content' => $content,
		'title' => $title,
));

echo elgg_view_page($title, $body);
