<?php
/**
 * Edit a webinar
 *
 * @package Elgg.Webinar
 */

gatekeeper();

$webinar_guid = (int)get_input('guid');
$webinar = get_entity($webinar_guid);
if (!$webinar) {
	register_error(elgg_echo('noaccess'));
	forward('');
}

if (!elgg_instanceof($webinar, 'object' , 'webinar', 'ElggWebinar')) {
	register_error(elgg_echo('not a webinar'));
	forward('');
}

$container = $webinar->getContainerEntity();
if (!$container) {
	register_error(elgg_echo('noaccess'));
	forward('');
}

elgg_set_page_owner_guid($container->getGUID());

elgg_push_breadcrumb($webinar->title, $webinar->getURL());
elgg_push_breadcrumb(elgg_echo('edit'));

$title = elgg_echo("webinar:edit");

if ($webinar->canEdit()) {
	$vars = webinar_prepare_form_vars($webinar);
	$content = elgg_view_form('webinar/save', array(), $vars);
} else {
	$content = elgg_echo("webinar:noaccess");
}

$body = elgg_view_layout('content', array(
	'filter' => '',
	'content' => $content,
	'title' => $title,
));

echo elgg_view_page($title, $body);
