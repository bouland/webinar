<?php

	global $CONFIG;

	gatekeeper();

	$user_guid = get_input('user_guid', get_loggedin_userid());
	$meeting_guid = get_input('meeting_guid');

	$user = get_entity($user_guid);
	$meeting = get_entity($meeting_guid);

	if (($user instanceof ElggUser) && ($meeting instanceof ElggMeeting))
	{
		if (!$meeting->isAttendee($user)) {
			if (!$meeting->isRegistered($user)) {
				if ($meeting->subscribe($user))
				{
					add_to_river('river/relationship/registered/create','register',$user->guid,$meeting->guid);
					
					system_message(elgg_echo("webinar:meeting:subscribe:success"));
		
					forward($meeting->getURL());
					exit;
				}else{
					system_message(elgg_echo("webinar:meeting:subscribe:failed"));
					register_error(elgg_echo("webinar:meeting:subscribe:failed"));
				}
			}else{
				system_message(elgg_echo("webinar:meeting:subscribe:duplicate"));
				register_error(elgg_echo("webinar:meeting:subscribe:duplicate"));
			}
		}
	}
	else
		register_error(elgg_echo("webinar:meeting:subscribe:crash"));

	forward($_SERVER['HTTP_REFERER']);
	exit;
