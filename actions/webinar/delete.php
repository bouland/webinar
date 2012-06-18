<?php

	$webinar_guid = get_input('webinar_guid');
	
	if ($webinar = get_entity($webinar_guid) && $webinar instanceof ElggWebinar) {
		
		if ($webinar->canEdit()) {

			$container = get_entity($webinar->container_guid);
			
			foreach($webinar->getEvent() as $event){
				$event->delete();
			}
			
			if ($webinar->delete()) {
				system_message(elgg_echo('webinar:delete:success'));
				
				set_page_owner($container->guid);
				forward("pg/webinar/owned/{$container->username}");
			}
			
		}
		
	}
	
	register_error(elgg_echo('pages:delete:failure'));
	forward($_SERVER['HTTP_REFERER']);

?>