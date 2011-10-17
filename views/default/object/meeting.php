<?php
	if ($vars['full']) {
		echo elgg_view("meeting/profile",$vars);
	} else {
		if (get_input('search_viewtype') == "gallery") {
			echo elgg_view('meeting/gallery',$vars); 				
		} else {
			echo elgg_view("meeting/listing",$vars);
		}
	}
?>