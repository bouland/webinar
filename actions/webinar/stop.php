<?php
	//$api = dirname(dirname(__FILE__)) . '/vendors/bbb-api-php/bbb_api.php';
	//require( $api );
// Load configuration
	global $CONFIG;

	gatekeeper();

	$webinar_guid = get_input('webinar_guid');
	
	$webinar = get_entity($webinar_guid);
	
	if ($webinar && $webinar instanceof ElggWebinar){
		
	if ($webinar->isRunning()){
			$webinar->status = 'done';
			$webinar->save();
		}else{
			system_message(elgg_echo("webinar:isDone"));
		}
	}else{
		register_error(elgg_echo("webinar:stop:failed"));
	}
	forward($_SERVER['HTTP_REFERER']);
	exit;
?>
