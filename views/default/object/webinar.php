<?php
	if ($vars['full']) {
		echo elgg_view("webinar/profile",$vars);
	} else {
		if (get_input('search_viewtype') == "gallery") {
			echo elgg_view('webinar/gallery',$vars); 				
		} else {
			echo elgg_view("webinar/listing",$vars);
		}
	}
?>