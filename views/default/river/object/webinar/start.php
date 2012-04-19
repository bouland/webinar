<?php

	$performed_by = get_entity($vars['item']->subject_guid); // $statement->getSubject();
	$object = get_entity($vars['item']->object_guid);
	$url = " <a href=\"" . $object->getURL() . "\">" . $object->title . "</a>";
	$string .= sprintf(elgg_echo("webinar:river:start"),$url) . " ";
	
?>

<?php echo $string; ?>