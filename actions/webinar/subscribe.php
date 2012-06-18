<?php
	global $CONFIG;

	gatekeeper();

	$user_guid = get_input('user_guid', get_loggedin_userid());
	$webinar_guid = get_input('webinar_guid');

	$user = get_entity($user_guid);
	$webinar = get_entity($webinar_guid);

	if (($user instanceof ElggUser) && ($webinar instanceof ElggWebinar))
	{
		if (!$webinar->isAttendee($user)) {
			if (!$webinar->isRegistered($user)) {
				if ($webinar->subscribe($user))
				{
					add_to_river('river/relationship/registered/create','register',$user->guid,$webinar->guid);
					
					system_message(elgg_echo("webinar:subscribe:success"));
		
					forward($webinar->getURL());
					exit;
				}else{
					system_message(elgg_echo("webinar:subscribe:failed"));
					register_error(elgg_echo("webinar:subscribe:failed"));
				}
			}else{
				system_message(elgg_echo("webinar:subscribe:duplicate"));
				register_error(elgg_echo("webinar:subscribe:duplicate"));
			}
		}
	}
	else
		register_error(elgg_echo("webinar:subscribe:crash"));

	forward($_SERVER['HTTP_REFERER']);
	exit;
?>
