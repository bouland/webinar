<div class="contentWrapper">
<?php
	global $CONFIG;

	if (empty($vars['entity'])) {

		$container_guid = (int)get_input('container_guid');
		if (!$container_guid) {
			$container_guid = page_owner();
		}
		$container = get_entity($container_guid);
		if ($container) {
			
			if ($container && $container instanceof ElggGroup){
				$access_id = $container->group_acl;
				$write_access_id = $container->group_acl;
			}else{
				$access_id = 0;
				$write_access_id = 0;
			}
		} elseif (defined('ACCESS_DEFAULT')){
			$access_id = ACCESS_DEFAULT;
			$write_access_id = ACCESS_DEFAULT;
		} else {
			$access_id = 0;
			$write_access_id = 0;
		}
		$meeting_guid = NULL;
	    $title = sprintf(elgg_echo('webinar:meeting:default:title'), $container->name);
	    $description = elgg_echo('webinar:meeting:default:description');
	    $owner_guid = page_owner();
	    $serverSalt = get_plugin_setting('serverSalt','webinar');
		if(!$serverSalt)
			$serverSalt = elgg_echo('webinar:meeting:settings');
		$serverURL = get_plugin_setting('serverURL','webinar');
		if(!$serverURL)
			$serverURL = elgg_echo('webinar:meeting:settings');
		$logoutURL = '';
		$welcomeString = sprintf(elgg_echo('webinar:meeting:default:welcome'), $container->name);
		$adminPwd = elgg_echo('webinar:meeting:default:adminPwd');
		$userPwd = elgg_echo('webinar:meeting:default:userPwd');
		$status = 'upcoming';
		if (is_plugin_enabled('event_calendar')){
			$slots = get_free_slots($container_guid, 5);
		}
	}else{
		$meeting = $vars['entity'];
		if ($meeting instanceof ElggMeeting){
			$meeting_guid = $vars['entity']->guid;
		    $access_id = $meeting->access_id;
			$write_access_id = $meeting->write_access_id;
		    $title = $meeting->title;
		    $description = $meeting->description;
		    $tags = $meeting->tags;
		    $owner_guid = $meeting->owner_guid;
			$container_guid = $meeting->container_guid;
			if (is_array($tagarray)) {
				$meeting->tags = $tagarray;
			}
			$serverSalt = $meeting->serverSalt;
			$serverURL = $meeting->serverURL;
			$logoutURL = $meeting->logoutURL;
			$welcomeString = $meeting->welcomeString;
			$adminPwd = $meeting->adminPwd;
			$userPwd = $meeting->userPwd;
			$status = $meeting->status;
		}else{
		
			
		}
	}
	
	$form = '<div><label for="title">' . elgg_echo('webinar:meeting:title') . '</label>' ;
	$form .= elgg_view('input/text', array('internalname' => 'title', 'internalid' => 'title', 'value' => $title)). '</div>';
	
	$form .= '<div><label for="description">' . elgg_echo('webinar:meeting:description') . '</label>' ;
	$form .= elgg_view('input/longtext', array('internalname' => 'description', 'internalid' => 'description', 'value' => $description)). '</div>';
	
	$form .= '<div><label for="tags">' . elgg_echo('webinar:meeting:tags') . '</label>' ;
	$form .= elgg_view('input/tags', array('internalname' => 'tags', 'internalid' => 'tags', 'value' => $tags)). '</div>';
	
	if (isset($slots)){
		
		$form .= '<div><label for="isDated">' . elgg_echo('webinar:meeting:slot:default') . '</label>' ;
		$form .= elgg_view('input/pulldown', array('internalname' => 'isDated', 'internalid' => 'isDated', 'value' => true , 'options_values' => array(true => elgg_echo('option:yes'), false => elgg_echo('option:no')))). '</div>';

		$options = array();
		foreach ($slots as $slot){
			$options[]= event_calendar_get_formatted_time($slot);
		}
		
		$form .= '<div><label for="slot">' . elgg_echo('webinar:meeting:slot') . '</label>' ;
		$form .= elgg_view('input/pulldown', array('internalname' => 'slot', 'internalid' => 'slot', 'value' => 0, 'options_values' => $options)). '</div>';
		$_SESSION['meetingslots'] = $slots;
	}	
	
	
	$form .= '<div><label for="access_id">' . elgg_echo('webinar:meeting:access_id') . '</label>' ;
	$form .= elgg_view('input/access', array('internalname' => 'access_id', 'internalid' => 'access_id', 'value' => $access_id)). '</div>';

	$form .= '<div><label for="write_access_id">' . elgg_echo('webinar:meeting:write_access_id') . '</label>' ;
	$form .= elgg_view('input/access', array('internalname' => 'write_access_id', 'internalid' => 'write_access_id', 'value' => $write_access_id)). '</div>';
	
 	$form .= '<div><label for="welcomeString">' . elgg_echo('webinar:meeting:welcomeString') . '</label>' ;
	$form .= elgg_view('input/text', array('internalname' => 'welcomeString', 'internalid' => 'welcomeString', 'value' => $welcomeString)). '</div>';
	
	$form .= '<div><label for="adminPwd">' . elgg_echo('webinar:meeting:adminPwd') . '</label>' ;
	$form .= elgg_view('input/text', array('internalname' => 'adminPwd', 'internalid' => 'adminPwd', 'value' => $adminPwd)). '</div>';
		
	$form .= '<div><label for="userPwd">' . elgg_echo('webinar:meeting:userPwd') . '</label>' ;
	$form .= elgg_view('input/text', array('internalname' => 'userPwd', 'internalid' => 'userPwd', 'value' => $userPwd)). '</div>';

	$form .= '<div><label for="status">' . elgg_echo('webinar:meeting:status') . '</label>' ;
	$form .= elgg_view('input/pulldown', array('internalname' => 'status', 'internalid' => 'status', 'value' => $status, 'options' => array('upcoming','running','done','cancel'))). '</div>';
	
	if (isadminloggedin()){
		$form .= '<div><label for="serverSalt">' . elgg_echo('webinar:meeting:salt') . '</label>' ;
		$form .= elgg_view('input/text', array('internalname' => 'serverSalt', 'internalid' => 'serverSalt', 'value' => $serverSalt)). '</div>';
		
		$form .= '<div><label for="serverURL">' . elgg_echo('webinar:meeting:serverURL') . '</label>' ;
		$form .= elgg_view('input/text', array('internalname' => 'serverURL', 'internalid' => 'serverURL', 'value' => $serverURL)). '</div>';

		$form .= '<div><label for="logoutURL">' . elgg_echo('webinar:meeting:logoutURL') . '</label>' ;
		$form .= elgg_view('input/text', array('internalname' => 'logoutURL', 'internalid' => 'logoutURL', 'value' => $logoutURL)). '</div>';
	}else{
		$form .= elgg_view('input/hidden', array('internalname' => 'serverURL', 'value' => $serverURL));
		$form .= elgg_view('input/hidden', array('internalname' => 'serverSalt', 'value' => $serverSalt));
		$form .= elgg_view('input/hidden', array('internalname' => 'logoutURL', 'value' => $logoutURL));
	}
	$form .= elgg_view('input/hidden', array('internalname' => 'container_guid', 'value' => $container_guid));
	
	$form .= elgg_view('input/hidden', array('internalname' => 'meeting_guid', 'value' => $meeting_guid));
	
	$form .= elgg_view('input/submit', array('internalname'=> 'save', 'value' => elgg_echo('webinar:meeting:edit:save')));
	
	echo elgg_view('input/form', array('action' => $vars['url'].'action/meeting/save', 'body' => $form, 'internalid' => 'meeting_edit'));

?>
</div>
