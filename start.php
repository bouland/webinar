<?php
	define('WEBINAR_MEETING_SLOT_DAY', 4);
	define('WEBINAR_MEETING_SLOT_TIME_START', 12);
	define('WEBINAR_MEETING_SLOT_TIME_END', 13);
		


	register_elgg_event_handler('init','system','webinar_init');
	
	function webinar_init(){
		global $CONFIG;
		
		require dirname(__FILE__) . "/lib/Webinar.php";
		require dirname(__FILE__) . '/vendors/bbb-api-php/bbb_api.php';
		
		register_page_handler('webinar','webinar_page_handler');
		
		register_elgg_event_handler('pagesetup','system','webinar_pagesetup');
		
		add_group_tool_option('webinar',elgg_echo('webinar:enable'),false);
		
		register_entity_url_handler('webinar_url', 'object', 'webinar');
		
		add_subtype("object", "webinar", "ElggWebinar");
		
		register_entity_type('object','webinar');
		
		// Listen to notification events and supply a more useful message
		// Register for notifications 
		if (is_callable('register_notification_object')) {
			register_notification_object('object', 'webinar', elgg_echo('webinar:notify:new'));
			
			// Listen to notification events and supply a more useful message
			register_plugin_hook('notify:entity:message', 'object', 'webinar_notify_message');
		}
		//intercept event_calendar notification because event that type is webinar are create by webinar object
		register_plugin_hook('object:notifications','object','webinar_event_notifications_intercept');
		
		register_action("webinar/subscribe",false, $CONFIG->pluginspath . "webinar/actions/subscribe.php");
		register_action("webinar/unsubscribe",false, $CONFIG->pluginspath . "webinar/actions/unsubscribe.php");
		register_action("webinar/attend",false, $CONFIG->pluginspath . "webinar/actions/attend.php");
		register_action("webinar/save",false, $CONFIG->pluginspath . "webinar/actions/save.php");
		register_action("webinar/delete",false, $CONFIG->pluginspath . "webinar/actions/delete.php");
		register_action("webinar/start",false, $CONFIG->pluginspath . "webinar/actions/start.php");
		register_action("webinar/stop",false, $CONFIG->pluginspath . "webinar/actions/stop.php");
		
		register_elgg_event_handler('create','attendee','webinar_notify_relationship');
		
		elgg_extend_view('groups/left_column', 'webinar/profilegroup');
		elgg_extend_view('submenu/extend', 'webinar/relationships');
		elgg_extend_view('css','webinar/css');
		
		elgg_extend_view('profile/tabs/menu_extend','webinar/group_profile_tabs_menu');
		
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
						set_input('webinar_guid', $page[1]);
						include($CONFIG->pluginspath . "webinar/edit.php");
					}else{
						return false;
					}
				break;
				case "list":
					if (isset($page[2]) && is_numeric($page[2])) {
						set_input('relationship', $page[1]);
						set_input('webinar_guid', $page[2]);
						include($CONFIG->pluginspath . "webinar/relationShips.php");
					}else{
						return false;
					}
				break;
				case "view":
					if (isset($page[1]) && is_numeric($page[1])) {
						set_input('webinar_guid', $page[1]);
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
					if (can_write_to_container()){
						//add_submenu_item(sprintf(elgg_echo("blog:group"),$page_owner->name), $CONFIG->wwwroot . "pg/blog/owner/" . $page_owner->username);
						add_submenu_item(elgg_echo('webinar:group:menu:new'),$CONFIG->wwwroot."pg/webinar/new/". $page[1]);
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
	function webinar_notify_relationship($event, $object_type, $object){
		if ($object instanceof ElggRelationship){
			add_to_river('river/relationship/attendee/create','attend',$object->guid_one,$object->guid_two);
		}
		return true;
	}
	function webinar_notify_message($hook, $entity_type, $returnvalue, $params)
	{
		$entity = $params['entity'];
		$to_entity = $params['to_entity'];
		$method = $params['method'];
		if ($entity instanceof ElggWebinar)
		{
			$owner = $entity->getOwnerEntity();
			if ($method == 'sms') {
				return $owner->name . elgg_echo('webinar:sms') . $entity->title;
			}
			if ($method == 'email') {
				if (is_callable('object_notifications_inria')) {
					$owner = $entity->getOwnerEntity();
					return array('to'      => $to_entity->guid,
											 'from'    => $entity->container_guid,
											 'subject' => $entity->title,
											 'message' => $entity->description . "\n\n--\n" . $owner->name  . "\n\n" . $entity->getURL());
				}else{
					return $returnvalue;
				}
			}

		}
		return null;
	}
	function webinar_event_notifications_intercept($hook, $entity_type, $returnvalue, $params) {
		if (isset($params)) {
			if ($params['event'] == 'create' && $params['object'] instanceof ElggObject) {
				if ($params['object']->getSubtype() == 'event_calendar') {
					if ($params['object']->event_type == 'webinar'){
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
			if($page_owner->webinar_enable != "no"){
				if ($page_owner->canEdit()){
				    //add_submenu_item(sprintf(elgg_echo("blog:group"),$page_owner->name), $CONFIG->wwwroot . "pg/blog/owner/" . $page_owner->username);
				    add_submenu_item(elgg_echo('webinar:group:menu:new'),$CONFIG->wwwroot."pg/webinar/new/". $page_owner->username);
				}
		    }
		}
	}
	function webinar_url($entity){
		global $CONFIG;
		$title = $entity->title;
		$title = friendly_title($title);
		return $CONFIG->url . "pg/webinar/view/" . $entity->getGUID() . "/" . $title;
	}
	function webinar_submenu(ElggWebinar $webinar){
		global $CONFIG;
		if (isloggedin()) {
			
			if (!$webinar->isRunning() && !$webinar->isDone()){
				if (!$webinar->isAttendee(get_loggedin_user())) {
					if ($webinar->isRegistered(get_loggedin_user())) {
						//unsubscribe
						$unsubscribe_url = elgg_add_action_tokens_to_url("{$CONFIG->wwwroot}action/webinar/unsubscribe?webinar_guid={$webinar->getGUID()}");
						add_submenu_item(elgg_echo('webinar:menu:unsubscribe'), $unsubscribe_url, 'webinaractions');
					} else {
						//subscribe
						$subscribe_url = elgg_add_action_tokens_to_url("{$CONFIG->wwwroot}action/webinar/subscribe?webinar_guid={$webinar->getGUID()}");
						add_submenu_item(elgg_echo('webinar:menu:subscribe'), $subscribe_url, 'webinaractions');
					}
				}
			}elseif($webinar->isRunning()){
				//attend
				$attend_url = elgg_add_action_tokens_to_url("{$CONFIG->wwwroot}action/webinar/attend?webinar_guid={$webinar->getGUID()}");
				add_submenu_item(elgg_echo('webinar:menu:attend'), $attend_url, 'webinaractions');
			}
			$container = get_entity($webinar->container_guid);
			if ($webinar->canEdit()){
				//edit
				add_submenu_item(elgg_echo('webinar:menu:edit'),"{$CONFIG->wwwroot}pg/webinar/edit/{$webinar->getGUID()}", 'webinaradmin');
				if (!$webinar->isRunning() & !$webinar->isDone()){
					//start
					$start_url = elgg_add_action_tokens_to_url("{$CONFIG->wwwroot}action/webinar/start?webinar_guid={$webinar->getGUID()}");
					add_submenu_item(elgg_echo('webinar:menu:start'), $start_url, 'webinaradmin');
					//delete
					$delete_url = elgg_add_action_tokens_to_url("{$CONFIG->wwwroot}action/webinar/delete?webinar_guid={$webinar->getGUID()}");
					add_submenu_item(elgg_echo('webinar:menu:delete'), $delete_url, 'webinaradmin', true);
				}
				if ($webinar->isRunning()){
					//stop
					$stop_url = elgg_add_action_tokens_to_url("{$CONFIG->wwwroot}action/webinar/stop?webinar_guid={$webinar->getGUID()}");
					add_submenu_item(elgg_echo('webinar:menu:stop'), $stop_url, 'webinaradmin');
				}
				//new
				if ($container instanceof ElggGroup)		
		    		add_submenu_item(elgg_echo('webinar:menu:new'),$CONFIG->wwwroot."pg/webinar/new/" . $container->username, 'webinaradmin2');
					
			}
			
		}
	}
