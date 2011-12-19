<?php
	global $CONFIG;

	gatekeeper();

	$user_guid = get_input('user_guid', get_loggedin_userid());
	$meeting_guid = get_input('meeting_guid');

	$user = get_entity($user_guid);
	$meeting = get_entity($meeting_guid);

	if (($user instanceof ElggUser) && ($meeting instanceof ElggMeeting))
	{
		if ($meeting->isRunning()){
			$response = $meeting->create(get_loggedin_user());
			if(!$response){//If the server is unreachable
				$message = elgg_echo('webinar:meeting:start:timeout');
			}
			else if( $response['returncode'] == 'FAILED' ) { //The meeting was not created
				if($response['messageKey'] == 'checksumError'){
					$message = elgg_echo("webinar:meeting:start:salterror");
				}else{
					$message = $response['message'];
				}				
			}
			if($message){
				system_message($message);
				register_error($message);
				forward();
			}
			
			$meeting->attend($user);
			
			// Remove any invite or join request flags
			remove_entity_relationship($meeting->guid, 'subscribe', $user->guid);
			// add to river
			
		
			$url = $meeting->joinAdminURL($user);
			forward($url);
			
		}
		
	}
	else
		register_error(elgg_echo("webinar:meeting:attend:crash"));

	forward($_SERVER['HTTP_REFERER']);
	exit;
?>
