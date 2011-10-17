<?php

	global $CONFIG;

	gatekeeper();

	$user_guid = get_input('user_guid', get_loggedin_userid());
	$meeting_guid = get_input('meeting_guid');

	$user = get_entity($user_guid);
	$meeting = get_entity($meeting_guid);

	if (($user instanceof ElggUser) && ($meeting instanceof ElggMeeting))
	{
		if ($meeting->isRegistered($user)) {
			if ($meeting->unsubscribe($user)){
				system_message(elgg_echo("webinar:meeting:unsubscribe:success"));
				forward($meeting->getURL());
				exit;
			}else{
				system_message(elgg_echo("webinar:meeting:unsubscribe:failed"));
				register_error(elgg_echo("webinar:meeting:unsubscribe:failed"));
			}
		}else{
			system_message(elgg_echo("webinar:meeting:unsubscribe:impossible"));
			register_error(elgg_echo("webinar:meeting:unsubscribe:impossible"));
		}
	}
	else
		register_error(elgg_echo("webinar:meeting:unsubscribe:crash"));

	forward($_SERVER['HTTP_REFERER']);
	exit;
