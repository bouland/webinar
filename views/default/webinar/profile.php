<?php
	/**
	 * Elgg Pages
	 * 
	 * @package ElggPages
	 */

	// Output body
	$webinar = $vars['entity'];
	if ($webinar && $webinar instanceof ElggWebinar) {	
		
		
		
		$rev = (int)get_input('rev');
	
		if ($rev) {	
			$latest = get_annotation($rev);	
		}
		else
		{
			$latest = $webinar->getAnnotations('webinar', 1, 0, 'desc');
			if ($latest) $latest = $latest[0];
		}
	
		echo '<div class="contentWrapper">';	
		echo '<div id="webinar_page">';
		$status = $webinar->status;
		echo '<h2>' . elgg_echo('webinar:status:title') . elgg_echo("webinar:status:{$status}") . '</h2>';
		
		echo elgg_view('output/longtext', array('value' => /*$entity->description*/ $latest->value));
		
		$tags = $vars['entity']->tags;
		if (!empty($tags)) {
			echo '<p class="tags">';	
			echo elgg_view('output/tags', array('tags' => $tags));
			echo '</p>';
		}

		$cats = elgg_view('categories/view',$vars);
		if (!empty($cats)) {
			echo '<p class="categories">';
			echo $cats;
			echo '</p>';
		}
		$event = $webinar->getEvent();
		if (is_array($event)){
			echo elgg_view_entity($event[0], false);
		}
		
		
		echo '</div>';
		echo '</div>';
	}