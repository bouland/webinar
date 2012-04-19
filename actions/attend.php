<?php
	global $CONFIG;

	gatekeeper();

	$user_guid = get_input('user_guid', get_loggedin_userid());
	$webinar_guid = get_input('webinar_guid');

	$user = get_entity($user_guid);
	$webinar = get_entity($webinar_guid);

	if (($user instanceof ElggUser) && ($webinar instanceof ElggWebinar))
	{
		if ($webinar->isRunning()){
			$response = $webinar->create(get_loggedin_user());
			if(!$response){//If the server is unreachable
				$message = elgg_echo('webinar:start:timeout');
			}
			else if( $response['returncode'] == 'FAILED' ) { //The meeting was not created
				if($response['messageKey'] == 'checksumError'){
					$message = elgg_echo("webinar:start:salterror");
				}else{
					$message = $response['message'];
				}				
			}
			if($message){
				system_message($message);
				register_error($message);
				forward();
			}
			
			$webinar->attend($user);
			
			// Remove any invite or join request flags
			remove_entity_relationship($webinar->guid, 'subscribe', $user->guid);
			// add to river
			
		
			$url = $webinar->joinAdminURL($user);
			forward($url);
			
		}
		
	}
	else
		register_error(elgg_echo("webinar:attend:crash"));

	forward($_SERVER['HTTP_REFERER']);
	exit;
?>
