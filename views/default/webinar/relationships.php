<?php
	/**
	 * Elgg groups items view.
	 * This is the messageboard, members, pages and latest forums posts. Each plugin will extend the views
	 * 
	 * @package ElggGroups
	 */
	if (isset($vars['page_owner_user']) && $vars['page_owner_user'] instanceof ElggGroup) {
		if (get_context() == 'webinar'){
			$webinar_guid = (int)get_input('webinar_guid');
			$webinar  = get_entity($webinar_guid);
			
			if ($webinar instanceof ElggWebinar) {
				
				$relationships = $webinar->getRelationShip();
				foreach ($relationships as $relationship) {
					$members = elgg_get_entities_from_relationship($webinar->getRelationShipOptions($relationship));
					if(is_array($members)) {
						set_input('members', $members);
						$more_url = "{$vars['url']}pg/webinar/list/{$relationship}/{$webinar_guid}/";
						echo '<div class="submenu_group sidebarBox"><div class="submenu_group_wrapper">';
						echo '<a href="' . $more_url . '">'.elgg_echo("webinar:list:{$relationship}").' (' . elgg_get_entities_from_relationship($webinar->getRelationShipOptions($relationship,0,true)) . ')</a>';
						echo elgg_view('custom_index_inria/members',array('members' => $members));
						echo '</div></div>';
					}
				}
			}
		}
	}
?>

