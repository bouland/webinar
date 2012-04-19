<?php
//require( dirname(dirname(__FILE__)) . '/vendors/bbb-api-php/bbb_api.php');

class ElggWebinar extends ElggObject {
	protected function initialise_attributes() {
		parent::initialise_attributes();
		$this->attributes['subtype'] = "webinar";
	}

	public function __construct($guid = null) {
		parent::__construct($guid);
	}
	public function create(ElggUser $admin){
		$server = $this->getServer();
		return $server->elgg_createWebinarArray($admin);
	}
	public function getServer() {
		$arg = $this->logoutURL;
		return new BigBlueButton($this->adminname, $this->guid, $this->welcomeString, $this->adminPwd, $this->userPwd, $this->serverSalt, $this->serverURL, $this->logoutURL);
	}
	public function isRunning(){
		return $this->status == 'running';
	}
	public function isDone(){
		return $this->status == 'done';
	}
	public function isUpcoming(){
		return $this->status == 'upcoming';
	}
	public function isCancel(){
		return $this->status == 'cancel';
	}
	public function updateStatus(){
		$server = $this->getServer();
		return $server->isWebinarRunning($this->guid, $this->serverURL, $this->serverSalt);
	}
	public function subscribe(ElggUser $user){
		return subscribe_webinar($this->guid, $user->guid);
	}
	public function unsubscribe(ElggUser $user){
		return unsubscribe_webinar($this->guid, $user->guid);
	}
	public function attend(ElggUser $user){
		return attend_webinar($this->guid, $user->guid);
	}
	public function isAttendee($user = 0) {
		if (!($user instanceof ElggUser)) {
			$user = get_loggedin_user();
		}
		if (!($user instanceof ElggUser)) {
			return false;
		}
		return is_webinar_attendee($this->getGUID(), $user->getGUID());
	}
	public function isRegistered($user = 0) {
		if (!($user instanceof ElggUser)) {
			$user = get_loggedin_user();
		}
		if (!($user instanceof ElggUser)) {
			return false;
		}
		return is_webinar_registered($this->getGUID(), $user->getGUID());
	}
	public function getAttendees($limit = 10, $offset = 0, $count = false) {
		return get_webinar_relationship('attendee', $this->getGUID(), $limit, $offset, 0 , $count);
	}
	public function getRegistereds($limit = 10, $offset = 0, $count = false) {
		return get_webinar_relationship('registered', $this->getGUID(), $limit, $offset, 0 , $count);
	}
	public function getRelationShip(){
		switch($this->status)
		{
			case "upcoming":
				return array('registered');
			break;
			case "running":
				return array('registered','attendee');
			break;
			case "done":
				return array('attendee');
			break;
			case "cancel":
				return array('registered','attendee');
			break;
		}
	}/*
	public function getRelationShipUsers($relationShip, $limit = 10, $offset = 0, $count = false) {
		if ($relationShip == 'attendee' || $relationShip == 'registered' ) {
			return );
		}else{
			return null;
		}
	}*/
	public function getRelationShipOptions($relationship, $limit = 10, $count = false){
		return array(	'relationship' => $relationship,
						'relationship_guid' => $this->guid,
						'inverse_relationship' => TRUE,
						'types' => 'user',
						'limit' => $limit,
						'offset' => 0,
						'count' => $count,
						'full_view' => FALSE,
						'pagination' => FALSE
					);
	}
	public function joinURL(ElggUser $user){
		$server = $this->getServer();
		return $server->elgg_joinURL($this->guid, $user->name, $this->userPwd);
	}
	public function joinAdminURL(ElggUser $admin){
		$server = $this->getServer();
		return  $server->elgg_joinURL($this->guid, $admin->name, $this->adminPwd);
	}
	public function isCreated(){
		$server = $this->getServer();
		$webinars = $server->getWebinars();
		foreach ($webinars as $webinar){
			if($webinar->webinarID == $this->guid)
				return true;
		}
		return false;
	}
	public function isWebinarRunning(){
		$server = $this->getServer();
		return $server->isWebinarRunning($this->guid);
	}
	public function stop(){
		$server = $this->getServer();
		return $server->elgg_endWebinar();
	}
	public function getEvent(){
		return elgg_get_entities_from_relationship(array(	'relationship' => 'webinar',
															'relationship_guid' => $this->guid,
															'inverse_relationship' => TRUE,
															'type' => 'object',
															'subtype' => 'event_calendar',
															'limit' => 0,
															'offset' => 0,
															'count' => false,
															'full_view' => FALSE,
															'pagination' => FALSE
													));
	}

}

function get_free_slots($container_guid, $limit = 1){
	$slots = array();
	$offset = 0;
	while(count($slots) < $limit){	
		$slot = get_next_slot($offset);
		if(is_free($slot,$container_guid)){
			$slots[] = $slot;
		}
		$offset++;
	}
	return $slots;
}
function is_free($slot,$container_guid){
	return event_calendar_get_events_between($slot->start_date, $slot->end_date, true, 10, 0,$container_guid) > 0 ? false : true ;
}
function get_next_slot($offset = 0){
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

function subscribe_webinar($webinar_guid, $user_guid) {
	trigger_elgg_event('subscribe', 'webinar', array('webinar' => get_entity($webinar_guid), 'user' => get_entity($user_guid)));
	return add_entity_relationship($user_guid, 'registered', $webinar_guid);
}
function unsubscribe_webinar($webinar_guid, $user_guid) {
	// event needs to be triggered while user is still member of group to have access to group acl
	trigger_elgg_event('unsubscribe', 'webinar', array('webinar' => get_entity($webinar_guid), 'user' => get_entity($user_guid)));
	$result = remove_entity_relationship($user_guid, 'registered', $webinar_guid);
	return $result;
}
function attend_webinar($webinar_guid, $user_guid){
	trigger_elgg_event('attend', 'webinar', array('webinar' => get_entity($webinar_guid), 'user' => get_entity($user_guid)));
	return add_entity_relationship($user_guid, 'attendee', $webinar_guid);
}
function is_webinar_registered($webinar_guid, $user_guid) {
	$object = check_entity_relationship($user_guid, 'registered', $webinar_guid);
	if ($object) {
		return true;
	} else {
		return false;
	}
}
function is_webinar_attendee($webinar_guid, $user_guid) {
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
?>