<?php

	$performed_by = get_entity($vars['item']->subject_guid); // $statement->getSubject();
	$object = get_entity($vars['item']->object_guid);
	$objecturl = $object->getURL();
	$contents = strip_tags($object->description);
	
	$url = "<a href=\"{$performed_by->getURL()}\">{$performed_by->name}</a>";
	$string = sprintf(elgg_echo("webinar:meeting:river:create"),$url) . " ";
	$string .= " <a href=\"" . $object->getURL() . "\">" . $object->title . "</a>";
	$string .= "<div class=\"river_content_display\">";
	$string .= elgg_get_excerpt($contents, 200);
	$string .= "</div>";
?>

<?php echo $string; ?>