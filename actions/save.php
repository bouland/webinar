<?php

	global $CONFIG;
	gatekeeper();
	
	$container_guid = (int) get_input('container_guid');
	$container = get_entity($container_guid);
    $owner_guid = (int) get_input('owner_guid');
    if ($owner_guid == 0) {
		$owner_guid = get_loggedin_userid();
    }
    $webinar_guid = get_input('webinar_guid');
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
	$slots = $_SESSION['webinarslots'];
	$index = get_input('slot', 0);
	
	$_SESSION['webinartitle'] = $title;
	$_SESSION['webinardesc'] = $description;
	$_SESSION['webinartags'] = $tagarray;
	
	if ($container->canEdit()) {
		
	    $webinar = new ElggWebinar($webinar_guid);
	    //$webinar->subtype = 'webinar';
	    $webinar->access_id = $access_id;
		$webinar->write_access_id = $write_access_id;
	    $webinar->title = $title;
	    $webinar->description = $description;
	    $webinar->owner_guid = $owner_guid;
		$webinar->container_guid = $container_guid;
		$webinar->serverSalt = $serverSalt;
		$webinar->serverURL = $serverURL;
		$webinar->welcomeString = $welcomeString;
		$webinar->adminPwd = $adminPwd;
		$webinar->userPwd = $userPwd;
		//STATE : upcoming, running, done, cancel
		$webinar->status = $status;
					
		
		if (!$webinar->save()) {
			register_error(elgg_echo("webinar:save:failed"));
			forward(get_input('forward_url', $_SERVER['HTTP_REFERER'])); //failed, so forward to previous page
		}
		
		if (!is_array($tagarray)) {
			$tagarray = string_to_tag_array($tagarray);
		}
		$webinar->clearMetadata('tags');
		$webinar->tags = $tagarray;
		
		$webinar->annotate('webinar', $webinar->description, $webinar->access_id);
		
		if (empty($logoutURL))
			$webinar->logoutURL = $CONFIG->url . 'pg/webinar/view/' . $webinar->guid;
		if (empty($webinar_guid)){
			add_to_river('river/object/webinar/create', 'create', get_loggedin_userid(), $webinar->guid);
		}
			
		// Remove the webinar post cache
		unset($_SESSION['tidypicstitle']); 
		unset($_SESSION['tidypicsbody']); 
		unset($_SESSION['tidypicstags']);
		
		if (is_plugin_enabled('event_calendar')){
			if ($isDated && is_array($slots)){
				$slot = $slots[$index];
				$event = $webinar->getEvent();
				if ($event){
					$event->start_date = $slot->start_date;
					$event->end_date = $slot->end_date;
					$event->start_time = $slot->start_time;
					$event->end_time = $slot->end_time;
				}else{
					$event = new ElggObject();
					$event->subtype = 'event_calendar';
					$event->owner_guid = get_loggedin_userid();
					$event->container_guid = $webinar->container_guid;
					$event->access_id = $webinar->access_id;
					$event->title = $webinar->title;
					$event->description = $webinar->description;
					$event->venue = $webinar->getURL();
					$event->start_date = $slot->start_date;
					$event->end_date = $slot->end_date;
					$event->start_time = $slot->start_time;
					$event->end_time = $slot->end_time;
					$event->region = '';
					$event->event_type = 'webinar';
					$event->fees = '';
					$event->contact = '';
					$event->organiser = '';
					$event->event_tags = $webinar->tags;
					$event->long_description = '';
				}
				if (!$event->save() || !add_entity_relationship($event->guid, 'webinar', $webinar->guid)){
					system_message(elgg_echo("webinar:event:create:failed"));
					register_error(elgg_echo("webinar:event:create:failed"));
				}
			}
		}

		forward("pg/webinar/view/" . $webinar->guid);

	} else {
	    register_error('webinar:manage:noprivileges');
	    forward($_SERVER['HTTP_REFERER']);
	}
?>