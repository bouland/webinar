<?php
/**
 * Elgg webinar plugin everyone page
 *
 * @package Elgg.Webinar
 */

elgg_pop_breadcrumb();
elgg_push_breadcrumb(elgg_echo('webinar'));

elgg_register_title_button();

$content = elgg_list_entities(array(
	'type' => 'object',
	'subtype' => 'webinar',
	'limit' => 10,
	'full_view' => false,
	'view_toggle_type' => false
));

if (!$content) {
	$content = elgg_echo('webinar:none');
}

$title = elgg_echo('webinar:everyone');

$body = elgg_view_layout('content', array(
	'filter_context' => 'all',
	'content' => $content,
	'title' => $title,
	'sidebar' => elgg_view('webinar/sidebar'),
));

echo elgg_view_page($title, $body);