<?php

	global $CONFIG;
	gatekeeper();
	
	$container_guid = (int) get_input('container_guid');
	$container = get_entity($container_guid);
    $owner_guid = (int) get_input('owner_guid');
    if ($owner_guid == 0) {
		$owner_guid = get_loggedin_userid();
    }
    $meeting_guid = get_input('meeting_guid');
    $access_id= (int)get_input('access_id', ACCESS_PRIVATE);
    $write_access_id = (int)get_input('write_access_id', ACCESS_PRIVATE);
    $title = get_input('title');
    $description = get_input('description');
	$tagarray = get_input('tags');
	
	$serverSalt = get_input('serverSalt'); // the security salt; gets encrypted with sha1
	$serverURL = get_input('serverURL'); // the url the bigbluebutton server is installed

	$logoutURL = get_input('logoutURL');
	
	$welcomeString = get_input('welcomeString');
	// the next 2 fields are maybe not needed?!?
	$adminPwd = get_input('adminPwd');// the moderator password 
	$userPwd = get_input('userPwd'); // the attendee pw

	$status = get_input('status');

	$isDated = get_input('isDated');
	$slots = $_SESSION['meetingslots'];
	$index = get_input('slot', 0);
	
	$_SESSION['meetingtitle'] = $title;
	$_SESSION['meetingdesc'] = $description;
	$_SESSION['meetingtags'] = $tagarray;
	
	if ($container->canEdit()) {
		
	    $meeting = new ElggMeeting($meeting_guid);
	    //$meeting->subtype = 'meeting';
	    $meeting->access_id = $access_id;
		$meeting->write_access_id = $write_access_id;
	    $meeting->title = $title;
	    $meeting->description = $description;
	    $meeting->owner_guid = $owner_guid;
		$meeting->container_guid = $container_guid;
		$meeting->serverSalt = $serverSalt;
		$meeting->serverURL = $serverURL;
		$meeting->welcomeString = $welcomeString;
		$meeting->adminPwd = $adminPwd;
		$meeting->userPwd = $userPwd;
		//STATE : upcoming, running, done, cancel
		$meeting->status = $status;
					
		
		if (!$meeting->save()) {
			register_error(elgg_echo("webinar:meeting:save:failed"));
			forward(get_input('forward_url', $_SERVER['HTTP_REFERER'])); //failed, so forward to previous page
		}
		
		if (!is_array($tagarray)) {
			$tagarray = string_to_tag_array($tagarray);
		}
		$meeting->clearMetadata('tags');
		$meeting->tags = $tagarray;
		
		$meeting->annotate('meeting', $meeting->description, $meeting->access_id);
		
		if (empty($logoutURL))
			$meeting->logoutURL = $CONFIG->url . 'pg/meeting/view/' . $meeting->guid;
		if (empty($meeting_guid)){
			add_to_river('river/object/meeting/create', 'create', get_loggedin_userid(), $meeting->guid);
		}
			
		// Remove the meeting post cache
		unset($_SESSION['tidypicstitle']); 
		unset($_SESSION['tidypicsbody']); 
		unset($_SESSION['tidypicstags']);
		
		if (is_plugin_enabled('event_calendar')){
			if ($isDated && is_array($slots)){
				$slot = $slots[$index];
				$event = $meeting->getEvent();
				if ($event){
					$event->start_date = $slot->start_date;
					$event->end_date = $slot->end_date;
					$event->start_time = $slot->start_time;
					$event->end_time = $slot->end_time;
				}else{
					$event = new ElggObject();
					$event->subtype = 'event_calendar';
					$event->owner_guid = get_loggedin_userid();
					$event->container_guid = $meeting->container_guid;
					$event->access_id = $meeting->access_id;
					$event->title = $meeting->title;
					$event->description = $meeting->description;
					$event->venue = $meeting->getURL();
					$event->start_date = $slot->start_date;
					$event->end_date = $slot->end_date;
					$event->start_time = $slot->start_time;
					$event->end_time = $slot->end_time;
					$event->region = '';
					$event->event_type = 'meeting';
					$event->fees = '';
					$event->contact = '';
					$event->organiser = '';
					$event->event_tags = $meeting->tags;
					$event->long_description = '';
				}
				if (!$event->save() || !add_entity_relationship($event->guid, 'meeting', $meeting->guid)){
					system_message(elgg_echo("webinar:meeting:event:create:failed"));
					register_error(elgg_echo("webinar:meeting:event:create:failed"));
				}
			}
		}

		forward("pg/meeting/view/" . $meeting->guid);

	} else {
	    register_error('meeting:manage:noprivileges');
	    forward($_SERVER['HTTP_REFERER']);
	}
