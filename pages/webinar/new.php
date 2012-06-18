<?php
/**
 * Create a new webinar
 *
 * @package Elgg.Webinar
 */

gatekeeper();

$container_guid = (int) get_input('guid');
$container = get_entity($container_guid);
if ($container) {

	elgg_set_page_owner_guid($container->getGUID());
	
	$title = elgg_echo('webinar:new');
	elgg_push_breadcrumb($title);
	
	$vars = webinar_prepare_form_vars(null);
	$content = elgg_view_form('webinar/save', array(), $vars);
	
	$body = elgg_view_layout('content', array(
		'filter' => '',
		'content' => $content,
		'title' => $title,
	));
	
	echo elgg_view_page($title, $body);
}