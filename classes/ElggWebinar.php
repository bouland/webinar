<?php
/**
 * This class represents a webinar.
 *
*
 * @class      ElggWebinar
 * @package    Elgg.Webinar
 */
class ElggWebinar extends ElggObject {
	/** BigBlueButton server */
	protected function initializeAttributes() {
		parent::initializeAttributes();
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
		return new BigBlueButton('adminname', $this->guid, $this->welcome_msg, $this->admin_pwd, $this->user_pwd, $this->server_salt, $this->server_url, $this->logout_url);
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
		return $server->isWebinarRunning($this->guid, $this->server_url, $this->server_salt);
	}
	public function subscribe(ElggUser $user){
		return webinar_subscribe($this->guid, $user->guid);
	}
	public function unsubscribe(ElggUser $user){
		return webinar_unsubscribe($this->guid, $user->guid);
	}
	public function join(ElggUser $user){
		return webinar_join($this->guid, $user->guid);
	}
	public function isAttendee($user = 0) {
			if (!($user && $user instanceof ElggUser)) {
			$user = elgg_get_logged_in_user_entity();
		}
		if ($user && $user instanceof ElggUser) {
			return webinar_is_attendee($this->getGUID(), $user->getGUID());
		}else{
			return false;
		}
	}
	public function isRegistered($user = null) {
		if (!($user && $user instanceof ElggUser)) {
			$user = elgg_get_logged_in_user_entity();
		}
		if ($user && $user instanceof ElggUser) {
			return webinar_is_registered($this->getGUID(), $user->getGUID());
		}else{
			return false;
		}
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
		return $server->elgg_joinURL($this->guid, $user->name, $this->user_pwd);
	}
	public function joinAdminURL(ElggUser $admin){
		$server = $this->getServer();
		return  $server->elgg_joinURL($this->guid, $admin->name, $this->admin_pwd);
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
