<?php
	//$api = dirname(dirname(__FILE__)) . '/vendors/bbb-api-php/bbb_api.php';
	//require( $api );
// Load configuration
	global $CONFIG;

	gatekeeper();

	$meeting_guid = get_input('meeting_guid');
	
	$meeting = get_entity($meeting_guid);
	
	if ($meeting && $meeting instanceof ElggMeeting){
		
		if (!$meeting->isDone()){
			$meeting->status = 'running';
			$meeting->save();
			
			add_to_river('river/object/meeting/start','start',get_loggedin_userid(),$meeting->guid);
			
		}else{
			system_message(elgg_echo("webinar:meeting:isDone"));
		}
		
	}else{
		register_error(elgg_echo("webinar:meeting:start:failed"));
	}
	forward($_SERVER['HTTP_REFERER']);
	exit;
?>
