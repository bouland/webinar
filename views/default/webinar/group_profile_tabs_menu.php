<?php
$group = page_owner_entity();
if ($group && $group instanceof ElggGroup && $group->webinar_enable == 'yes'){
	echo '<li ';
	if (get_context() == 'webinar') {
		echo "class='selected'";
	}
	echo '><a href="' . $vars['url'] . "pg/webinar/owned/" . $group->username . '">' . elgg_echo('webinar:tab') . '</a></li>';
}
	