<?php
	/**
	 * Elgg groups items view.
	 * This is the messageboard, members, pages and latest forums posts. Each plugin will extend the views
	 * 
	 * @package ElggGroups
	 */
	if (isset($vars['page_owner_user']) && $vars['page_owner_user'] instanceof ElggGroup) {
		if (get_context() == 'meeting'){
			$meeting_guid = (int)get_input('meeting_guid');
			$meeting  = get_entity($meeting_guid);
			
			if ($meeting instanceof ElggMeeting) {
				
				$relationships = $meeting->getRelationShip();
				foreach ($relationships as $relationship) {
					$members = elgg_get_entities_from_relationship($meeting->getRelationShipOptions($relationship));
					if(is_array($members)) {
						set_input('members', $members);
						$more_url = "{$vars['url']}pg/meeting/list/{$relationship}/{$meeting_guid}/";
						echo '<div class="submenu_group sidebarBox"><div class="submenu_group_wrapper">';
						echo '<a href="' . $more_url . '">'.elgg_echo("webinar:meeting:list:{$relationship}").' (' . elgg_get_entities_from_relationship($meeting->getRelationShipOptions($relationship,0,true)) . ')</a>';
						echo elgg_view('custom_index_inria/members',array('members' => $members));
						echo '</div></div>';
					}
				}
			}
		}
	}
?>

