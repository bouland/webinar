<?php
	//$api = dirname(dirname(__FILE__)) . '/vendors/bbb-api-php/bbb_api.php';
	//require( $api );
// Load configuration
	global $CONFIG;

	gatekeeper();

	$webinar_guid = get_input('webinar_guid');
	
	
	
	if ( ($webinar = get_entity($webinar_guid)) && $webinar instanceof ElggWebinar){
		
		if (!$webinar->isDone()){
			$webinar->status = 'running';
			$webinar->save();
			
			add_to_river('river/object/webinar/start','start',get_loggedin_userid(),$webinar->guid);
			
		}else{
			system_message(elgg_echo("webinar:isDone"));
		}
		
	}else{
		register_error(elgg_echo("webinar:start:failed"));
	}
	forward($_SERVER['HTTP_REFERER']);
	exit;
?>
