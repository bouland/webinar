<?php
	define('WEBINAR_MEETING_SLOT_DAY', 4);
	define('WEBINAR_MEETING_SLOT_TIME_START', 12);
	define('WEBINAR_MEETING_SLOT_TIME_END', 13);
		


	register_elgg_event_handler('init','system','webinar_init');
	
	function webinar_init(){
		global $CONFIG;
		
		require dirname(__FILE__) . "/lib/Meeting.php";
		require dirname(__FILE__) . '/vendors/bbb-api-php/bbb_api.php';
		
		register_page_handler('meeting','webinar_page_handler');
		
		register_elgg_event_handler('pagesetup','system','webinar_pagesetup');
		
		add_group_tool_option('meeting',elgg_echo('webinar:meeting:enable'),false);
		
		register_entity_url_handler('webinar_meeting_url', 'object', 'meeting');
		
		add_subtype("object", "meeting", "ElggMeeting");
		
		register_entity_type('object','meeting');
		
		// Listen to notification events and supply a more useful message
		register_plugin_hook('notify:entity:message', 'object', 'meeting_notify_message');
		// Register for notifications 
		if (is_callable('register_notification_object')) {
			register_notification_object('object', 'meeting', elgg_echo('webinar:meeting:notify:new'));
			
			// Listen to notification events and supply a more useful message
			register_plugin_hook('notify:entity:message', 'object', 'webinar_meeting_notify_message');
		}
		//intercept event_calendar notification because event that type is meeting are create by meeting object
		register_plugin_hook('object:notifications','object','webinar_event_notifications_intercept');

		
		register_action("meeting/subscribe",false, $CONFIG->pluginspath . "webinar/actions/subscribe.php");
		register_action("meeting/unsubscribe",false, $CONFIG->pluginspath . "webinar/actions/unsubscribe.php");
		register_action("meeting/attend",false, $CONFIG->pluginspath . "webinar/actions/attend.php");
		register_action("meeting/save",false, $CONFIG->pluginspath . "webinar/actions/save.php");
		register_action("meeting/delete",false, $CONFIG->pluginspath . "webinar/actions/delete.php");
		register_action("meeting/start",false, $CONFIG->pluginspath . "webinar/actions/start.php");
		register_action("meeting/stop",false, $CONFIG->pluginspath . "webinar/actions/stop.php");
		
		
		elgg_extend_view('groups/left_column', 'meeting/profilegroup');
		elgg_extend_view('submenu/extend', 'meeting/relationships');
		elgg_extend_view('css','webinar/css');
		
	}
	
	function webinar_page_handler($page){
	
		global $CONFIG;
			
		if (isset($page[0])){
		// See what context we're using
			switch($page[0]) {
				case "new":
					set_input('username', $page[1]);
					include($CONFIG->pluginspath . "webinar/new.php");
				break;
				case "edit":
					if (isset($page[1]) && is_numeric($page[1])) {
						set_input('meeting_guid', $page[1]);
						include($CONFIG->pluginspath . "webinar/edit.php");
					}else{
						return false;
					}
				break;
				case "list":
					if (isset($page[2]) && is_numeric($page[2])) {
						set_input('relationship', $page[1]);
						set_input('meeting_guid', $page[2]);
						include($CONFIG->pluginspath . "webinar/relationShips.php");
					}else{
						return false;
					}
				break;
				case "view":
					if (isset($page[1]) && is_numeric($page[1])) {
						set_input('meeting_guid', $page[1]);
						include($CONFIG->pluginspath . "webinar/view.php");
					}else{
						return false;
					}
				break;
				case "owned":
    				// Owned by a user
    				if (isset($page[1])) {
    					set_input('username',$page[1]);
					}
    					
    				include($CONFIG->pluginspath . "webinar/index.php");	
    			break;
				default:
	    			return false;
	    		break;
			}
		}else{
			return false;
		}			
	}
	/**
	 * Notification message handler
	 */
	function webinar_notify_message($hook, $entity_type, $returnvalue, $params)
	{
		$entity = $params['entity'];
		$to_entity = $params['to_entity'];
		$method = $params['method'];
		if ($entity instanceof WebinarMeeting)
		{
			// block notification message when the album doesn't have any photos
			//if ($entity->new_album == TP_NEW_ALBUM)
			//	return false;
				
			$descr = $entity->description;
			$title = $entity->title;
			$owner = $entity->getOwnerEntity();
			return sprintf(elgg_echo('webinar:meeting:new:river'), $owner->name) . ': ' . $title . "\n\n" . $descr . "\n\n" . $entity->getURL();
		}
		return null;
	}
	function webinar_meeting_notify_message($hook, $entity_type, $returnvalue, $params)
	{
		$entity = $params['entity'];
		$to_entity = $params['to_entity'];
		$method = $params['method'];
		if ($entity instanceof ElggMeeting)
		{

			$descr = $entity->description;
			$title = $entity->title;
			global $CONFIG;
			$url = $entity->getURL();

			$msg = get_input('topicmessage');
			if (empty($msg)) $msg = get_input('topic_post');
			if (!empty($msg)) $msg = $msg . "\n\n"; else $msg = '';

			$owner = get_entity($entity->container_guid);
			if ($method == 'sms') {
				return elgg_echo("groupforumtopic:new") . ': ' . $url . " ({$owner->name}: {$title})";
			} else {
				return $_SESSION['user']->name . ' ' . elgg_echo("groups:viagroups") . ': ' . $title . "\n\n" . $msg . "\n\n" . $entity->getURL();
			}

		}
		return null;
	}

	function webinar_event_notifications_intercept($hook, $entity_type, $returnvalue, $params) {
		if (isset($params)) {
			if ($params['event'] == 'create' && $params['object'] instanceof ElggObject) {
				if ($params['object']->getSubtype() == 'event_calendar') {
					if ($params['object']->event_type == 'meeting'){
						return true;
					}
				}
			}
		}
		return null;
	}
	function webinar_pagesetup() {
			
		global $CONFIG;

		//add submenu options
		
			
		// Group submenu
		$page_owner = page_owner_entity();
			
		if ($page_owner instanceof ElggGroup && get_context() == 'groups') {
			if($page_owner->meeting_enable != "no"){
				if ($page_owner->canEdit()){
				    //add_submenu_item(sprintf(elgg_echo("blog:group"),$page_owner->name), $CONFIG->wwwroot . "pg/blog/owner/" . $page_owner->username);
				    add_submenu_item(elgg_echo('webinar:meeting:group:menu:new'),$CONFIG->wwwroot."pg/meeting/new/". $page_owner->username);
				}
		    }
		}
	}
	function webinar_meeting_url($entity){
		global $CONFIG;
		$title = $entity->title;
		$title = friendly_title($title);
		return $CONFIG->url . "pg/meeting/view/" . $entity->getGUID() . "/" . $title;
	}
	function webinar_meeting_submenu(ElggMeeting $meeting){
		global $CONFIG;
		if (isloggedin()) {
			
			if (!$meeting->isRunning() && !$meeting->isDone()){
				if (!$meeting->isAttendee(get_loggedin_user())) {
					if ($meeting->isRegistered(get_loggedin_user())) {
						//unsubscribe
						$unsubscribe_url = elgg_add_action_tokens_to_url("{$CONFIG->wwwroot}action/meeting/unsubscribe?meeting_guid={$meeting->getGUID()}");
						add_submenu_item(elgg_echo('webinar:meeting:menu:unsubscribe'), $unsubscribe_url, 'meetingactions');
					} else {
						//subscribe
						$subscribe_url = elgg_add_action_tokens_to_url("{$CONFIG->wwwroot}action/meeting/subscribe?meeting_guid={$meeting->getGUID()}");
						add_submenu_item(elgg_echo('webinar:meeting:menu:subscribe'), $subscribe_url, 'meetingactions');
					}
				}
			}elseif($meeting->isRunning()){
				//attend
				$attend_url = elgg_add_action_tokens_to_url("{$CONFIG->wwwroot}action/meeting/attend?meeting_guid={$meeting->getGUID()}");
				add_submenu_item(elgg_echo('webinar:meeting:menu:attend'), $attend_url, 'meetingactions');
			}
			$container = get_entity($meeting->container_guid);
			if ($meeting->canEdit()){
				//edit
				add_submenu_item(elgg_echo('webinar:meeting:menu:edit'),"{$CONFIG->wwwroot}pg/meeting/edit/{$meeting->getGUID()}", 'meetingadmin');
				if (!$meeting->isRunning() & !$meeting->isDone()){
					//start
					$start_url = elgg_add_action_tokens_to_url("{$CONFIG->wwwroot}action/meeting/start?meeting_guid={$meeting->getGUID()}");
					add_submenu_item(elgg_echo('webinar:meeting:menu:start'), $start_url, 'meetingadmin');
					//delete
					$delete_url = elgg_add_action_tokens_to_url("{$CONFIG->wwwroot}action/meeting/delete?meeting_guid={$meeting->getGUID()}");
					add_submenu_item(elgg_echo('webinar:meeting:menu:delete'), $delete_url, 'meetingadmin', true);
				}
				if ($meeting->isRunning()){
					//stop
					$stop_url = elgg_add_action_tokens_to_url("{$CONFIG->wwwroot}action/meeting/stop?meeting_guid={$meeting->getGUID()}");
					add_submenu_item(elgg_echo('webinar:meeting:menu:stop'), $stop_url, 'meetingadmin');
				}
				//new
				if ($container instanceof ElggGroup)		
		    		add_submenu_item(elgg_echo('webinar:meeting:menu:new'),$CONFIG->wwwroot."pg/meeting/new/" . $container->username, 'meetingadmin');
					
			}
			//view
			if ($container instanceof ElggGroup)
				add_submenu_item(elgg_echo('webinar:meeting:menu:view'),"{$CONFIG->wwwroot}pg/meeting/owned/{$container->username}", 'meetingadmin');
			
		}
	}
?>