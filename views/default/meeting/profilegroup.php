<?php

/**
 * List most recent pages on group profile page
 */

if ($vars['entity']->meeting_enable != 'no') {
	$context = get_context();
	set_context('search');
	$content = elgg_list_entities(array('types' => 'object',
										'subtypes' => array('meeting'),
										'container_guid' => $vars['entity']->guid,
										'limit' => 5,
										'full_view' => FALSE,
										'pagination' => FALSE));
	set_context($context);
	if ($content) {
		echo "<div class=\"group_widget\">";
		$more_url = "{$vars['url']}pg/meeting/owned/{$vars['entity']->username}/";
		echo "<h2><a href=\"$more_url\">" . elgg_echo("webinar:meeting:profilegroup") . "</a></h2>";
	
		echo $content;
		echo "</div>";
	}
}