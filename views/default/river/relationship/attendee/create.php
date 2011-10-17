<?php

	$performed_by = get_entity($vars['item']->subject_guid);
	$object = get_entity($vars['item']->object_guid);
	$objecturl = $object->getURL();

	$url = "<a href=\"{$performed_by->getURL()}\">{$performed_by->name}</a>";
	$string = sprintf(elgg_echo("webinar:meeting:river:attendee:create"),$url) . " ";
	$string .= " <a href=\"" . $object->getURL() . "\">" . $object->title . "</a>";

?>

<?php echo $string; ?>