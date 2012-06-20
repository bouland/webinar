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
$user = elgg_get_logged_in_user_entity();

if(elgg_is_admin_logged_in() || ($user && $user->getGUID() == $webinar->getOwnerGUID())) {
	if ($webinar->isUpcoming()){
		//start button
		elgg_register_menu_item('title', array(
								'name' => 'start',
								'href' => "action/webinar/start?webinar_guid={$webinar->getGUID()}",
								'text' => elgg_echo("webinar:start"),
								'is_action' => true,
								'link_class' => 'elgg-button elgg-button-action',
								));
	}else if ($webinar->isRunning()) {
		//stop button
		elgg_register_menu_item('title', array(
								'name' => 'stop',
								'href' => "action/webinar/stop?webinar_guid={$webinar->getGUID()}",
								'text' => elgg_echo("webinar:stop"),
								'is_action' => true,
								'link_class' => 'elgg-button elgg-button-action',
								));
	}
	
}
// this page is public but actions need logged user
if($user){
	if ($webinar->isUpcoming()){
		if ($webinar->isRegistered($user)) {
			//unsubscribe button
			elgg_register_menu_item('title', array(
									'name' => 'unsubscribe',
									'href' => "action/webinar/unsubscribe?webinar_guid={$webinar->getGUID()}",
									'text' => elgg_echo("webinar:unsubscribe"),
									'is_action' => true,
									'link_class' => 'elgg-button elgg-button-action',
									));
		} else {
			elgg_register_menu_item('title', array(
									'name' => 'subscribe',
									'href' => "action/webinar/subscribe?webinar_guid={$webinar->getGUID()}",
									'text' => elgg_echo("webinar:subscribe"),
									'is_action' => true,
									'link_class' => 'elgg-button elgg-button-action',
									));
		}
	}else if ($webinar->isRunning()) {
		elgg_register_menu_item('title', array(
								'name' => 'join',
								'href' => "action/webinar/join?webinar_guid={$webinar->getGUID()}",
								'text' => elgg_echo("webinar:join"),
								'is_action' => true,
								'link_class' => 'elgg-button elgg-button-action',
								));
	}
}

$content .= elgg_view_entity($webinar, array('full_view' => true));
$content .= elgg_view_comments($webinar);

$body = elgg_view_layout('content', array(
		'filter' => '',
		'content' => $content,
		'title' => $title,
));

echo elgg_view_page($title, $body);
