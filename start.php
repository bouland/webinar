<?php
	/**
	 * Elgg Webinar
	 *
	 * @package Elgg.Webinar
	 */
	define('WEBINAR_MEETING_SLOT_DAY', 4);
	define('WEBINAR_MEETING_SLOT_TIME_START', 12);
	define('WEBINAR_MEETING_SLOT_TIME_END', 13);

	register_elgg_event_handler('init','system','webinar_init');
	
	/**
	 * Initialize the webinar plugin.
	 */
	function webinar_init(){
		
		// register a library for new object class webinar
		elgg_register_library('elgg:webinar', elgg_get_plugins_path() . 'webinar/lib/webinar.php');
		// register BigBlueButton API
		elgg_register_library('elgg:bbb', elgg_get_plugins_path() . 'webinar/vendors/bbb-api-php/bbb_api.php');
		// Register a url handler for the new object
		elgg_register_entity_url_handler('object', 'webinar', 'webinar_url');
		
		//add a tab in site menu
		$item = new ElggMenuItem('webinar', elgg_echo('webinar:menu:site'), 'webinar/all');
		elgg_register_menu_item('site', $item);
		
		// Register a page handler, so we can have nice URLs
		elgg_register_page_handler('webinar','webinar_page_handler');
		
		// Register some actions
		$action_base = elgg_get_plugins_path() . 'webinar/actions/webinar';
		elgg_register_action("webinar/subscribe", "$action_base/subscribe.php");
		elgg_register_action("webinar/unsubscribe", "$action_base/unsubscribe.php");
		elgg_register_action("webinar/join", "$action_base/join.php");
		elgg_register_action("webinar/save", "$action_base/save.php");
		elgg_register_action("webinar/delete", "$action_base/delete.php");
		elgg_register_action("webinar/start", "$action_base/start.php");
		elgg_register_action("webinar/stop", "$action_base/stop.php");
		
		// Extend the main css view
		elgg_extend_view('css','webinar/css');
		
		// Register entity type for search
		elgg_register_entity_type('object','webinar');
		
		//register_elgg_event_handler('pagesetup','system','webinar_pagesetup');
		// Register for notifications
		register_notification_object('object', 'webinar', elgg_echo('webinar:notify:new'));
		
		// add checkbox on group edit page to activate webinar
		add_group_tool_option('webinar',elgg_echo('webinar:enable'),false);
		
		elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'webinar_menu_owner_block');
		// entity menu
		elgg_register_plugin_hook_handler('register', 'menu:entity', 'webinar_menu_entity');
		// title menu
		elgg_register_plugin_hook_handler('register', 'menu:title', 'webinar_menu_title');
		
		// Listen to notification events and supply a more useful message
		elgg_register_plugin_hook_handler('notify:entity:message', 'object', 'webinar_handler_notify_message');
		
		elgg_register_plugin_hook_handler('permissions_check', 'object', 'webinar_handler_permissions_check');
		
		//intercept event_calendar notification because event that type is webinar are create by webinar object
		elgg_register_plugin_hook_handler('object:notifications','object','webinar_handler_notifications_intercept');
		
		//on event create attendee relation ship, call add_river
		elgg_register_event_handler('create','attendee','webinar_handler_relationship_river');
		
		//elgg_extend_view('groups/left_column', 'webinar/profilegroup');
		//elgg_extend_view('submenu/extend', 'webinar/relationships');
		
		//elgg_extend_view('profile/tabs/menu_extend','webinar/group_profile_tabs_menu');
		
	}
	/**
	 * Dispatcher for webinar.
	 * URLs take the form of
	 *  All webinar:      webinar/all
	 *  View webinar: 	  webinar/view/<guid>
	 *  Add webinar:      webinar/new/<guid_container> (container: user, group)
	 *  Edit webinar:     webinar/edit/<guid>
	 *  User's webinar:   webinar/owner/<guid>/all
	 *  Group webinar:    webinar/group/<guid>/all
	 *  Relationship to webinar : webinar/<attendee | registered>/<guid>
	 *
	 * Title is ignored
	 *
	 * @param array $page
	 * @return bool
	 */
	function webinar_page_handler($page){
	
		elgg_load_library('elgg:webinar');
		
		if (!isset($page[0])) {
			$page[0] = 'all';
		}
		
		$base_dir = elgg_get_plugins_path() . 'webinar/pages/webinar';
		// See what context we're using
		switch($page[0]) {
			case 'all':
    				include "$base_dir/all.php";
    				break;
			case "view":
				if (isset($page[1]) && is_numeric($page[1])) {
					set_input('guid', $page[1]);
					include "$base_dir/view.php";
				}else{
					return false;
				}
				break;
			case "add":
				gatekeeper();
				if (isset($page[1]) && is_numeric($page[1])) {
					set_input('guid', $page[1]);
					include "$base_dir/new.php";
				}else{
					return false;
				}
				break;
			case "edit":
				gatekeeper();
				if (isset($page[1]) && is_numeric($page[1])) {
					set_input('guid', $page[1]);
					include "$base_dir/edit.php";
				}else{
					return false;
				}
				break;
			case "owner":
				include "$base_dir/owner.php";
				break;
			case "group":
				group_gatekeeper();
	    		if (isset($page[1]) && is_numeric($page[1])) {
	    			set_input('guid',$page[1]);
				}
				include "$base_dir/owner.php";	
    			break;
    		case "attendee":
			case "registered":
				if (isset($page[1]) && is_numeric($page[1])) {
					set_input('relationship', $page[0]);
					set_input('guid', $page[1]);
					include "$base_dir/relationShips.php";
				}else{
					return false;
				}
				break;
			default:
	    		return false;
	    		break;
		}
		return true;
	}
	/**
	 * Add a menu item to the user ownerblock
	 */
	function webinar_menu_owner_block($hook, $type, $return, $params) {
		if (elgg_instanceof($params['entity'], 'user')) {
			$url = "webinar/owner/{$params['entity']->username}";
			$item = new ElggMenuItem('webinar', elgg_echo('pages'), $url);
			$return[] = $item;
		} else {
			if ($params['entity']->webinar_enable != "no") {
				$url = "webinar/group/{$params['entity']->guid}/all";
				$item = new ElggMenuItem('webinar', elgg_echo('webinar:group'), $url);
				$return[] = $item;
			}
		}
	
		return $return;
	}
	/**
	 * Add specific webinar links/info to entity menu.
	 */
	function webinar_menu_entity($hook, $type, $return, $params) {
		if (elgg_in_context('widgets')) {
			return $return;
		}
	
		$entity = $params['entity'];
		$handler = elgg_extract('handler', $params, false);
		if ($handler != 'webinar') {
			return $return;
		}
		
		// status
		$status = elgg_view('output/status', array('entity' => $entity));
		$options = array(
				'name' => 'status',
				'text' => $status,
				'href' => false,
				'priority' => 50,
		);
		$return[] = ElggMenuItem::factory($options);
		
		// remove delete if not owner or admin
		if (!elgg_is_admin_logged_in() && elgg_get_logged_in_user_guid() != $entity->getOwnerGuid()) {
			foreach ($return as $index => $item) {
				if ($item->getName() == 'delete') {
					unset($return[$index]);
				}
			}
		}
	
		return $return;
	}
	/**
	 * Add specific webinar action to title menu on view page
	 */
	function webinar_menu_title($hook, $type, $return, $params) {
		if (elgg_in_context('widgets')) {
			return $return;
		}
	
		$entity = $params['entity'];
		$handler = elgg_extract('handler', $params, false);
		if ($handler != 'webinar') {
			return $return;
		}
		if (elgg_is_logged_in()) {
		
			if (!$handler) {
				$handler = elgg_get_context();
			}
		
			$page_owner = elgg_get_page_owner_entity();
			if (!$page_owner) {
				// no owns the page so this is probably an all site list page
				$page_owner = elgg_get_logged_in_user_entity();
			}
			if ($page_owner && $page_owner->canWriteToContainer()) {
				$options = array(	'name' => 'start',
							'href' => "action/webinar/start/{$entity->guid}",
							'text' => elgg_echo("webinar:start"),
							'link_class' => 'elgg-button elgg-button-action',
						);
				$return[] = ElggMenuItem::factory($options);
			}
		}
		
		return $return;
	}
	function webinar_handler_notify_message($hook, $entity_type, $returnvalue, $params)
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
	function webinar_handler_notifications_intercept($hook, $entity_type, $returnvalue, $params) {
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
	function webinar_handler_permissions_check($hook, $entity_type, $returnvalue, $params) {
		if (isset($params)) {
			$entity = $params['entity'];
			$user = $params['user'];
			if ($entity && $entity instanceof ElggWebinar
			    &&  $user && $user instanceof ElggUser) {
				if($entity->getOwnerGUID() == $user->getGUID()){
					$returnvalue = true;
				}else{
					$returnvalue = false;
				}
			}
		}
		return $returnvalue;
	}
	function webinar_url($entity){
		global $CONFIG;
		$title = $entity->title;
		$title = elgg_get_friendly_title($title);
		return $CONFIG->url . "pg/webinar/view/" . $entity->getGUID() . "/" . $title;
	}
