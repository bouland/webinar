<?php

	$meeting_guid = get_input('meeting_guid');
	
	if ($meeting = get_entity($meeting_guid)) {
		
		if ($meeting->canEdit()) {

			$container = get_entity($meeting->container_guid);
			
			foreach($meeting->getEvent() as $event){
				$event->delete();
			}
			
			if ($meeting->delete()) {
				system_message(elgg_echo('webinar:meeting:delete:success'));
				
				set_page_owner($container->guid);
				forward("pg/meeting/owned/{$container->username}");
			}
			
		}
		
	}
	
	register_error(elgg_echo('pages:delete:failure'));
	forward($_SERVER['HTTP_REFERER']);

?>