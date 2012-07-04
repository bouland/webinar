<?php
	global $CONFIG;

	gatekeeper();

	$user_guid = get_input('user_guid', elgg_get_logged_in_user_guid());
	$webinar_guid = get_input('webinar_guid');

	$user = get_entity($user_guid);
	$webinar = get_entity($webinar_guid);

	if (($user instanceof ElggUser) && ($webinar instanceof ElggWebinar))
	{
		elgg_load_library('elgg:webinar');
		elgg_load_library('elgg:bbb');
		if ($webinar->isRunning()){
			$response = $webinar->create($user);
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
				forward(REFERER);
			}
			
			$webinar->join($user);
			add_to_river('river/relationship/attendee/create','attendee',$user->guid,$webinar->guid);
			
		
			$url = $webinar->joinAdminURL($user);
			forward($url);
			
		}
		
	}else{
		register_error(elgg_echo("webinar:attend:crash"));
	}
	forward(REFERER);
	exit;
?>
