<?php
/**
 * Webinar helper functions
 *
 * @package Webinar
 */
/**
 * Prepare the new/edit form variables
 *
 * @param ElggObject $webinar
 * @return array
 */
function webinar_prepare_form_vars($webinar = null) {
	$plugin =  elgg_get_calling_plugin_entity();
	// input names => defaults
	$values = array(
			'title' => '',
			'description' => '',
			'access_id' => ACCESS_DEFAULT,
			'tags' => '',
			'status' => 'upcoming',
			'welcome_msg' => '',
			'server_salt' => $plugin->server_salt,
			'server_url' => $plugin->server_url,
			'logout_url' => null,
			'admin_pwd' => $plugin->admin_pwd,
			'user_pwd' => $plugin->user_pwd,
			'container_guid' => elgg_get_page_owner_guid(),
			'guid' => null				
	);
	// if webinar exists, populate form with his values
	if ($webinar) {
		foreach (array_keys($values) as $field) {
			if (isset($webinar->$field)) {
				$values[$field] = $webinar->$field;
			}
		}
	}
	// overwrite by a form saved in this session
	if (elgg_is_sticky_form('webinar')) {
		$sticky_values = elgg_get_sticky_values('webinar');
		foreach ($sticky_values as $key => $value) {
			$values[$key] = $value;
		}
	}

	elgg_clear_sticky_form('webinar');

	return $values;
}
elgg_set_config('pages', array(
		'title' => 'text',
		'description' => 'longtext',
		'tags' => 'tags',
		'access_id' => 'access',
		'write_access_id' => 'write_access',
));
function webinar_get_free_slots($container_guid, $limit = 1){
	$slots = array();
	$offset = 0;
	while(count($slots) < $limit){	
		$slot = get_next_slot($offset);
		if(webinar_is_free($slot,$container_guid)){
			$slots[] = $slot;
		}
		$offset++;
	}
	return $slots;
}
function webinar_is_free($slot,$container_guid){
	return event_calendar_get_events_between($slot->start_date, $slot->end_date, true, 10, 0,$container_guid) > 0 ? false : true ;
}
function webinar_get_next_slot($offset = 0){
	$nowDayOfWeek = date('w');
	$nowDayOfYear = date('z');
	$delta = WEBINAR_MEETING_SLOT_DAY - $nowDayOfWeek;
	if ($delta <= 0 ){
		$offset += 1;
	}
	$slotDayOfYear = $nowDayOfYear + $offset*7 + $delta;
	$dateTime = date_create_from_format('z', $slotDayOfYear);
	$date = $dateTime->format('Y-m-d');
	$timestamp = strtotime($date . ' 00:00:00');
	$slot = new stdClass();
	$slot->start_time = WEBINAR_MEETING_SLOT_TIME_START*60;
	$slot->end_time = WEBINAR_MEETING_SLOT_TIME_END*60;
	$slot->start_date = $timestamp + 60*$slot->start_time;
	$slot->end_date = $timestamp + 60*$slot->end_time;
	return $slot;
}

function webinar_subscribe($webinar_guid, $user_guid) {
	$result = elgg_trigger_plugin_hook('webinar:subscribe', 'webinar', array('webinar' => get_entity($webinar_guid), 'user' => get_entity($user_guid)),true);
	if($result){
		return add_entity_relationship($user_guid, 'registered', $webinar_guid);
	}else{
		return false;
	}
}
function webinar_unsubscribe($webinar_guid, $user_guid) {
	$result = elgg_trigger_plugin_hook('webinar:unsubscribe', 'webinar', array('webinar' => get_entity($webinar_guid), 'user' => get_entity($user_guid)),true);
	if($result){
		return remove_entity_relationship($user_guid, 'registered', $webinar_guid);
	}else{
		return false;
	}
}
function webinar_join($webinar_guid, $user_guid){
	$result = elgg_trigger_plugin_hook('webinar:join', 'webinar', array('webinar' => get_entity($webinar_guid), 'user' => get_entity($user_guid)),true);
	if($result){
		remove_entity_relationship($user_guid, 'registered', $webinar_guid);
		return add_entity_relationship($user_guid, 'attendee', $webinar_guid);
	}else{
		return false;
	}
}
function webinar_is_registered($webinar_guid, $user_guid) {
	$object = check_entity_relationship($user_guid, 'registered', $webinar_guid);
	if ($object) {
		return true;
	} else {
		return false;
	}
}
function webinar_is_attendee($webinar_guid, $user_guid) {
	$object = check_entity_relationship($user_guid, 'attendee', $webinar_guid);
	if ($object) {
		return true;
	} else {
		return false;
	}
}

/*
 function get_webinar_relationship($relationship, $webinar_guid, $limit = 10, $offset = 0, $site_guid = 0, $count = false) {

// in 1.7 0 means "not set."  rewrite to make sense.
if (!$site_guid) {
$site_guid = ELGG_ENTITIES_ANY_VALUE;
}

return elgg_get_entities_from_relationship(array(
		'relationship' => $relationship,
		'relationship_guid' => $webinar_guid,
		'inverse_relationship' => TRUE,
		'types' => 'user',
		'limit' => $limit,
		'offset' => $offset,
		'count' => $count,
		'site_guid' => $site_guid
));
}*/
