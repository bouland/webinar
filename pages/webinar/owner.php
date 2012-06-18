<?php
/**
 * Elgg Pages
 * 
 * @package ElggPages
 */

$page_owner = elgg_get_page_owner_entity();
if (!$page_owner) {
	forward('webinar/all');
}
	
elgg_push_breadcrumb($page_owner->name);

elgg_register_title_button();

$content .= elgg_list_entities(array(
		'type' => 'object',
		'subtype' => 'webinar',
		'container_guid' => $page_owner->guid,
		'limit' => 10,
		'full_view' => false,
		'view_toggle_type' => false
));

if (!$content) {
	$content = elgg_echo('webinar:none');
}

$title = elgg_echo('webinar:owner', array($page_owner->name));

$filter_context = '';
if ($page_owner->getGUID() == elgg_get_logged_in_user_guid()) {
	$filter_context = 'mine';
}

$vars = array(
		'filter_context' => $filter_context,
		'content' => $content,
		'title' => $title,
		'sidebar' => elgg_view('webinar/sidebar'),
);

// don't show filter if out of filter context
if ( elgg_instanceof($page_owner, 'group') ) {
	$vars['filter'] = false;
}

$body = elgg_view_layout('content', $vars);

echo elgg_view_page($title, $body);
