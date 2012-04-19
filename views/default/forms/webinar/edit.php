<div class="contentWrapper">
<?php
	global $CONFIG;

	if (empty($vars['entity'])) {

		$container_guid = (int)get_input('container_guid');
		if (!$container_guid) {
			$container_guid = page_owner();
		}
		$container = get_entity($container_guid);
		if (defined('ACCESS_DEFAULT')){
			$access_id = ACCESS_DEFAULT;
			$write_access_id = ACCESS_DEFAULT;
		} else {
			$access_id = 0;
			$write_access_id = 0;
		}
		$webinar_guid = NULL;
	    $title = sprintf(elgg_echo('webinar:default:title'), $container->name);
	    $description = elgg_echo('webinar:default:description');
	    $owner_guid = page_owner();
	    $serverSalt = get_plugin_setting('serverSalt','webinar');
		if(!$serverSalt)
			$serverSalt = elgg_echo('webinar:settings');
		$serverURL = get_plugin_setting('serverURL','webinar');
		if(!$serverURL)
			$serverURL = elgg_echo('webinar:settings');
		$logoutURL = '';
		$welcomeString = sprintf(elgg_echo('webinar:default:welcome'), $container->name);
		$adminPwd = elgg_echo('webinar:default:adminPwd');
		$userPwd = elgg_echo('webinar:default:userPwd');
		$status = 'upcoming';
		if (is_plugin_enabled('event_calendar')){
			$slots = get_free_slots($container_guid, 5);
		}
	}else{
		$webinar = $vars['entity'];
		if ($webinar instanceof ElggWebinar){
			$webinar_guid = $vars['entity']->guid;
		    $access_id = $webinar->access_id;
			$write_access_id = $webinar->write_access_id;
		    $title = $webinar->title;
		    $description = $webinar->description;
		    $tags = $webinar->tags;
		    $owner_guid = $webinar->owner_guid;
			$container_guid = $webinar->container_guid;
			if (is_array($tagarray)) {
				$webinar->tags = $tagarray;
			}
			$serverSalt = $webinar->serverSalt;
			$serverURL = $webinar->serverURL;
			$logoutURL = $webinar->logoutURL;
			$welcomeString = $webinar->welcomeString;
			$adminPwd = $webinar->adminPwd;
			$userPwd = $webinar->userPwd;
			$status = $webinar->status;
		}else{
		
			
		}
	}
	
	$form = '<div><label for="title">' . elgg_echo('webinar:title') . '</label>' ;
	$form .= elgg_view('input/text', array('internalname' => 'title', 'internalid' => 'title', 'value' => $title)). '</div>';
	
	$form .= '<div><label for="description">' . elgg_echo('webinar:description') . '</label>' ;
	$form .= elgg_view('input/longtext', array('internalname' => 'description', 'internalid' => 'description', 'value' => $description)). '</div>';
	
	$form .= '<div><label for="tags">' . elgg_echo('webinar:tags') . '</label>' ;
	$form .= elgg_view('input/tags', array('internalname' => 'tags', 'internalid' => 'tags', 'value' => $tags)). '</div>';
	
	if (isset($slots)){
		
		$form .= '<div><label for="isDated">' . elgg_echo('webinar:slot:default') . '</label>' ;
		$form .= elgg_view('input/pulldown', array('internalname' => 'isDated', 'internalid' => 'isDated', 'value' => true , 'options_values' => array(true => elgg_echo('option:yes'), false => elgg_echo('option:no')))). '</div>';

		$options = array();
		foreach ($slots as $slot){
			$options[]= event_calendar_get_formatted_time($slot);
		}
		
		$form .= '<div><label for="slot">' . elgg_echo('webinar:slot') . '</label>' ;
		$form .= elgg_view('input/pulldown', array('internalname' => 'slot', 'internalid' => 'slot', 'value' => 0, 'options_values' => $options)). '</div>';
		$_SESSION['webinarslots'] = $slots;
	}	
	
	
	$form .= '<div><label for="access_id">' . elgg_echo('webinar:access_id') . '</label>' ;
	$form .= elgg_view('input/access', array('internalname' => 'access_id', 'internalid' => 'access_id', 'value' => $access_id)). '</div>';

	$form .= '<div><label for="write_access_id">' . elgg_echo('webinar:write_access_id') . '</label>' ;
	$form .= elgg_view('input/access', array('internalname' => 'write_access_id', 'internalid' => 'write_access_id', 'value' => $write_access_id)). '</div>';
	
 	$form .= '<div><label for="welcomeString">' . elgg_echo('webinar:welcomeString') . '</label>' ;
	$form .= elgg_view('input/text', array('internalname' => 'welcomeString', 'internalid' => 'welcomeString', 'value' => $welcomeString)). '</div>';
	
	$form .= '<div><label for="adminPwd">' . elgg_echo('webinar:adminPwd') . '</label>' ;
	$form .= elgg_view('input/text', array('internalname' => 'adminPwd', 'internalid' => 'adminPwd', 'value' => $adminPwd)). '</div>';
		
	$form .= '<div><label for="userPwd">' . elgg_echo('webinar:userPwd') . '</label>' ;
	$form .= elgg_view('input/text', array('internalname' => 'userPwd', 'internalid' => 'userPwd', 'value' => $userPwd)). '</div>';

	$form .= '<div><label for="status">' . elgg_echo('webinar:status') . '</label>' ;
	$form .= elgg_view('input/pulldown', array('internalname' => 'status', 'internalid' => 'status', 'value' => $status, 'options' => array('upcoming','running','done','cancel'))). '</div>';
	
	if (isadminloggedin()){
		$form .= '<div><label for="serverSalt">' . elgg_echo('webinar:salt') . '</label>' ;
		$form .= elgg_view('input/text', array('internalname' => 'serverSalt', 'internalid' => 'serverSalt', 'value' => $serverSalt)). '</div>';
		
		$form .= '<div><label for="serverURL">' . elgg_echo('webinar:serverURL') . '</label>' ;
		$form .= elgg_view('input/text', array('internalname' => 'serverURL', 'internalid' => 'serverURL', 'value' => $serverURL)). '</div>';

		$form .= '<div><label for="logoutURL">' . elgg_echo('webinar:logoutURL') . '</label>' ;
		$form .= elgg_view('input/text', array('internalname' => 'logoutURL', 'internalid' => 'logoutURL', 'value' => $logoutURL)). '</div>';
	}else{
		$form .= elgg_view('input/hidden', array('internalname' => 'serverURL', 'value' => $serverURL));
		$form .= elgg_view('input/hidden', array('internalname' => 'serverSalt', 'value' => $serverSalt));
		$form .= elgg_view('input/hidden', array('internalname' => 'logoutURL', 'value' => $logoutURL));
	}
	$form .= elgg_view('input/hidden', array('internalname' => 'container_guid', 'value' => $container_guid));
	
	$form .= elgg_view('input/hidden', array('internalname' => 'webinar_guid', 'value' => $webinar_guid));
	
	$form .= elgg_view('input/submit', array('internalname'=> 'save', 'value' => elgg_echo('webinar:edit:save')));
	
	echo elgg_view('input/form', array('action' => $vars['url'].'action/webinar/save', 'body' => $form, 'internalid' => 'webinar_edit'));

?>
</div>
