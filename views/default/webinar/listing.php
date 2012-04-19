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

	
	
	$info .= elgg_view('entities/footer', $vars);
	
	$vars = array_merge( $vars, array('icon' => $icon, 'info' => $info) );
	
	echo elgg_view('entities/entity_listing', $vars);
?>