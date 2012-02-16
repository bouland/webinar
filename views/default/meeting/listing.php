<?php
	/**
	 * Elgg Pages
	 * 
	 * @package ElggPages
	 */

	$icon = elgg_view(
			"graphics/icon", array(
			'entity' => $vars['entity'],
			'size' => 'small',
		  )
		);

	$info .= "<p><b><a href=\"" . $vars['entity']->getUrl() . "\">" . $vars['entity']->title . "</a></b></p>";

	
	$latest = $vars['entity']->getAnnotations('page', 1, 0, 'desc');
	if ($latest) {
		$latest = $latest[0];
	
		$time_updated = $latest->time_created;
		$owner_guid = $latest->owner_guid;
		$owner = get_entity($owner_guid);
		
			
		$info .= "<p class=\"owner_timestamp\">".sprintf(elgg_echo("pages:strapline"),
						elgg_view_friendly_time($time_updated),
						"<a href=\"" . $owner->getURL() . "\">" . $owner->name ."</a>"
		) . "</p>";
	}
	
	$vars = array_merge( $vars, array('icon' => $icon, 'info' => $info) );
	
	echo elgg_view('entities/entity_listing', $vars);
?>